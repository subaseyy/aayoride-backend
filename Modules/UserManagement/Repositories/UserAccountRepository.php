<?php

namespace Modules\UserManagement\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\UserManagement\Entities\UserAccount;
use Modules\TransactionManagement\Entities\Transaction;
use Modules\UserManagement\Interfaces\UserAccountInterface;

class UserAccountRepository implements UserAccountInterface
{
    public function __construct(
        private Transaction $transaction,
        private UserAccount $customerAccount
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value = array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $search = array_key_exists('search', $attributes) ? $attributes['search'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->customerAccount
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->when(!empty($relations[0]), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->when(array_key_exists('customer_id', $attributes) && $attributes['customer_id'] != null, function ($query) use ($attributes) {
                $query->where('user_id', $attributes['customer_id']);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('id', 'like', '%' . $key . '%');
                    }
                });
            })
            ->when($value != 'all', function ($query) use ($column, $value) {
                $query->where($column, $value);
            })
            ->when($attributes['transaction_type'] = null, function ($query) use ($attributes) {
                $query->transactionType($attributes['transaction_type']);
            })
            ->latest();
        if ($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }

        return $query->paginate(paginationLimit())
            ->appends($queryParams);

    }

    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->customerAccount
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->where($column, $value)
            ->first();
    }

    public function store(array $attributes): Model
    {
        DB::beginTransaction();
        if ($attributes['customer_id'] == "all") {
            $this->customerAccount->increment('wallet_balance', $attributes['amount']);
            $customers = $this->customerAccount
                ->query()
                ->select('user_id', 'wallet_balance')
                ->whereHas('user', function($q){
                    $q->where('user_type', 'customer')
                    ->where('is_active',true);
                })
                ->get();
            $data = [];
            foreach ($customers as $customer) {
                $data[] = [
                    'id'=>Str::uuid(),
                    'balance' => $customer['wallet_balance'],
                    'attribute' => 'fund_by_admin',
                    'account' => 'wallet_balance',
                    'credit' => $attributes['amount'],
                    'user_id' => $customer['user_id'],
                    'trx_ref_id' => $attributes['reference'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            $this->transaction->insert($data);
        }else{
            $customer = $this->getBy(column: 'user_id', value: $attributes['customer_id']);
            $customer->user_id = $attributes['customer_id'];
            $customer->wallet_balance += $attributes['amount'];
            $customer->save();

            $transaction = $this->transaction;
            $transaction->balance = $customer->wallet_balance;
            $transaction->attribute = 'fund_by_admin';
            $transaction->account = 'wallet_balance';
            $transaction->credit = $attributes['amount'];
            $transaction->user_id = $attributes['customer_id'];
            $transaction->trx_ref_id = $attributes['reference'];
            $transaction->save();
        }

        DB::commit();
        return $this->customerAccount->latest()->first();
    }

    public function update(array $attributes, string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);
        return $customer;
    }

    public function destroy(string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);
        $customer->delete();
        return $customer;
    }

}
