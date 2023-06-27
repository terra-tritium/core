<?php

namespace App\Http\Controllers;

use App\Models\Aliance;
use App\Models\Aliances;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

            return response()->json($aliances, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(['message' => 'Error finding alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $aliances->type = $request->input('type');
            $aliances->founder = Player::getPlayerLogged();

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileUrl = $this->addImage($file);
                $aliances->avatar = $fileUrl;
            }

            $aliances->save();

            return response()->json($aliances, Response::HTTP_OK);

        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error : failed to create alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
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

            if ($request->has('type')) {
                $aliances->type = $request->input('type');
            }

            if ($request->hasFile('avatar')) {
                $imagePath = $request->file('avatar')->store('public/uploads');
                $aliances->avatar = $imagePath;
            }

            $aliances->save();

            return response()->json($aliances, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error : failed to update alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
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

            return response()->json(['message' => 'Alliances deleted successfully'], Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error deleting Alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
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

            return response()->json($aliances, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error: failed to update avatar'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function joinAliance(Request $request)
    {
        $aliancaId = $request->input('alianca_id');
        $aliance = Aliance::findOrFail($aliancaId);

        $player = Player::getPlayerLogged();
        $player->aliance = $aliancaId;
        $player->save();

        if ($aliance->type == 0) {
            $player->aliance = $aliancaId;
            $player->save();
            return response()->json(['message' => 'Player joined the alliance successfully'], Response::HTTP_OK);
        } elseif ($aliance->type == 1) {

            $existingRequest = DB::table('aliances_requests')
                ->where('player_id', $player->id)
                ->where('aliance_id', $aliancaId)
                ->first();

            if ($existingRequest) {
                return response()->json(['message' => 'Request already sent. Wait for founder approval.']);
            }

            DB::table('aliances_requests')->insert([
                'player_id' => $player->id,
                'founder_id' => $aliance->founder,
                'message' => 'Solicitação de entrada na aliança',
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json(['message' => 'Request sent to alliance founder'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Invalid alliance type'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function handlePlayerRequest(Request $request)
    {
        $playerId = $request->input('player_id');
        $alianceId = $request->input('aliance_id');
        $acceptRequest = $request->input('accept_request'); // true or false

        if ($acceptRequest) {
            $result = $this->alianceService->acceptPlayerRequest($playerId, $alianceId);

            if ($result) {
                // Atualizar a solicitação com o status de aceitação
                DB::table('aliances_requests')
                    ->where('player_id', $playerId)
                    ->where('aliance_id', $alianceId)
                    ->update([
                        'status' => 'accepted',
                        'message' => 'Request accepted by founder.',
                    ]);

                // Atualizar o jogador com o ID da aliança
                Player::where('id', $playerId)->update(['aliance' => $alianceId]);

                return response()->json(['message' => 'Player accepted into the alliance.']);
            } else {
                // Atualizar a solicitação com o status de recusa
                DB::table('aliances_requests')
                    ->where('player_id', $playerId)
                    ->where('aliance_id', $alianceId)
                    ->update([
                        'status' => 'failed',
                        'message' => 'Failed to accept player into alliance.',
                    ]);

                return response()->json(['message' => 'Failed to accept player into alliance.']);
            }
        } else {
            // Atualizar a solicitação com o status de recusa
            DB::table('aliances_requests')
                ->where('player_id', $playerId)
                ->where('aliance_id', $alianceId)
                ->update([
                    'status' => 'rejected',
                    'message' => 'Request declined by founder.',
                ]);

            return response()->json(['message' => 'Player declined in alliance.']);
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
