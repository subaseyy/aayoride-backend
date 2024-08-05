<?php

namespace Modules\TransactionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\TransactionManagement\Entities\Transaction;
use Modules\TransactionManagement\Repository\TransactionRepositoryInterface;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}
