<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostCollection;
use Illuminate\Support\Facades\Redis;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //수정
        // if($posts = Redis::get('posts')){
        //     return json_decode($posts);
        // }

        // $posts = Post::all();
        // Redis::set('posts', json_encode($posts));
        // return $posts;

        $posts = Post::all();
        Redis::set('posts', json_encode($posts));
        $posts = Redis::get('posts');
        return $posts;

        //모든 글 가져오기
        // $posts = Post::all()->toArray();
        // return array_reverse($posts);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $redis = Redis::connection();
        //글 작성
        $post = new Post([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author' => $request->input('author')
        ]);
        $post->save();

        //redis 코드 추가
        $redis->append('posts', $post);

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
        $redis = Redis::connection();
        //글 갱신
        $post = Post::find($id);
        $post->update($request->all());

        //redis 코드 추가
        $redis->append('posts', $post);

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

        return response()->json('The post successfully deleted');
    }
}
