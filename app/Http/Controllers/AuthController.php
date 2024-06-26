<?php

namespace App\Http\Controllers;

use App\Enums\Auth\Token\ProjectTokenEnum;
use App\Enums\Auth\Token\TaskTokenEnum;
use App\Enums\Auth\Token\UserTokenEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Log in an user.
     *
     * @param  Request  $request  The given request.
     * @return void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(
                ['message' => 'The provided credentials are incorrect.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $accessToken = ($user->isAdmin())
            ? $this->createAdminToken($user)
            : $this->createUserToken($user);

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Create a new token for a user with the
     * role ADIMINISTATOR.
     */
    private function createAdminToken($user): NewAccessToken
    {
        return $user->createToken(
            'admin-token',
            array_map(
                fn ($tokenEnum) => $tokenEnum->value,
                array_merge(
                    TaskTokenEnum::cases(),
                    ProjectTokenEnum::cases(),
                    UserTokenEnum::cases()
                )
            )
        );
    }

    /**
     * Create a new token for a user with the role USER.
     */
    private function createUserToken($user): NewAccessToken
    {
        return $user->createToken(
            TaskTokenEnum::NAME,
            [
                TaskTokenEnum::Read,
                TaskTokenEnum::Create,
                TaskTokenEnum::Update,
                TaskTokenEnum::Delete,
            ]
        );
    }
}
