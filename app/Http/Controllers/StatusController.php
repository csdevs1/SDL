<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Status;

class StatusController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function index(){
		$statuss = DB::table('status')
                    ->orderBy('code')
                    ->get();

		$statusEntries = array();

		//$statuss =	DB::table('status')->get();

		foreach ($statuss as $status) {


			$statusEntries[] = array(
				'id'            => $status->id,
				'is_document'   => $status->is_document,
				'status'        => $status->status,
                'code'          => $status->code,
                'label'         => $status->label,
                'description'   => $status->description,
                'enable'        => $status->enable,
                'created'       => $status->created,
                'modified'      => $status->modified
				);
		}

        return view('maintainers.status.show')
            ->with('result', $statusEntries);
	}
    
    public function create(Request $request){        
        try{
            $to_save=json_decode($request['arr'],true);
            $to_save['created']=date('Y-m-d H:i:s');
            //\Log::info($to_save);
            //dd($to_save);
            $status= Status::create($to_save);
            $response=array('id'=>$status->id, 'code'=>$status->code, 'reason'=>$status->description,'is_document'=>$status->is_document, 'access'=>$status->enable, 'created'=>$status->created);
            \Log::info($response);
            return response()->json($response);
        }catch(\Exception $e){
            \Log::info($e);
            return $e;
        }
	}
    
    public function update_access(Request $request){
        try{
            $status=Status::select(['enable'])->where('id',$request->id)->first();
            $to_update=array('enable'=>!$status->enable);
            $response = Status::where('id',$request->id)->update($to_update);
            return response()->json(!$status->enable);
        }catch(\Exception $e){
            return response()->json($e);
        }
    }
}