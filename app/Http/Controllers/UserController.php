<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::paginate(20);
    }

    public function create(Request $request)
    {
        $newUser = $request->only('name', 'email', 'phone', 'date_of_birth');

        User::create($newUser);
    }

    public function update($userId, Request $request)
    {
        $user = $request->only('name', 'email', 'phone', 'date_of_birth');

        if (! User::find($userId)->update($user)) {
            return 'User not updated';
        }

        return 'User has been updated.';
    }

    public function delete($userId)
    {
        return User::find($userId)->delete();
    }
}
