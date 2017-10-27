<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class DevicesController extends Controller{
	
	public function __contruct()
	{
	}
    private function get_devices_info(){
        $result = array();
        $devices    = DB::table('device')->orderBy('id','DESC')->get();

        if ( empty( $devices ) )
        {
            $this->pushError('Tabla Device sin datos.', 1001);

            return $this->response();
        }

        foreach ( $devices as $device) {


            $result[] = array(
                'id'            => $device->id,
                'label'         => $device->label,
                'imei'          => $device->imei,
                'imsi'          => $device->imsi,
                'plate_number'  => $device->plate_number,
                'type_device'   => $device->type_device,
                'enable'        => $device->enable,
                'created'       => $device->created,
                //'created'       => date('Y-m-d H:i:s', strtotime($device->created)),
                'modified'      => $device->modified,
                //'modified'      => date('Y-m-d H:i:s', strtotime($device->modified)),
                'google'        => $device->google,
                'apk_version'   => $device->apk_version,
                'apk_url'       => $device->apk_url,
                'actual'        => $device->apk_version_actual,
                'fecha_apk'     => $device->apk_actual_update
            );
    
        }

        return $result;
    }

    public function show(){
        $result = $this->get_devices_info();
        //dd($result);
        return view('maintainers.android.show')
            ->with('result', $result);
    }

    public function save(Request $request){
        try{
            $to_save=json_decode($request['arr'],true);
            $apk_version=DB::table('parameter')->select(['prm_desc'])->where('prm_type','VERSION_APK')->first();
            $to_save['apk_version']=$apk_version->prm_desc;
            $id= DB::table('device')->insertGetId($to_save);
            $response=array(
                'bool'=>true,
                'id'=>$id,
                'label'=>$to_save['label'],
                'imei'=>$to_save['imei'],
                'imsi'=>$to_save['imsi'],
                'apk_version'=>$apk_version->prm_desc
            );
            \Log::info($response);
            return response()->json($response);
        }catch(\Exception $e){
            return $e;
        }
    }

    public function update(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $to_update['modified']=date("Y-m-d H:i:s");
            $response = DB::table('device')
                ->where('id',$request['id'])
                ->update($to_update);
            \Log::info($response);
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }

    public function delete(Request $request){
        try{
            $id=$request['id'];
            $response=DB::table('device')->where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }
}
