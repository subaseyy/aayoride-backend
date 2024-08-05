<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zone_id',
        'latitude',
        'longitude',
        'city',
        'street',
        'house',
        'zip_code',
        'country',
        'contact_person_name',
        'contact_person_phone',
        'address',
        'address_label',
        'created_at',
        'updated_at',
    ];
    protected $table = 'user_address';
    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserAddressFactory::new();
    }
}
