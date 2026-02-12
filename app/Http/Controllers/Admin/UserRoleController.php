<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

use App\Models\User;

class UserRoleController extends Controller
{
    public function index(){
        $users = User::with('roles')->get();
        return UserResource::collection($users);
    }
}
