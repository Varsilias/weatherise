<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @OA\Schema(
 * @OA\Xml(name="data"),
 * @OA\Property(property="id", type="integer", readOnly="true", example="31"),
 * @OA\Property(property="user_id", type="integer", readOnly="true", example="6"),
 * @OA\Property(property="city_name", type="string", readOnly="true", example="New York"),
 * @OA\Property(property="city_key", type="string", example="232"),
 * @OA\Property(property="created_at", type="string", readOnly="true", example="2019-02-25 12:59:20"),
 * @OA\Property(property="updated_at", type="string", readOnly="true", example="2019-02-25 12:59:20"),

 * )
 *
 */

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_name', 'city_key'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
