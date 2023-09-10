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

        if (! $foundUser = User::find($userId)) {
            return response('Unable to find user: User not updated', 404)
                  ->header('Content-Type', 'text/plain');
        }

        $foundUser->update($user);

        $badResponse = response('User Update has failed: User not updated', 400)
        ->header('Content-Type', 'text/plain');

        return $foundUser ? 'User has been updated.' : $badResponse;
    }

    public function delete($userId)
    {
        $user = User::find($userId);

        if (! $user) {
            return response('Unable to find user: User not deleted', 404)
            ->header('Content-Type', 'text/plain');
        }

        $user->delete();
    }
}
