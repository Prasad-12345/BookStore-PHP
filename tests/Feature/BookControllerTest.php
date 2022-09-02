<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function successfulAddBookTest()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            "Authorization" => 'Bearer 14|NDtQDf78hbsZqgCrjxuZKNwBPmkV1o4j3XmKltza'
            ])->json('POST', '/api/addBook',[
            'name' => 'Book2',
            'description' => 'Book is Good',
            'author' => 'Mayur', 
            'price' => 500, 
            'quantity' => 2,
            'image' => 'image.jpg'
        ]);
        $response->assertStatus(201);
    }

    public function testShowBooksApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            // "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('get', '/api/showBooks',[
            
        ]);
        // echo $response;
        $response->assertStatus(200);
    }

    public function testDeleteBookApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            // "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/delete',[
            'id'=>3
        ]);
        $response->assertStatus(200);
    }

    public function testSearchBookApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            // "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('get', '/api/searchBook',[
            'value'=>"xyz"
        ]);
        $response->assertStatus(200);
    }

    public function testSortOnPriceLowToHigh()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            // "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('get', '/api/sortOnPriceLowToHigh',[
            
        ]);
        $response->assertStatus(200);
    }

    public function testSortOnPriceHighToLow()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            // "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('get', '/api/sortOnPriceHighToLow',[
            
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateQuantityById()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            // "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/updateQuantityById',[
            'id'=>1,
            'quantity'=>20
        ]);
        $response->assertStatus(200);
    }
}
