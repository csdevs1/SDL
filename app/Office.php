<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Office extends Model {

	protected $table = 'office';

	protected $fillable = ['id','label','description','geofence','enable','created','modified', 'autoupdate' ];

	protected $softDelete = false;

	public $timestamps = false;

	public function vehicleGroups()
	{
		return $this->hasMany('VehicleGroup', 'vehicle_group_id');
	}

	public function officeGroup()
	{
		return $this->belongsTo('OfficeGroup');
	}

	public function sellers()
	{
		return $this->hasMany('Seller', 'seller_id');
	}

	public function invoices()
	{
		$invoiceEntries = array();
		foreach ($this->sellers as $seller) {
			foreach ($seller->invoices() as $invoice)
				$invoiceEntries[] = $invoice;
		}

		return $invoiceEntries;
	}

}
