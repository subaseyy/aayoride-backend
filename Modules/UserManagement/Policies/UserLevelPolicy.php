<?php

namespace Modules\UserManagement\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\UserManagement\Entities\User;

class UserLevelPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     * @return bool
     */

    public function view(){
        return $this->checkAccess(module_name: 'customer_setup', action: 'view');
    }
    public function add(){
        return $this->checkAccess(module_name: 'customer_setup', action: 'add');
    }
    public function edit(){
        return $this->checkAccess(module_name: 'customer_setup', action: 'edit');
    }
    public function delete(){
        return $this->checkAccess(module_name: 'customer_setup', action: 'delete');
    }
    public function log(){
        return $this->checkAccess(module_name: 'customer_setup', action: 'log');
    }
    public function export(){
        return $this->checkAccess(module_name: 'customer_setup', action: 'export');
    }

    private function checkAccess($module_name, $action){
        return auth()->user()->user_type == 'super-admin' || (in_array($module_name, auth()->user()->role->modules) && auth()->user()->moduleAccess->where('module_name', $module_name)->first()?->$action);
    }
}
