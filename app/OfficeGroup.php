<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeGroup extends Model {

	//protected $table = 'office_groups';
	protected $table = 'group_of_office';

	protected $fillable = ['id','label', 'description', 'enable','created','modified'];

	protected $softDelete = false;

	public $timestamps = false;

	public function offices()
	{
		return $this->hasMany('Office', 'office_id');
	}

	public function users()
	{
		return $this->hasMany('User', 'user_id');
	}

	public function getOfficeIds()
	{
		$officeIds = array();
		foreach ($this->offices as $office) {
			$officeIds[] = $office->id;
		}

		return $officeIds;
	}
}
