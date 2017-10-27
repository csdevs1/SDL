<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Employee;

class EmployeeController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function index(){
		$employees = DB::table('employee')->get();

		$employeeEntries = array();

		foreach ($employees as $employee) 
        {

			$employeeEntries[] = array(
				'id'        => $employee->id,
				'name'      => $employee->name,
				'mobile'    => $employee->mobile,
				'code'      => $employee->code,
				'type_employee'   => $employee->type_employee,
				'boss_id'   => $employee->boss_id,
				'enable'    => $employee->enable,
                'created'   => $employee->created,
                'modified'  => $employee->modified
				);

		}

        return view('maintainers.employees.show')
            ->with('result', $employeeEntries);
	}
    
    public function create(Request $request){        
        try{
            $to_save=json_decode($request['arr'],true);
            $to_save['created']=date('Y-m-d H:i:s');
            $employee= Employee::create($to_save);
            $response=array('id'=>$employee->id, 'code'=>$employee->code, 'name'=>$employee->name, 'created'=>$employee->created);
            \Log::info($response);
            return response()->json($response);
        }catch(\Exception $e){
            \Log::info($e);
            return $e;
        }
	}
    
    public function update(Request $request){
        try{
            $to_update=json_decode($request['arr'],true);
            $to_update['modified']=date("Y-m-d H:i:s");
            //\Log::info($to_update);
            //dd($to_update);
            $response = Employee::where('id',$request['id'])->update($to_update);
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }

    public function delete(Request $request){
        try{
            $id=$request['id'];
            $response=Employee::where('id',$id)->delete();
            return response()->json($response);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }
}
