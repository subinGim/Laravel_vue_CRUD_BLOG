<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostCollection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        if(Cache::has('posts.all') == null)
        {
            $posts = Post::all()->toArray();
            Cache::store('redis')->put('posts.all', json_encode($posts), 1800); // 30분동안 캐쉬 유지
        }
        $value = Cache::get('posts.all');
        return $value;
        
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        //글 작성
        $post = new Post([
            'title' => $request->input('title'), 
            'content' => $request->input('content'), 
            'author' => $request->input('author')
        ]);
        $post->save();

        //redis 코드 추가
        Cache::forget('posts.all');

        return response()->json('The Post successfully added');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        //글 수정
        $post = Post::find($id);
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //글 갱신
        $post = Post::find($id);
        $post->update($request->all());

        //redis 코드 추가
        Cache::forget('posts.all');

        return response()->json('The post successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        //글 삭제
        $post = Post::find($id);
        $post->delete();

        Cache::forget('posts.all');

        return response()->json('The post successfully deleted');
    }
}
