<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            return  response(
                [
                    'status' => 'success',
                    'result' => Auth::user()
                ],
                200
            );
        }

        return  response(
            [
                'status' => 'failed',
                'result' => 'Wrong Password Or email'
            ],
            200
        );
    }


    /**
     * Auth
     *
     * @return \Illuminate\Http\Response
     */
    public function signUp(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:users,name|string',
                'email' => 'required|unique:users,email|string',
                'password' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'failed',
                    'result'    => 'name or email already taken'
                ],
                200
            );
        }

        $user = User::create([
            'name' => $request->all()['name'],
            'email' => $request->all()['email'],
            'password' => Hash::make($request->all()['password'])
        ]);

        return  response(
            [
                'status' => 'success',
                'result' => $user
            ],
            200
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(
                [
                    'status' => 'failed',
                    'result' => []
                ],
                404
            );
        }
        try {
            $user->update([
                'name' => $request->all()['name'],
                'email' => $request->all()['email'],
            ]);
        } catch (\Throwable $th) {
            return response([
                'status' => 'faild',
                'message' => 'Username Or Email Already Exists',
                'result' => []
            ], 400);
        }

        return response([
            'status' => 'success',
            'result' => $user
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response(
                [
                    'status' => 'failed',
                    'result' => []
                ],
                404
            );
        }

        if (Hash::check($request->all()['confirm_password'], $user->password)) {
            try {
                $user->update([
                    'name' => $request->all()['name'],
                    'email' => $request->all()['email'],
                ]);
            } catch (\Throwable $th) {
                return response(
                    [
                        'status' => 'failed',
                        'message' => 'Username Or Email Already Exists',
                        'result' => []
                    ],
                    200
                );
            }
            return response([
                'status' => 'success',
                'result' => $user
            ]);
        } else {

            return response([
                'status' => 'failed',
                'message' => 'Password Failed',
                'result' => []
            ]);
        }
    }
}