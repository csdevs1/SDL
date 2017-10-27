<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OfficeGroup;
use App\Office;
use DB;
class OfficesController extends Controller{
	public function __contruct()
	{
	}
    
    //GROUP OF OFFICES
    public function getGroup()
    {
        $officeGroups = DB::table('group_of_office')
                            ->orderBy('label', 'ASC')
                            ->get();
        $offices=DB::table('office')->select(['id','label'])->get();
        $response = array();
        foreach ( $officeGroups as $officeGroup )
        {

            $count =
            DB::table('office_group_office')->where('id_group_of_office',$officeGroup->id)->count();

            $response[] = array(
                'id'          => $officeGroup->id,
                'label'       => $officeGroup->label,
                'description' => $officeGroup->description,
                'count'       => $count,
                'enable'      => $officeGroup->enable,
                //'created'     => date('Y-m-d H:i:s',strtotime($officeGroup->created)),
                'created'     => $officeGroup->created,
                //'updated'     => date('Y-m-d H:i:s',strtotime($officeGroup->modified))
                'modified'     => $officeGroup->modified
            );
        }

        return view('maintainers.office_group.office')
            ->with('result', $response)
            ->with('offices', $offices);
    }
    
    public function getOfficeByOfficeGroupId(Request $request){
        $office_group_id=$request->id;
        $office=DB::select('SELECT id FROM office as o JOIN office_group_office as ogo ON ogo.id_office=o.id WHERE ogo.id_group_of_office='.$office_group_id);
        return response()->json($office);
    }
    
    public function storeGroup(Request $request)
	{
        $input=json_decode($request['arr'],true);
        $input['created']=date('Y-m-d H:i:s');
        $input['modified']=NULL;
        
		$officeGroup  = OfficeGroup::create($input);
        \Log::info($officeGroup);
		$response = array('id' => $officeGroup->id,'response'=>$officeGroup);
		return response()->json($officeGroup);
	}
    public function update(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $to_update['modified']=date("Y-m-d H:i:s");
            if(isset($request['offices']) && !empty($request['offices'])){
                $offices=explode(',',$request['offices']);
               // DB::table('office_group_office')->where('id_group_of_office',$request['id'])->delete();
                \Log::info('Delete: '.DB::table('office_group_office')->where('id_group_of_office',$request['id'])->delete());
                foreach($offices as $office_id){
                    $ogo=array(
                        'id_group_of_office'=>$request['id'],
                        'id_office'=>$office_id
                    );
                    \Log::info('Update: '.DB::table('office_group_office')->insert($ogo));
                }
            }
            $response = OfficeGroup::where('id',$request['id'])->update($to_update);
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
	}
    
    public function delete(Request $request){
        try{
            $id=$request['id'];
            $response=OfficeGroup::where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }    
    
    //Offices
    public function getOffices(){
        $result = array();
        $offices = DB::table('office')
                    ->orderBy('label', 'ASC')
                    ->get();

		foreach ( $offices as $office) {

            $count =
            DB::table('office_group_vehicle')->where('id_office',$office->id)->count();
			$result[] = array(
				'id'          => $office->id,
				'label'       => $office->label,
				'description' => $office->description,
				'geofence'    => $office->geofence,
                'count'       => $count,
				'autoupdate'  => $office->autoupdate,
				'enable'      => $office->enable,
				//'created'     => date('Y-m-d H:i:s', strtotime($office->created)),
				'created'     => $office->created,
				//'modified'    => date('Y-m-d H:i:s', strtotime($office->modified))
				'modified'    => $office->modified
				);
		}
        $g_vehicles=DB::table('group_of_vehicle')->select(['id','label'])->get();
        return view('maintainers.offices.offices')
            ->with('result', $result)
            ->with('g_vehicles', $g_vehicles);
    }
    
    public function store(Request $request)
	{
        $input=json_decode($request['arr'],true);
        $input['created']=date('Y-m-d H:i:s');
        $input['modified']=NULL;
        //\Log::info($input);
        //dd($input);
		$office = Office::create($input);
        \Log::info($office);
		return response()->json($office);
	}
    
    public function update_office(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $to_update['modified']=date("Y-m-d H:i:s");
            if(isset($request['g_vehicles']) && !empty($request['g_vehicles'])){
                $g_vehicles=explode(',',$request['g_vehicles']);
             //   \Log::info($g_vehicles);
              //  dd($g_vehicles);
                \Log::info('Delete: '.DB::table('office_group_vehicle')->where('id_office',$request['id'])->delete());
                foreach($g_vehicles as $g_vehicles_id){
                    $ogv=array(
                        'id_office'=>$request['id'],
                        'id_group_of_vehicle'=>$g_vehicles_id
                    );
                    \Log::info('Update: '.DB::table('office_group_vehicle')->insert($ogv));
                }
            }
            $response = Office::where('id',$request['id'])->update($to_update);
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
	}
    
    public function delete_office(Request $request){
        try{
            $id=$request['id'];
            $response=Office::where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }
}