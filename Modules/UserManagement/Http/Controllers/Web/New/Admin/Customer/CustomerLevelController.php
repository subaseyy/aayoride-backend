<?php

namespace Modules\UserManagement\Http\Controllers\Web\New\Admin\Customer;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
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
use Modules\UserManagement\Http\Requests\CustomerLevelStoreUpdateRequest;
use Modules\UserManagement\Service\Interface\CustomerLevelServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerLevelController extends BaseController
{
    use AuthorizesRequests;

    protected $customerLevelService;

    public function __construct(CustomerLevelServiceInterface $customerLevelService)
    {
        parent::__construct($customerLevelService);
        $this->customerLevelService = $customerLevelService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('user_view');
        $levels = $this->customerLevelService->index(criteria: $request?->all(), relations: ['users.customerTrips'], orderBy: ['sequence' => 'asc'], limit: paginationLimit(),offset:$request['page']??1, withCountQuery: ['users' => []]);
        return view('usermanagement::admin.customer.level.index', compact('levels'));
    }

    public function create(): Renderable
    {
        $this->authorize('user_add');

        $levels = $this->customerLevelService->getBy(criteria: ['user_type' => CUSTOMER],withTrashed: true);
        $levelArray = $levels->pluck('sequence')->toArray();
        $sequence_array = range(1, 12);
        $sequences = array_values(array_diff($sequence_array, $levelArray));

        return view('usermanagement::admin.customer.level.create', compact('sequences'));
    }

    public function store(CustomerLevelStoreUpdateRequest $request): RedirectResponse|Renderable
    {
        $this->authorize('user_add');
        $levels = $this->customerLevelService->getBy(criteria: ['user_type' => CUSTOMER]);
        if (($levels->isEmpty()) && $request['sequence'] != 1) {
            Toastr::error(LEVEL_CREATE_403['message']);
            return back();
        }
        $this->customerLevelService->create(data: $request->validated());
        Toastr::success(LEVEL_CREATE_200['message']);
        return redirect(route('admin.customer.level.index'));

    }

    public function edit($id): Renderable
    {
        $this->authorize('user_edit');
        $level = $this->customerLevelService->findOne(id: $id);
        return view('usermanagement::admin.customer.level.edit', compact('level'));
    }

    public function update(CustomerLevelStoreUpdateRequest $request, $id): RedirectResponse|Renderable
    {
        $this->authorize('user_edit');
        $this->customerLevelService->update(id: $id, data: $request->validated());
        Toastr::success(LEVEL_UPDATE_200['message']);
        return back();
    }

    public function destroy($id): Renderable|RedirectResponse
    {
        $this->authorize('user_delete');
        $level = $this->customerLevelService->findOne(id: $id, withCountQuery: ['users' => []]);
        if ($level->users_count > 0) {
            Toastr::error(LEVEL_DELETE_403['message']);
            return back();
        }
        $this->customerLevelService->delete(id: $id);
        Toastr::success(LEVEL_DELETE_200['message']);
        return back();
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $this->authorize('user_edit');
        $driver = $this->customerLevelService->statusChange(id: $request->id, data: $request->all());
        return response()->json($driver);
    }


    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_export');
        $data = $this->customerLevelService->export(criteria: $request->all(), relations: ['users.customerTrips'], orderBy: ['sequence' => 'asc'], withCountQuery: ['users' => []]);
        return exportData($data, $request['file'], 'usermanagement::admin.customer.level.print');
    }


    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('user_log');
        $request->merge([
            'logable_type' => 'Modules\UserManagement\Entities\UserLevel',
            'user_type' => 'customer'
        ]);
        return log_viewer($request->all());
    }

    public function statistics(Request $request)
    {
        $levels = $this->customerLevelService->getStatistics($request->all());
        return response()->json(view('usermanagement::admin.customer.level._statistics', compact('levels'))->render());
    }

    public function trash(Request $request)
    {
        $this->authorize('super-admin');
        $levels = $this->customerLevelService->trashedData(criteria: $request->all(),limit: paginationLimit(), offset:$request['page']??1);
        return view('usermanagement::admin.customer.level.trashed', compact('levels'));
    }

    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->customerLevelService->restoreData(id: $id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.customer.level.index');
    }

    public function permanentDelete($id)
    {
        $this->authorize('super-admin');
        $this->customerLevelService->permanentDelete(id: $id);
        Toastr::success(CUSTOMER_DELETE_200['message']);
        return back();
    }
}
