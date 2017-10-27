<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class OfficeGroupOffice extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'office_group_office';

	protected $fillable = ['id_group_of_office','id_office'];

	protected $softDelete = false;

	public $timestamps = false;

}
