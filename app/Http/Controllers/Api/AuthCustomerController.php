<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;


class AuthCustomerController extends Controller
{
    use Message;

    public function register(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6',
            'password_copy' => 'required|same:password',
            'image' => 'required|mimes:jpg,png',
            "status" => 'in:active,inActive'
        ]);

        if($v->fails()) {
            return $this->sendError('There is an error in the data',$v->errors());
        }

        //start  create user
        $user =  User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "auth" => 3,
            "status" => 'active'
        ]);

        //uploadFile
        $this->uploadFile($request,$user);

        if($user){
            return  $this->sendResponse(new UserResourse($user),'Data exited successfully');
        }else{
            return $this->sendError('There is an error in the data');
        }
    }//**********end register************/

    public function forgetPassword(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(),['email' => 'required|email']);

        if($v->fails()) {
            return $this->sendError('There is no email with this name',$v->errors());
        }

        // send link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if($status === Password::RESET_LINK_SENT){
            $this->sendResponse([], __($status));
        }else{
            $this->sendError('There is no email with this name');
        };

    }//**********end forgetPassword************/

    public function reset(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(),[
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if($v->fails()) {
            return $this->sendError('There is an error in the data',$v->errors());
        }

        // update password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {

            return $this->sendResponse([],'Password reset successfully');
            response([
                'message'=> 'Password reset successfully'
            ]);

        }

        return $this->sendError('There is an error in the data',$status);

    }//**********end reset************/

}