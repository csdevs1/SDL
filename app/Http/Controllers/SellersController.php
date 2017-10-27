<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Seller;
use App\Profile;
use App\User;
use App\Customer;

class SellersController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function index(){
		$sellers = DB::table('seller')->get();

		$sellersEntries = array();
        $id_profile_seller=Profile::select(['id'])->where('label','Ventas')->first();
        $id_profile_consoulting=Profile::select(['id'])->where('label','Consultas')->first();
		foreach ($sellers as $seller) 
        {

            $seller_customer_arr = array ();
  
			$seller_customers   = 
			DB::table('customer')->WHERE('id_seller',$seller->id)->get();

            foreach ( $seller_customers as $seller_customer )
            {
                $seller_customer_arr [] = $seller_customer->id;
            }


			$sellersEntries[] = array(
				'id'        => $seller->id,
				'code'      => $seller->code,
				'name'      => $seller->name,
				'id_user'   => $seller->id_user,
				'customers' => $seller_customer_arr,
				'enable'    => $seller->enable,
                'created'   => $seller->created,
                'modified'  => $seller->modified,
				);
		}

        return view('maintainers.sellers.show')
            ->with('id_profile_seller', $id_profile_seller['id'])
            ->with('id_profile_consoulting', $id_profile_consoulting['id'])
            ->with('result', $sellersEntries);
	}
    
    public function get_clients(Request $request){
        $id=$request->id;
        $customers=DB::select('SELECT * FROM customer WHERE id_seller NOT IN ('.$id.') ORDER BY name ASC');
        return response()->json($customers);
    }
    public function get_clients_id(Request $request){
        $id=$request->id;
        $customers=DB::table('customer')->WHERE('id_seller',$id)->get();
        return response()->json($customers);
    }
    
    public function create(Request $request){        
        try{
            $input=json_decode($request['arr'],true);
            //First: Create user
            $user=array(
                'email'=>$input['email'],
                'password'=>$input['password'],
                'password'=>$input['password'],
                'id_profile'=>$input['id_profile'],
                'id_group_of_office'=>$input['id_group_of_office']
            );
            $user_insert=User::insertGetId($user);
            \Log::info($user_insert);
            //Then: the seller
            $seller=array(
                'name'=>$input['name'],
                'code'=>$input['code'],
                'enable'=>true,
                'id_user'=>$user_insert,
                'created'=>date("Y-m-d H:i:s")
            );
            $seller=Seller::create($seller);
            \Log::info($seller);
            return response()->json($seller);
        }catch(\Exception $e){
            \Log::info($e);
            return $e;
        }
	}
    
    public function associate_customer(Request $request){
        try{
            $input=json_decode($request['arr'],true);
            $response=array();
            foreach($input as $k=>$v){
                $to_associate=array(
                    'id_seller'=>$request['id']
                );
                $rs=Customer::where('id',$v)->update($to_associate);
                array_push($response,$rs);
                \Log::info($rs);
            }
            return response()->json($response);
        }catch(\Exception $e){
            \Log::info($e);
            return $e;
        }
    }
}