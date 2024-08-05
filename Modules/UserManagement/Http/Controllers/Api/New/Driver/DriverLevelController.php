<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\TripManagement\Service\Interface\TripRequestFeeServiceInterface;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;
use Modules\UserManagement\Lib\LevelUpdateCheckerTrait;
use Modules\UserManagement\Service\Interface\DriverLevelServiceInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;
use Modules\UserManagement\Service\Interface\UserLevelHistoryServiceInterface;
use Modules\UserManagement\Transformers\DriverLevelResource;

class DriverLevelController extends Controller
{
    use LevelUpdateCheckerTrait;

    protected $driverLevelService;
    protected $driverService;
    protected $tripRequestService;
    protected $tripRequestFeeService;
    protected $userLevelHistoryService;

    public function __construct(DriverLevelServiceInterface      $driverLevelService, DriverServiceInterface $driverService,
                                TripRequestServiceInterface      $tripRequestService, TripRequestFeeServiceInterface $tripRequestFeeService,
                                UserLevelHistoryServiceInterface $userLevelHistoryService)
    {
        $this->driverLevelService = $driverLevelService;
        $this->driverService = $driverService;
        $this->tripRequestService = $tripRequestService;
        $this->tripRequestFeeService = $tripRequestFeeService;
        $this->userLevelHistoryService = $userLevelHistoryService;
    }

    public function getDriverLevelWithTrip()
    {
        $user = auth()->user();
        $userLevelHistory = $this->userLevelHistoryService->findOneBy(criteria: ['user_id' => $user->id, 'user_level_id' => $user?->level?->id]);
        $user = $this->driverLevelUpdateChecker($user);
        $user = $this->driverService->findOne($user->id);
        $level = $user->level;
        $currentSequence = $level->sequence;
        $nextLevel = $this->driverLevelService->findOneBy(criteria: ['user_type' => DRIVER, ['sequence', '>', $currentSequence], 'is_active' => 1], orderBy: ['sequence' => 'asc']);
        if ($nextLevel) {
            $nextLevel = DriverLevelResource::make($nextLevel);
        } else {
            $nextLevel = null;
        }
        $level = DriverLevelResource::make($level);
        $totalTrip = $user?->driverTrips?->count();

        $tripTotalEarning = $this->tripRequestService->getBy(criteria: ['driver_id' => $user->id, 'payment_status' => PAID])->sum('paid_fare');
        $trip = $this->tripRequestService->getBy(criteria: ['driver_id' => $user->id, 'payment_status' => PAID]);
        $adminCommission = $this->tripRequestFeeService->getBy(whereInCriteria: ['trip_request_id' => $trip->pluck('id')])->sum('admin_commission');
        $cancelTrip = $this->tripRequestService->getBy(criteria: ['driver_id' => $user->id, 'current_status' => CANCELLED])->count();
        $completedTrip = $this->tripRequestService->getBy(criteria: ['driver_id' => $user->id, 'current_status' => COMPLETED])->count();
        $reviewGiven = $user->givenReviews->count();
        $cancellationRate = ($cancelTrip / ($totalTrip == 0 ? 1 : $totalTrip)) * 100;
        $earningAmount = $tripTotalEarning - $adminCommission;

        $completedCurrentLevelTarget = [
            'review_given' => $level->targeted_review <= $reviewGiven ? $level->targeted_review : $reviewGiven,
            'review_given_point' => $level->targeted_review <= $reviewGiven ? $level->targeted_review_point : round(($level->targeted_review_point / $level->targeted_review) * $reviewGiven),
            'ride_complete' => $level->targeted_ride <= $completedTrip ? $level->targeted_ride : $completedTrip,
            'ride_complete_point' => $level->targeted_ride <= $completedTrip ? $level->targeted_ride_point : round(($level->targeted_ride_point / $level->targeted_ride) * $completedTrip),
            'earning_amount' => $level->targeted_amount <= $earningAmount ? $level->targeted_amount : $earningAmount,
            'earning_amount_point' => $level->targeted_amount <= $earningAmount ? $level->targeted_amount_point : round(($level->targeted_amount_point / $level->targeted_amount) * $earningAmount),
            'cancellation_rate' => $level->targeted_cancel == 0 ? $level->targeted_cancel : ($level->targeted_cancel <= $cancellationRate ? $cancellationRate : $cancellationRate),
            'cancellation_rate_point' => $level->targeted_cancel <= $cancellationRate ? round(($level->targeted_cancel_point / (100 - $level->targeted_cancel)) * $cancellationRate) : $level->targeted_cancel_point,
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
