<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PasswordControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function testForgotPasswordApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/forgotPassword',[
            'email'=>"prasadsomvanshi471@gmail.com"
        ]);
        $response->assertStatus(200);
    }

    public function testResetApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 16|5nfHwsiyNQH9lHbboBMN8RTuZ76kFvGmvocQl9CP'
            ])->json('post', '/api/reset',[
            'email'=>"prasadsomvanshi471@gmail.com",
            "password"=>"Prasad@123",
            "token"=>"H9o95T5bkp"
        ]);
        $response->assertStatus(200);
    }
}
