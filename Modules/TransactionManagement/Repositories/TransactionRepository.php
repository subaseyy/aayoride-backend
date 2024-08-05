<?php

namespace Modules\TransactionManagement\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\TransactionManagement\Entities\Transaction;
use Modules\TransactionManagement\Interfaces\TransactionInterface;
use Modules\UserManagement\Entities\UserAccount;

class TransactionRepository implements TransactionInterface
{
    public function __construct(
        private Transaction $transaction,
        private UserAccount $customerAccount
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value = $attributes['value'] ?? 'all';
        $column = $attributes['query'] ?? null;
        $search = $attributes['search'] ?? null;
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];
        $query = $this->transaction
            ->query()
            ->when(!empty($relations), fn($query) => $query->with($relations))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('id', 'like', '%' . $key . '%');
                    }
                });
            })
            ->when(!empty($attributes['dates']),
                fn($query) => $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]))
            ->when(($attributes['user_id'] ?? null), fn($query) => $query->where('user_id', $attributes['user_id']))
            ->when($value && $value != 'all', fn($query) => $query->where($column, $value))
            ->when(($attributes['transaction_type'] ?? null), fn($query) => $query->transactionType($attributes['transaction_type']))
            ->when(($attributes['account_type'] ?? null), fn($query) => $query->accountType($attributes['account_type']))
            ->latest();

        if (!$dynamic_page) {
            return $query->paginate($limit)->appends($queryParams);
        }

        return $query->paginate(perPage: $limit, page: $offset);

    }

    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->customerAccount
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->firstWhere($column, $value);
    }

    public function store(array $attributes): Model
    {
        DB::beginTransaction();

        return $this->getBy(column: 'customer_id', value: $attributes['customer_id']);
    }

    public function update(array $attributes, string $id): Model
    {
        return $this->getBy(column: 'id', value: $id);
    }

    public function destroy(string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);
        $customer->delete();
        return $customer;
    }

}
