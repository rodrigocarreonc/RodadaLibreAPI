<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function upload(Request $request)
    {
        try {
            $request->validate(Photo::createValidation(), Photo::createMessageErrors());

            $user = auth()->user();
            
            $status = $user->hasAnyRole(['admin', 'moderator']) ? 'approved' : 'pending';

            $uploadedPhotos = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('photos', 'public');

                    $photo = Photo::create([
                        'url_source' => asset('storage/' . $path),
                        'status' => $status,
                        'place_id' => null
                    ]);

                    $uploadedPhotos[] = $photo;
                }
            }

            return response()->json([
                "message" => "Photos uploaded successfully",
                "photos" => $uploadedPhotos
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        //
    }
}
