<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleGroup extends Model {

	//protected $table = 'vehicle_group';
	protected $table = 'group_of_vehicle';

	//protected $fillable = ['label', 'description','enable','created','modified', 'office_id'];
	protected $fillable = ['id','label', 'description','enable','created','modified'];

	//protected $softDelete = true;
	protected $softDelete = false;

	public $timestamps = false;

	public function vehicles()
	{
		return $this->hasMany('Vehicle', 'vehicle_id');
	}

	public function office()
	{
		return $this->belongsTo('Office', 'office_id');
	}
}
