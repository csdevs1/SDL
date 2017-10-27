<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model {
	
	protected $table = 'document';

	protected $fillable = ['uploaded_date', 'delivery_date','document','id_status', 'plate_number', 'id_client',  'dispatch_quantity','dispatch_bulk','accountable','containera_out','containerb_out','containera_ret','containerb_ret'];

	protected $softDelete = false;

	public $timestamps = false;

	public function client()
	{
		return $this->belongsTo('Client', 'client_id');
	}

	public function status()
	{
		return $this->belongsTo('Status', 'id_status');
	}

	public function customer()
	{
		return $this->belongsTo('Customer', 'id_client');
	}
}
