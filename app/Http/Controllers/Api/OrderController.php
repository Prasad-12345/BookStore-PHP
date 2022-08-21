<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\sendCancelledOrderDetails;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendOrderDetails;

class OrderController extends Controller
{
    public function getBookById($id){
        return DB::table('books')->where('id', $id)->first(); 
    }

    public function placeOrder(Request $request){
        $request->validate([
            'cart_id',
            'address_id'
        ]);

        $getUser = $request->user();
        $cart = DB::table('cart')->where('id', $request->cart_id)->first();

        $book = $this->getBookById($cart->book_id);
        $order = new Order();
        $order->user_id = $getUser->id;
        $order->cart_id = $request->input('cart_id');
        $order->address_id = $request->input('address_id');
        $order->book_name = $book->name;
        $order->book_author = $book->author;
        $order->book_price = $book->price;
        $order->book_quantity = $cart->book_quantity;
        $order->total_price = $cart->book_quantity * $book->price;
        $randomCode = Str::random(10);
        $order->order_id = $randomCode;
        $order->save();

        Mail::to($getUser->email)->send(new sendOrderDetails($getUser, $order, $book));
        return response()->json(["message"=>"order placed successfully", "successStatus"=>200]);
    }

    public function cancelOrder(Request $request){
        $request->validate([
            'order_id' => 'required|string'
        ]);
        $getUser = $request->user();
        $order = DB::table('orders')->where('order_id', $request->order_id)->first();
        $cart = DB::table('cart')->where('id', $order->cart_id)->first();
        $book = DB::table('books')->where('id', $cart->book_id)->first();
        $response = DB::table('orders')->where('order_id', $request->order_id)->delete();
        Mail::to($getUser->email)->send(new sendCancelledOrderDetails($getUser, $order, $book));
        return response()->json(["message"=>"Order cancelled"]);
    }
}
