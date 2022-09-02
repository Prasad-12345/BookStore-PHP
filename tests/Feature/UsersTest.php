<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function testUserCanRegister(){
        $response = $this->json('POST', '/api/register',[
            'first_name' => 'Swapnil',
            'last_name' => 'Polkar',
            'email' => 'swapnil@gmail.com',
            'phone_no' => '7777777777',
            'password' => 'Swapnil@gmail.com',
            'role' => 'user'
        ]);
        $response->assertStatus(201);
    }

    public function testUserCanLogin(){
        $response = $this->json('POST', '/api/login',[
            'email' => 'swapnil@gmail.com',
            'password' => 'Swapnil@gmail.com',
        ]);
        $response->assertStatus(200);
    }

    public function testUserCanLogout(){
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
            "Authorization" => 'Bearer 12|CxX6kBNiAlZqWRvulWMHcIpqFUxJ7LbwV2f5TY7U'
        ])->json('POST', '/api/logout',[
            
        ]);
        $response->assertStatus(200);
    }
}
