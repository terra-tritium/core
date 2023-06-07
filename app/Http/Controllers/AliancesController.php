<?php

namespace App\Http\Controllers;

use App\Models\Aliances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 *   @OA\Schema(
 *     schema="Aliances",
 *     required={"name"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="avatar", type="file")
 * )
 *
 * @OA\Schema(
 *     schema="AliancesUpdateRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="New Name"),
 *     @OA\Property(property="description", type="string", example="New Description"),
 * )
 */
class AliancesController extends Controller
{
    /**
     * * @OA\Get(
     *     path="/aliances/list",
     *     tags={"Aliances"},
     *     summary="List all alliances",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Aliances")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error finding alliances",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error finding alliances")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $aliances = Aliances::all();

            return response()->json($aliances, 200);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(['message' => 'Error finding alliances'], 500);
        }
    }

    /**
     *
     *@OA\Post(
     *     path="/aliances/create",
     *     tags={"Aliances"},
     *     summary="Create an alliance",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="avatar", type="file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Alliance created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Aliances")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error: failed to create alliance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error: failed to create alliance")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        try {
            $aliances = app(Aliances::class);
            $aliances->name = $request->input('name');
            $aliances->description = $request->input('description');

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileUrl = $this->addImage($file);
                $aliances->avatar = $fileUrl;
            }

            $aliances->save();

            return response()->json($aliances, 201);

        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error : failed to create alliances'], 500);
        }
    }

    /**
     *
     * * @OA\Put(
     *     path="/aliances/edit/{id}",
     *     tags={"Aliances"},
     *     summary="Update an alliance",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alliance to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AliancesUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alliance updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Aliances")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error: failed to update alliance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error: failed to update alliance")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $aliances = Aliances::findOrFail($id);

        try {
            $aliances->fill($request->all([
                'name',
                'description',
                'avatar'
            ]));

            if ($request->has('name')) {
                $aliances->name = $request->input('name');
            }

            if ($request->has('description')) {
                $aliances->description = $request->input('description');
            }

            if ($request->hasFile('avatar')) {
                $imagePath = $request->file('avatar')->store('public/uploads');
                $aliances->avatar = $imagePath;
            }

            $aliances->save();

            return response()->json($aliances, 200);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error : failed to update alliances'], 500);
        }
    }

    /**
     *
     * @OA\Delete(
     *     path="/aliances/delete/{id}",
     *     tags={"Aliances"},
     *     summary="Delete an alliance",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alliance to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alliance deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alliance deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting alliance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error deleting alliance")
     *         )
     *     )
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $aliances = Aliances::findOrFail($id);
            $aliances->delete();

            return response()->json(['message' => 'Alliances deleted successfully'], 200);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error deleting Alliances'], 500);
        }
    }

    public function updateAvatar(Request $request, int $id)
    {
        $aliances = Aliances::findOrFail($id);

        try {
            if ($request->file('avatar')) {
                $imagePath = $request->file('avatar')->store('images');
                $aliances->avatar = $imagePath;
            }

            $aliances->save();

            return response()->json($aliances, 200);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error: failed to update avatar'], 500);
        }
    }

    public function addImage($imageFile)
    {
        $fileName = time() . '_' . $imageFile->getClientOriginalName();
        $filePath = $imageFile->storeAs('images', $fileName);
        $fileUrl = Storage::url($filePath);

        return $fileUrl;
    }


}
