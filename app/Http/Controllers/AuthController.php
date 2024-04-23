<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use App\Enums\TaskTokenEnum;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Log in an user.
     * 
     * @param Request $request The given request
     * @return void 
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                ['message' => 'The provided credentials are incorrect.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return response()->json([
            'access_token' => $user->createToken(
                'task-access',
                [
                    TaskTokenEnum::List,
                    TaskTokenEnum::Read,
                    TaskTokenEnum::Create,
                    TaskTokenEnum::Update,
                    TaskTokenEnum::Delete,
                ]
            )->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }
}