<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResourse;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    use Message;

    public function index(Request $request)
    {

        // get product
        $product = Product::when($request->active,function($q) use($request){

            return $q->whereActive($request->active);

        })->when($request->price,function($q) use($request){

            return $q->where('price',"<",$request->price);

        })
        ->with('media:file_name,mediable_id','category:id,name')->paginate(10);


        return $this->sendResponse(ProductResourse::collection($product),'Data exited successfully');

    }//**********end index************/

    public function store(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(), [
            "name" => "required|unique:products",
            "discription" => "required",
            "price" => "required|numeric",
            "category_id" => "required|numeric",
            "active" => "required|boolean",
            "images" => "required",
            "images.*" => 'image|mimes:jpg,png'
        ]);


        if($v->fails()) {
            return $this->sendError('There is an error in the data',$v->errors());
        }

        // srtart create product
        $product = Product::create($request->only('name','discription',"price","category_id","active"));

        //upload images
        $this->uploadFile($request,$product);

        return $this->sendResponse(new ProductResourse($product),'Successfully added');

    }//**********end store************/

    public function show($id)
    {

        // find product
        $product = Product::find($id);

        if($product){
            return $this->sendResponse(new ProductResourse($product),'Data exited successfully');
        }

        return $this->sendError('ID is not exist');
    }//**********end show************/

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if($product){

            // Validator request
            $v = Validator::make($request->all(), [
                "name" => "required|unique:products,name,". $product->id,
                "discription" => "required",
                "price" => "required|numeric",
                "category_id" => "required|numeric",
                "active" => "required|boolean",
                "images.*" => 'image|mimes:jpg,png'
            ]);

            if($v->fails()) {
                return $this->sendError('There is an error in the data',$v->errors());
            }

            //upload images
            if($request->images && count($request->images) > 0){

                // delete images
                File::deleteDirectory(storage_path('app/public/product/'.$product->id));

                foreach($product->media as $image){
                    $image->delete();
                }

                //upload images function
                $this->uploadFile($request,$product);

            }

            // start update product
            $product->update($request->only('name','discription',"price","category_id","active"));

            return $this->sendResponse(new ProductResourse($product),'Edited successfully');
        }else{

            return $this->sendError('ID is not exist');

        }
    }//**********end update************/


    public function destroy($id)
    {
        $product = Product::find($id);

        if($product){

            // delete images
            File::deleteDirectory(storage_path('app/public/product/'.$product->id));

            foreach($product->media as $image){
                $image->delete();
            }

            //delete product
            $product->delete();

            return $this->sendResponse([],'Deleted successfully');
        }

        return $this->sendError('ID is not exist');
    }//**********end destroy************/


    //start  uploadFile
    public function uploadFile($request,$product)
    {

        $i = 1;

        foreach($request->images as $cover){

            $file_size = $cover->getSize();
            $file_type = $cover->getMimeType();
            $file_image = time() . $cover->getClientOriginalName();

            // picture move
            $cover->storeAs($product->id, $file_image,'product');

            $product->media()->create([
                'file_name' => $file_image ,
                'file_size' => $file_size,
                'file_type' => $file_type,
                'file_status' => true,
                'file_sort' => $i,
            ]);

            $i++;
        }
    }//**********end destroy************/

    public function productExcel(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx,csv'
        ]);

        if($v->fails()) {
            return $this->sendError('There is an error in the data',$v->errors());
        }

        // import excel
        Excel::import(new ProductsImport, $request->file);

        return $this->sendResponse([],'Data exited successfully');

    }//**********end productExcel************/
}