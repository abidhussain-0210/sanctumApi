<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Post::all();
        return response()-json([

            'status' => true,
            'message' => 'All Post Dta',
            'data' => $user

        ] , 200);
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

            return response()->json([

                'status' => false,
                'message' => 'Validation Error',
                'error' => $validator->errors()->all()
            ]);
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imgName = time(). '.' .$ext;
        $img->move(public_path(). '/uploads',$imgName);

        $post = Post::create([

            'name' => $request->name,
            'descripton' => $request->descripton,
            'image' => $imgName

        ]);

        return response()->json([

            'status' => true,
            'message' => 'Post Created Successfully',
            'post' => $post
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $singlePost = Post::select('id' , 'name' , 'description' , 'image')->where('id' , $id)->get();
        return response()->json([

            'status' => true,
            'message' => 'Your Single Post',
            'post' => $singlePost
        ]);
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

            return response()->json([

                'status' => false,
                'message' => 'Validation Error',
                'error' => $validator->errors()->all()
            ] , 405);
        }

        $post = Post::select('id' , 'image')->get();

        if($request->image != ''){
            $path = public_path(). '/uploads';
            if($post->image != '' && $post->image != null){
                $old_file = $path. $post->image;
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
            $imgName = $post->image;
        }


        $post = Post::where('id' , $id)->update([

            'name' => $request->name,
            'descripton' => $request->descripton,
            'image' => $imgName

        ]);

        return response()->json([

            'status' => true,
            'message' => 'Post Updated Successfully',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = Post::select('image')->where('id' , $id)->get();
        $filePath = public_path(). '/uploads' . $imagePath[0]['image'];
        unlink($filePath);

        $post = Post::where('id' , $id)->delete();

        return response()->json([

            'status' => true,
            'message' => 'Post Deleted Successfully',
        ]);
    }
}
