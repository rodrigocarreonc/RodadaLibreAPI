<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ChangeRequest $changeRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChangeRequest $changeRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChangeRequest $changeRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChangeRequest $changeRequest)
    {
        //
    }
}
