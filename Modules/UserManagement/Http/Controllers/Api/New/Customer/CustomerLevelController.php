<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Customer;

use App\Http\Controllers\Controller;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\UserManagement\Lib\LevelUpdateCheckerTrait;
use Modules\UserManagement\Service\Interface\CustomerLevelServiceInterface;
use Modules\UserManagement\Service\Interface\UserLevelHistoryServiceInterface;
use Modules\UserManagement\Transformers\CustomerLevelResource;

class CustomerLevelController extends Controller
{
    use LevelUpdateCheckerTrait;

    protected $customerLevelService;
    protected $tripRequestService;
    protected $userLevelHistoryService;

    public function __construct(CustomerLevelServiceInterface $customerLevelService, TripRequestServiceInterface $tripRequestService,
                                UserLevelHistoryServiceInterface $userLevelHistoryService)
    {
        $this->customerLevelService = $customerLevelService;
        $this->tripRequestService = $tripRequestService;
        $this->userLevelHistoryService = $userLevelHistoryService;
    }

    public function getCustomerLevelWithTrip()
    {
        $user = auth()->user();
        $userLevelHistory = $this->userLevelHistoryService->findOneBy(criteria: ['user_id' => $user->id, 'user_level_id' => $user?->level?->id]);
        $user = $this->customerLevelUpdateChecker($user);
        $level = $user->level;
        $currentSequence = $level->sequence;
        $nextLevel = $this->customerLevelService->findOneBy(criteria: ['user_type' => CUSTOMER, ['sequence', '>', $currentSequence],'is_active'=>1], orderBy: ['sequence' => 'asc']);
        if ($nextLevel) {
            $nextLevel = CustomerLevelResource::make($nextLevel);
        }else{
            $nextLevel = null;
        }

        $level = CustomerLevelResource::make($level);
        $spendAmount = $this->tripRequestService->getBy(criteria: ['customer_id' => $user->id, 'payment_status' => PAID])->sum('paid_fare');
        $totalTrip = $user?->customerTrips?->count();
        $reviewGiven = $user->givenReviews->count();
        $cancelTrip = $this->tripRequestService->getBy(criteria: ['customer_id' => $user->id, 'current_status' => CANCELLED])->count();
        $completedTrip = $this->tripRequestService->getBy(criteria: ['customer_id' => $user->id, 'current_status' => COMPLETED])->count();
        $cancellationRate = ($cancelTrip / ($totalTrip == 0 ? 1 : $totalTrip)) * 100;

        $completedCurrentLevelTarget = [
            'review_given' => $level->targeted_review <= $reviewGiven ? $level->targeted_review : $reviewGiven,
            'review_given_point' => $level->targeted_review <= $reviewGiven ? $level->targeted_review_point : round(($level->targeted_review_point / $level->targeted_review) * $reviewGiven),
            'ride_complete' => $level->targeted_ride <= $completedTrip ? $level->targeted_ride : $completedTrip,
            'ride_complete_point' => $level->targeted_ride <= $completedTrip ? $level->targeted_ride_point : round(($level->targeted_ride_point / $level->targeted_ride) * $completedTrip),
            'spend_amount' => $level->targeted_amount <= $spendAmount ? $level->targeted_amount : $spendAmount,
            'spend_amount_point' => $level->targeted_amount <= $spendAmount ? $level->targeted_amount_point : round(($level->targeted_amount_point / $level->targeted_amount) * $spendAmount),
            'cancellation_rate' => $level->targeted_cancel==0 ?$level->targeted_cancel:( $level->targeted_cancel<=$cancellationRate ?  $cancellationRate : $cancellationRate),
            'cancellation_rate_point' => $level->targeted_cancel<=$cancellationRate ? round(($level->targeted_cancel_point / (100-$level->targeted_cancel)) * $cancellationRate) : $level->targeted_cancel_point,
        ];
        $data = [
            'next_level' => $nextLevel,
            'current_level' => $level,
            'completed_current_level_target' => collect($completedCurrentLevelTarget),
            'level_completed' => (bool)$userLevelHistory,
        ];
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $data));
    }
}
