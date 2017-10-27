<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Hash;
use App\Profile;
use App\User;
use App\OfficeGroup;
class UsersController extends Controller{
	
	public function __contruct()
	{
	}

    public function user_profile(){
        return view('profile.profile');
    }

    public function index(){
        $users = User::get();
        $profiles=Profile::get();
        $group_of_offices=OfficeGroup::get();
        $sellers = array ();
        
        $userEntries = array();
		foreach ($users as $user) {
			$profile=DB::table('profile')->WHERE('id',$user->id_profile)->select('id','label','description')->first();
			$group  = DB::table('group_of_office')->WHERE('id',$user->id_group_of_office)->select('label')->first();
            if(isset($profile->label) && !empty($profile->label))
                $profile=$profile->label;
            else
                $profile='Perfil no asig.';
            if(isset($group->label) && !empty($group->label))
                $group=$group->label;
            else
                $group='Grupo no asig.';
            
            $sellers = DB::table('seller')->where('id_user',$user->id)->count();
			$userEntries[] = array(
				'id'       => $user->id,
				'email'    => $user->email,
				'label_profile'     => $profile,
				'id_profile'     => $user->id_profile,
                'label_group_office'=> $group,
                'id_group_of_office'     => $user->id_group_of_office,
				'enable'    => $user->enable,
                'created'   => $user->created,
                'modified'  => $user->modified,
                'seller'    => $sellers>0?true:false
				);
		}
        return view('maintainers.users.users')
                ->with('users', $userEntries) 
                ->with('group_of_offices', $group_of_offices)
                ->with('profiles', $profiles);
    }

    public function create(Request $request){
        try{
            $to_save=json_decode($request['arr'],true);
            $to_save['password']=bcrypt($to_save['password']);
            $to_save['created']=date('Y-m-d H:i:s');
            //\Log::info($to_save);
            //dd($to_save);
            $user= User::create($to_save);
            $profile=Profile::select(['label'])->where('id',$user->id_profile)->first();
            $group=DB::table('group_of_office')->select(['label'])->where('id',$user->id_group_of_office)->first();
            $response=array('email'=>$user->email, 'profile'=>$profile->label, 'group'=>$group->label,'created'=>$user->created);
            return response()->json($response);
        }catch(\Exception $e){
            \Log::info($e);
            return $e;
        }
    }

    public function update(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $current_password=$request['current_password'];
            if(Hash::check($to_update['password'], Auth::user()->password))
                return response()->json(['error'=>'La nueva contraseña ingresada no puede ser igual a la actual!']);
            if(empty($request['id']) && !isset($request['id']))
                $request['id']=Auth::user()->id;
            if(!empty($to_update['password']) && isset($to_update['password'])){
                $to_update['password']=bcrypt($to_update['password']);
                $check_password=Hash::check($current_password, Auth::user()->password);
            }
            if($check_password){
                $to_update['modified']=date("Y-m-d H:i:s");
                $response = User::where('id',$request['id'])->update($to_update);
                \Log::info($response);
                return response()->json($response);
            }else{
                return response()->json(['error'=>'Contraseña actual ingresada no coincide con la ingresada en la base de datos.']);
            }
            if(empty($to_update['password']) && !isset($to_update['password'])){
                $to_update['modified']=date("Y-m-d H:i:s");
                $response = User::where('id',$request['id'])->update($to_update);
                return response()->json($response);
            }
        }catch(\Exception $e){
            \Log::info($e);
            return response()->json($e);
        }
	}

    public function delete(Request $request){
        try{
            $id=$request['id'];
            $response=User::where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }
}
