<?php

namespace Modules\FareManagement\Http\Controllers\Web\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Modules\ZoneManagement\Interfaces\ZoneInterface;
use Modules\FareManagement\Interfaces\TripFareInterface;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;

class TripFareController extends Controller
{


    public function __construct(
        protected VehicleCategoryInterface $vehicleCategories,
        protected ZoneInterface            $zone,
        protected TripFareInterface        $tripFare
    )
    {}

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(): Renderable
    {
        $vehicleCategories = $this->vehicleCategories->get(limit: 1000, offset: 1, attributes: [
                'query' => 'is_active',
                'value' => 'active'
            ]);
        $zones = $this->zone->get(limit: 1000, offset: 1, attributes: [
                'withCount' => ['customers', 'drivers'],
                'query' => 'is_active',
                'value' => 'active'
            ]);
        $fares = $this->tripFare->get(limit: 1000, offset: 1);

        return view('faremanagement::admin.trip.index', compact('vehicleCategories', 'zones', 'fares'));
    }

    /**
     * Show the form for creating a new resource.
     * @param $zone_id
     * @return Renderable|RedirectResponse
     */
    public function create($zone_id): Renderable|RedirectResponse
    {
        $zone = $this->zone->getBy(column: 'id', value: $zone_id, attributes: ['relations' => ['defaultFare','defaultFare.tripFares']]);
        if ( ! $zone ) {

            Toastr::error(ZONE_404['message']);
            return redirect()->back();
        }
        $vehicleCategories = $this->vehicleCategories->get(limit: 1000, offset: 1, attributes: [
            'query' => 'is_active',
            'value' => 'active',
        ]);
        if ( $vehicleCategories->count() < 1) {

            Toastr::warning(VEHICLE_CATEGORY_404['message']);
            return back();
        }

        $defaultTripFare = $zone?->defaultFare;
        $tripFares = $zone?->defaultFare?->tripFares;
        return view('faremanagement::admin.trip.create', compact('vehicleCategories', 'zone', 'tripFares','defaultTripFare'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable|RedirectResponse
     */
    public function store(Request $request): Renderable|RedirectResponse
    {
        $request->validate([
            'base_fare' => 'nullable|gt:0',
            'base_fare_per_km' => 'nullable|gt:0',
            'waiting_fee' => 'nullable|gte:0',
            'cancellation_fee' => 'nullable|gte:0',
            'min_cancellation_fee' => 'nullable|gte:0',
            'idle_fee' => 'nullable|gte:0',
            'trip_delay_fee' => 'nullable|gte:0',
            'penalty_fee_for_cancel' => 'nullable|gte:0',
            'fee_add_to_next' => 'nullable|gte:0',
            'minimum_pickup_distance' => 'nullable|gte:0',
            'pickup_bonus_amount' => 'nullable|gte:0',
        ]);

        $hasDynamicField = collect($request->all())->keys()->contains(fn($fieldName) => Str::startsWith($fieldName, 'vehicle_category_'));

        if (!$hasDynamicField) {

            Toastr::error('Please select vehicle category');
            return back();
        }
        $vehicleCategories = $this->vehicleCategories->get(limit: 1000, offset: 1);
        $request->merge(['vehicleCategories' => $vehicleCategories]);

        $this->tripFare->store($request->all());

        Toastr::success(TRIP_FARE_STORE_200['message']);
        return back();
    }

}
