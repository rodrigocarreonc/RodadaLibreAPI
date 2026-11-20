<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\ChangeRequest;
use App\Models\Photo;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlaceController extends Controller
{
    private const NEARBY_RADIUS_KM = 30;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'lat' => 'nullable|required_with:lng|numeric|between:-90,90',
                'lng' => 'nullable|required_with:lat|numeric|between:-180,180',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $query = Place::with('category', 'photos');

        if ($request->has('category')) {
            $query->where('category_id', $request->query('category'));
        }

        // Without a location, return everything so older app versions keep working
        if (! $request->filled('lat')) {
            return PlaceResource::collection($query->get());
        }

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');

        // Bounding box in SQL first, then the exact distance in PHP on the few rows left
        $latDelta = self::NEARBY_RADIUS_KM / 111.045;
        $lngDelta = self::NEARBY_RADIUS_KM / (111.045 * max(cos(deg2rad($lat)), 0.01));

        $places = $query
            ->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
            ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta])
            ->get()
            ->filter(fn (Place $place) => self::distanceKm($lat, $lng, $place->latitude, $place->longitude) <= self::NEARBY_RADIUS_KM)
            ->values();

        return PlaceResource::collection($places);
    }

    private static function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 6371 * 2 * asin(min(1, sqrt($a)));
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
        try {
            $data = $request->validate(Place::validations(), Place::messageErrors());

            $user = auth()->user();
            if ($user->hasAnyRole(['admin', 'moderator'])) {
                $place = Place::create(collect($data)->except('photo_ids')->toArray());

                // Associate photos with place by id
                if (! empty($request->photo_ids)) {
                    Photo::withoutGlobalScope('approved')
                        ->whereIn('id', $request->photo_ids)
                        ->update(['place_id' => $place->id, 'status' => 'approved']);
                }

                return response()->json([
                    'message' => 'Place created successfully',
                    'place' => new PlaceResource($place->load('category', 'photos')),
                ], 201);
            }

            ChangeRequest::create([
                'user_id' => $user->id,
                'action_type' => 'create',
                'payload' => $data,
            ]);

            return response()->json([
                'message' => 'Thanks! Your request has been submitted successfully',
            ], 202);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place)
    {
        return new PlaceResource($place->load('category', 'photos'));
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
    public function update(Request $request, $id)
    {
        try {
            $place = Place::find($id);

            if (! $place) {
                return response()->json([
                    'message' => 'Place not found',
                ], 404);
            }

            $data = $request->validate(Place::validations(), Place::messageErrors());

            $user = auth()->user();
            if ($user->hasAnyRole(['admin', 'moderator'])) {
                $currentPhotoIds = $place->photos->pluck('id')->toArray();
                $newPhotoIds = $request->photo_ids ?? [];

                $photosIdsToDessociate = array_diff($currentPhotoIds, $newPhotoIds);

                if (! empty($photosIdsToDessociate)) {
                    Photo::withoutGlobalScope('approved')
                        ->whereIn('id', $photosIdsToDessociate)
                        ->update(['place_id' => null, 'status' => 'pending']);
                }

                $place->update(collect($data)->except('photo_ids')->toArray());

                if (! empty($request->photo_ids)) {
                    Photo::withoutGlobalScope('approved')
                        ->whereIn('id', $request->photo_ids)
                        ->update(['place_id' => $place->id, 'status' => 'approved']);
                }

                return response()->json([
                    'message' => 'Place updated successfully',
                    'place' => new PlaceResource($place->load('category', 'photos')),
                ], 200);
            }

            ChangeRequest::create([
                'user_id' => $user->id,
                'place_id' => $place->id,
                'action_type' => 'update',
                'payload' => $data,
            ]);

            return response()->json([
                'message' => 'Thanks! Your request has been submitted successfully',
            ], 202);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $place = Place::find($id);
        $user = auth()->user();

        if (! $place) {
            return response()->json([
                'message' => 'Place not found',
            ], 404);
        }

        if ($user->hasAnyRole(['admin', 'moderator'])) {
            $place->delete();

            return response()->json([
                'message' => 'Place deleted successfully',
            ], 200);
        }

        $pending = ChangeRequest::where('place_id', $place->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('action_type', 'delete')
            ->exists();

        if ($pending) {
            return response()->json([
                'message' => 'You already have a pending delete request for this place',
            ], 409);
        }

        ChangeRequest::create([
            'user_id' => $user->id,
            'place_id' => $place->id,
            'action_type' => 'delete',
            'payload' => null,
        ]);

        return response()->json([
            'message' => 'Thanks! Your delete request has been submitted successfully',
        ], 202);
    }
}
