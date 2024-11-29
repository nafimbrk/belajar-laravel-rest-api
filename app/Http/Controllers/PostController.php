<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class PostController extends Controller
{
    public function index() {
        // $posts = Post::with('writer:id,username')->get(); // kalo mau pake with disini
        $posts = Post::all();
        // return response()->json($posts);
        // return response()->json(['data', $posts]);
        return PostDetailResource::collection($posts->loadMissing('writer:id,username', 'comments:id,post_id,user_id,comments_content')); // ini sama kaya yang atas hasilnya
        // return PostDetailResource::collection($posts->loadMissing('writer:id,username')); // kalo mau pake loadmissing disini
    }

    public function show($id) {
        $post = Post::with('writer:id,username')->findOrFail($id);  // pk(id) harus di ikutkan dan setelah koma gk boleh ada spasi
        // return response()->json(['data', $post]);
        return new PostDetailResource($post->loadMissing('writer:id,username', 'comments:id,post_id,user_id,comments_content'));
    }

    // kalo hasilnya lebih dari satu pakai collection
    // tapi kalo hasilnya cuma satu pakai new

    // function show2($id) {
    //     $post = Post::findOrFail($id);
    //     return new PostDetailResource($post);
    // }

    public function store(Request $request) {

        // return $request->file;

        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required'
        ]);

        $image = null;
        if ($request->file) {
            // disini kita upload filenya
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName . '.' . $extension;

            Storage::putFileAs('image', $request->file, $image);
        }

        // return response()->json('ok method store bisa diakses');

        $request['image'] = $image;
        $request['author'] = Auth::user()->id;
        
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required'
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    function destroy($id) {
        $post = Post::findOrFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    function generateRandomString($length = 30) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
    
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
    
        return $randomString;
    }
}
