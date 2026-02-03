<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Http\Resources\PlaceResource;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $places = Place::all()->load('category', 'photos');

        if ($request->has('category')) {
            $categoryId = $request->query('category');
            $places = $places->where('category_id', $categoryId)->values();
        }
        
        return PlaceResource::collection($places);
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
    public function store(Request $request)
    {
        try{
            $data = $request->validate(Place::createValidation(), Place::createMessageErrors());

            $place = Place::create(collect($data)->except('photo_ids')->toArray());

            // Associate photos with place by id
            if (!empty($request->photo_ids)) {
                Photo::withoutGlobalScope('approved')
                    ->whereIn('id', $request->photo_ids)
                    ->update(['place_id' => $place->id, 'status' => 'approved']);
            }

            return response()->json([
                "message" => "Place created successfully",
                "place" => new PlaceResource($place->load('category', 'photos'))
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
    public function show(Place $place)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Place $place)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Place $place)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Place $place)
    {
        //
    }
}
