<?php

namespace Modules\AdminModule\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;
use Modules\TripManagement\Entities\TripRequest;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Service\Interface\CustomerServiceInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;
use Modules\UserManagement\Service\Interface\EmployeeServiceInterface;
use Modules\UserManagement\Service\Interface\UserAccountServiceInterface;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends BaseController
{
    protected $zoneService;
    protected $tripRequestService;
    protected $transactionService;
    protected $userAccountService;
    protected $driverService;
    protected $customerService;
    protected $employeeService;

    public function __construct(ZoneServiceInterface        $zoneService, TripRequestServiceInterface $tripRequestService,
                                TransactionServiceInterface $transactionService, UserAccountServiceInterface $userAccountService,
                                DriverServiceInterface      $driverService, CustomerServiceInterface $customerService, EmployeeServiceInterface $employeeService)
    {
        parent::__construct($zoneService);
        $this->zoneService = $zoneService;
        $this->tripRequestService = $tripRequestService;
        $this->transactionService = $transactionService;
        $this->userAccountService = $userAccountService;
        $this->driverService = $driverService;
        $this->customerService = $customerService;
        $this->employeeService = $employeeService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $zones = $this->zoneService->getBy(criteria: [
            'is_active' => 1
        ]);
        $transactions = $this->transactionService->getBy(criteria: ['user_id' => \auth()->user()->id], orderBy: ['created_at' => 'desc'])->take(7);
        $superAdmin = $this->employeeService->findOneBy(criteria: ['user_type' => 'super-admin']);
        $superAdminAccount = $this->userAccountService->findOneBy(criteria: ['user_id' => $superAdmin?->id]);
        $customers = $this->customerService->getBy(criteria: ['user_type' => CUSTOMER, 'is_active' => true])->count();
        $drivers = $this->driverService->getBy(criteria: ['user_type' => DRIVER, 'is_active' => true])->count();
        $totalCouponAmountGiven = $this->tripRequestService->getBy(criteria: ['payment_status' => PAID])->SUM('coupon_amount');
        $totalDiscountAmountGiven = $this->tripRequestService->getBy(criteria: ['payment_status' => PAID])->SUM('discount_amount');
        $totalTrips = $this->tripRequestService->getAll()->count();
        return view('adminmodule::dashboard', compact('zones', 'transactions', 'superAdminAccount', 'customers',
            'drivers', 'totalDiscountAmountGiven','totalCouponAmountGiven','totalTrips'));
    }

    public function recentTripActivity()
    {
        $trips = $this->tripRequestService->getBy(relations: ['customer', 'vehicle', 'vehicleCategory'], orderBy: ['created_at' => 'desc'], limit: 5, offset: 1);
        return response()->json(view('adminmodule::partials.dashboard._recent-trip-activity', compact('trips'))->render());
    }


    public function leaderBoardDriver(Request $request)
    {
        $request->merge(['user_type' => DRIVER]);
        $leadDriver = $this->tripRequestService->getLeaderBoard($request->all(), limit: 20);
        return response()->json(view('adminmodule::partials.dashboard._leader-board-driver', compact('leadDriver'))->render());
    }

    public function leaderBoardCustomer(Request $request)
    {
        $request->merge(['user_type' => CUSTOMER]);
        $leadCustomer = $this->tripRequestService->getLeaderBoard($request->all(), limit: 20);
        return response()->json(view('adminmodule::partials.dashboard._leader-board-customer', compact('leadCustomer'))->render());
    }

    public function adminEarningStatistics(Request $request)
    {
        $data = $this->tripRequestService->getAdminZoneWiseEarning($request->all());
        return response()->json($data);
    }


    public function zoneWiseStatistics(Request $request)
    {
        $data = $this->tripRequestService->getAdminZoneWiseStatistics(data: $request->all());
        return response()
            ->json(view('adminmodule::partials.dashboard._areawise-statistics', ['trips' => $data['zoneTripsByDate'], 'totalCount' => $data['totalTrips']])
                ->render());
    }
}
