<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Http\Controllers\API\BaseController as BaseController;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Post::all();
        return $this->sendMessage($data , 'All Posts Data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all() , [

            'name' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg,gif'

        ]);

        if($validator->fails()){

            return $this->sendError('Validation Error' , $validator->errors()->all());
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imgName = time(). '.' .$ext;
        $img->move(public_path(). '/uploads',$imgName);

        $post = Post::create([

            'name' => $request->name,
            'description' => $request->description,
            'image' => $imgName

        ]);

        return $this->sendMessage($post , 'Post Created Successfully');

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $singlePost = Post::select('id' , 'name' , 'description' , 'image')->where('id' , $id)->get();
        return $this->sendMessage($singlePost , 'Your Single Post');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all() , [

            'name' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpg,png,jpeg,gif'

        ]);

        if($validator->fails()){

            return $this->sendError('Validation Error' , $validator->errors()->all());

        }

        $postImage = Post::select('id' , 'image')->where('id' , $id)->get();
        

        if($request->image != ''){
            $path = public_path(). '/uploads';
            if($postImage[0]->image != '' && $postImage[0]->image != null){
                $old_file = $path. $postImage[0]->image;
                if(file_exists($old_file)){
                    unlink($old_file);
                }
            }

            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imgName = time(). '.' .$ext;
            $img->move(public_path(). '/uploads',$imgName);
        }
        else{
            $imgName = $postImage->image;
        }


        $post = Post::where('id' , $id)->update([

            'name' => $request->name,
            'description' => $request->description,
            'image' => $imgName

        ]);


        return $this->sendMessage($post , 'Post Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = Post::select('image')->where('id' , $id)->get();
        $filePath = public_path(). '/uploads/' . $imagePath[0]['image'];
        unlink($filePath);

        $post = Post::where('id' , $id)->delete();

        return $this->sendMessage($post , 'Post Deleted Successfully');
        
    }
}
