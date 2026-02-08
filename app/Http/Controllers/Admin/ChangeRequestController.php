<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Place;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $changeRequests = ChangeRequest::where('status', 'pending')
        ->with(['user','place'])
        ->orderBy('created_at', 'asc')->get();

        if($request->has('status')){
            $status = $request->query('status');
            $changeRequests = $changeRequests->where('status', $status)->values();
        }

        if($request->has('action')){
            $action = $request->query('action');
            $changeRequests = $changeRequests->where('action_type', $action)->values();
        }

        return RequestResource::collection($changeRequests);
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
    public function approve($id)
    {
        $request = ChangeRequest::findOrFail($id);

        if($request->status !== 'pending'){
            return response()->json(['message' => 'Request already processed'], 400);
        }

        $data = $request->payload;

        if($request->action_type === 'create'){
            $place = Place::create(collect($data)->except('photos_ids')->toArray());
            $this->syncPhotos($place->$id, $data['photos_ids'] ?? []);
        }

        else if($request->action_type === 'update'){
            $place = Place::findOrFail($request->place_id);
            $place->update(collect($data)->except('photos_ids')->toArray());
            $this->syncPhotos($place->$id, $data['photos_ids'] ?? []);
        }

        else if($request->action_type === 'delete'){
            $place = Place::findOrFail($request->place_id);
            $place->delete();
        }

        $request->update(['status' => 'approved']);

        return response()->json([
            "message" => "Change request approved and applied successfully"
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChangeRequest $changeRequest)
    {
        //
    }
}
