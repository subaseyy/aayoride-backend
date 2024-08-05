<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Entities\OtpVerification;
use Modules\UserManagement\Interfaces\OtpVerificationInterface;

class OtpVerificationRepository implements OtpVerificationInterface
{
    public function __construct(
        private OtpVerification $verification
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        // TODO: Implement get() method.
    }

    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->verification
            ->query()
            ->where($column, $value)
            ->when(array_key_exists('value', $attributes), function ($query) use($attributes){
                $query->where('otp', $attributes['value']);
            })
            ->first();
    }

    public function store(array $attributes): Model
    {
        $verification = $this->verification;
        $verification->phone_or_email = $attributes['phone_or_email'];
        $verification->otp = $attributes['otp'];
        array_key_exists('otp_hit_count', $attributes) ? $verification->otp_hit_count = $attributes['otp_hit_count'] : 0;
        array_key_exists('is_blocked', $attributes) ? $verification->is_blocked = $attributes['is_blocked'] : 0;
        array_key_exists('is_temp_blocked', $attributes) ? $verification->otp_hit_count = $attributes['is_temp_blocked'] : 0;
        $verification->expires_at = $attributes['expires_at'];
        $verification->save();

        return $verification;
    }

    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
    }

    public function destroy(string $id): Model
    {
       $verification = $this->getBy(column: 'id', value: $id);
       $verification->delete();
       return $verification;
    }

}
