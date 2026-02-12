<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(){
        $users = User::with('roles')->get();
        return UserResource::collection($users);
    }

    public function updateRole(Request $request, $id){
        try{
            $user = User::find($id);

            if(!$user){
                return response()->json(['message' => 'User not found'], 404);
            }

            $data = $request->validate(User::validations(), User::messageErrors());
            $newRole = $request->input('role');

            if ($user->id === auth()->id() && $newRole !== 'admin') {
                return response()->json(['message' => 'You can not change your own role as admin'], 403);
            }

            $user->syncRoles($newRole);

            return response()->json([
                "message" => "User role updated successfully",
                "user" => new UserResource($user->load('roles'))
            ]);
        }
        catch(ValidationException $e){
            return response()->json([
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);
        }
    }
}
