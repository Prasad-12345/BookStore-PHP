<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WishlistController extends Controller
{
    public function addBookToWishlist(Request $request){
        $request->validate([
            'cart_id'=>'required|integer',
            // 'book_id' => 'required|integer'
        ]);
        $wishlist = new Wishlist();
        $cart = DB::table('cart')->where('id', $request->cart_id)->first();
        $bookData = DB::table('cart')->where('book_id', $cart->book_id)->first();
        
        $getUser = $request->user()->id;

        
        $checkWishlist = DB::table('wishlists')->where('user_id', $getUser)->where('book_id', $request->book_id)->first();
        if($checkWishlist){
            Log::channel('custom')->error("Book already exists in wishlist");
        }
        else{
            if($bookData){
                $wishlist->book_id = $cart->book_id;
                $wishlist->user_id = $getUser;
                $wishlist->cart_id = $request->input('cart_id');
                $wishlist->save();
                return response()->json(["data"=>"Book added to wishlist", 'successstatus'=>200]);
            }
            else{
                Log::channel('custom')->error("Book is not available in your cart");
            }
        }
    }

    public function getAllBooksFromWishlists(Request $request){
        // $request->validate([
        //     'user_id' => 'required|integer'
        // ]);
        // $userId = $request->user_id;
        $data = Cache::remember('books',10, function(){
            // $userId = $request->user()->id;
            return DB::table('wishlists')->join('books', 'wishlists.book_id', '=', 'books.id')
            ->select('books.id', 'books.name', 'books.author', 'books.description', 'books.price', 'wishlists.id')
            ->get();
            // return $wishlist;
        });
        return $data;
    }

    public function deleteBookFromWishlists(Request $request){
        $request->validate([
            'id' => 'required|integer'
        ]);

        $response = DB::table('wishlists')->where('id', $request->id)->delete();
        if($response){
            return response()->json(["message"=>"Book removed from wishlists", "sussessststus"=>200]);
        }
        else{
            Log::channel('custom')->error("You entered invalid id");
        }
    }
}
