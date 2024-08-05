<?php

namespace App\Providers;


use App\Http\Controllers\BaseController;
use App\Http\Controllers\BaseControllerInterface;
use App\Repository\Eloquent\BaseRepository;
use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use App\Service\BaseServiceInterface;
use Illuminate\Support\ServiceProvider;
use Modules\AdminModule\Interfaces\AdminNotificationInterface;
use Modules\AdminModule\Interfaces\DashboardInterface;
use Modules\AdminModule\Repositories\AdminNotificationRepository;
use Modules\AdminModule\Repositories\DashboardRepository;
use Modules\ReviewModule\Interfaces\ReviewInterface;
use Modules\TripManagement\Interfaces\TripRequestTimeInterface;
use Modules\TripManagement\Repositories\TripRequestTimeRepository;
use Modules\UserManagement\Interfaces\AppNotificationInterface;
use Modules\UserManagement\Interfaces\LoyaltyPointsHistoryInterface;
use Modules\UserManagement\Interfaces\UserInterface;
use Modules\UserManagement\Interfaces\WithdrawalMethodInterface;
use Modules\UserManagement\Interfaces\WithdrawRequestInterface;
use Modules\UserManagement\Repositories\AppNotificationRepository;
use Modules\UserManagement\Repositories\LoyaltyPointsHistoryRepository;
use Modules\UserManagement\Repositories\WithdrawalMethodRepository;
use Modules\UserManagement\Repositories\WithdrawRequestRepository;
use Modules\ZoneManagement\Interfaces\AreaInterface;
use Modules\ZoneManagement\Interfaces\ZoneInterface;
use Modules\UserManagement\Interfaces\DriverInterface;
use Modules\ReviewModule\Repositories\ReviewRepository;
use Modules\UserManagement\Interfaces\AddressInterface;
use Modules\UserManagement\Repositories\UserRepository;
use Modules\ZoneManagement\Repositories\ZoneRepository;
use Modules\AdminModule\Interfaces\ActivityLogInterface;
use Modules\FareManagement\Interfaces\TripFareInterface;
use Modules\UserManagement\Interfaces\CustomerInterface;
use Modules\UserManagement\Interfaces\EmployeeInterface;
use Modules\ZoneManagement\Interfaces\PickHourInterface;
use Modules\TripManagement\Interfaces\TripRouteInterface;
use Modules\UserManagement\Repositories\DriverRepository;
use Modules\FareManagement\Interfaces\ParcelFareInterface;
use Modules\UserManagement\Repositories\AddressRepository;
use Modules\VehicleManagement\Interfaces\VehicleInterface;
use Modules\AdminModule\Repositories\ActivityLogRepository;
use Modules\FareManagement\Repositories\TripFareRepository;
use Modules\PromotionManagement\Interfaces\BannerInterface;
use Modules\TripManagement\Interfaces\FareBiddingInterface;
use Modules\UserManagement\Interfaces\DriverLevelInterface;
use Modules\UserManagement\Interfaces\LevelAccessInterface;
use Modules\UserManagement\Interfaces\UserAccountInterface;
use Modules\UserManagement\Repositories\CustomerRepository;
use Modules\UserManagement\Repositories\EmployeeRepository;
use Modules\PromotionManagement\Interfaces\CoupounInterface;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\TripManagement\Repositories\TripRouteRepository;
use Modules\UserManagement\Interfaces\EmployeeRoleInterface;
use Modules\UserManagement\Interfaces\ModuleAccessInterface;
use Modules\FareManagement\Repositories\ParcelFareRepository;
use Modules\TripManagement\Interfaces\RecentAddressInterface;
use Modules\UserManagement\Interfaces\CustomerLevelInterface;
use Modules\UserManagement\Interfaces\DriverDetailsInterface;
use Modules\UserManagement\Interfaces\DriverTimeLogInterface;
use Modules\VehicleManagement\Repositories\VehicleRepository;
use Modules\BusinessManagement\Interfaces\SocialLinkInterface;
use Modules\ParcelManagement\Interfaces\ParcelWeightInterface;
use Modules\PromotionManagement\Repositories\BannerRepository;
use Modules\PromotionManagement\Repositories\CouponRepository;
use Modules\TripManagement\Interfaces\FareBiddingLogInterface;
use Modules\TripManagement\Repositories\FareBiddingRepository;
use Modules\TripManagement\Repositories\TripRequestRepository;
use Modules\UserManagement\Repositories\DriverLevelRepository;
use Modules\UserManagement\Repositories\LevelAccessRepository;
use Modules\UserManagement\Repositories\UserAccountRepository;
use Modules\ChattingManagement\Interfaces\ChannelListInterface;
use Modules\ChattingManagement\Interfaces\ChannelUserInterface;
use Modules\UserManagement\Interfaces\OtpVerificationInterface;
use Modules\UserManagement\Repositories\EmployeeRoleRepository;
use Modules\UserManagement\Repositories\ModuleAccessRepository;
use Modules\VehicleManagement\Interfaces\VehicleBrandInterface;
use Modules\VehicleManagement\Interfaces\VehicleModelInterface;
use Modules\ParcelManagement\Interfaces\ParcelCategoryInterface;
use Modules\TripManagement\Repositories\RecentAddressRepository;
use Modules\UserManagement\Interfaces\UserLastLocationInterface;
use Modules\UserManagement\Repositories\CustomerLevelRepository;
use Modules\UserManagement\Repositories\DriverDetailsRepository;
use Modules\UserManagement\Repositories\DriverTimeLogRepository;
use Modules\BusinessManagement\Repositories\SocialLinkRepository;
use Modules\ParcelManagement\Repositories\ParcelWeightRepository;
use Modules\TripManagement\Repositories\FareBiddingLogRepository;
use Modules\ChattingManagement\Repositories\ChannelListRepository;
use Modules\ChattingManagement\Repositories\ChannelUserRepository;
use Modules\TransactionManagement\Interfaces\TransactionInterface;
use Modules\UserManagement\Repositories\OtpVerificationRepository;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;
use Modules\VehicleManagement\Repositories\VehicleBrandRepository;
use Modules\VehicleManagement\Repositories\VehicleModelRepository;
use Modules\BusinessManagement\Interfaces\BusinessSettingInterface;
use Modules\BusinessManagement\Interfaces\SettingInterface;
use Modules\ParcelManagement\Repositories\ParcelCategoryRepository;
use Modules\UserManagement\Repositories\UserLastLocationRepository;
use Modules\TripManagement\Interfaces\TempTripNotificationInterface;
use Modules\TransactionManagement\Repositories\TransactionRepository;
use Modules\TripManagement\Interfaces\RejectedDriverRequestInterface;
use Modules\VehicleManagement\Repositories\VehicleCategoryRepository;
use Modules\BusinessManagement\Repositories\BusinessSettingRepository;
use Modules\BusinessManagement\Repositories\SettingRepository;
use Modules\BusinessManagement\Interfaces\NotificationSettingInterface;
use Modules\ChattingManagement\Interfaces\ChannelConversationInterface;
use Modules\TripManagement\Repositories\TempTripNotificationRepository;
use Modules\TripManagement\Repositories\RejectedDriverRequestRepository;
use Modules\BusinessManagement\Repositories\NotificationSettingRepository;
use Modules\ChattingManagement\Repositories\ChannelConversationRepository;
use Modules\BusinessManagement\Interfaces\FirebasePushNotificationInterface;
use Modules\BusinessManagement\Repositories\FirebasePushNotificationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //New Controller & Interface
        $this->app->bind(BaseControllerInterface::class,BaseController::class);

        //New Service & Interface
        $this->app->bind(BaseServiceInterface::class,BaseService::class);

        //New Repository & Interface
        $this->app->bind(EloquentRepositoryInterface::class,BaseRepository::class);




        //Old Repository & Interface
        $this->app->bind(AppNotificationInterface::class, AppNotificationRepository::class);


        $this->app->bind(SocialLinkInterface::class, SocialLinkRepository::class);
        $this->app->bind(UserInterface::class, UserRepository::class);

        // Vehicle Repository class
        $this->app->bind(VehicleInterface::class, VehicleRepository::class);
        $this->app->bind(VehicleBrandInterface::class, VehicleBrandRepository::class);
        $this->app->bind(VehicleModelInterface::class, VehicleModelRepository::class);
        $this->app->bind(VehicleCategoryInterface::class, VehicleCategoryRepository::class);

        // User Management Bindings
        $this->app->bind(AdminNotificationInterface::class, AdminNotificationRepository::class);
        $this->app->bind(WithdrawalMethodInterface::class, WithdrawalMethodRepository::class);
        $this->app->bind(WithdrawRequestInterface::class, WithdrawRequestRepository::class);
        $this->app->bind(CustomerInterface::class, CustomerRepository::class);
        $this->app->bind(CustomerLevelInterface::class, CustomerLevelRepository::class);
        $this->app->bind(LoyaltyPointsHistoryInterface::class, LoyaltyPointsHistoryRepository::class);

        $this->app->bind(DriverInterface::class, DriverRepository::class);
        $this->app->bind(DriverLevelInterface::class, DriverLevelRepository::class);
        $this->app->bind(DriverDetailsInterface::class, DriverDetailsRepository::class);


        $this->app->bind(EmployeeRoleInterface::class, EmployeeRoleRepository::class);
        $this->app->bind(AddressInterface::class, AddressRepository::class);

        $this->app->bind(ModuleAccessInterface::class, ModuleAccessRepository::class);
        $this->app->bind(OtpVerificationInterface::class, OtpVerificationRepository::class);

        $this->app->bind(UserAccountInterface::class, UserAccountRepository::class);

        //Transaction Management
        $this->app->bind(TransactionInterface::class, TransactionRepository::class);



        // Zone Repository class
        $this->app->bind(ZoneInterface::class, ZoneRepository::class);
        $this->app->bind(EmployeeInterface::class, EmployeeRepository::class);

        $this->app->bind(LevelAccessInterface::class, LevelAccessRepository::class);

        //Business Management Bindings
        $this->app->bind(BusinessSettingInterface::class, BusinessSettingRepository::class);
        $this->app->bind(SettingInterface::class, SettingRepository::class);
        $this->app->bind(FirebasePushNotificationInterface::class, FirebasePushNotificationRepository::class);
        $this->app->bind(NotificationSettingInterface::class, NotificationSettingRepository::class);


        // Promotion Repository class
        $this->app->bind(BannerInterface::class, BannerRepository::class);
        $this->app->bind(CoupounInterface::class, CouponRepository::class);

        // Parcel Repository class

        $this->app->bind(ParcelCategoryInterface::class, ParcelCategoryRepository::class);
        $this->app->bind(ParcelWeightInterface::class, ParcelWeightRepository::class);

        // Fare Module Bindings
        $this->app->bind(TripFareInterface::class, TripFareRepository::class);
        $this->app->bind(ParcelFareInterface::class, ParcelFareRepository::class);

        //Trip Module Bindings
        $this->app->bind(TripRequestInterfaces::class, TripRequestRepository::class);
        $this->app->bind(FareBiddingInterface::class, FareBiddingRepository::class);
        $this->app->bind(FareBiddingLogInterface::class, FareBiddingLogRepository::class);
        $this->app->bind(RecentAddressInterface::class, RecentAddressRepository::class);
        $this->app->bind(TripRouteInterface::class, TripRouteRepository::class);
        $this->app->bind(RejectedDriverRequestInterface::class, RejectedDriverRequestRepository::class);
        $this->app->bind(TempTripNotificationInterface::class, TempTripNotificationRepository::class);
        $this->app->bind(TripRequestTimeInterface::class, TripRequestTimeRepository::class);

        // Chatting module bindings
        $this->app->bind(ChannelConversationInterface::class, ChannelConversationRepository::class);
        $this->app->bind(ChannelListInterface::class, ChannelListRepository::class);
        $this->app->bind(ChannelUserInterface::class, ChannelUserRepository::class);

        //Review module bindings
        $this->app->bind(ReviewInterface::class, ReviewRepository::class);

        //Last Location bindings
        $this->app->bind(UserLastLocationInterface::class, UserLastLocationRepository::class);

        //Log Bindings
        $this->app->bind(ActivityLogInterface::class, ActivityLogRepository::class);

        //Driver Time Log Bindings
        $this->app->bind(DriverTimeLogInterface::class, DriverTimeLogRepository::class);
        $this->app->bind(DashboardInterface::class, DashboardRepository::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
