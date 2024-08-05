<?php

namespace Modules\UserManagement\Http\Controllers\Web\New\Admin\Driver;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Traits\PdfGenerator;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\UserManagement\Http\Requests\WithdrawRequestActionRequest;
use Modules\UserManagement\Http\Requests\WithdrawRequestMultipleActionRequest;
use Modules\UserManagement\Service\Interface\WithdrawMethodServiceInterface;
use Modules\UserManagement\Service\Interface\WithdrawRequestServiceInterface;

class WithdrawRequestController extends BaseController
{
    use AuthorizesRequests;
    use PdfGenerator;

    protected $withdrawMethodService;
    protected $withdrawRequestService;

    public function __construct(WithdrawMethodServiceInterface $withdrawMethodService, WithdrawRequestServiceInterface $withdrawRequestService)
    {
        parent::__construct($withdrawMethodService);
        $this->withdrawMethodService = $withdrawMethodService;
        $this->withdrawRequestService = $withdrawRequestService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('user_view');
        $value = $request->status ?? 'all';
        $criteria = [];
        if ($value == SETTLED) {
            $criteria['status'] = SETTLED;
        } elseif ($value == DENIED) {
            $criteria['status'] = DENIED;
        } elseif ($value == APPROVED) {
            $criteria['status'] = APPROVED;
        } elseif ($value == PENDING) {
            $criteria['status'] = PENDING;
        }

        if ($request?->has('method')) {
            if ($request?->get('method') != ALL) {
                $method = $this->withdrawMethodService->findOneBy(criteria: ['method_name' => $request?->get('method')]);
                $criteria['method_id'] = $method->id;
            }
        }

        $searchCriteria = [];
        if ($request->has('search')) {
            $searchCriteria = [
                'relations' => [
                    'user' => ['full_name', 'first_name', 'last_name'],
                ],
                'value' => $request->search,
            ];
        }
        $requests = $this->withdrawRequestService->getBy(criteria: $criteria, searchCriteria: $searchCriteria, orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset: $request['page'] ?? 1);
        $withdrawMethods = $this->withdrawMethodService->getBy(criteria: ['is_active' => 1]);
        return view('usermanagement::admin.driver.withdraw-request.index', compact('requests', 'withdrawMethods'));
    }

    public function requestDetails($id)
    {
        $this->authorize('user_view');
        $request = $this->withdrawRequestService->findOne(id: $id, relations: ['user' => []]);
        return view('usermanagement::admin.driver.withdraw.details', compact('request'));
    }

    public function action(string|int $id, WithdrawRequestActionRequest $request)
    {
        $this->authorize('user_edit');
        $withdrawRequest = $this->withdrawRequestService->findOne(id: $id, relations: ['user' => []]);
        if ($withdrawRequest) {
            if ($withdrawRequest->method == null || $withdrawRequest->method == null) {
                Toastr::error(translate("Withdraw method or driver not found"));
                return redirect()->back();
            }
            $this->withdrawRequestService->update(id: $id, data: $request->all());
            Toastr::success(translate('Withdraw request updated successfully.'));
            return redirect(route('admin.driver.withdraw.requests'));
        }
        Toastr::error(translate("Withdraw request not found"));
        return redirect()->back();
    }

    public function multipleAction(WithdrawRequestMultipleActionRequest $request)
    {
        $this->authorize('user_edit');
        $data = $request->all();
        if (array_key_exists('status', $data) && !is_null($data['status']) && array_key_exists('ids', $data) && (count($data['ids']) > 0)) {
            if ($data['status'] == 'invoice') {
                $whereInCriteria = [
                    'id' => $data['ids']
                ];
                $data = $this->withdrawRequestService->getBy(whereInCriteria: $whereInCriteria, orderBy: ['created_at' => 'desc']);

                $mpdf_view = \Illuminate\Support\Facades\View::make('usermanagement::admin.driver.withdraw-request.invoice',
                    compact('data')
                );
                $this->generatePdf(view: $mpdf_view, filePrefix: 'withdraw_request_', filePostfix: time());
            }else{
                foreach ($data['ids'] as $id) {
                    $withdrawRequest = $this->withdrawRequestService->findOne(id: $id, relations: ['user' => []]);
                    if ($withdrawRequest) {
                        if (count($data['ids']) == 1 && ($withdrawRequest->method == null || $withdrawRequest->method == null)) {
                            Toastr::error(translate("Withdraw method or driver not found"));
                            return redirect()->back();
                        } else {
                            if (!($withdrawRequest->method == null || $withdrawRequest->method == null)) {
                                $this->withdrawRequestService->update(id: $id, data: $data);
                            }
                        }
                    }
                }
                Toastr::success(translate('Withdraw request updated successfully.'));
                return redirect(route('admin.driver.withdraw.requests'));
            }
        }
        Toastr::error(translate("Withdraw request not found"));
        return redirect()->back();
    }

    public function singleInvoice(int|string $id, Request $request)
    {
        $this->authorize('user_view');
        $type = $request->get('file', 'pdf');
        $criteria = [
            'id' => $id
        ];
        $data = $this->withdrawRequestService->getBy(criteria: $criteria, orderBy: ['created_at' => 'desc']);
        if ($type != 'pdf') {
            return exportData($data, $type, 'usermanagement::admin.driver.withdraw-request.invoice');
        }

        $mpdf_view = \Illuminate\Support\Facades\View::make('usermanagement::admin.driver.withdraw-request.invoice',
            compact('data')
        );
        $this->generatePdf(view: $mpdf_view, filePrefix: 'withdraw_request_', filePostfix: time());
    }

    public function multipleInvoice(Request $request)
    {
        $this->authorize('user_view');
        $value = $request->status ?? 'all';
        $criteria = [];
        if ($value == SETTLED) {
            $criteria['status'] = SETTLED;
        } elseif ($value == DENIED) {
            $criteria['status'] = DENIED;
        } elseif ($value == APPROVED) {
            $criteria['status'] = APPROVED;
        } elseif ($value == PENDING) {
            $criteria['status'] = PENDING;
        }

        if ($request?->has('method')) {
            if ($request?->get('method') != ALL) {
                $method = $this->withdrawMethodService->findOneBy(criteria: ['method_name' => $request?->get('method')]);
                $criteria['method_id'] = $method->id;
            }
        }

        $searchCriteria = [];
        if ($request->has('search')) {
            $searchCriteria = [
                'relations' => [
                    'user' => ['full_name', 'first_name', 'last_name'],
                ],
                'value' => $request->search,
            ];
        }
        $data = $this->withdrawRequestService->getBy(criteria: $criteria, searchCriteria: $searchCriteria, orderBy: ['created_at' => 'desc']);

        $mpdf_view = \Illuminate\Support\Facades\View::make('usermanagement::admin.driver.withdraw-request.invoice',
            compact('data')
        );
        $this->generatePdf(view: $mpdf_view, filePrefix: 'withdraw_request_', filePostfix: time());
    }
}
