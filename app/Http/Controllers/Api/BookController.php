<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
     /**
     * @OA\Post(
     *   path="/api/addBook",
     *   summary="add Book",
     *   description="add Book",
     *   @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","description", "author", "price", "quantity", "image"},
     *               @OA\Property(property="name", type="string"),
     *               @OA\Property(property="description", type="string"),
     *               @OA\Property(property="author", type="string"),
     *               @OA\Property(property="price", type="integer"),
     *               @OA\Property(property="quantity", type="integer"),
     *               @OA\Property(property="image", type="image"),
     *            ),
     *        ),
     *    ),
     *   @OA\Response(response=201, description="User successfully registered"),
     *   @OA\Response(response=401, description="The email has already been taken"),
     * )
     * 
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBook(Request $request){
        $request->validate([
            'name' => 'required|string|min:4',
            'description' => 'required|string|min:5|max:1000',
            'author' => 'required|string', 
            'price' => 'required|integer', 
            'quantity' => 'required|integer',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg,webp|max:5MB',  
        ]);

        $getUser = $request->user()->id;
        if($getUser){
            $check = DB::table('books')->where('name', $request->name)->first();
            if($check){
                Log::channel('custom')->error("Book already exists");
            }
            else{
                $book = new Book();
                $book->user_id = $getUser;
                $book->name = $request->name;
                $book->description = $request->description;
                $book->author = $request->author;
                $book->price = $request->price;
                $book->quantity = $request->quantity;
    
                $path = Storage::disk('s3')->put('images', $request->image);
                $url = env('AWS_URL') . $path;
                $book->image = $url;
                $book->save();
                $response = $book;
                return ($response);
            }

            // if($request->hasFile('image')){
            //     $file = $request->file('image');
            //     $extension = $file->getClientOriginalExtension();
            //     $fileName = time() . '.' . $extension;
            //     $file->move('uploads/books', $fileName);
            //     $book->image = $fileName;
            // }

            // if($request->hasFile('image')){
            //     $file = $request->file('image');
            //     $extension = $file->getClientOriginalExtension();
            //     $fileName = time() . '.' . $extension;
            //     $path = "s-folder/" . $fileName;
            //     Storage::disk("s3")->put($path, file_get_contents($file));
            // }
        }
        else{
            Log::channel('custom')->error("User is not authirized");
        }
    }

    public function updateBook(Request $request){
        $request->validate([
            'id' => 'required',
            'name' => 'required|string|min:4',
            'description' => 'required|string|min:5|max:1000',
            'author' => 'required|string', 
            'price' => 'required|integer', 
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg,webp|max:5MB',  
        ]);

        $book = new Book();
        if($request->hasFile('image')){
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $file->move('uploads/books', $fileName);
            $book->image = $fileName;
        }

        // if($request->hasFile('image')){
        //     $file = $request->file('image');
        //     $extension = $file->getClientOriginalExtension();
        //     $fileName = time() . '.' . $extension;
        //     $path = "s-folder/" . $fileName;
        //     Storage::disk("s3")->put($path, file_get_contents($file));
        // }

        $data = DB::table('books')->where('id', $request->id)->update(['name'=>$request->name, 'description'=>$request->description, 'author'=>$request->author, 'price'=>$request->price,  'image'=>$fileName]);
        if($data){
            return response($data);
        }
        else{
            Log::channel('custom')->error("Book not available");
        }
    }

    public function showBooks(){
    //     $books = Book::all();
    //  //   $books = auth()->books();
    //     return $books;
        $book = Cache::remember('books',10, function(){
            return Book::all();
        });
        return $book;
    }

    public function delete(Request $request){
        $request->validate([
            'id' => 'required'
        ]);

        $response = DB::table('books')->where('id', $request->id)->delete();
        if($response){
            return $response;
        }
        else{
            Log::channel('custom')->error("You entered invalid id");
        }
    }

    public function searchBook(Request $request){
        $request->validate([
            'value' => 'required'
        ]);

        $response = DB::table('books')->where('name', $request->value)->
                                        orWhere('id', $request->value)->
                                        orWhere('author', $request->value)->first();
        if($response){
            return $response;
        }
        else{
            Log::channel('custom')->error("Book is not available");
        }
    }

    public function sortOnPriceLowToHigh(){
        $books = Book::select('*')->orderBy('price')->paginate(3);
        return $books;
    }

    public function sortOnPriceHighToLow(){
        $books = Book::select('*')->orderBydesc('price')->get();
        return $books;
    }

    public function updateQuantityById(Request $request){
        $request->validate([
            'id' => 'required',
            'quantity' => 'required'
        ]);

        $response = DB::table('books')->where('id', $request->id)->update(['quantity'=>$request->quantity]);
        if($response){
            return response()->json(["message"=>"quantity of books is updated", "sussesstoken"=>200]);
        }
        else{
            Log::channel('custom')->error("You entered invalid book id");
        }
    }
}
