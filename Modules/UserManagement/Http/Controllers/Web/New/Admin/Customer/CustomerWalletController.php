<?php

namespace Modules\UserManagement\Http\Controllers\Web\New\Admin\Customer;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;
use Modules\UserManagement\Http\Requests\CustomerWalletStoreOrUpdateRequest;
use Modules\UserManagement\Service\Interface\CustomerAccountServiceInterface;

class CustomerWalletController extends BaseController
{
    use AuthorizesRequests;

    protected $customerAccountService;
    protected $transactionService;

    public function __construct(CustomerAccountServiceInterface $customerAccountService, TransactionServiceInterface $transactionService)
    {
        parent::__construct($customerAccountService);
        $this->customerAccountService = $customerAccountService;
        $this->transactionService = $transactionService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $this->authorize('user_view');
        $request?->validate([
            'data' => 'in:all_time,this_week,last_week,this_month,last_month,last_15_days,this_year,last_year,custom_date',
            'start' => 'required_if:data,custom_date',
            'end' => 'required_if:data,custom_date',
        ]);
        $transactions = $this->transactionService->customerWalletTransaction(data: $request?->all(), orderBy: ['created_at' => 'desc'], limit: paginationLimit(), offset:$request['page']??1);
        return view('usermanagement::admin.customer.wallet.index', compact('transactions'));
    }

    public function store(CustomerWalletStoreOrUpdateRequest $request)
    {
        $this->authorize('user_add');
        DB::beginTransaction();
        $this->customerAccountService->create(data: $request->validated());
        Toastr::success(CUSTOMER_FUND_STORE_200['message']);
        DB::commit();
        return redirect()->back();
    }

    public function export(Request $request)
    {
        $this->authorize('user_export');
        $transactions = $this->transactionService->customerWalletTransaction($request?->all(), orderBy: ['created_at' => 'desc']);
        $exportData = $this->customerAccountService->export($transactions);
        return exportData($exportData, $request['file'], 'usermanagement::admin.customer.wallet.print');
    }
}
