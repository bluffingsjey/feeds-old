<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'feeds_user_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password','no_hash'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
	
	/**
     * A user can have many truck.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function truck() 
	{
		return $this->hasMany('App\Truck');
	}
	
	/**
     * A user can have many farms.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function farms() 
	{
		return $this->hasMany('App\Farms');
	}
	
	/**
     * A user can have many bins.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bins() 
	{
		return $this->hasMany('App\Bins');
	}
	
	/**
     * A user can have many bins.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function binscat() 
	{
		return $this->hasMany('App\BinsCat');
	}
	
	/**
     * A user can have many feed types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function feedtype() 
	{
		return $this->hasMany('App\FeedTypes');
	}
	
	/**
     * A user can have many medication.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function medication() 
	{
		return $this->hasMany('App\Medication');
	}
	
	/**
     * A user can have many user information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function userInfo()
	{
		return $this->hasOne('App\UserInfo');
	}
	
	/**
     * A user can have many user type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */	
	public function userType()
	{
		return $this->hasOne('App\UserType');
	}
	
}
