<?php

namespace Modules\TransactionManagement\Http\Controllers\Web\New\Admin\Transaction;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\TransactionManagement\Interfaces\TransactionInterface;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;

class TransactionController extends BaseController
{
    use AuthorizesRequests;

    protected $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        parent::__construct($transactionService);
        $this->transactionService = $transactionService;
    }

    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        $transactions = $this->transactionService->index(criteria: $request?->all(), relations: ['user'], orderBy : ['created_at' => 'desc'], limit: paginationLimit(), offset:$request['page']??1);
        return view('transactionmanagement::admin.transaction.index', compact('transactions'));
    }

    public function export(Request $request)
    {
        $this->authorize('transaction_export');
        $exportData = $this->transactionService->export(criteria: $request->all());
        return exportData($exportData, $request['file'],'');
    }
}
