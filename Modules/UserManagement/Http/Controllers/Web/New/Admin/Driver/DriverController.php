<?php

namespace Modules\UserManagement\Http\Controllers\Web\New\Admin\Driver;

use App\Http\Controllers\BaseController;
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
use Illuminate\View\View;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\UserManagement\Entities\AppNotification;
use Modules\UserManagement\Http\Requests\DriverStoreOrUpdateRequest;
use Modules\UserManagement\Interfaces\DriverDetailsInterface;
use Modules\UserManagement\Service\Interface\AppNotificationServiceInterface;
use Modules\UserManagement\Service\Interface\DriverLevelServiceInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriverController extends BaseController
{
    use AuthorizesRequests;

    protected $driverService;
    protected $driverLevelService;
    protected $appNotificationService;
    protected $transactionService;
    protected $trip;

    public function __construct(
        DriverServiceInterface          $driverService,
        DriverLevelServiceInterface     $driverLevelService,
        AppNotificationServiceInterface $appNotificationService,
        TransactionServiceInterface     $transactionService,
        TripRequestInterfaces           $trip,
    )
    {
        parent::__construct($driverService);
        $this->driverService = $driverService;
        $this->driverLevelService = $driverLevelService;
        $this->appNotificationService = $appNotificationService;
        $this->transactionService = $transactionService;
        $this->trip = $trip;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('user_view');
        $drivers = $this->driverService->index(criteria: $request?->all(), relations: ['level', 'driverTrips', 'driverTripsStatus', 'lastLocations.zone'], orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page'] ?? 1);
        return view('usermanagement::admin.driver.index', compact('drivers'));
    }

    public function create(): Renderable
    {
        $this->authorize('user_add');
        return view('usermanagement::admin.driver.create');
    }

    public function store(DriverStoreOrUpdateRequest $request): RedirectResponse
    {
        $this->authorize('user_add');
        $firstLevel = $this->driverLevelService->findOneBy(criteria: ['user_type' => DRIVER, 'sequence' => 1]);
        if (!$firstLevel) {
            Toastr::error(LEVEL_403['message']);
            return back();
        }
        $request->merge([
            'user_level_id' => $firstLevel->id
        ]);
        $this->driverService->create(data: $request->validated());
        Toastr::success(DRIVER_STORE_200['message']);
        return redirect(route('admin.driver.index'));

    }

    public function show($id, Request $request): Renderable|RedirectResponse
    {
        $this->authorize('user_view');
        $driver = $this->driverService->findOne(id: $id, relations: ['userAccount', 'receivedReviews', 'driverTrips', 'driverDetails', 'driverTrips']);
        if (!$driver) {
            Toastr::warning(translate("Driver not found"));
            return back();
        }
        $data = $this->driverService->show(id: $id, data: $request->all());
        $commonData = $data['commonData'];
        $otherData = $data['otherData'];

        return view('usermanagement::admin.driver.details', compact('driver', 'commonData', 'otherData'));

    }

    public function edit($id): Renderable
    {
        $this->authorize('user_edit');
        $driver = $this->driverService
            ->findOneBy(criteria: ['id' => $id, 'user_type' => DRIVER]);
        return view('usermanagement::admin.driver.edit', compact('driver'));
    }

    public function update(DriverStoreOrUpdateRequest $request, $id): RedirectResponse
    {
        $this->authorize('user_edit');
        $data = array_merge($request->validated(), ['type' => 'web']);
        $this->driverService->update(id: $id, data: $data);
        Toastr::success(DRIVER_UPDATE_200['message']);
        return back();
    }


    public function destroy($id): RedirectResponse
    {
        $this->authorize('user_delete');
        $driver = $this->driverService->findOne($id);
        if(count($driver->getDriverLastTrip())!=0|| $driver?->userAccount->payable_balance>0 || $driver?->userAccount->pending_balance>0 || $driver?->userAccount->receivable_balance>0){
            Toastr::success(translate("Sorry you can't delete this driver, because there are ongoing rides or payment due this driver."));
            return back();
        }
        $this->driverService->delete(id: $id);
        Toastr::success(DRIVER_DELETE_200['message']);
        return back();
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->authorize('user_edit');
        $driver = $this->driverService->statusChange(id: $request->id, data: $request->all());
        $driverNotification = $this->appNotificationService->getBy(criteria: ['user_id' => $request->id, 'action' => 'account_approved']);
        if (count($driverNotification) == 0) {
            $push = getNotification('registration_approved');
            if ($request->status && $driver?->fcm_token) {
                sendDeviceNotification(
                    fcm_token: $driver?->fcm_token,
                    title: translate($push['title']),
                    description: translate($push['description']),
                    action: 'account_approved',
                    user_id: $driver?->id
                );
            }
        }
        if ($driver?->is_active == 0) {
            foreach ($driver?->tokens as $token) {
                $token->revoke();
            }
        }
        return response()->json($driver);
    }

    public function getAllAjax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'sometimes'
        ]);
        $drivers = $this->driverService->getDriverWithoutVehicle(criteria:$request->all(),limit: 100, offset:$request['page']??1);
        $mapped = $drivers->map(function ($items) {
            return [
                'text' => $items['first_name'] . ' ' . $items['last_name'] . ' ' . '(' . $items['phone'] . ')',
                'id' => $items['id']
            ];
        });
        if ($request->all_driver) {
            $all_driver = (object)['id' => 0, 'text' => translate('all_driver')];
            $mapped->prepend($all_driver);
        }

        return response()->json($mapped);
    }

    public function getAllAjaxVehicle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => 'sometimes'
        ]);

        $drivers = $this->driverService->getDriverWithoutVehicle(criteria:$request->all(),limit: 100, offset:$request['page']??1);
        $mapped = $drivers->map(function ($items) {
            return [
                'text' => $items['first_name'] . ' ' . $items['last_name'] . ' ' . '(' . $items['phone'] . ')',
                'id' => $items['id']
            ];
        });
        if ($request->all_driver) {
            $all_driver = (object)['id' => 0, 'text' => translate('all_driver')];
            $mapped->prepend($all_driver);
        }

        return response()->json($mapped);
    }

    public function statistics(Request $request)
    {
        $analytics = $this->driverService->getStatisticsData($request->all());
        $total = $analytics['total'];
        $active = $analytics['active'];
        $inactive = $analytics['inactive'];
        $car = $analytics['car'];
        $motor_bike = $analytics['motor_bike'];
        return response()->json(view('usermanagement::admin.driver._statistics',
            compact('total', 'active', 'inactive', 'car', 'motor_bike'))->render());
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_export');
        $attributes = [
            'relations' => ['level'],
            // 'query' => $request['query'],
            // 'value' => $request['value'],
        ];

        !is_null($request['search']) ? $attributes['search'] = $request['search'] : '';
        !is_null($request['query']) ? $attributes['query'] = $request['query'] : '';
        !is_null($request['value']) ? $attributes['value'] = $request['value'] : '';

        $request->merge(['relations' => ['level', 'driverTrips', 'driverTripsStatus', 'lastLocations.zone']]);

        $data = $this->driverService->export(criteria: $request->all(), relations: ['level', 'driverTrips', 'driverTripsStatus', 'lastLocations.zone'], orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'usermanagement::admin.driver.print');
    }

    public function driverTransactionExport(Request $request)
    {
        $request->merge([
            'driver_id' => $request['id']
        ]);
        $exportData = $this->transactionService->export(criteria: $request->all(), orderBy: ['created_at' => 'desc']);
        return exportData($exportData, $request['file'], 'usermanagement::admin.driver.transaction.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_log');
        $request->merge([
            'logable_type' => 'Modules\UserManagement\Entities\User',
            'user_type' => 'customer'
        ]);
        return log_viewer($request->all());
    }

    public function trash(Request $request)
    {
        $this->authorize('super-admin');
        $drivers = $this->driverService->trashedData(criteria: $request->all(), relations: ['level', 'lastLocations.zone', 'driverTrips', 'driverTripsStatus'], limit: paginationLimit(), offset:$request['page']??1);
        return view('usermanagement::admin.driver.trashed', compact('drivers'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->driverService->restoreData(id: $id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.driver.index');
    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->driverService->permanentDelete(id: $id);
        Toastr::success(DRIVER_DELETE_200['message']);
        return back();
    }

    //identity image change
    public function profileUpdateRequestList(Request $request): Renderable
    {
        $this->authorize('user_edit');
        $request->merge(['pending' => true]);
        $drivers = $this->driverService->index(criteria: $request?->all(), relations: ['level', 'driverTrips', 'driverTripsStatus', 'lastLocations.zone'], orderBy : ['created_at' => 'desc'], limit: paginationLimit(), offset:$request['page'] ?? 1);
        return view('usermanagement::admin.driver.profile-update-request', compact('drivers'));
    }

    public function profileUpdateRequestListExport(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_edit');
        $request->merge(['pending' => true]);

        $attributes = [
            'relations' => ['level'],
        ];

        !is_null($request['search']) ? $attributes['search'] = $request['search'] : '';
        !is_null($request['query']) ? $attributes['query'] = $request['query'] : '';
        !is_null($request['value']) ? $attributes['value'] = $request['value'] : '';

        $request->merge(['relations' => ['level', 'driverTrips', 'driverTripsStatus', 'lastLocations.zone']]);

        $data = $this->driverService->export(criteria: $request->all(), relations: ['level', 'driverTrips', 'driverTripsStatus', 'lastLocations.zone'],orderBy : ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'usermanagement::admin.driver.print');
    }

    public function profileUpdateRequestApprovedOrRejected($id,Request $request)
    {
        $this->authorize('user_edit');
        $this->driverService->updateIdentityImage(id: $id,data: $request->all());
        $driver = $this->driverService->findOne(id: $id);
        if ($request->status=='approved'){
            $push = getNotification('identity_image_approved');
            sendDeviceNotification(
                fcm_token: $driver?->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                action: 'identity_image_approved',
                user_id: $driver?->id
            );
            Toastr::success(translate('driver_identity_image_approved_successfully'));
        }else{
            $push = getNotification('identity_image_rejected');
            sendDeviceNotification(
                fcm_token: $driver?->fcm_token,
                title: translate($push['title']),
                description: translate($push['description']),
                action: 'identity_image_rejected',
                user_id: $driver?->id
            );
            Toastr::success(translate('driver_identity_image_rejected_successfully'));
        }
        return redirect()->back();
    }
}
