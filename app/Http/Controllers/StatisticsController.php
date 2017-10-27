<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class StatisticsController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function index()
	{
		//$profiles = \Profile::all();

        $uploads = DB::table('upload')->orderBy('id', 'DESC')->get();

		$response = array();
		foreach ($uploads as $upload) {

            $email = DB::TABLE('users')->WHERE('id',$upload->id_user)->pluck('email');

			$response[]= array(
				'id'            => $upload->id,
				'name'          => $upload->name,
				'email'         => $email,
				'lines'         => $upload->lines,
				'employee_new'  => $upload->employee_new,
				'employee_duplicated'   => $upload->employee_duplicated,
				'employee_error'        => $upload->employee_error,
				'office_new'    => $upload->office_new,
				'office_duplicated'     => $upload->office_duplicated,
				'office_error'  => $upload->office_error,
				'vehicle_new'   => $upload->vehicle_new,
				'vehicle_duplicated'  => $upload->vehicle_duplicated,
				'vehicle_error' => $upload->vehicle_error,
				'customer_new'  => $upload->customer_new,
				'customer_duplicated'   => $upload->customer_duplicated,
				'customer_error'=> $upload->customer_error,
				'document_new'  => $upload->document_new,
				'document_duplicated'   => $upload->document_duplicated,
				'document_error'        => $upload->document_error,
				'product_new'           => $upload->product_new,
				'product_duplicated'    => $upload->product_duplicated,
				'product_error'         => $upload->product_error,

				'enable'        => $upload->enable,
				'created'       => date('Y-m-d H:i:s', strtotime($upload->created)),
				'modified'      => date('Y-m-d H:i:s', strtotime($upload->modified))
				);
		}

		return view('upload.upload')
                ->with('documents', $response);
	}

    public function licence(Request $request)
    {
        $result = array();
        $input=$request->all();
        $date=$request->date;
        if ( empty ( $date ) )
        {
            $this->pushError($validator->messages()->all(), 1001);

            return $this->response();
        }
        $year   =   substr( $date ,0,4);
        $month  =   substr( $date ,4,2);

        $registers = DB::select(
                'SELECT  EXTRACT(YEAR FROM created_date) as anio,
                EXTRACT(MONTH FROM created_date) as mes, EXTRACT(DAY FROM
                created_date) as dia, plate_number, imei, request,
                created_date FROM device_licence WHERE
                EXTRACT(YEAR FROM created_date)=\''.$year.'\' AND EXTRACT(MONTH
                FROM created_date) = \''.$month.'\' ORDER BY dia;'
                );       

        foreach ( $registers as $register )
        {
            $data_row = array();
            foreach ( $register as $key => $value )
            {
                $data_row [ $key] = $value ;
            }

            $result[] = $data_row ;

        }
        
		$response = array('result' => $result );

		return response()->json($response);
    }
}
