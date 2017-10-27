<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class DeliveryController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function Document(Request $request){

        $document=$request['id_document'];
        $delivery_date=$request['delivery_date'];

        $data_result    =   array ();
        $document_data  =   array ();
        $product        =   array ();
        $document_status_detail =   array();

        if ( empty( $document ) || empty( $delivery_date ) ){
            Log::info('Id de documento o fecha vacia o nula.');
            return $document_data;
        }

        // Se determina el status del documento
        if ( $delivery_date == 'unknown' ){
            $document_node = DB::table('document')
                    ->WHERE('document',$document )
                    ->orderBy('delivery_date','DESC' )
                    ->first();
        }else{
            $document_node = DB::table('document')
                    ->WHERE('document',$document )
                    ->WHERE('delivery_date',$delivery_date)
                    ->first();
        }



        if (  empty( $document_node ))
        {
            Log::info('Document ['.$document.'] no posee data asociado.');
            return response()->json(['result' => array()  ]);
        }

        $document_status_id =   $document_node->id_status;


        $document_status_detail = $this->Status ( $document_status_id );

        $document_items = DB::table('document_item')
                    ->WHERE('id_document',$document_node->id )
                    #->WHERE('delivery_date',$delivery_date)
                    ->get();

        if ( empty($document_items) ){
            Log::info('No hay item asociados a este Id ['.$document.'] fecha ['.$delivery_date.']');
            $document_data  = [];

            return $document_data ;
        }


        foreach ( $document_items as $document_item ){

            // Se traduce los datos relacionados a los productos
            $product_data    = DB::table('product')
                    ->WHERE('id',$document_item->id_product)
                    ->first();

            if( empty ( $product_data ) )
            {
                    $product_data    = array(
                            'code'          =>  '',
                            'description'   =>  ''
                        );
            };

            $document_data[]  = array(
                'code'              =>  $product_data->code,
                'description'       =>  $product_data->description,
                'status'            =>  $this->Status( $document_item->id_status ),
                'dispatch_quantity' =>  $document_item->dispatch_quantity,
                'dispatch_bulk'     =>  $document_item->dispatch_bulk,
                'dispatch_volume'   =>  $document_item->dispatch_volume,
                'rejected_quantity' =>  $document_item->rejected_quantity,
                'rejected_bulk'     =>  $document_item->rejected_bulk,
                'rejected_volume'   =>  $document_item->rejected_volume
                );

        }

        $data_result =  array (
                'document_status'   =>  $document_status_detail['status'],
                'item_detail'       =>  $document_data,
                'delivery_date'     =>  $document_node->delivery_date
                );

        return response()->json($data_result);
    }

    public function Office_by_Office_Group ( $id )
    {
        $offices = array ();

        $office_group = \OfficeGroup::find( $id );
        
        if (  empty( $office_group ) )
        {
            return $offices;
        };

        $id_group_of_offices = DB::table('office_group_office')
            ->WHERE('id_group_of_office',$id)
            ->get();

        foreach( $id_group_of_offices as $id_group_of_office) 
        {
            $offices[]  = $id_group_of_office->id_office ;
        }

        return $offices ;
    }
    
    private function DashBoard_new( $office_group_id , $date_ini , $date_end  )
	{
		$rows = DB::select('SELECT array_to_json(array_agg(row_to_json(row_all)))  as  results
			FROM            
			(
				SELECT ogo.id_office as office_id, o.label as office_name, 
					( 
				        SELECT array_agg((s.status,d.id))
					FROM office_group_vehicle ogv
			                JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id 
					JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
			                JOIN vehicle v ON  v.id = vg.id_vehicle
					JOIN document d ON d.plate_number = v.plate_number 
						AND d.delivery_date between \''.$date_ini.'\' and \''.$date_end.'\'
					JOIN status s ON d.id_status = s.id
					WHERE ogv.id_office = ogo.id_office
				        ) as status
				FROM office_group_office  ogo
			        JOIN office o ON ogo.id_office = o.id
				WHERE ogo.id_group_of_office = '.$office_group_id.'
			) row_all;');
		foreach ( $rows as $row ){
            $data_row = array();
            foreach ( $row as $key => $value ){
                $data_row [ $key] = $value ;
                return json_decode($value);
            }
        }
	}
    
    public function DashBoard ( Request $request )
    {
        $office_group_id=$request->office_group;
        $date_ini=$request->date_ini;
        $date_end=$request->date_end;

        if ( empty($office_group_id) || empty($date_ini) || empty($date_end ) )
        {
            \Log::info('Error en alguno de los parametros de entrada.');
            return response()->json(['Error en alguno de los parametros de entrada.', 1001]);
        }
        $response=$this->DashBoard_new( $office_group_id , $date_ini, $date_end );

        return response()->json($response);

        // Determinanos las oficinas relacionadas al grupo de oficinas 
        $offices = $this->Office_by_Office_Group( $office_group_id );

        if ( empty( $offices ))
        {   
            \Log::info('No hay oficinas para este grupo de oficinas.');
            return response()->json(['No hay oficinas para este grupo de oficinas.', 1001]);
        }
        foreach ( $offices  as $office )
        {
            $status             = array ();
            $vehicles_groups    = array ();

            \Log::info('Oficina ['.$office.']');

            
            $vehicles_groups = $this->Vehicles_Groups_by_Office ( $office );
            
            $documents  = array ( );
            foreach ( $vehicles_groups as $vehicles_group  )
            {
                
                \Log::info('Grupo de vehiculo  ['.$vehicles_group.']');
                $documents =   $this->VehicleGroup_Detail($vehicles_group,$delivery_date);

            
                foreach ( $documents  as $document   )
                {
                    //Log::info('Plate ['.$document['plate_number'].']');
                    foreach(  $document['documents'] as $docs )
                    {
                        $status[] = $docs['status'];
                    }

                }

            }

            $office_node [] =   array (
                'office_id'     =>  $this->Office ( $office )['id'],
                'office_name'   =>  $this->Office ( $office )['label'],
                'status'        =>  $status
            );
        }

        return response()->json($office_node);
    }

    private function Vehicles_Groups_by_Office( $id )
    {
        $vehicles_groups = array ();

        $office_group_vehicles = DB::table('office_group_vehicle')
            ->WHERE('id_office',$id)
            ->get();

        foreach( $office_group_vehicles as $office_group_vehicle )
        {
            $vehicles_groups[]  = $office_group_vehicle->id_group_of_vehicle ;
        }

        return $vehicles_groups ;
    }

    private function VehicleGroup_Detail ( $vehicle_group_id ,  $delivery_date )
    {
        $plates = array();
        $vehicle_group  = array();

        if ( empty( $vehicle_group_id )) {

            #$this->pushError('Id vacio o nulo.', 1001);
            #return $this->response();
            Log::info('Id vacio o nulo.');
            return $vehicle_group;
        }


        $group_of_vehicle = DB::table('group_of_vehicle')
                            ->where('id', $vehicle_group_id )
                            ->first();

        if ( empty( $group_of_vehicle )) {

            #$this->pushError('Grupo de vehiculo no existe.', 1001);
            #return $this->response();
            Log::info('Grupo de vehiculo no existe.');
            return $vehicle_group;
        }


        // Determinamos las patentes asociadas a este grupo de vehiculos.
        $plates = $this-> Plate_by_Vehicle_Group ( $vehicle_group_id );

        if ( empty( $plates ))
        {
            Log::info('Grupo de vehiculo no contiene vehiculo asociados.');
        }

        foreach ( $plates['vehicles'] as $plate)
        {
            //Log::info('Creando nodo para ['.$plate.']');

            $vehicle_group[]= array(
                'plate_number'  =>  $plate,
                'driver'        =>  $this->Driver_by_Plate_Number  ( $plate ),
                'documents'     =>  $this->Document_by_Plate_by_Date ($plate, $delivery_date   )
                );
        }
        return $vehicle_group;
    }

    private function Driver_by_Plate_Number ( $plate_number )
    {
        $Driver = array () ;

        if (  empty( $plate_number ))
        {
            Log::info("Plate Number [".$plate_number."] no contiene data.");
            return $Driver ;
        }

        $id_employee = DB::table('vehicle')
                    ->WHERE('plate_number',$plate_number)
                    ->pluck('id_employee');

        // Ahora se determina los datos del chofer a partir del id del
        // vehiculo
        $employee_data = DB::table('employee')
                        ->WHERE('id',$id_employee)
                        ->first();

        if( empty($employee_data))
        {
            Log::info('Employee id ['.$id_employee.'] no registrado.');
            return $Driver ;
        }

        return array(
            'name'  =>  $employee_data->name,
            'phone' =>  $employee_data->mobile
        );

     }

    private function Document_by_Plate_by_Date( $plate, $date )
    {
        $document_node = array();

        if( empty ($plate) )
        {
            Log::info('Patente vacia o nula ['.$plate.']');
            return $document_node ;
        }

        #Log::info('Document_by_Plate_by_Date ['.$plate.']['.$date.']');
        $documents = DB::table('document')
                    ->WHERE('plate_number',$plate)
                    ->WHERE('delivery_date',$date)
                    ->get();

       if( empty ($documents) )
        {
            Log::info('No hay documentos asociados a la patente ['.$plate.'] fecha ['.$date.']');
            return $document_node ;
        }

        foreach (  $documents as $document )
        {
            if (isset($document->arrival_time) && !empty($document->arrival_time)) $eta =date("H:i:s",strtotime("+5 minutes",strtotime($document->arrival_time))); else $eta="N/D";
            
	    $customer = DB::table('customer')
			->WHERE('id', $document->id_client )
			->first();

            $document_node[] = array (
                    //'status'            =>  $document->id_status,
                    'status'            =>  $this->Status($document->id_status)['status'],
                    'dispatch_quantity' =>  $document->dispatch_quantity,
                    'dispatch_bulk'     =>  $document->dispatch_bulk,
                    'dispatch_volume'   =>  $document->dispatch_volume,
                    'rejected_quantity' =>  $document->rejected_quantity,
                    'rejected_bulk'     =>  $document->rejected_bulk,
                    'rejected_volume'   =>  $document->rejected_volume,
                    'id_customer'       =>  $document->id_client,
                    'modified'          =>  $document->modified,
                    'delivery_type'     =>  $document->delivery_type,
		    'containera_out'	=>  $document->containera_out,
		    'containerb_out'	=>  $document->containerb_out,
		    'containera_ret'	=>  $document->containera_ret,
		    'containerb_ret'	=>  $document->containerb_ret,
		    'distance'	        =>  $document->distance,
		    'eta'	        =>  $eta,
		    'latitude'          =>  $customer->latitude,
		    'longitude'         =>  $customer->longitude,
                );
        }

        return $document_node;
    }

    public function ChannelDeliverybyDate(Request $request)
    {
        $office_group_id=$request->office_group_id;
        $office_id=$request->office_id;
        $date_ini=$request->date_ini;
        $date_end=$request->date_end;
        $query ="SELECT COUNT(d.*) AS total, d.channel, s.status
		FROM office_group_office  ogo
		JOIN office o ON ogo.id_office = o.id
		JOIN office_group_vehicle ogv ON ogv.id_office = o.id
		JOIN group_of_vehicle gov ON ogv.id_group_of_vehicle = gov.id
		JOIN vehicle_group vg ON vg.id_group_of_vehicle = gov.id
		JOIN vehicle v ON  v.id = vg.id_vehicle
		JOIN document d ON d.plate_number = v.plate_number
		LEFT JOIN status AS s ON d.id_status = s.id
		WHERE d.delivery_date >='".$date_ini."'
		and d.delivery_date <='".$date_end."' ".($office_group_id>0?" AND ogo.id_group_of_office=".$office_group_id:" ")
		.($office_id>0?" AND o.id=".$office_id:" ")."GROUP BY d.channel, s.status ORDER BY d.channel, s.status;";

        $channels = DB::select( $query );

        return response()->json($channels);


    }
    
    public function Vehicle ( $plate , $delivery_date, Request $request ){
        $document_data   = array ( );

        if ( empty( $plate )) {
            return response()->json('Patente vacia o nula.', 1001);
        }

        if($request->ajax()){
            /*$documents = DB::table('document')
                        ->SELECT(['DISTINC ON (id_client) *'])
                        ->WHERE('plate_number',$plate)
                        ->WHERE('delivery_date',$delivery_date)
                        ->orderBy('delivery_order','ASC' )
                        ->get();*/
            $documents=DB::select("SELECT * FROM (SELECT DISTINCT ON (id_client) * FROM document WHERE plate_number='$plate' AND delivery_date='$delivery_date') AS a ORDER BY delivery_order ASC;");
        }else{
            $documents = DB::table('document')
                    ->WHERE('plate_number',$plate)
                    ->WHERE('delivery_date',$delivery_date)
                    ->orderBy('delivery_order','ASC' )
                    ->get();
        }
       if( empty ($documents) )
        {
           \Log::info('No hay documentos asociados a la patente ['.$plate.'] fecha ['.$delivery_date.']');
           return response()->json( $document_data);
        }

        foreach (  $documents as $document )
        {
            if (isset($document->arrival_time) && !empty($document->arrival_time)) $eta =date("H:i:s",strtotime("+5 minutes",strtotime($document->arrival_time))); else $eta="N/D";
            
            $document_data[]    = array (
                    'id'            =>  $document->id,
                    'document'      =>  $document->document,
                    'order_number'  =>  $document->order_number,
                    'route_sheet'   =>  $document->route_sheet,
                    'channel'       =>  $document->channel,
                    'dispatch_bulk' =>  $document->dispatch_bulk,
                    'dispatch_volume'   =>  $document->dispatch_volume,
                    'dispatch_quantity' =>  $document->dispatch_quantity,
                    'status'            =>  $this->Status( $document->id_status ),
                    'modified'      =>  $document->modified,
                    'delivery_date'    =>  $document->delivery_date,
                    'location'      =>  $document->location,
                    'payment'       =>  $document->payment,
                    'client'        =>  $this->Customer( $document->id_client ),
                    'rejected_bulk' =>  $document->rejected_bulk,
                    'rejected_volume'   =>  $document->rejected_volume,
                    'rejected_quantity' =>  $document->rejected_quantity,
                    'delivery_type' =>  $document->delivery_type,
                    'distance' =>  $document->distance,
                    'attachment'    =>  $this->DocumentAttachmentbyRS( $document->document ,'0', $delivery_date )['id'],
                    'signature'     =>  $this->DocumentSignaturebyRS( $document->document ,'0', $delivery_date )['id'],
          		    'containera_out'=>	$document->containera_out,
        		    'containerb_out'=>	$document->containerb_out,
        		    'containera_ret'=>	$document->containera_ret,
        		    'containerb_ret'=>	$document->containerb_ret,
                    'eta'           => ( $document->id_status >= 1 )?($eta):"--:--:--",
                    'order'         =>  (empty($document->delivery_order))?'N/D':$document->delivery_order,
                );
        }
        if($request->ajax()){
            \Log::info($document_data);
            return response()->json($document_data);
        }
        return view('deliveries.vehicle')
            ->with('result', $document_data)
            ->with('plate', $plate)
            ->with('delivery_date', $delivery_date);
    }
    
    private function DocumentAttachmentbyRS ( $document ,$type , $delivery_date  ){
        $doc_attachment = array(
                            'id'    => ''
                            );
        
        if ( empty ( $document ) )
        {
            Log::info('Documento no existe.  ['.$document.']');
            return $doc_attachment ;
        };

        if ( $type == '1' )
        {
            // Correponde a la vista en la cual se muestra el archivo
            // atachado de las hojas de rutas. Aca se debe anexar el
            // de Cross docking.
            $filter1 = [ 'name' => $route_sheet , 'type' => 1];
            $filter2 = [ 'name' => $route_sheet , 'type' => 3];
            $data =  DB::table('document_attachment')
                        ->where   ( $filter1)
                        ->orwhere ( $filter2)
                        ->get () ;

            if ( empty ( $data ) )
            {
                return $doc_attachment;
            };
            
            foreach ( $data as $dat )
            {
                $id[] = $dat->id;
            };



        }else {
            $data =  DB::table('document_attachment')
                        ->where ( 'id_document',  $document  )
                        ->where ( 'delivery_date',  $delivery_date )
                        ->where ( 'enable',  TRUE )
                        ->first () ;

            if ( empty ( $data ) )
                return array (
                            'id' => ''
                );

            $id = $data->id;
        }


        return array (
                'id'    =>      $id
                );

    }
    
    private function DocumentSignaturebyRS ( $document ,$type , $delivery_date  ){
        $doc_signature = array(
                            'id'    => ''
                            );

        if ( empty ( $document ) ){
            Log::info('Documento no existe.  ['.$document.']');
            return $doc_attachment ;
        };

        if ( $type == '1' )
        {
            // Correponde a la vista en la cual se muestra el archivo
            // atachado de las hojas de rutas. Aca se debe anexar el
            // de Cross docking.
            $filter1 = [ 'name' => $route_sheet , 'type' => 1];
            $filter2 = [ 'name' => $route_sheet , 'type' => 3];
            $data =  DB::table('document_attachment')
                        ->where   ( $filter1)
                        ->orwhere ( $filter2)
                        ->get () ;

            if ( empty ( $data ) )
            {
                return $doc_signature;
            };

            foreach ( $data as $dat )
            {
                $id[] = $dat->id;
            };
        }else {
            $data =  DB::table('document_attachment')
                        ->where ( 'id_document',  $document  )
                        ->where ( 'delivery_date',  $delivery_date )
                        ->where ( 'enable',  TRUE )
                        ->first () ;

            if ( empty ( $data ) )
                return array (
                            'id' => ''
                );

            $id = $data->id;
        }

        return array (
                'id'    =>      $id
                );
    }

    private function Customer (  $code ){

        $customer = array ( );

        if ( empty( $code) )
        {
            Log::info('Customer ['.$code.'] vacio .'); 

            return $customer ;
        }


        $customer = DB::table('customer')
                ->WHERE('id',$code )
                ->first();

        if ( empty( $customer) )
        {
            Log::info('Customer sin data.');
            return $customer;
        }

    
        return array(
            'name'      =>  $customer->name,
            'code'      =>  $customer->code,
            'subcode'   =>  $customer->subcode,
            'phone'     =>  $customer->phone,
            'commune'   =>  $customer->commune,
            'address'   =>  $customer->address,
            'id_office' =>  $customer->id_office
            );
    }
    
    private function Status ( $status_id ){
        
        $status = array ( );

        if (  empty ($status_id)  )
        {
            Log::info('Status ['.$status_id.'] no definido.');

            return $status ;
        }


        $status = DB::table('status')
                ->WHERE('id',$status_id)
                ->first();
 
        return array(
            'status'        =>  $status->status,
            'label'         =>  $status->label, 
            'description'   =>  $status->description
            );
    }
    
    public function Document_Attachment (Request $request)
    {
        $field=$request['field'];
        $value=$request['value'];
    
        $document_attach =   array ();
        if (  empty( $value) )
        {
            \Log::info('Error en Document_Attachment(), key vacio o nulo.');
            return $document_attach ;
        }

        $lines = DB::table('document_attachment')
                    ->WHERE( $field, $value )
                    ->get();

        if ( empty($lines))
        {
            return $documen_attach ;
        }

        foreach( $lines as $line)
        {
            $path1='';
            $path2='';
            $path3='';
            $path4='';
            if(!empty($line->path1) && file_exists($line->path1)){
                $type   = pathinfo($line->path1, PATHINFO_EXTENSION);
                $data   = file_get_contents($line->path1);
                $path1 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }if(!empty($line->path2) && file_exists($line->path2)){
                $type   = pathinfo($line->path2, PATHINFO_EXTENSION);
                $data   = file_get_contents($line->path2);
                $path2 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }if(!empty($line->path3) && file_exists($line->path3)){
                $type   = pathinfo($line->path3, PATHINFO_EXTENSION);
                $data   = file_get_contents($line->path3);
                $path3 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }if(!empty($line->path4) && file_exists($line->path4)){
                $type   = pathinfo($line->path4, PATHINFO_EXTENSION);
                $data   = file_get_contents($line->path4);
                $path4 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            $document_attach[]   =   array (
                'id_document'   =>  $line->id_document,
                'uploaded_date' =>  $line->uploaded_date,
                'delivery_date' =>  $line->delivery_date,
                'name'          =>  $line->name,
                'path'          =>  $line->path,
                'type'          =>  $line->type,
                'enable'        =>  $line->enable,
                'created'       =>  $line->created,
                'modified'      =>  $line->modified,
                'id'            =>  $line->id,
		        'name1'         =>  $line->name1,
                'path1'         =>  $path1,
                'name2'         =>  $line->name2,
                'path2'         =>  $path2,
                'name3'         =>  $line->name3,
                'path3'         =>  $path3,
                'name4'         =>  $line->name4,
                'path4'         =>  $path4,
                );
        }
        \Log::info($document_attach);
        return response()->json($document_attach[0]);

    }

    public function Document_Sign (Request $request ){
        $document_sign =   array ();
        $field=$request['field'];
        $value=$request['value'];
        if (empty( $value))
        {
            \Log::info('Error en Document_Attachment(), key vacio o nulo.');
            return $document_sign ;
        }

        $lines = DB::table('document_attachment')
                    ->WHERE( $field, $value )
                    ->get();
        if ( empty($lines))
        {
            return $document_sign ;
        }
        
        foreach( $lines as $line)
        {
            $path5='';
            if(!empty($line->path5) && file_exists($line->path5)){
                $type   = pathinfo($line->path5, PATHINFO_EXTENSION);
                $data   = file_get_contents($line->path5);
                $path5 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            
            $document_sign[]   =   array (
                'id_document'   =>  $line->id_document,
                'uploaded_date' =>  $line->uploaded_date,
                'delivery_date' =>  $line->delivery_date,
                'name'          =>  $line->name,
                'path'          =>  $line->path,
                'type'          =>  $line->type,
                'enable'        =>  $line->enable,
                'created'       =>  $line->created,
                'modified'      =>  $line->modified,
                'id'            =>  $line->id,
                'name5'         =>  $line->name5,
                'path5'         =>  $path5,
                );

        }
        return response()->json($document_sign[0]);
    }

    public function vehicleRoute(Request $request){
        try{
            $plate=$request['plate'];
            $delivery_date=$request['delivery_date'];
            $documents=$this->Document_by_Plate_by_Date ($plate, $delivery_date);
            $document=array();
            $location=$this->Location_by_Plate ($plate);
            foreach($documents as $key=>$val){
                $customer=$this->Customer($val['id_customer']);
                $temp=array(
                    'status'=>$val['status'],
                    'eta'=>$val['eta'],
                    'distance'=>$val['distance']/1000,
                    'customer'=>$customer,
                    'lat'=>(float)$val['latitude'],
                    'lng'=>(float)$val['longitude'],
                );
                array_push($document,$temp);
            }
            \Log::info($documents);
            $response=array('document'=>$document,'device_location'=>$location);
            return response()->json($response);
        }catch(Exception $e){
            \Log::info($e);
        }
    }

    private function Location_by_Plate ($plate_number){
        $location = array () ;

        if(empty( $plate_number ))        {
            \Log::info("Plate Number [".$plate_number."] no contiene data.");
            return $location ;
        }

        $location = DB::table('device')
                    ->WHERE('plate_number',$plate_number)
                    ->first();

        if( empty($location))
        {
          // \Log::info('Plate  ['.$plate_number.'] sin informacion.');
            return $location ;
        }

        return array(
            'longitude'     =>  $location->mobile_longitude,
            'latitude'      =>  $location->mobile_latitude,
            'battery'       =>  $location->mobile_battery,
            'precision'     =>  $location->mobile_precision,
            'updated'       =>  $location->mobile_updated,
            'signal'        =>  $location->signal,
            'available_mb'  =>  $location->available_mb,
            'total_mb'      =>  $location->total_mb,
        );
    }
    
    public function getIconImage(Request $request){
        $string = $request['letter'];
        $icon = $request['icon'];
        $im = imagecreatefrompng("../public/images/icons/".$icon);
        imageAlphaBlending($im, true);
        imageSaveAlpha($im, true);
        $fuente = imageloadfont("../public/fonts/arial.gdf");
        $black = imagecolorallocate($im, 255, 255, 255);
        header('Content-Type: image/png');
        imagestring($im,5,14,30,$string,$black); 
        imagepng($im, null, 0,PNG_NO_FILTER);
        imagedestroy($im);
    }
    
    public function updateGauge(Request $request){
        $index=$request['index'];
        $id_office=$request['id_office'];
        $id_g_office=0;
        $date_input=$request['date_input'];
        $res = new \ListVehicles();
        $info=$res->ListComplete('full',$id_office,$date_input,$id_g_office);
        if(isset($info) && !empty($info)){
            $deault_vehicle_name=$info[0]->name;
            $vehicle_groups_default=$info[0]->vehicle_groups;
            foreach($info as $key=>$val){
                $vehicle_groups=$info[$key]->vehicle_groups;
            }
            $vehicles_name_arr=array();
            $vehicle_groups=array();
            foreach($vehicle_groups_default as $key=>$val){
                if(!is_null($val->vehicles))
                    array_push($vehicle_groups,$val->vehicles); //Check if vehicles List is not null and store it in a variable;
            }
    //\Log::info($vehicle_groups);
    //dd($vehicle_groups);
            foreach($vehicle_groups[0] as $key=>$val){
                    $plate=$val->name;
                    array_push($vehicles_name_arr,$plate);
            }

            $vehicleObj=new \VehiclesList();
            $default_plates=$vehicleObj->VehicleList($vehicles_name_arr,$date_input); // For date use date('Y-m-d') to get current records.
        }
        
        $documents=$default_plates[$index]['documents'];
        $number_documents = count($documents);
        $rejected_quantity = 0;
        $dispatch_quantity = 0;
        $rejected_quantity = 0;
        $doc = array(0,0,0,0,0,0);
        $pdocument = array(0,0,0,0,0,0);
        $pbulkqt = array(0,0,0,0,0,0);
        $bulk = array(0,0,0,0,0,0);
        $bulkqt = array(0,0,0,0,0,0);
        if(isset($default_plates[$index]['documents']) && count($default_plates[$index]['documents'])>0){
            foreach($documents as $k=>$val){
                $document=json_decode(json_encode($val), true);
                $status=$document['status'];
                $client_unique[$status][$document["id_customer"]] = true;
                $dispatch_quantity += $document["dispatch_quantity"];
                $rejected_quantity += $document["rejected_quantity"];
                $doc[$status]++;
                $bulkqt[$status] += $document["dispatch_quantity"];
                $bulk[$status] += $document["dispatch_bulk"];
            }
        }
        $response=array(
            'delivered'=>$doc[1],
            'rejected'=>$doc[4],
            'partial_reject'=>$doc[3],
            'sum_rt_pt'=>$doc[3]+$doc[4],
            'n_visited_points'=>$doc[4]+$doc[3]+$doc[1],
            'total_docs'=>$number_documents
        );
        return response()->json($response);
    }
}
