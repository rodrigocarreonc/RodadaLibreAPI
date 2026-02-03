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
        try{
            $data = $request->validate(Photo::createValidation(), Photo::createMessageErrors());
            $path = $request->file('image')->store('photos', 'public');

            $photo = Photo::create([
                'url_source' => asset('storage/'.$path),
                'status' => 'pending',
                'place_id' => null
            ]);

            return response()->json([
                "message" => "Photo uploaded successfully",
                "photo" => $photo
            ], 201);

        }
        catch(ValidationException $e){
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
