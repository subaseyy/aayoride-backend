<?php

namespace Modules\BusinessManagement\Service;

use App\Repository\Eloquent\BaseRepository;
use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\BusinessManagement\Repository\CancellationReasonRepositoryInterface;
use Modules\BusinessManagement\Service\Interface\CancellationReasonServiceInterface;

class CancellationReasonService extends BaseService implements Interface\CancellationReasonServiceInterface
{
    protected $cancellationReasonRepository;
    public function __construct(CancellationReasonRepositoryInterface $cancellationReasonRepository)
    {
        parent::__construct($cancellationReasonRepository);
        $this->cancellationReasonRepository = $cancellationReasonRepository;
    }
}
