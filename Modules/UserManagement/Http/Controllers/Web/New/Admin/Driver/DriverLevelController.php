<?php

namespace Modules\UserManagement\Http\Controllers\Web\New\Admin\Driver;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\UserManagement\Http\Requests\DriverLevelStoreOrUpdateRequest;
use Modules\UserManagement\Http\Requests\DriverLevelStoreUpdateRequest;
use Modules\UserManagement\Service\Interface\DriverLevelServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriverLevelController extends BaseController
{
    protected $driverLevelService;

    public function __construct(DriverLevelServiceInterface $driverLevelService)
    {
        parent::__construct($driverLevelService);
        $this->driverLevelService = $driverLevelService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('user_view');
        $levels = $this->driverLevelService->index(criteria: $request?->all(), relations: ['users.driverTrips', 'users.driverTripsStatus'], orderBy: ['sequence' => 'asc'], limit: paginationLimit(), offset:$request['page']??1, withCountQuery: ['users' => []]);
        return view('usermanagement::admin.driver.level.index', compact('levels'));
    }

    public function create(): Renderable
    {
        $this->authorize('user_add');

        $levels = $this->driverLevelService->getBy(criteria: ['user_type' => DRIVER],withTrashed: true);
        $levelArray = $levels->pluck('sequence')->toArray();
        $sequence_array = range(1, 12);
        $sequences = array_values(array_diff($sequence_array, $levelArray));

        return view('usermanagement::admin.driver.level.create', compact('sequences'));
    }

    public function store(DriverLevelStoreOrUpdateRequest $request): RedirectResponse|Renderable
    {
        $this->authorize('user_add');
        $levels = $this->driverLevelService->getBy(criteria: ['user_type' => DRIVER]);
        if (($levels->isEmpty()) && $request['sequence'] != 1) {
            Toastr::error(LEVEL_CREATE_403['message']);
            return back();
        }
        $this->driverLevelService->create(data: $request->validated());
        Toastr::success(LEVEL_CREATE_200['message']);
        return redirect(route('admin.driver.level.index'));

    }

    public function edit($id): Renderable
    {
        $this->authorize('user_edit');
        $level = $this->driverLevelService
            ->findOne(id: $id);
        return view('usermanagement::admin.driver.level.edit', compact('level'));
    }

    public function update(DriverLevelStoreOrUpdateRequest $request, $id): RedirectResponse|Renderable
    {
        $this->authorize('user_edit');
        $this->driverLevelService->update(id: $id, data: $request->validated());
        Toastr::success(LEVEL_UPDATE_200['message']);
        return back();
    }


    public function destroy($id): RedirectResponse
    {
        $this->authorize('user_delete');
        $level = $this->driverLevelService->findOne(id: $id, withCountQuery: ['users' => []]);
        if ($level?->users_count > 0) {
            Toastr::error(LEVEL_DELETE_403['message']);
            return back();
        }
        $this->driverLevelService->delete(id: $id);
        Toastr::success(LEVEL_DELETE_200['message']);
        return back();
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->authorize('user_edit');
        $driver = $this->driverLevelService->statusChange(id: $request->id, data: $request->all());
        return response()->json($driver);
    }

    public function statistics(Request $request): JsonResponse
    {
        $levels = $this->driverLevelService->getStatistics(data: $request->all());
        return response()->json(view('usermanagement::admin.driver.level._statistics', compact('levels'))->render());
    }


    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_export');
        $data = $this->driverLevelService->export(criteria: $request->all(), relations: ['users.driverTrips', 'users.driverTripsStatus'], orderBy: ['sequence' => 'asc'], withCountQuery: ['users' => []]);
        return exportData($data, $request['file'], 'usermanagement::admin.driver.level.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_log');
        $request->merge([
            'logable_type' => 'Modules\UserManagement\Entities\UserLevel',
            'user_type' => 'driver'
        ]);
        return log_viewer($request->all());
    }

    public function trash(Request $request)
    {
        $this->authorize('super-admin');
        $levels = $this->driverLevelService->trashedData(criteria: $request->all(), limit: paginationLimit(), offset:$request['page']??1);
        return view('usermanagement::admin.driver.level.trashed', compact('levels'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->driverLevelService->restoreData($id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.driver.level.index');

    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->driverLevelService->permanentDelete(id: $id);
        Toastr::success(DRIVER_DELETE_200['message']);
        return back();
    }
}
