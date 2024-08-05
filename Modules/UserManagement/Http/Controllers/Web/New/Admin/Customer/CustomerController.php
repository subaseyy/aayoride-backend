<?php

namespace Modules\UserManagement\Http\Controllers\Web\New\Admin\Customer;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Http\Requests\CustomerStoreOrUpdateRequest;
use Modules\UserManagement\Service\Interface\CustomerLevelServiceInterface;
use Modules\UserManagement\Service\Interface\CustomerServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends BaseController
{
    use AuthorizesRequests;

    protected $customerService;
    protected $customerLevelService;
    protected $businessSettingService;
    protected $transactionService;
    protected $trip;

    public function __construct(
        CustomerServiceInterface        $customerService,
        CustomerLevelServiceInterface   $customerLevelService,
        BusinessSettingServiceInterface $businessSettingService,
        TransactionServiceInterface     $transactionService,
        TripRequestInterfaces           $trip,
    )
    {
        parent::__construct($customerService);
        $this->customerService = $customerService;
        $this->customerLevelService = $customerLevelService;
        $this->businessSettingService = $businessSettingService;
        $this->transactionService = $transactionService;
        $this->trip = $trip;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('user_view');
        $customers = $this->customerService
            ->index(criteria: $request?->all(), relations: ["customerTrips", "level"], orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page']??1);
        $levels = $this->customerLevelService->getBy(criteria: ['user_type' => CUSTOMER], orderBy: ['created_at' => 'asc']);
        return view('usermanagement::admin.customer.index', compact('customers', 'levels'));
    }

    public function create(): Renderable
    {
        $this->authorize('user_add');
        return view('usermanagement::admin.customer.create');
    }

    public function store(CustomerStoreOrUpdateRequest $request): RedirectResponse|Renderable
    {
        $this->authorize('user_add');
        $firstLevel = $this->customerLevelService->findOneBy(criteria: ['user_type' => CUSTOMER, 'sequence' => 1]);
        if (!$firstLevel) {
            Toastr::error(LEVEL_403['message']);
            return back();
        }
        $this->customerService->create($request->validated());
        Toastr::success(DRIVER_STORE_200['message']);
        return redirect(route('admin.customer.index'));
    }

    public function show($id, Request $request)
    {
        $this->authorize('user_view');
        $customer = $this->customerService
            ->findOneBy(criteria: ['id' => $id, 'user_type' => CUSTOMER], relations: ['customerTrips']);
        if (!$customer) {
            Toastr::warning(translate("Customer not found"));
            return back();
        }
        $data = $this->customerService->show(id: $id, data: $request->all());
        $commonData = $data['commonData'];
        $otherData = $data['otherData'];
        return view('usermanagement::admin.customer.details', compact('customer', 'commonData', 'otherData'));
    }

    public function edit(string $id): Renderable
    {
        $this->authorize('user_edit');
        $customer = $this->customerService
            ->findOneBy(criteria: ['id' => $id, 'user_type' => CUSTOMER]);
        return view('usermanagement::admin.customer.edit', compact('customer'));
    }

    public function update(CustomerStoreOrUpdateRequest $request, string $id)
    {

        $this->authorize('user_edit');
        $this->customerService->update(id: $id, data: $request->validated());
        Toastr::success(DRIVER_UPDATE_200['message']);
        return back();
    }

    public function destroy(string $id): RedirectResponse|Renderable
    {
        $this->authorize('user_delete');
        $customer = $this->customerService->findOne($id);
        if (count($customer->getCustomerUnpaidParcelAndTrips())>0|| count($customer->getCustomerPendingTrips())>0|| count($customer->getCustomerAcceptedTrips())>0 || count($customer->getCustomerOngingTrips())>0){
            Toastr::error(translate("Sorry you can't delete this customer, because there are ongoing rides or payment due this customer."));
            return back();
        }
        $this->customerService->delete(id: $id);
        Toastr::success(DRIVER_DELETE_200['message']);
        return back();
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->authorize('user_edit');
        $request->validate([
            'status' => 'required'
        ]);
        $customer = $this->customerService->statusChange(id: $request->id, data: $request->all());
        if ($customer?->is_active == 0) {
            foreach ($customer?->tokens as $token) {
                $token->revoke();
            }
        }
        return response()->json($customer);
    }

    public function getAllAjax(Request $request): JsonResponse
    {
        $customers = $this->customerService
            ->index(criteria: $request->all(), limit: paginationLimit(), offset:$request['page']??1,orderBy : ['created_at' => 'desc']);

        $mapped = $customers->map(function ($items) {
            return [
                'text' => $items['first_name'] . ' ' . $items['last_name'] . ' ' . '(' . $items['phone'] . ')',
                'id' => $items['id']
            ];
        });
        if ($request->all_customer) {
            $all_customer = (object)['id' => 'all', 'text' => translate('all_customer')];
            $mapped->prepend($all_customer);
        }

        return response()->json($mapped);
    }

    public function statistics()
    {

        $totalCustomers = $this->customerService->getBy(criteria: ['user_type' => CUSTOMER])->count();
        $inactive = $this->customerService->getBy(criteria: ['user_type' => CUSTOMER, 'is_active' => 0])->count();
        $active = $this->customerService->getBy(criteria: ['user_type' => CUSTOMER, 'is_active' => 1])->count();
        $newCustomers = $this->customerService->getBy(criteria: ['user_type' => CUSTOMER,['created_at', '>=', Carbon::now()->subMonths(6)]])->count();
        return response()->json(view(
            'usermanagement::admin.customer._statistics',
            compact('totalCustomers', 'inactive', 'newCustomers', 'active')
        )->render());
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_export');
        $data = $this->customerService->export($request->all(), relations: ['level'],orderBy : ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'usermanagement::admin.customer.print');
    }

    public function customerTransactionExport($id, Request $request)
    {

        $request->merge([
            'customer_id' => $id
        ]);
        $exportDatas = $this->transactionService->export(criteria: $request->all(),orderBy : ['created_at' => 'desc']);
        return exportData($exportDatas, $request['file'], 'usermanagement::admin.driver.transaction.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_log');
        $request->merge([
            'logable_type' => 'Modules\UserManagement\Entities\User',
            'user_type' => CUSTOMER
        ]);
        return log_viewer($request->all());
    }

    public function trash(Request $request)
    {
        $this->authorize('super-admin');
        $customers = $this->customerService->trashedData(criteria: $request->all(), relations: ['level', 'lastLocations.zone', 'customerTrips', 'customerTripsStatus'], orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page']??1);
        return view('usermanagement::admin.customer.trashed', compact('customers'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->customerService->restoreData(id: $id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.customer.index');
    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->customerService->permanentDelete(id: $id);
        Toastr::success(CUSTOMER_DELETE_200['message']);
        return back();
    }

    public function getLevelWiseCustomer(Request $request)
    {
        if ($request->has('levels')){
            $levels = $request->levels;
        }else{
            return response()->json([]);
        }

        if (in_array(ALL,$levels)){
            $customers= $this->customerService->getBy(criteria: ['user_type' => CUSTOMER]);
        }else{
            $customers= $this->customerService->getBy(criteria: ['user_type' => CUSTOMER],whereInCriteria: ['user_level_id'=>$levels]);
        }
        return response()->json($customers);
    }
}
