<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Auth
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (Auth::attempt($credentials)) {
            return  response([
                'status' => 'success',
                'result' => Auth::user()
            ], 200);
        }

        return  response(
            [
                'status' => 'Error',
                'result' => 'Wrong Password Or email'
            ],
            400
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $loggedInUser = Auth::user();
        $loggedInUser = User::find(1);
        // if ($loggedInUser->role !== 'admin') {
        //     return response([
        //         'status' => 'failed',
        //         'result' => []
        //     ], 403);
        // }

        $users = User::all();

        return response([
            'status' => 'success',
            'result' => $users
        ]);
    }
}