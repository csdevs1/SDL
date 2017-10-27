<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class DocumentsController extends Controller{
	
	public function __contruct()
	{
	}
    
    public function getDocument(Request $request){
        $id=$request->id;
        $document=DB::table('document')
                ->select(['document','id_status','plate_number'])
                ->WHERE('id',$id)
                ->first();
        $status=DB::table('status')
                ->select(['label'])
                ->WHERE('id',$document->id_status)
                ->first();
        $document->status=$status->label;
        $document=json_encode($document,true);
        return response()->json($document);
    }
    
    public function ListDocuments ()
    {
        $documents   =   DB::table('document')
                //->WHERE($key,$value)
                ->orderBy('plate_number','ASC')
                ->orderBy('route_sheet','ASC')
                ->orderBy('document','ASC')
                ->get();
        var_dump($documents);
       /* $rules = array(
            'key'       => 'required',
            'value'     => 'required'
            );

        $validator = \Validator::make(\Input::all(), $rules);


        if ($validator->fails()) {
            $this->pushError($validator->messages()->all(), 1001);

            return $this->response();
        }

        $key            =   \Input::get('key');
        $value          =   \Input::get('value');

        $result = array ();

        $documents   =   DB::table('document')
                //->WHERE($key,$value)
                ->orderBy('plate_number','ASC')
                ->orderBy('route_sheet','ASC')
                ->orderBy('document','ASC')
                ->get();

        if ( empty( $documents ) )
        {
            Log::info('Document not matched.');
        }
        
        # Define un arreglo con los id de status almacenados.
        $status_Ids = array ();       
        $client_Ids = array ();       

        foreach ( $documents as $document )
        {

            if ( empty ( $document->id_status  ) )
            {
                continue;
            };

            if ( ! array_key_exists( $document->id_status , $status_Ids ) ) 
            {
                $status = DB::table ('status')
                        ->WHERE('id',$document->id_status)
                        ->first();


                $status_Ids[ $document->id_status ]  =  array (
                                                        'status'        => $status->status,
                                                        'label'         => $status->label,
                                                        'description'   => $status->description
                                                    );
            }

            if ( ! array_key_exists( $document->id_client , $client_Ids ) )
            {
                $client = DB::table ('customer')
                        ->WHERE('id',$document->id_client)
                        ->first();


                $client_Ids[ $document->id_client ]  =  array (
                                                        'name'      =>$client->name,
                                                        'code'      =>$client->code,
                                                        'subcode'   =>$client->subcode
                                                    );
            }
            
        
            $result[] =   array (
                'id'                =>  $document->id,
                'uploaded_date'     =>  $document->uploaded_date,
                'delivery_date'     =>  $document->delivery_date,
                'document'          =>  $document->document,
                'status'            =>  $status_Ids[ $document->id_status ],
                'plate_number'      =>  $document->plate_number,
                'client'            =>  $client_Ids[ $document->id_client ],
                'route_sheet'       =>  $document->route_sheet
                );
        }

        return $this->response ( array('result' => $result));*/

    }

    
    public function DashBoard ( $office_group_id , $date_ini, $date_end )
    {

        if ( empty($office_group_id) || empty($date_ini) || empty($date_end ) )
        {
            Log::info('Error en alguno de los parametros de entrada.');
            
            $this->pushError('Error en alguno de los parametros de entrada.', 1001);

            return $this->response();
        }

        return $this->response(['result' => $this->DashBoard_new( $office_group_id , $date_ini, $date_end )]);

        // Determinanos las oficinas relacionadas al grupo de oficinas 
        $offices = $this->Office_by_Office_Group( $office_group_id );

        if ( empty( $offices ))
        {   
            Log::info('No hay oficinas para este grupo de oficinas.');
            $this->pushError('No hay oficinas para este grupo de oficinas.', 1001);

            return $this->response();
        }

    
        foreach ( $offices  as $office )
        {
            $status             = array ();
            $vehicles_groups    = array ();

            Log::info('Oficina ['.$office.']');

            
            $vehicles_groups = $this->Vehicles_Groups_by_Office ( $office );
            
            $documents  = array ( );
            foreach ( $vehicles_groups as $vehicles_group  )
            {
                
                Log::info('Grupo de vehiculo  ['.$vehicles_group.']');
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

        return $this->response(['result' => $office_node ]);

    }
    
    public function getDocumentByStatus(Request $request){
        $status=$request['status'];
        $plate=$request['plate'];
        $delivery_date=$request['delivery_date'];
        $documents=DB::table('document')
                ->WHERE('id_status',$status)
                ->WHERE('plate_number',$plate)
                ->WHERE('delivery_date',$delivery_date)
                ->get();
        $reject_reason='';
        if($status == 28 || $status == 7){
            $reject_reason=DB::table('status')
                ->select(['description'])
                ->WHERE('id',$status)
                ->first();
        }
        $response=array();
        foreach($documents as $k=>$document){
            $customer=DB::table('customer')
                ->WHERE('id',$document->id_client)
                ->first();
            $arr=array(
                'document'=>$document->document,
                'plate_number'=>$document->plate_number,
                'arrival_time'=>$document->arrival_time,
                'order_number'=>$document->order_number,
                'route'=>$document->route_sheet,
                'customer_name'=>$customer->name,
                'customer_address'=>$customer->address,
                'reject_reason'=>$reject_reason
            );
            array_push($response,$arr);
        }
        \Log::info($response);
        return response()->json($response);
    }
}
