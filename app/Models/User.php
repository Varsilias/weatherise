<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Location;

/**
 *
 * @OA\Schema(
 * required={"password"},
 * @OA\Xml(name="user"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="3"),
 * @OA\Property(property="firstname", type="string", readOnly="true", description="User role", example="Daniel"),
 * @OA\Property(property="lastname", type="string", readOnly="true", description="User role", example="Okoronkwo"),
 * @OA\Property(property="email", type="string", readOnly="true", format="email", description="User unique email address", example="danielokoronkwo@yahoo.com"),
 * @OA\Property(property="email_verified_at", type="string", readOnly="true", format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
 * @OA\Property(property="created_at", type="string", readOnly="true", description="date User registered", example="2019-02-25 12:59:20"),
 * @OA\Property(property="updated_at", type="string", readOnly="true", description="last date user updated their profile", example="2019-02-25 12:59:20"),


 * )
 *
 */

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
