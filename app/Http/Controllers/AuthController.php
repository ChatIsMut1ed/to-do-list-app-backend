<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $loggedInUser = Auth::user();
        if ($loggedInUser->role !== 'admin') {
            return response([
                'status' => 'failed',
                'result' => []
            ], 403);
        }

        $users = User::whereNot('id', $loggedInUser->id)->get();

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
    public function update(UpdateUserRequest $request, int $id)
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
    public function updateProfile(UpdateUserRequest $request, int $id)
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


    public function sendEmail(Request $request)
    {
        $user = User::where('email', $request->all()['email'])->first();
        if (!$user) {
            return response([
                'status' => 'failed',
                'result' => 'Email does not exsits'
            ]);
        }

        $key = Str::random(16);
        $user->remember_token = $key;

        $data = [
            'name' => $user->name,
            'message' => 'Verification Code: ' . $key,
        ];

        $email = $user->email;

        $message = "Hello {$data['name']},\n\n{$data['message']}\n\nBest regards";
        Mail::raw($message, function ($m) use ($email) {
            $m->to($email)->subject('Recover Password');
        });

        $user->save();
        return response([
            'status' => 'success',
            'result' => 'Email was sent successfully'
        ]);
    }


    public function resetPassowrd(Request $request)
    {
        $user = User::where('email', $request->all()['email'])->first();
        if (!$user) {
            return response([
                'status' => 'failed',
                'result' => 'Email does not exsits'
            ], 400);
        }

        if ($user->remember_token !== $request->all()['key']) {
            return response(
                [
                    'status' => 'failed',
                    'result' => 'Wrong Key'
                ],
                400
            );
        }

        $user->remember_token = '';
        $user->save();

        $user->update([
            'password' => Hash::make($request->all()['password'])
        ]);

        return  response(
            [
                'status' => 'success',
                'result' => 'Success'
            ],
            200
        );
    }
}