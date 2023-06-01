<?php

namespace App\Http\Controllers;

use App\Models\Aliances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AliancesController extends Controller
{
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
