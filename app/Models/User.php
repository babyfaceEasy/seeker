<?php

namespace App\Models;


use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone_no', 'first_name', 'last_name', 'email', 'password', 'middle_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // changes for the passport

    /**
     * This sets the field to be used as username for laravel's auth
     * @return string
     */
    public function username()
    {
        return 'phone_no';
    }

    /**
     * Find the user instance for the given phone_no
     * @param string $phone_no
     * @return User
     */
    public function findForPassport( string $phone_no ) : User
    {
        return $this->where('username', $phone_no)->first();
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('avatar')->useDisk('do_spaces');
    }
}
