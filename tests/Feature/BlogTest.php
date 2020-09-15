<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;

class BlogTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test
     * Read Function
     */
    public function a_user_can_read_all_posts()
    {
        $posts = factory('App\Post')->create();
        
        $response = $this->get('/posts');

        $response->assertStatus(200);
        // $this->assertTrue(true);
        
    }

    public function a_user_can_read_single_post()
    {
        $post = factory('App\Post')->create();
        
        $title = $post->title;
        $content = $post->content;
        $author = $post->author;

        $value = $response->$this->get('/edit/'.$post->id);

        $this->assertEquals($value->title, $title)
            ->assertEquals($value->content, $content)
            ->assertEquals($value->author, $author);
    }

    public function a_user_can_create_post()
    {
        $post = factory('App\Post')->make();
        
        $this->post('/add', $post);

        $this->assertEquals(1, Post::all()->count());
    }

    public function a_user_can_update_post()
    {
        $post = factory('App\Post')->create();

        $this->put('/update/'.$post->id, $post);

        $this->assetDatabaseHas('posts', ['id'=>$post->id]);
    } 

    public function a_user_can_delete_post()
    {
        $post = factory('App\Post')->create();

        $this->delete('/delete/'.$post->id);

        $this->assertDatabaseMissing('posts', ['id'=>$post->id]);
    }
}
