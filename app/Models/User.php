<?php

namespace App\Models;


use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, HasMediaTrait, HasRoles;

    /**
     * Tells us how the user object would be returned.
     * @return array
     */
    public function format()
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'phone' => $this->phone_no,
            'status' => $this->status,
            'role' => implode(",", $this->getRoleNames()->toArray()),
            'phone_confirmed' => (empty($this->phone_no_confirmed)) ? 'No' : 'Yes',
            'email_confirmed' => (empty($this->email_confirmed)) ? 'No' : 'Yes',
            'created_on' => $this->created_at,
        ];
    }

    public static function getSortableColumn() : string
    {
        return 'first_name';
    }

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

    /**
     * Create the collection to hold user's avatar
     */
    public function registerMediaCollections()
    {
        $this->addMediaCollection('avatar')
            ->useDisk('profiles');

        $this->addMediaCollection('service_provider')
            ->useDisk('service_providers');
    }

    // relationships

    /**
     * Gets the details of the service provider attached to this user, if any.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function serviceProvider()
    {
        return $this->hasOne(ServiceProvider::class);
    }

    /**
     * Gets the details of the services saved for the given user.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function savedServices()
    {
        return $this->belongsToMany(Service::class);
    }
}
