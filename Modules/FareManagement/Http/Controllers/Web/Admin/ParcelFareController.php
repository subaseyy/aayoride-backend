<?php

namespace Modules\FareManagement\Http\Controllers\Web\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\FareManagement\Entities\ParcelFare;
use Modules\FareManagement\Entities\ParcelFareWeight;
use Modules\FareManagement\Interfaces\ParcelFareInterface;
use Modules\FareManagement\Interfaces\TripFareInterface;
use Modules\ParcelManagement\Interfaces\ParcelCategoryInterface;
use Modules\ParcelManagement\Interfaces\ParcelWeightInterface;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;
use Modules\ZoneManagement\Interfaces\ZoneInterface;

class ParcelFareController extends Controller
{

    public function __construct(
        private ZoneInterface           $zone,
        private ParcelCategoryInterface $parcelCategory,
        private ParcelWeightInterface   $parcelWeight,
        private ParcelFareInterface     $fare,
    )
    {
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(): Renderable
    {
        $parcelCategory = $this->parcelCategory->get(limit: 1000, offset: 1, attributes: [
            'query' => 'is_active',
            'value' => 'active'
        ]);
        $zones = $this->zone->get(limit: 1000, offset: 1, attributes: [
            'withCount' => ['customers', 'drivers'],
            'query' => 'is_active',
            'value' => 'active'
        ]);
        $fares = $this->fare->get(limit: 1000, offset: 1, relations: ['fares']);

        return view('faremanagement::admin.parcel.index', compact('parcelCategory', 'zones', 'fares'));
    }

    /**
     * Show the form for creating a new resource.
     * @param $zone_id
     * @return Renderable|RedirectResponse
     */
    public function create($zone_id): Renderable|RedirectResponse
    {
        $zone = $this->zone->getBy(column: 'id', value: $zone_id);
        if (!$zone) {

            Toastr::error(ZONE_404['message']);
            return redirect()->back();
        }
        $parcelCategory = $this->parcelCategory->get(limit: 1000, offset: 1);
        $parcelWeight = $this->parcelWeight->get(limit: 1000, offset: 1);
        if ($parcelWeight->count() < 1) {

            Toastr::error(PARCEL_WEIGHT_404['message']);
            return back();
        }
        $fares = $this->fare->getBy(column: 'zone_id', value: $zone_id);

        return view('faremanagement::admin.parcel.create',
            compact('zone', 'parcelCategory', 'parcelWeight', 'fares'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse|Renderable
     */
    public function store(Request $request): RedirectResponse|Renderable
    {
        $request->validate([
            'parcel_category' => 'required|array',
            'base_fare' => 'required|gt:0',
        ], [
            'parcel_category.required' => 'Must select at least one parcel category'
        ]);

        $parcelWeight = $this->parcelWeight->get(limit: 1000, offset: 1);
        $request->merge(['parcel_weight' => $parcelWeight]);
        $this->fare->store(attributes: $request->all());

        Toastr::success(PARCEL_FARE_STORE_200['message']);
        return back();
    }

}
