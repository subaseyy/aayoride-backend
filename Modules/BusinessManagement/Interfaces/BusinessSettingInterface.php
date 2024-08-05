<?php

namespace Modules\BusinessManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface BusinessSettingInterface extends BaseRepositoryInterface
{
    public function createTestimonial(array $attributes);
    public function updateTestimonial(string $id, array $attributes);
}
