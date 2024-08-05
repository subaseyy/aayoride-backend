<?php

namespace Modules\TransactionManagement\Http\Controllers\Web\Admin\Transaction;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Modules\TransactionManagement\Interfaces\TransactionInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TransactionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TransactionInterface $transaction,
    )
    {
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $this->authorize('transaction_view');
        $request->validate([
            'search' => 'sometimes',
            'query' => 'sometimes',
            'value' => 'sometimes|in:all',
        ]);

        $transactions = $this->transaction
            ->get(limit: paginationLimit(), offset: 1, attributes: $request->all(), relations: ['user']);

        return view('transactionmanagement::admin.transaction.index', [
            'search' => $request['search'],
            'value' => $request['value'] ?? 'all',
            'transactions' => $transactions,
        ]);
    }

    public function export(Request $request)
    {
        $this->authorize('transaction_export');
        $attributes = [
            'relations' => ['user'],
            'query' => $request['query'],
            'value' => $request['value'],
        ];

        !is_null($request['search']) ? $attributes['search'] = $request['search'] : '';
        $roles = $this->transaction->get(limit: 9999999999999999, offset: 1, attributes: $attributes);
        $exportDatas = $roles->map(function ($item) {
            return [
                'id' => $item['id'],
                'transaction_id' => $item['id'],
                'reference' => $item['trx_ref_id'],
                'transaction_date' => $item['created_at'],
                'transaction_to' => $item->user?->first_name . ' ' . $item->user?->last_name,
                'credit' => $item['credit'],
                'balance' => $item['balance'],
            ];
        });

        return exportData($exportDatas, $request['file'], 'transactionmanagement::admin.transaction.print');
    }


}
