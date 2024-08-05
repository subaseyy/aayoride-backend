<?php

namespace Modules\UserManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface WithdrawalMethodInterface extends BaseRepositoryInterface
{
    public function AjaxDefaultStatusUpdate($id);

    public function AjaxActiveStatusUpdate($id);

}
