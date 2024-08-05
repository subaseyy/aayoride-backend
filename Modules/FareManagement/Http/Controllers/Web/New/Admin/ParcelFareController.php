<?php

namespace Modules\FareManagement\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Service\BaseServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\FareManagement\Http\Requests\ParcelFareStoreOrUpdateRequest;
use Modules\FareManagement\Service\Interface\ParcelFareServiceInterface;
use Modules\ParcelManagement\Service\Interface\ParcelCategoryServiceInterface;
use Modules\ParcelManagement\Service\Interface\ParcelWeightServiceInterface;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;

class ParcelFareController extends BaseController
{
    protected $parcelFareService;
    protected $parcelWeightService;
    protected $parcelCategoryService;
    protected $zoneService;

    public function __construct(ParcelFareServiceInterface     $parcelFareService, ParcelWeightServiceInterface $parcelWeightService,
                                ParcelCategoryServiceInterface $parcelCategoryService, ZoneServiceInterface $zoneService)
    {
        parent::__construct($parcelFareService);
        $this->parcelFareService = $parcelFareService;
        $this->parcelWeightService = $parcelWeightService;
        $this->parcelCategoryService = $parcelCategoryService;
        $this->zoneService = $zoneService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $parcelCategoryCriteria = [
            'is_active' => 1
        ];
        $parcelCategory = $this->parcelCategoryService->getBy(criteria: $parcelCategoryCriteria);
        $zoneCriteria = [
            'is_active' => 1
        ];
        $withCountCriteria = [
            'drivers'=>[]
        ];
        $zones = $this->zoneService->getBy(criteria: $zoneCriteria, withCountQuery: $withCountCriteria);
        $fares = $this->parcelFareService->getAll(relations: ['fares']);

        return view('faremanagement::admin.parcel.index', compact('parcelCategory', 'zones', 'fares'));
    }

    /**
     * Show the form for creating a new resource.
     * @param $zone_id
     * @return Renderable|RedirectResponse
     */
    public function create($zone_id): Renderable|RedirectResponse
    {
        $zone = $this->zoneService->findOne(id: $zone_id);
        if (!$zone) {
            Toastr::error(ZONE_404['message']);
            return redirect()->back();
        }
        $parcelCategory = $this->parcelCategoryService->getAll();
        $parcelWeight = $this->parcelWeightService->getAll();
        if ($parcelWeight->count() < 1) {

            Toastr::error(PARCEL_WEIGHT_404['message']);
            return back();
        }
        $fares = $this->parcelFareService->findOneBy(criteria: [ 'zone_id'=> $zone_id]);

        return view('faremanagement::admin.parcel.create',
            compact('zone', 'parcelCategory', 'parcelWeight', 'fares'));
    }


    public function store(ParcelFareStoreOrUpdateRequest $request): RedirectResponse|Renderable
    {
        $parcelWeight = $this->parcelWeightService->getAll();
        $request->merge(['parcel_weight' => $parcelWeight]);
        $this->parcelFareService->create(data: $request->all());

        Toastr::success(PARCEL_FARE_STORE_200['message']);
        return back();
    }

}
