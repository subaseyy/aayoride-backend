<?php

namespace Modules\TripManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

interface FareBiddingLogInterface
{
 public function store($attributes): mixed;

public function storeAll($attributes): mixed;
public function destroyData($attributes): mixed;

}
