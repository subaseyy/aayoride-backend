<?php

namespace Modules\Gateways\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Gateways\Traits\HasUuid;

class Setting extends Model
{
    use HasFactory, HasUuid;

    protected $casts = [
        'live_values'=>'array',
        'test_values'=>'array',
        'is_active'=>'integer',
    ];

    protected $fillable = ['key_name', 'live_values', 'test_values', 'settings_type', 'mode', 'is_active','additional_data','created_at','updated_at'];

    public function scopeSettingsType($query, $value){
        $query->where('settings_type', $value);
    }
}
