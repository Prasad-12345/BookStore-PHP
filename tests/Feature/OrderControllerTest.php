<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @test
     */
    public function testPlaceOrderApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 17|vofEVhWJjDY0GlDAADT6EHQJdr0neJhQuTsWrPR6'
            ])->json('post', '/api/placeOrder',[
                'cartId_json' => [3,4],
                'address_id' => 2
        ]);
        $response->assertStatus(200);
    }

    public function testCancelOrderApi()
    {
        $response = $this->withHeaders([
            'Content-Type' => 'Appilication/json',
             "Authorization" => 'Bearer 17|vofEVhWJjDY0GlDAADT6EHQJdr0neJhQuTsWrPR6'
            ])->json('post', '/api/cancelOrder',[
                'order_id'=>'WxChArEsLQ'
        ]);
        $response->assertStatus(200);
    }
}
