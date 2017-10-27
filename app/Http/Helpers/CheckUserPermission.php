<?php
    use \Illuminate\Support\Facades\DB;
    class CheckUserPermission{
        public function __contruct()
        {
        }
        public function checkPerission($uri){
            $user_id=Auth::user()->id;
            $check = DB::select("SELECT u.email, p.label as profile_name,per.permission as permssion, m.label as module_label, m.module as module FROM users u 
JOIN profile p ON p.id=u.id_profile
JOIN ProfilePermission pp ON p.id=pp.id_profile
JOIN permissions per ON per.id=pp.id_permissions
JOIN modules m ON m.id=pp.id_module
WHERE m.module='".$uri."' AND u.id=".$user_id.";");
            $permissions=array();
            $user=DB::select('SELECT p.label as profile FROM users u JOIN profile p ON u.id_profile=p.id WHERE u.id='.$user_id.' GROUP BY p.label;');

            if($user[0]->profile=='Admin'){
                $u_permissions=DB::table('permissions')->select(['permission'])->get();
                foreach($u_permissions as $key=>$val){
                    $val=json_decode(json_encode($val,true));
                    $permissions[$val->permission]=$val->permission;;
                }
                return $permissions;
            }
            foreach($check as $key=>$val){
                $val=json_decode(json_encode($val,true));
                $permissions[$val->permission]=$val->permission;
            }            
            return $permissions;
        }
    }
