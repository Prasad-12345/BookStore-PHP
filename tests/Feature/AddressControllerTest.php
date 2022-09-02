<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function testAddAddressApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 17|vofEVhWJjDY0GlDAADT6EHQJdr0neJhQuTsWrPR6'
            ])->json('post', '/api/addAddress',[
                "address" => 'Ganesh Nagar, Latur',
                "landmark" => "Near SBI Bank",
                "city" => "Latur",
                "state" => "Maharashtra",
                "pincode" => "441122",
                "address_type" => "home"
        ]);
        $response->assertStatus(200);
    }

    public function testUpdateAddressApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 17|vofEVhWJjDY0GlDAADT6EHQJdr0neJhQuTsWrPR6'
            ])->json('post', '/api/updateAddress',[
                'id'=>2,
                "address" => 'Ganesh Nagar, Solapur',
                "landmark" => "Near SBI Bank",
                "city" => "Solapur",
                "state" => "Maharashtra",
                "pincode" => "441122",
                "address_type" => "home"
        ]);
        $response->assertStatus(200);
    }

    public function testGetAllAddressesApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 17|vofEVhWJjDY0GlDAADT6EHQJdr0neJhQuTsWrPR6'
            ])->json('get', '/api/getAllAddresses',[
               
        ]);
        $response->assertStatus(200);
    }

    public function testDeleteAddressApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 17|vofEVhWJjDY0GlDAADT6EHQJdr0neJhQuTsWrPR6'
            ])->json('post', '/api/deleteAddress',[
               'id'=>2
        ]);
        $response->assertStatus(200);
    }
}
