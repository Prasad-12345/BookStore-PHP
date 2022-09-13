<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AddressController extends Controller
{
    public function addAddress(Request $request){
        $request->validate([
            "address" => 'required|string|min:5|max:150',
            "landmark" => "required|string|min:5|max:100",
            "city" => "required|string|min:5|max:15",

            "state" => "required|string",
            "pincode" => "required|string",
            "address_type" => "required"
        ]);

        $getUser = $request->user()->id;
        if($getUser){
            $check = DB::table('addresses')->where('user_id', $getUser)->first();
            if($check){
                Log::channel('custom')->error("Address already exist");
            }
            else{
                $address = new Address();
                $address->user_id = $getUser;
                $address->address = $request->input('address');
                $address->landmark = $request->input('landmark');
                $address->city = $request->input('city');
                $address->state = $request->input('state');
                $address->pincode = $request->input('pincode');
                $address->address_type = $request->input('address_type');

                $address->save();
                return response()->json(["message"=>"address added successfully", "sussessstatus"=>200]);
            }
            
        }
        else{
            Log::channel('custom')->error("User is unauthorized");
        }
        
    }

    public function updateAddress(Request $request){
        $request->validate([
            'id' => 'required|integer',
            "address" => 'required|string|min:5|max:150',
            "landmark" => "required|string|min:5|max:100",
            "city" => "required|string|min:3|max:15",
            "state" => "required|string",
            "pincode" => "required|string",
            "address_type" => "required|string"
        ]);

        $getUser = $request->user()->id;
        $response = DB::table('addresses')->where('id', $request->id)->update(['user_id'=>$getUser,'address'=>$request->address, 'landmark'=>$request->landmark,
                            'city'=>$request->city, 'state'=>$request->state, 'pincode'=>$request->pincode, 'address_type'=>$request->address_type]);

        if($response){
            return response()->json(["message"=>"address added successfully", "successstatus"=>200]);
        }
        else{
            Log::channel('custom')->error("You entered invalid id");
        }
    }

    public function getAllAddresses(){
        $address = Cache::remember('books',10, function(){
            return address::all();
        });
        return $address;
    }

    public function deleteAddress(Request $request){
        $request->validate([
            'id'=>'required|integer'
        ]);

        $response = DB::table('addresses')->where('id', $request->id)->delete();
        if($response){
            return response()->json(["message"=>"Address deleted", "successstatus"=>200]);
        }
        else{
            Log::channel('custom')->error("You entered invalid id");
        }
    }
}
