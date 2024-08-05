<?php

namespace Modules\AuthManagement\Http\Controllers\Web\New\Admin\Auth;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Service\Interface\EmployeeServiceInterface;

class LoginController extends BaseController
{
    protected $employeeService;

    public function __construct(EmployeeServiceInterface $employeeService)
    {
        parent::__construct($employeeService);
        $this->employeeService = $employeeService;
        $this->middleware(function ($request, $next) {
            if (auth()->check()) {
                return redirect(route('admin.dashboard'));
            }
            return $next($request);
        })->except('logout');
    }

    /**
     * @return Renderable
     */

    public function loginView(): Renderable
    {
        return view('authmanagement::login');
    }

    public function login(Request $request)
    {
        try {
            $user = $this->employeeService->findOneBy(criteria: ['email' => $request['email']]);
        } catch (\Exception $e) {
            Toastr::error(NO_DATA_200['message']);
            return back();
        }
        if (isset($user) && Hash::check($request['password'], $user->password)) {
            if (($user && $user?->role?->is_active) || $user->user_type === 'super-admin') {
                if (auth()->attempt(['email' => $request['email'], 'password' => $request['password']])) {
                    Toastr::success(AUTH_LOGIN_200['message']);
                    return redirect()->route('admin.dashboard');
                }
            }
            Toastr::error(ACCOUNT_DISABLED['message']);
            return back();
        }
        Toastr::error(AUTH_LOGIN_401['message']);
        return back();
    }

    public function logout()
    {
        if (auth()->user()) {
            auth()->guard('web')->logout();
            Toastr::success(AUTH_LOGOUT_200['message']);
            return redirect(route('admin.auth.login'));
        }
        return redirect()->back();
    }
}
