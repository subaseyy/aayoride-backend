<?php

namespace Modules\TripManagement\Http\Controllers\Web\New;

use App\Http\Controllers\BaseController;
use Carbon\Factory;
use Illuminate\Console\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Facade\FlareClient\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TripController extends BaseController
{
    use AuthorizesRequests;

    protected $tripRequestservice;
    public function __construct(TripRequestServiceInterface $tripRequestservice)
    {
        parent::__construct($tripRequestservice);
        $this->tripRequestservice = $tripRequestservice;
    }
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('trip_view');

        $attributes = [];
        $search = null;
        $date = null;
        if ($request->has('data')) {
            $date = getDateRange($request->data);
            $attributes['from'] = $date['start'];
            $attributes['to'] = $date['end'];
        }
        if($type != 'all'){
            $attributes['current_status'] = $type;
        }
        $request->has('search') ? ($search = $attributes['search'] = $request->search) : null;
        $trips = $this->tripRequestservice->index(limit: paginationLimit(), offset: $request['page']??1, criteria: $attributes, relations: ['tripStatus', 'customer', 'driver', 'fee']);


        $trip_counts = null;
        if ($type == 'all') {
            $trip_counts = $this->tripRequestservice->statusWiseTotalTripRecords(['from' => $date['start'] ?? null, 'to' => $date['end'] ?? null]);
        }
        if ($request->ajax()) {
            return response()->json(view('tripmanagement::admin.trip.partials._trip-list-stat', compact('trip_counts', 'type'))->render());
        }
        return view('tripmanagement::admin.trip.index', compact('trips', 'type', 'trip_counts', 'search'));
    }


    public function show($id, Request $request) : Application|Factory|View|RedirectResponse
    {
        $this->authorize('trip_view');

        $trip = $this->tripRequestservice->findOne(id: $id,relations: ['vehicleCategory','vehicle'], withTrashed: true);
        if (!$trip) {
            Toastr::error(TRIP_REQUEST_404['message']);
            return back();
        }
        if ($request['page'] == 'log') {

            return view('tripmanagement::admin.trip.log', compact('trip'));
        }

        return view('tripmanagement::admin.trip.details', compact('trip',
        ));
    }

    public function invoice($id)
    {
        $this->authorize('trip_view');

        $trip = $this->tripRequestservice->findOne(id: $id);

        return view('tripmanagement::admin.trip.invoice', compact('trip'));
    }

    public function export(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('trip_export');

        $data = $this->tripRequestservice->export(criteria: $request->all(), relations: ['tripStatus', 'customer', 'driver', 'fee'], orderBy: ['created_at' => 'desc']);

        return exportData($data, $request['file'], 'tripmanagement::admin.trip.print');
    }


    public function log(Request $request): View|Factory|Response|StreamedResponse|string|Application
    {
        $this->authorize('trip_log');

        $request->merge([
            'logable_type' => 'Modules\TripManagement\Entities\TripRequest',
        ]);
        return log_viewer($request->all());

    }

    public function trashed(Request $request): View
    {
        $this->authorize('super-admin');
        $search = $request->has('search') ? $request->search : null;

        // $trips = $this->trip->trashed();

        $trips = $this->tripRequestservice->trashedData(criteria: ['search' => $search],limit: paginationLimit());
        return view('tripmanagement::admin.trip.trashed', compact('trips', 'search'));

    }

    public function destroy($id)
    {
        $this->authorize('trip_delete');

        $this->tripRequestservice->delete($id);

        Toastr::success(TRIP_REQUEST_DELETE_200['message']);
        return back();
    }


    public function restore($id): RedirectResponse
    {
        $this->authorize('super-admin');

        $this->tripRequestservice->restoreData($id);

        Toastr::success(DEFAULT_RESTORE_200['message']);
        return redirect()->route('admin.trip.index', ['all']);

    }
}
