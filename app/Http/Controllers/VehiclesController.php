<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\VehicleGroup;
class VehiclesController extends Controller{
	
	public function __contruct()
	{
	}
    private function get_vehicles_info(){
        //$vehicles = \Vehicle::all();
        $vehicles = DB::table('vehicle')
                    ->orderBy('label', 'ASC')
                    ->get();

		$response = array();
		foreach ( $vehicles as $vehicle) {
           $driver_detail = array();

            $driver=DB::table('employee')->where('id',$vehicle->id_employee)->first();
            if (!empty($driver)) {
                $driver_detail = array(
                    'id'     => $driver->id,
                    'name'   => $driver->name,
                    'mobile' => $driver->mobile,
                    'code'    => $driver->code,
                    'type'   => $driver->type_employee,
                    );
            }

            $device_detail =
            DB::table('device')->WHERE('id',$vehicle->id_device)->first();

			$response[] = array(
				'id'           => $vehicle->id,
				'plate_number' => $vehicle->plate_number,
				'label'        => $vehicle->label,
                'id_device'    => $vehicle->id_device,
                'label_device' =>
                (empty($device_detail))?'':$device_detail->label,
                'driver'       => $driver_detail,
                'enable'       => $vehicle->enable,
                'weight'       => $vehicle->weight,
                'volume'       => $vehicle->volume,
				//'created'       => date('Y-m-d H:i:s'), strtotime($vehicle->created),
				'created'       => $vehicle->created,
				//'updated'   => date('Y-m-d H:i:s', strtotime($vehicle->modified))
				'modified'   => $vehicle->modified
				//'updated'   => NULL
				);
			//$driver = $vehicle->id_employee;
		}
        return $response;
    }

    public function show(){
        $result = $this->get_vehicles_info();
        return view('maintainers.vehicles.show')
            ->with('result', $result);
    }
    public function create(){
        $result = $this->get_vehicles_info();
        return view('maintainers.vehicles.new')
            ->with('result', $result);
    }

    public function save(Request $request){
        try{
            $to_save=json_decode($request['arr'],true);
           // dd($to_save);
            $id= DB::table('vehicle')->insertGetId($to_save);
            $response=array('1',$id,date("Y-m-d H:i:s"));
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
            \Log::info($to_update);
            //return response()->json($to_update);
            $response = DB::table('vehicle')
                ->where('id',$request['id'])
                ->update($to_update);
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }

    public function delete(Request $request){
        try{
            $id=$request['id'];
            $response=DB::table('vehicle')->where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }

    public function get_vehicles_by_office(Request $request){
        $office_id=$request->id;
        $group_vehicles=DB::select('SELECT id FROM group_of_vehicle as gov JOIN office_group_vehicle as ogv ON ogv.id_group_of_vehicle=gov.id WHERE ogv.id_office='.$office_id);
        \Log::info($group_vehicles);
        return response()->json($group_vehicles);
    }

    // Matainer
    public function listGroup(){

        $vehicleGroups = DB::table('group_of_vehicle')
                        ->orderBy('label', 'ASC')
                        ->get();

        $result = array();
        foreach ($vehicleGroups as $vehicleGroup) {

            $count = DB::table('vehicle_group')->where('id_group_of_vehicle',$vehicleGroup->id)->count();

            $result[] = array(
                'id'          => $vehicleGroup->id,
                'label'       => $vehicleGroup->label,
                'description' => $vehicleGroup->description,
                'created'     => $vehicleGroup->created,
                'modified'    => $vehicleGroup->modified,
                'count'       => $count
                );
         }
        $vehicles=DB::table('vehicle')->select(['id','plate_number as plate'])->get();
        return view('maintainers.vehicle_group.vehicle_group')
                ->with('result', $result)
                ->with('vehicles', $vehicles);
    }

    public function storeGroup(Request $request){
		$input=json_decode($request['arr'],true);
        $input['created']=date('Y-m-d H:i:s');
        $input['modified']=NULL;

		$vehicleGroup = VehicleGroup::create($input);
		return response()->json($vehicleGroup);
	}

    public function update_group(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $to_update['modified']=date("Y-m-d H:i:s");
            if(isset($request['vehicles']) && !empty($request['vehicles'])){
                $vehicles=explode(',',$request['vehicles']);
                //\Log::info($vehicles);
                //dd($vehicles);
               // DB::table('office_group_office')->where('id_group_of_office',$request['id'])->delete();
                \Log::info('Delete: '.DB::table('vehicle_group')->where('id_group_of_vehicle',$request['id'])->delete());
                foreach($vehicles as $vehicle_id){
                    $vg=array(
                        'id_group_of_vehicle'=>$request['id'],
                        'id_vehicle'=>$vehicle_id
                    );
                    \Log::info('Update: '.DB::table('vehicle_group')->insert($vg));
                }
            }
            $response = VehicleGroup::where('id',$request['id'])->update($to_update);
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
	}

    public function delete_group(Request $request){
        try{
            $id=$request['id'];
            $response=VehicleGroup::where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }

    public function get_vehicles_by_group(Request $request){
        $vehicle_group_id=$request->id;
        $group_vehicles=DB::select('SELECT id FROM vehicle as v JOIN vehicle_group as vg ON vg.id_vehicle=v.id WHERE vg.id_group_of_vehicle='.$vehicle_group_id);
        \Log::info($group_vehicles);
        return response()->json($group_vehicles);
    }
}
