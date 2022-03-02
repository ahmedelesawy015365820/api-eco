<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use Message;

    // login user & create token
    public function login(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if($v->fails()) {
            return $this->sendError('Your Email/Password is wrong',$v->errors(),401);
        }

        //start access token
        $credentials = $request->only("email", "password");

        if ($token = Auth::guard('api')->attempt($credentials)) {

            if(Auth::guard('api')->user()->status != 'InActive'){

                return  $this->sendResponse($this->respondWithToken($token),'Data exited successfully');

            }else{

                return $this->sendError('You do not have access to');
            }

        }else{

            return $this->sendError('Your Email/Password is wrong');
        }

    }//**********end login************/

    //logout
    public function logout(Request $request) {

        auth()->guard('api')->logout();

        return $this->sendResponse([],'User successfully signed out');
    }//**********end logout************/

    // create token
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => new UserResourse(auth()->guard('api')->user())
        ];

    }//**********end respondWithToken************/
}
