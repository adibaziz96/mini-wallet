<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Return a list of all users except the authenticated user.
     */
    public function index()
    {
        $currentUserId = Auth::id();

        $users = User::where('id', '<>', $currentUserId)
            ->select('id', 'name', 'email')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($users);
    }
}
