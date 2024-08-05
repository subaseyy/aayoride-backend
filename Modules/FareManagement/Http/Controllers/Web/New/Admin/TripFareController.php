<?php

namespace Modules\FareManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\FareManagement\Http\Requests\TripFareStoreOrUpdateRequest;
use Modules\FareManagement\Service\Interface\TripFareServiceInterface;
use Modules\VehicleManagement\Service\Interface\VehicleCategoryServiceInterface;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;

class TripFareController extends BaseController
{
    protected $vehicleCategoryService;
    protected $tripFareService;
    protected $zoneService;

    public function __construct(VehicleCategoryServiceInterface     $vehicleCategoryService, TripFareServiceInterface $tripFareService,
                                 ZoneServiceInterface $zoneService)
    {
        parent::__construct($vehicleCategoryService);
        $this->vehicleCategoryService = $vehicleCategoryService;
        $this->tripFareService = $tripFareService;
        $this->zoneService = $zoneService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $vehicleCategoryCriteria = [
            'is_active' => 1,
        ];
        $vehicleCategories = $this->vehicleCategoryService->getBy(criteria: $vehicleCategoryCriteria);
        $zoneCriteria = [
            'is_active' => 1
        ];
        $withCountCriteria = [
            'drivers'=>[]
        ];
        $zones = $this->zoneService->getBy(criteria: $zoneCriteria, withCountQuery: $withCountCriteria);
        $fares = $this->tripFareService->getAll();

        return view('faremanagement::admin.trip.index', compact('vehicleCategories', 'zones', 'fares'));
    }

    public function create($zone_id): Renderable|RedirectResponse
    {
        $zone = $this->zoneService->findOne(id: $zone_id, relations: ['defaultFare','defaultFare.tripFares']);
        if ( is_null($zone) ) {
            Toastr::error(ZONE_404['message']);
            return redirect()->back();
        }
        $vehicleCategoryCriteria = [
            'is_active' => 1,
        ];
        $vehicleCategories = $this->vehicleCategoryService->getBy(criteria: $vehicleCategoryCriteria);
        if ( $vehicleCategories->count() < 1) {

            Toastr::warning(VEHICLE_CATEGORY_404['message']);
            return back();
        }
        $defaultTripFare = $zone?->defaultFare;
        $tripFares = $zone?->defaultFare?->tripFares;
        return view('faremanagement::admin.trip.create', compact('vehicleCategories', 'zone', 'tripFares','defaultTripFare'));
    }

    public function store(TripFareStoreOrUpdateRequest $request): Renderable|RedirectResponse
    {
        $hasDynamicField = collect($request->all())->keys()->contains(fn($fieldName) => Str::startsWith($fieldName, 'vehicle_category_'));

        if (!$hasDynamicField) {

            Toastr::error('Please select vehicle category');
            return back();
        }
        $vehicleCategories = $this->vehicleCategoryService->getAll();
        $request->merge(['vehicleCategories' => $vehicleCategories]);

        $this->tripFareService->create($request->all());

        Toastr::success(TRIP_FARE_STORE_200['message']);
        return back();
    }
}
