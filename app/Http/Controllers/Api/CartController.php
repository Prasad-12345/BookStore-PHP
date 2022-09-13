<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Cart;
use app\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Cache;

class CartController extends Controller
{
    public function addBookTocart(Request $request){
        $request->validate([
            'book_id' => 'required|integer',
            'book_quantity' => 'required|integer'
        ]);
        $cart = new Cart();
        $book = new Book();
        $checkUser = $request->user()->id;
        $cart->user_id = $checkUser;
        $checkBookId = DB::table('books')->where('id', $request->book_id)->first();

        $checkBookInCart = DB::table('cart')->where('book_id', $request->book_id)->where('user_id', $checkUser)->first();
        if($checkBookInCart){
            Log::channel('custom')->error("Book already exists in cart");
        }
        else{
            if($checkBookId){
                if($checkBookId->quantity < $request->book_quantity){
                    Log::channel('custom')->error("Book is out of stock");
                }
                else{
                    $cart->book_id = $request->input('book_id');
                    $cart->book_quantity = $request->input('book_quantity');
                    $cart->save();
                    return response()->json(["message"=>"book added to cart successfully", "success"=>200]);
                }
            }
            else{
                Log::channel('custom')->error("Book is not available");
            }
        }      
    }

    public function deleteBookFromCart(Request $request){
        $request->validate([
            'id' => 'required|integer'
        ]);
        $response = DB::table('cart')->where('id', $request->id)->delete();
        if($response){
            return response()->json(["message"=>"Book removed from cart", "success"=>200]);
        }
        else{
            Log::channel('custom')->error("id is invalid");
        }
    }

    public function getAllBooks(Request $request){
        $getUser = $request->user();
        $userId = $getUser->id;

            $cart = Cart::join('books', 'cart.book_id', '=', 'books.id')
            ->select( 'books.name', 'books.author', 'books.description', 'books.price', 'cart.book_quantity', 'cart.id', 'cart.book_id')
            ->where('cart.user_id', '=', $userId)
            ->get();
        if(!$cart){
            Log::channel('custom')->error("id is invalid");
        }
        return $cart;       
    }

    public function updateBookInCart(Request $request){
        $request->validate([
            'id' => 'required',
            'book_id' => 'required|integer',
            'book_quantity' => 'required|integer'
        ]);
        $cart = new Cart();
        $checkUser = $request->user()->id;
        $cart->user_id = $checkUser;
        $checkBookId = DB::table('books')->where('id', $request->book_id)->first();
        if($checkBookId){
            if($checkBookId->quantity < $request->book_quantity){
                Log::channel('custom')->error("Book is out of stock");
            }
            else{
                $response = DB::table('cart')->where('id', $request->id)->update(['book_id'=>$request->book_id, 'user_id'=>$checkUser, 'book_quantity'=>$request->book_quantity]);
                return response()->json(["message"=>"book updated successfully", "successstatus"=>200]);
            }
            
        }
        else{
            Log::channel('custom')->error("Book is not available");
        }
    }

    public function updateQuantityInCart(Request $request){
        $request->validate([
            'id' =>'required|integer',
            'book_quantity' => 'required|integer'
        ]);
        $cart = DB::table('cart')->where('id', $request->id)->first();
        
        $checkBookId = DB::table('books')->where('id', $cart->book_id)->first();
        if($cart){
            if($checkBookId->quantity < $request->book_quantity){
                Log::channel('custom')->error("Book is out of stock");
            }
            else{
                $response = DB::table('cart')->where('id', $request->id)->update(['book_quantity'=>$request->book_quantity]);
                return response()->json(["message"=>"Quantity updated"]);
            }
            
        }
        else{
            Log::channel('custom')->error("id is invalid");
        }
    }

    public function incrementQuantityInCart(Request $request){
        $request->validate([
            'id' => 'required|integer'
        ]);
        $book_data = DB::table('cart')->where('id', $request->id)->first();
        $book_quantity = $book_data->book_quantity + 1;
        $response = DB::table('cart')->where('id', $request->id)->update(['book_quantity'=>$book_quantity]);
        if($response){
            return response()->json(["message"=>"Quantity incremented"]);
        }
    }

    public function decrementQuantityInCart(Request $request){
        $request->validate([
            'id' => 'required|integer'
        ]);
        $book_data = DB::table('cart')->where('id', $request->id)->first();
        $book_quantity = $book_data->book_quantity - 1;
        $response = DB::table('cart')->where('id', $request->id)->update(['book_quantity'=>$book_quantity]);
        if($response){
            return response()->json(["message"=>"Quantity cecremented"]);
        }
    }
}
