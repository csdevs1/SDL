<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class ReportController extends Controller{
	public function __contruct()
	{
	}
    public function RejectsByReason (Request $request){
        $date_ini = $request->date_ini;
        $date_end = $request->date_end;
        $office_group_id = empty($request->office_group_id)?0:$request->office_group_id;
        $office_id       = empty($request->office_id)?0:$request->office_id;

        $query = "SELECT sum(di.rejected_quantity) as total,
                        s.description,array_to_json(array_agg((d.document,p))) as di_info
                    FROM office_group_office  ogo
                        JOIN office o ON ogo.id_office = o.id
                        JOIN office_group_vehicle ogv ON ogv.id_office = o.id
                        JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id
                        JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
                        JOIN vehicle v ON  v.id = vg.id_vehicle
                        JOIN document d ON d.plate_number = v.plate_number
                        JOIN document_item as di ON di.id_document = d.id
                        JOIN status as s ON di.id_status = s.id
                        JOIN product as p ON p.id=di.id_product
                    WHERE
                            d.delivery_date BETWEEN '".$date_ini."' AND '".$date_end."'
                    AND
                            s.status > 3 "
            .($office_group_id>0?" AND ogo.id_group_of_office=".$office_group_id:" ")
            .($office_id>0?" AND o.id=".$office_id:" ")
                    ." GROUP BY s.description;";
        //\Log::info($query);
        //dd($query);
        $data = DB::select($query);
        \Log::info($data);
        return response()->json($data);
    }

    public function PartialRejectsByReason (Request $request){
        $date_ini = $request->date_ini;
        $date_end = $request->date_end;
        $office_group_id = empty($request->office_group_id)?0:$request->office_group_id;
        $office_id       = empty($request->office_id)?0:$request->office_id;

	$query = "SELECT
                        s.description as description,
                        count(*) as total,array_to_json(array_agg((d.document,p))) as di_info
                FROM    office_group_office  ogo
                        JOIN office o ON ogo.id_office = o.id
                        JOIN office_group_vehicle ogv ON ogv.id_office = o.id
                        JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id
                        JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
                        JOIN vehicle v ON  v.id = vg.id_vehicle
                        JOIN document d ON d.plate_number = v.plate_number
                        JOIN document_item as di ON di.id_document = d.id
                        JOIN status as s ON di.id_status = s.id
                        JOIN product as p ON di.id_product = p.id
                WHERE
                        di.delivery_date BETWEEN :date_ini AND :date_end
                AND
                        d.id_status= 7
		AND 	s.status=4
		AND 	s.is_document=FALSE "
                .($office_group_id>0?" AND ogo.id_group_of_office=".$office_group_id:" ")
                .($office_id>0?" AND o.id=".$office_id:" ")
                ." GROUP BY s.description;";
        $data = DB::select( $query , [$date_ini, $date_end] );
       /* $data[0]->total = 10;
        $data[2]->total = 100;
        $data[3]->total = 500;*/
        return response()->json($data);
    }

    public function HistoricTendency (Request $request){
        $data_node = array ();
        $date_ini = $request->date_ini;
        $date_end = $request->date_end;
        $office_group_id = $request->office_group_id;
        $office_id       = empty($request->office_id)?0:$request->office_id;
        $query = "select d.created::date  as created,
		count (*) as total_docs,
		count( CASE WHEN d.modified::date <= d.delivery_date THEN 1 END )
		as kpi_docs
		FROM office_group_office  ogo
		JOIN office o ON ogo.id_office = o.id
		JOIN office_group_vehicle ogv ON ogv.id_office = o.id
		JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id
		JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
		JOIN vehicle v ON  v.id = vg.id_vehicle
		JOIN document d ON d.plate_number = v.plate_number
		AND d.created between :date_ini and :date_end
	        JOIN status s ON d.id_status = s.id
		WHERE ogo.id_group_of_office = :office_group_id
		AND s.id > 2 "
                .($office_id>0?" AND o.id=".$office_id:" ")
		." group by d.created::date order by d.created::date;";

        $data = DB::select( $query , [$date_ini, $date_end, $office_group_id] );
        foreach ($data as $row) {
            if (empty($row)){
                continue;
            }
            $data_node[] = array (
                'created' => $row->created,
                'total_docs' => $row->total_docs,
                'kpi_docs' => $row->kpi_docs,
            );
        }
        return response()->json($data_node);
    }

    public function KpiIndicator (Request $request){
        $data_nodes = array ();

        $date_ini = $request->date_ini;
        $date_end = $request->date_end;
        $office_group_id = $request->office_group_id;
        $office_id = empty($request->office_id)?0:$request->office_id;

        if ( $office_id > 0 ){
            $data = DB::select("select count (*) as total_docs,
			count( CASE WHEN d.modified::date <= d.delivery_date THEN 1 END )
			as kpi_docs
		FROM office_group_office  ogo
		JOIN office o ON ogo.id_office = o.id
		JOIN office_group_vehicle ogv ON ogv.id_office = o.id
		JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id
		JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
		JOIN vehicle v ON  v.id = vg.id_vehicle
		JOIN document d ON d.plate_number = v.plate_number
		AND d.created between :date_ini and :date_end
	        JOIN status s ON d.id_status = s.id
		WHERE ogo.id_group_of_office = :office_group_id
		AND o.id = :office_id
		AND s.id > 2;"
		,[$date_ini, $date_end, $office_group_id, $office_id ]);
        }else{
            $data = DB::select("select count (*) as total_docs,
                        count( CASE WHEN d.modified::date <= d.delivery_date THEN 1 END )
                        as kpi_docs
                FROM office_group_office  ogo
                JOIN office o ON ogo.id_office = o.id
                JOIN office_group_vehicle ogv ON ogv.id_office = o.id
                JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id
                JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
                JOIN vehicle v ON  v.id = vg.id_vehicle
                JOIN document d ON d.plate_number = v.plate_number
                AND d.created between :date_ini and :date_end
                JOIN status s ON d.id_status = s.id
                WHERE ogo.id_group_of_office = :office_group_id
                AND s.id > 2;"
                ,[$date_ini, $date_end, $office_group_id]);
        }
        foreach ($data as $row) {
            if (empty($row)){
                continue;
            }

            $data_node = array (
                'total_docs' => $row->total_docs,
                'kpi_docs' => $row->kpi_docs,
            );
        }
        return response()->json($data_node);
    }

    public function GetDispatchReport(Request $request){
        $date_ini=$request['date_ini'];
        $date_end=$request['date_end'];
        if ( empty( $date_ini) || empty( $date_end ) )
        {
            \Log::info('Error. Incomming parameter empty.');
            return response()->json(['Incomming parameter empty.', 1001]);
        };

        $data = \DB::select("SELECT v.plate_number as plate, count (document) as docs, sum(CASE WHEN  s.status = 0 THEN 1 ELSE 0 END) as reparto,sum(CASE WHEN  s.status = 1 THEN 1 ELSE 0 END) as f_aceptada, sum(CASE WHEN  s.status = 2 THEN 1 ELSE 0 END) as redespacho, sum(CASE WHEN  s.status = 3 THEN 1 ELSE 0 END) as r_parcial, sum(CASE WHEN  s.status = 4 THEN 1 ELSE 0 END) as f_rechazada, sum(CASE WHEN  s.status = 5 THEN 1 ELSE 0 END) as no_entregado FROM document as d join status as s on d.id_status = s.id  JOIN vehicle as v on d.plate_number = v.plate_number WHERE d.delivery_date::date between '{$date_ini}' and '{$date_end}' GROUP BY  v.plate_number;");

        $result=array();
        foreach ($data as $row){
            $result[] = array (
                'documents'    => $row->docs,
                'plate'        => $row->plate,
                'reparto'      => $row->reparto,
                'f_aceptada'   => $row->f_aceptada,
                'redespacho'   => $row->redespacho,
                'r_parcial'    => $row->r_parcial,
                'f_rechazada'  => $row->f_rechazada,
                'no_entregado' => $row->no_entregado
            );
        }
        if(isset($request['export']) && !empty($request['export']) && !empty($result)){
            return \Excel::create('SDL - Open Wireless Laboratories', function($excel) use ($result) {
                $excel->sheet('mySheet', function($sheet) use ($result)
                {
                    $sheet->fromArray($result);
                });
            })->download($request['export']);
        }else{
            \Log::info($result);
            return response()->json($result);
        }
    }

    public function GetDispatchReportByEmployee(Request $request){
        $date_ini=$request['date_ini'];
        $date_end=$request['date_end'];
        if ( empty( $date_ini) || empty( $date_end ) )
        {
            \Log::info('Error. Incomming parameter empty.');
            return response()->json(['Incomming parameter empty.', 1001]);
        };

         $data = \DB::select("SELECT e.name as driver, count (document) as docs, sum(CASE WHEN  s.status = 0 THEN 1 ELSE 0 END) as reparto,sum(CASE WHEN  s.status = 1 THEN 1 ELSE 0 END) as f_aceptada, sum(CASE WHEN  s.status = 2 THEN 1 ELSE 0 END) as redespacho, sum(CASE WHEN  s.status = 3 THEN 1 ELSE 0 END) as r_parcial, sum(CASE WHEN  s.status = 4 THEN 1 ELSE 0 END) as f_rechazada, sum(CASE WHEN  s.status = 5 THEN 1 ELSE 0 END) as no_entregado FROM document as d join status as s on d.id_status = s.id JOIN employee as e ON d.id_employee = e.id  WHERE d.delivery_date::date between '{$date_ini}' and '{$date_end}' GROUP BY e.name;");
        $result=array();
        foreach ($data as $row){
            $result[] = array (
                'documents'    => $row->docs,
                'driver'        => $row->driver,
                'reparto'      => $row->reparto,
                'f_aceptada'   => $row->f_aceptada,
                'redespacho'   => $row->redespacho,
                'r_parcial'    => $row->r_parcial,
                'f_rechazada'  => $row->f_rechazada,
                'no_entregado' => $row->no_entregado
            );
        }
        if(isset($request['export']) && !empty($request['export']) && !empty($result)){
            return \Excel::create('SDL - Open Wireless Laboratories', function($excel) use ($result) {
                $excel->sheet('mySheet', function($sheet) use ($result)
                {
                    $sheet->fromArray($result);
                });
            })->download($request['export']);
        }else{
            \Log::info($result);
            return response()->json($result);
        }
    }

}
