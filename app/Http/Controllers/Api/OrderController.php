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
use App\Models\Book;
use Illuminate\Support\Facades\Log;
use LengthException;

class OrderController extends Controller
{
    public function getBookById($id){
        return DB::table('books')->where('id', $id)->first(); 
    }

    public function placeOrder(Request $request){
        $request->validate([
            'cartId_json' => 'required',
            'address_id' => 'required|integer'
        ]);
        $cartId_json = $request->cartId_json;
        $length = sizeof($cartId_json);
        
        // return $length;
        for($i=0; $i < $length; $i++){
            $getUser = $request->user();
            $cart = DB::table('cart')->where('id', $cartId_json[$i])->first();
            $book = new Book();
            $book = $this->getBookById($cart->book_id);

            $order = new Order();
            $order->user_id = $getUser->id;
            $order->cartId_json = $cartId_json[$i];
            $order->cart_id = $cartId_json[$i];
            $order->address_id = $request->input('address_id');
            $order->book_name = $book->name;
            $order->book_author = $book->author;
            $order->book_price = $book->price;
            $order->book_quantity = $cart->book_quantity;
            $order->total_price = $cart->book_quantity * $book->price;
            $randomCode = Str::random(10);
            $order->order_id = $randomCode;

            // $check = DB::table('orders')->where('cart_id', $cartId_json[$i])->
                        // where('user_id', $getUser->id);
            // if($check){
            //     Log::channel('custom')->error('Order already exists');
            // }
            // else{
                $order->save();
                $book->quantity -= $cart->book_quantity;
                // $book->save();
                DB::table('books')->where('id', $cart->book_id)->update(['quantity'=>$book->quantity]);
                Mail::to($getUser->email)->send(new sendOrderDetails($getUser, $order, $book));
            // }
            
        }
        // if(!$check){
            return response()->json(["message"=>"order placed successfully", "successStatus"=>200]);
        // }     
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
        if($response){
            $book->quantity += $cart->book_quantity;
            DB::table('books')->where('id', $cart->book_id)->update(['quantity'=>$book->quantity]);
            Mail::to($getUser->email)->send(new sendCancelledOrderDetails($getUser, $order, $book));
            return response()->json(["message"=>"Order cancelled"]);
        }
        else{
            Log::channel('custom')->error("Check order_id you entered");
        }
    }
}
