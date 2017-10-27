<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

	protected $table = 'customer';

	protected $fillable =
	['id','name','code','address','phone','id_seller','geofence','subcode','id_office','commune','location','enable','created','modified'];

	protected $softDelete = false;

	public $timestamps = false;
 
    protected function list_filtered( $customer_id, $date_ini, $date_end )
    {
        
        $data = array ();

        $lines = DB::TABLE('document')
            ->SELECT('id','document','delivery_date','uploaded_date','id_status','route_sheet','plate_number')
            ->WHERE('id_client', $customer_id )
            ->WHEREBETWEEN('delivery_date',array($date_ini, $date_end) )
            ->get();

        

        foreach ( $lines as $line )
        {
            $data[] = array(
                'id'            =>   $line->id,
                'uploaded_date' =>   $line->uploaded_date,
                'delivery_date' =>   $line->delivery_date,
                'document'      =>   $line->document,
                'id_status'     =>   $this->Status($line->id_status),
                'plate_number'  =>   $line->plate_number,
                'route_sheet'   =>   $line->route_sheet
            );
        }

        

        return $data;

    }


    public function Status ( $status_id )
    {

        $status = array ( );

        if ( empty( $status_id ) )
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
}
