<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Profile;
class ProfileController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function index(){
        $profiles=Profile::get();
        return view('maintainers.profile.profile')
                ->with('result', $profiles); 
    }
    
    public function permissionList(Request $request){
        $profile_id=$request->id;
        $profile=Profile::select(['label','id'])->where('id',$profile_id)->first();
        $modules=DB::table('modules')->get();
        $permissions=DB::table('permissions')->get();
        return view('maintainers.profile.permission')
                ->with('profile', $profile)
                ->with('modules', $modules)
                ->with('permissionsList', $permissions);
    }
    public function create(Request $request){
		$input=json_decode($request['arr'],true);
        $input['created']=date('Y-m-d H:i:s');
        $input['modified']=NULL;
        \Log::info($input);
		$profile = Profile::create($input);
		return response()->json($profile);
	}
    
    public function create_permission(Request $request){
        try{
            $permissions=$request['permissions_id'];
            $id_profile=$request['id_profile'];
            $id_module=$request['id_module'];
            $response=array();
            $delete = DB::table('profilepermission')->where('id_module',$id_module)->delete();
            \Log::info($delete);
            foreach($permissions as $permission){
                $input=array(
                    'id_permissions'=>$permission,
                    'id_profile'=>$id_profile,
                    'id_module'=>$id_module,
                );
                array_push($response,DB::table('profilepermission')->insertGetId($input));
                \Log::info($response);
            }
            return response()->json($response);
        }catch(\Exception $e){
            \Log::info($e);
            return response()->json($e);
        }
	}
    
    public function update(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $to_update['modified']=date("Y-m-d H:i:s");
            $response = Profile::where('id',$request['id'])->update($to_update);
            return response()->json($to_update);
        }catch(\Exception $e){
            return response()->json($e);
        }
	}
    
    public function delete(Request $request){
        try{
            $id=$request['id'];
            $response=Profile::where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }
    
    public function get_permissions(Request $request){
        $profile_id=$request->id;
        $permissions=DB::select('SELECT p.id FROM permissions as p JOIN profilepermission as pp ON p.id=pp.id_permissions WHERE pp.id_profile='.$profile_id);
        return response()->json($permissions);    
    }
}
