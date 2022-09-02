<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function testAddBookTocartApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/addBookTocart',[
            'book_id'=>4,
            'book_quantity'=>1
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateBookInCartApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/updateBookInCart',[
            'id'=>4,
            'book_id'=>4,
            'book_quantity'=>2
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateQuantityInCartApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/updateQuantityInCart',[
            'id'=>4,
            // 'book_id'=>4,
            'book_quantity'=>1
        ]);
        $response->assertStatus(200);
    }

    public function testGetAllBooksApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('get', '/api/getAllBooks',[
            'user_id'=>1
        ]);
        $response->assertStatus(200);
    }
 
    public function testDeleteBookFromCartApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/deleteBookFromCart',[
            'id'=>4
        ]);
        $response->assertStatus(200);
    }
}
