<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model {

	protected $table = 'profile';

	protected $fillable = ['label','description','modules','enable','created','modified','category'];

	protected $softDelete = false;

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User');
	}
}
