<?php

namespace Modules\UserManagement\Service;

use App\Service\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\TransactionManagement\Repository\TransactionRepositoryInterface;
use Modules\UserManagement\Repository\UserAccountRepositoryInterface;

class CustomerAccountService extends BaseService implements Interface\CustomerAccountServiceInterface
{
    protected $userAccountRepository;
    protected $transactionRepository;

    public function __construct(UserAccountRepositoryInterface $userAccountRepository, TransactionRepositoryInterface $transactionRepository)
    {
        parent::__construct($userAccountRepository);
        $this->userAccountRepository = $userAccountRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function create(array $data): ?Model
    {
        if ($data['customer_id'] == "all") {
            $relations = [
                'user' => [
                    ['user_type', '=', CUSTOMER],
                    ['is_active', '=', true],
                ],
            ];
            $whereHasRelations = [
                'user' => ['user_type' => 'CUSTOMER', 'is_active' => true]
            ];
            $customerAccountIds = $this->userAccountRepository->getBy(whereHasRelations: $whereHasRelations, relations: $relations)->pluck('id')->toArray();
            $this->userAccountRepository->updateManyWithIncrement(ids: $customerAccountIds, column: 'wallet_balance', amount: $data['amount']);
            $customerAccounts = $this->userAccountRepository->getBy(whereHasRelations: $whereHasRelations, relations: $relations);

            $transactionData = [];
            foreach ($customerAccounts as $customer) {
                $transactionData[] = [
                    'id' => Str::uuid(),
                    'balance' => $customer->wallet_balance,
                    'attribute' => 'fund_by_admin',
                    'account' => 'wallet_balance',
                    'credit' => $data['amount'],
                    'user_id' => $customer->user_id,
                    'trx_ref_id' => $data['reference'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
            $this->transactionRepository->createMany($transactionData);
        } else {
            $customerId = $this->userAccountRepository->findOneBy(criteria: ['user_id' => $data['customer_id']]);
            $this->userAccountRepository->update(id: $customerId->id, data:[
                'wallet_balance' => $customerId->wallet_balance + $data['amount']
            ]);
            $customer = $this->userAccountRepository->findOneBy(criteria: ['user_id' => $data['customer_id']]);

            $transactionData = [
                'balance' => $customer->wallet_balance,
                'attribute' => 'fund_by_admin',
                'account' => 'wallet_balance',
                'credit' => $data['amount'],
                'user_id' => $data['customer_id'],
                'trx_ref_id' => $data['reference']
            ];
            $this->transactionRepository->create($transactionData);
        }
        $relations = [
            'user' => [
                ['user_type', '=', CUSTOMER],
                ['is_active', '=', true],
            ],
        ];
        $whereHasRelations = [
            'user' => ['user_type' => 'CUSTOMER', 'is_active' => true]
        ];
        return $this->userAccountRepository->getBy(whereHasRelations: $whereHasRelations, relations: $relations)->last();
    }

    public function export(Collection $data): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $data->map(function ($item) {
            return [
                'Id' => $item['id'],
                'Transaction Id' => $item['id'],
                'Reference' => $item['trx_ref_id'],
                'Transaction Date' => $item['created_at']->format('d-m-Y h:m:i A'),
                'Transaction To' => $item->user?->first_name . ' ' . $item->user?->last_name,
                'Debit' => getCurrencyFormat($item['debit']),
                'Credit' => getCurrencyFormat($item['credit']),
                'Balance' => getCurrencyFormat($item['balance']),
            ];
        });
    }
}
