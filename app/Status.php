<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'status';

	protected $fillable = ['id','is_document','status','code','label','description','enable','created','modified'];

	protected $softDelete = false;

	public $timestamps = false;

}
