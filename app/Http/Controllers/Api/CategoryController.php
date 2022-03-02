<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResourse;
use App\Models\Category;
use App\Traits\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    use Message;

    public function index(Request $request)
    {

        // get category
        $category = Category::when($request->active,function($q) use($request){

            return $q->whereActive($request->active);

        })->withCount('product')->with('media:file_name,mediable_id')->get();


        return $this->sendResponse(CategoryResourse::collection($category),'Data exited successfully');

    }//**********end index************/


    public function store(Request $request)
    {

        // Validator request
        $v = Validator::make($request->all(), [
            "name" => "required|unique:categories",
            "active" => "required",
            "cover" => 'required|image|mimes:jpg,png'
        ]);

        if($v->fails()) {
            return $this->sendError('There is an error in the data',$v->errors());
        }

        // start create category
        $category = Category::create($request->only('name','active'));

        // uploadFile
        $this->uploadFile($request,$category);

        return $this->sendResponse(new CategoryResourse($category),'Successfully added');
    }//**********end store************/

    public function show($id)
    {
        // start show category
        $category = Category::find($id);

        if($category){
            return $this->sendResponse(new CategoryResourse($category),'Data exited successfully');
        }

        return $this->sendError('ID is not exist');

    }//**********end show************/


    public function update(Request $request,$id)
    {

        $category = Category::find($id);

        if($category){

            // Validator request
            $v = Validator::make($request->all(), [
                "name" => "required|unique:categories,name," .$category->id ,
                "active" => "required",
                'cover' => "image|mimes:jpg,png"
            ]);

            if($v->fails()) {
                return $this->sendError('There is an error in the data',$v->errors());
            }

            // start update category
            $category->update($request->only('name','active'));

            // uploadFile
            if($request->hasFile('cover')){

                // delete images
                File::deleteDirectory(storage_path('app/public/category/'.$category->id));
                $category->media->delete();

                // uploadFile function
                $this->uploadFile($request,$category);

            }

            return $this->sendResponse(new CategoryResourse($category),'Edited successfully');

        }else{
            return $this->sendError('ID is not exist',[]);
        }

    }//**********end update************/

    public function destroy($id)
    {
        $category = Category::find($id);

        if($category){
            // delete images
            File::deleteDirectory(storage_path('app/public/category/'.$category->id));
            $category->media->delete();

            $category->delete();

            return $this->sendResponse([],'Deleted successfully');
        }

        return $this->sendError('ID is not exist');
    }//**********end destroy************/

    public function uploadFile($request,$category)
    {

        $cover = $request->cover;
        $file_size = $cover->getSize();
        $file_type = $cover->getMimeType();
        $file_image = time() . $cover->getClientOriginalName();

        // picture move
        $cover->storeAs($category->id, $file_image,'category');

        $category->media()->create([
            'file_name' => $file_image ,
            'file_size' => $file_size,
            'file_type' => $file_type,
            'file_status' => true,
            'file_sort' => 1,
        ]);

    }//**********end uploadFile************//

}
