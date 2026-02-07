<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChangeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $requests = ChangeRequest::where('status', 'pending')
        ->with(['user:id, name','place:id, name'])
        ->orderBy('created_at', 'asc')->get();

        return response()->json($requests);
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
