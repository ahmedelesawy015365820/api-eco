<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    use Message;

    public function index()
    {

        // get user
        $user = User::with('media:file_name,mediable_id')->paginate(10);

        return $this->sendResponse(UserResourse::collection($user),'Data exited successfully');

    }//**********end index************/


    public function store(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:6',
            'password_copy' => 'required|same:password',
            'image' => 'required|mimes:jpg,png',
            'auth' => 'required|in:1,2,3',
            "status" => 'in:active,inActive'
        ]);

        if($v->fails()) {
            return $this->sendError('There is an error in the data',$v->errors());
        }

        // start create user
        $user =  User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => $request->password,
            "auth" => $request->auth,
            "status" => ($request->status == 'active'? 'active':'inActive')
        ]);

        //uploadFile
        $this->uploadFile($request,$user);

        if($user){
            return  $this->sendResponse(new UserResourse($user),'Data exited successfully');
        }else{
            return $this->sendError('There is an error in the data');
        }
    }//**********end store************/

    public function show($id)
    {

        // find user
        $user = User::find($id);

        if($user){
            return $this->sendResponse(new UserResourse($user),'Data exited successfully');
        }

        return $this->sendError('ID is not exist');
    }//**********end show************/

    public function update(Request $request, $id)
    {

        // find user
        $user = User::find($id);

        if($user){

            // Validator request
            $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email,'.$user->id,
            'password' => 'min:6',
            'password_copy' => 'same:password',
            'image' => 'mimes:jpg,png',
            'auth' => 'required|in:1,2,3',
            "status" => 'required|in:active,inActive'
            ]);

            if($v->fails()) {
                return $this->sendError('There is an error in the data',$v->errors());
            }

            // start update user
            $data= [];
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            if($request->password != ''){
                $data['password'] = $request->password;
            }
            $data['auth'] = $request->auth;
            $data['status'] = $request->status;

            $user->update($data);

            //uploadFile
            if($request->hasFile('image')){
                // delete images
                File::deleteDirectory(storage_path('app/public/user/' . $user->id));

                $user->media->delete();

                //uploadFile function
                $this->uploadFile($request,$user);
            }

            if($user){
                return  $this->sendResponse(new UserResourse($user),'Edited successfully');
            }else{
                return $this->sendError('There is an error in the data');
            }
        }

    }//**********end update************/

    public function destroy($id)
    {
        $user = User::find($id);

        if($user){

            //delete (image-user)
            File::deleteDirectory(storage_path('app/public/user/' . $user->id));
            $user->media->delete();

            $user->delete();
            return $this->sendResponse([],'Deleted successfully');
        }

        return $this->sendError('ID is not exist');
    }//**********end destroy************/


    // uploadFile
    public function uploadFile($request,$user)
    {

        $cover = $request->image;
        $file_size = $cover->getSize();
        $file_type = $cover->getMimeType();
        $file_image = time() . $cover->getClientOriginalName();

        // picture move
        $cover->storeAs($user->id, $file_image,'user');

        $user->media()->create([
            'file_name' => $file_image ,
            'file_size' => $file_size,
            'file_type' => $file_type,
            'file_status' => true,
            'file_sort' => 1,
        ]);

    }//**********end uploadFile************/
}