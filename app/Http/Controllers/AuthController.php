<?php

namespace App\Http\Controllers;

use App\Enums\Auth\Token\TaskTokenEnum;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
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

        // TODO: handle admin.

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                ['message' => 'The provided credentials are incorrect.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // if ($user->isAdmin()) {
            // TODO: create another token.
        // }

        return response()->json([
            'access_token' => $user->createToken(
                TaskTokenEnum::NAME,
                [
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
