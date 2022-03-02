<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResourse;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    use Message;

    public function getCart(){

        $id = auth()->guard('api')->user()->id;
        $product = Cart::whereUserId($id)->selection()->with('product:id,name,price')->get();

        return $this->sendResponse($product,'Data exited successfully') ;

    }//**********end getCart************/

    public function addCart($id)
    {
        $product = Product::find($id);

        if($product){

            $user = auth()->guard('api')->user()->id;

            $cart = Cart::create(['product_id' => $id,'user_id' => $user]);

            return  $this->sendResponse([],'Data exited successfully');

        }else{
            return $this->sendError('ID is not exist');
        }

    }//**********end addCart************/

    public function editCart($id,Request $request)
    {
        $cart = Cart::find($id);

        if($cart){

            $v = Validator::make($request->all(), ["quantity" => "required"]);

            if($v->fails()) {
                return $this->sendError('There is an error in the data',$v->errors());
            }

            $cart->update(['quantity' => $request->quantity]);

            return  $this->sendResponse([],'Edited successfully');

        }else{
            return $this->sendError('ID is not exist');
        }

    }//**********end editCart************/

    public function deleteCart($id)
    {
        $cart = Cart::find($id);

        if($cart){

            $cart->delete();

            return  $this->sendResponse([],'Deleted successfully');

        }else{
            return $this->sendError('ID is not exist');
        }

    }//**********end deleteCart************/



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