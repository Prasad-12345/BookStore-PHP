<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WishlistControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function testAddBookToWishlistApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/addBookToWishlist',[
            'cart_id'=>4
        ]);
        $response->assertStatus(200);
    }

    public function testgetAllBooksFromWishlistsApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('get', '/api/getAllBooksFromWishlists',[
            // 'cart_id'=>4
        ]);
        $response->assertStatus(200);
    }

    public function testDeleteBookFromWishlistsApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/getAllBooksFromWishlists',[
            'id'=>4
        ]);
        $response->assertStatus(200);
    }
}
