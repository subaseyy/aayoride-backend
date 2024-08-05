<?php

namespace Modules\AuthManagement\Http\Controllers\Admin\Auth;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\UserManagement\Interfaces\EmployeeInterface;

class LoginController extends Controller
{

    public function __construct(
        private EmployeeInterface $employee
    )
    {
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
            $user = $this->employee->getBy(column: 'email', value: $request['email']);
        }
         catch (\Exception $e) {

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
        if(auth()->user()) {
            auth()->guard('web')->logout();
            Toastr::success(AUTH_LOGOUT_200['message']);
            return redirect(route('admin.auth.login'));
        }
        return redirect()->back();
    }

}
