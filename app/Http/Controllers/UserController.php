<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartialUserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function validate(UserRequest|UserLoginRequest|PartialUserRequest $request, $keys = ["name", "email", "password"])
    {
        return $request->only($keys);
    }
    public function parseUserToken($token)
    {
        $token = PersonalAccessToken::findToken($token);
        return $token->tokenable_id;
    }
    public function create(UserRequest $request)
    {
        $data = $this->validate($request);
        $user = User::create($data);
        if ($user) {
            return response([
                "messsage" => "Account created successfuly",
                "user" => new UserResource($user)
            ], 201);
        } else {
            return response([
                "messsage" => "Internal server error !"
            ], 500);
        }
    }
    public function login(UserLoginRequest $request)
    {
        $data = $this->validate($request, ["email", "password"]);
        $user = User::where("email", $data["email"])->first();
        if (!$user)
            return response(["message" => "Internal server error !"], 500);

        if (!Hash::check($data["password"], $user->password))
            return response(["message" => "The password is incorrect"], 400);

        $token = $user->createToken($user->name);
        return response(["token" => $token->plainTextToken]);
    }
    public function update(PartialUserRequest $request)
    {
        $data = $this->validate($request);
        if (!$data)
            return response(["message" => "Empty request !"], 400);

        $userId = $this->parseUserToken($request->header("token"));
        $user = User::where("id", $userId)->first();
        if (!$user)
            return response(["message" => "Internal server error !"], 500);

        $user->update($data);
        return response([
            "message" => "Account data updated successfuly",
            "user" => new UserResource($user)
        ]);
    }
}
