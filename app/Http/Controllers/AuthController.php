<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private AuthInterface $authInterface;

    public function __construct(AuthInterface $authInterface)
    {
        $this->authInterface = $authInterface;
    }


    public function register(RegistrationRequest $request)
    {

        $data = [
            'email' => $request->email,
            'name' => $request->name,
            'password' => $request->password,
        ];


        DB::beginTransaction();

        try {
            $user = $this->authInterface->register($data);

            DB::commit();

            return ApiResponse::sendResponse(true, [new UserResource($user)], 'Opération effectuée.', 201);

        } catch (\Throwable $ex) {

            return ApiResponse::rollback($ex);
        }

    }

    public function login(LoginRequest $request)
    {

        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];


        DB::beginTransaction();

        try {

            $user = $this->authInterface->login($data);

            DB::commit();

            if (!$user) {
                return ApiResponse::sendResponse(
                    false,
                    [],
                    'Identifiants invalides.',
                    200
                );

            } else {
                return ApiResponse::sendResponse(
                    true,
                    [new UserResource($user)],
                    'Login successfully.',
                    200
                );
            }


        } catch (\Throwable $ex) {
            return ApiResponse::rollback($ex);
        }

    }

    public function checkOtpCode(Request $request)
    {

        $data = [
            'email' => $request->email,
            'code' => $request->code,
        ];

        DB::beginTransaction();

        try {

            $user = $this->authInterface->checkOtpCode($data);

            DB::commit();

            if (!$user) {
                return ApiResponse::sendResponse(
                    false,
                    [],
                    'Code de confirmation inalide.',
                    200
                );
            }

            return ApiResponse::sendResponse(
                true,
                [new UserResource($user)],
                'Code de confirmation inalide.',
                200
            );


        } catch (\Throwable $ex) {
            return ApiResponse::rollback($ex);
        }

    }


    public function logout()
    {

        try {
            $user = User::find(auth()->user()->getAuthIdentifier());
            
            $user->tokens()->delete();

            return ApiResponse::sendResponse(
                true,
                [],
                'utilisateur déconnecté',
                200
            );
        } catch (\Throwable $th) {
            return ApiResponse::rollback($th);
        }

    }


}
