<?php

namespace Modules\AdminModule\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\AdminModule\Http\Requests\EmployeeUpdateApiRequest;
use Modules\AdminModule\Http\Requests\EmployeeUpdateSettingRequest;
use Modules\UserManagement\Service\Interface\EmployeeServiceInterface;

class SettingController extends BaseController
{
    protected $employeeService;

    public function __construct(EmployeeServiceInterface $employeeService)
    {
        parent::__construct($employeeService);
        $this->employeeService = $employeeService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        return view('adminmodule::profile-settings');
    }

    public function update(EmployeeUpdateSettingRequest $request, $id)
    {
        $this->employeeService->update(id: $id, data: $request->all());
        Toastr::success(translate(DEFAULT_200['message']));
        return back();

    }
}
