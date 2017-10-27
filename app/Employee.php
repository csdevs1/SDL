<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'employee';

	protected $fillable = ['name', 'mobile','code','type_employee','boss_id','enable','created','modified'];

	protected $softDelete = false;

	public $timestamps = false;

}
