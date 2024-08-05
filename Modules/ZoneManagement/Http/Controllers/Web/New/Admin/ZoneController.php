<?php

namespace Modules\ZoneManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Auth\Access\AuthorizationException;
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
use Modules\TripManagement\Entities\TripRequest;
use Modules\ZoneManagement\Http\Requests\ZoneStoreUpdateRequest;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ZoneController extends BaseController
{
    use AuthorizesRequests;

    protected $zoneService;
    public function __construct(ZoneServiceInterface $zoneService)
    {
        parent::__construct($zoneService);
        $this->zoneService = $zoneService;
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View|Collection|LengthAwarePaginator|callable|RedirectResponse|null
     * @throws AuthorizationException
     */
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('zone_view');
        $zones = $this->zoneService->index(criteria: $request?->all(), relations: ['customers'], orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page']??1);
        $tripsCount = TripRequest::query()->count();

        return view('zonemanagement::admin.zone.index', compact('zones','tripsCount'));
    }

    public function store(ZoneStoreUpdateRequest $request): RedirectResponse
    {
        $this->authorize('zone_add');
        try {
            $this->zoneService->create($request->validated());
        } catch (\Exception $e) {
            Toastr::error(DEFAULT_400['message']);
            return back();
        }
        Toastr::success(ZONE_STORE_200['message']);
        Toastr::warning(ZONE_STORE_INSTRUCTION_200['message']);

        return back();
    }

    public function edit(string $id): Renderable|RedirectResponse
    {
        $this->authorize('zone_edit');

        $zone = $this->zoneService->findOne($id);
        if (isset($zone)) {
            $area = json_decode($zone->coordinates[0]->toJson(),true);
            $current_zone = formatCoordinates(json_decode($zone->coordinates[0]->toJson(),true));
            $centerLat = trim(explode(' ', $zone->coordinates)[1], 'POINT()');
            $centerLng = trim(explode(' ', $zone->coordinates)[0], 'POINT()');
            return view('zonemanagement::admin.zone.edit', compact('zone', 'current_zone', 'centerLat', 'centerLng','area'));
        }

        Toastr::error(DEFAULT_204['message']);
        return back();
    }

    public function update(ZoneStoreUpdateRequest $request, string $id): RedirectResponse
    {
        $this->authorize('zone_edit');
        $this->zoneService->update(id: $id, data: $request->validated());
        Toastr::success(ZONE_UPDATE_200['message']);
        return back();
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->authorize('zone_delete');
        $this->zoneService->delete(id: $id);
        Toastr::success(ZONE_DESTROY_200['message']);
        return back();
    }

    public function status(Request $request): JsonResponse
    {
        $this->authorize('zone_edit');
        $model = $this->zoneService->statusChange(id: $request->id, data: $request->toArray());
        return response()->json($model);
    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $zones = $this->zoneService->trashedData(criteria: $request->all(),relations: ['customers'], orderBy: ['created_at'=>'desc'], limit: paginationLimit());
        $tripsCount = TripRequest::query()->count();
        return view('zonemanagement::admin.zone.trashed', compact('zones','tripsCount'));

    }

    public function restore(string|int $id): RedirectResponse
    {
        $this->authorize('super-admin');
        $this->zoneService->restoreData($id);
        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.zone.index');

    }

    public function permanentDelete(string|int $id): RedirectResponse
    {
        $this->authorize('zone_delete');
        $this->zoneService->permanentDelete(id: $id);
        Toastr::success(ZONE_DESTROY_200['message']);
        return back();
    }

    public function getZones(Request $request): JsonResponse
    {
        $all_zone_data = $this->zoneService->getZones(criteria: $request->all());
        return response()->json($all_zone_data, 200);
    }

    public function getCoordinates(string $id): JsonResponse
    {
        $zone = $this->zoneService->findOne(id: $id);
        $data = formatCoordinates($zone['coordinates'][0]);
        $center = (object)['lat' => (float)trim(explode(' ', $zone['center'])[1], 'POINT()'), 'lng' => (float)trim(explode(' ', $zone['center'])[0], 'POINT()')];
        return response()->json(['coordinates' => $data, 'center' => $center]);
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('zone_export');
        $data = $this->zoneService->export(criteria: $request->all(), relations: ['tripRequest'], orderBy: ['created_at' => 'desc']);
        return exportData($data, $request['file'], 'zonemanagement::admin.zone.print');
    }

    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('zone_log');

        $request->merge([
            'logable_type' => 'Modules\ZoneManagement\Entities\Zone',
        ]);
        return log_viewer($request->all());
    }

}
