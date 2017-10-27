<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'seller';

	protected $fillable = ['code', 'id_user','name','enable','created','modified'];

	protected $softDelete = false;

	public $timestamps = false;
	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}

	public function officeGroup()
	{
		return $this->belongsTo('OfficeGroup', 'id_group_of_office');
	}

	public function profile()
	{
		return $this->belongsTo('Profile', 'id_profile');
	}

}
