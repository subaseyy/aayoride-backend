<?php

namespace Modules\BusinessManagement\Http\Controllers\Web\New\Admin\SystemSetting;

use App\Http\Controllers\BaseController;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;

class SystemSettingController extends BaseController
{
    use AuthorizesRequests;

    public function __construct(BusinessSettingServiceInterface $businessSettingService)
    {
        parent::__construct($businessSettingService);
    }

    public function envSetup()
    {
        $this->authorize('business_view');
        return view('businessmanagement::admin.system-settings.environment-setup');
    }

    public function envUpdate(Request $request): Renderable|RedirectResponse
    {
        $this->authorize('business_edit');
        try {
            $env = app()->environmentFilePath();
            try {
                chmod($env, 777);
            }catch(Exception $exception){

            }
            self::setEnvironmentValue('APP_DEBUG', $request['app_debug'] ?? env('APP_DEBUG'));
            self::setEnvironmentValue('APP_MODE', $request['app_mode'] ?? env('APP_MODE'));
        } catch (\Exception $exception) {
            Toastr::error(DEFAULT_FAIL_200['message']);
            return back();
        }

        Toastr::success(SYSTEM_SETTING_UPDATE_200['message']);
        return back();
    }


    public function dbIndex(): Renderable
    {
        $this->authorize('business_view');
        $db_name = env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');
        $tables = collect($tables)->flatten()->pluck('Tables_in_' . $db_name)->toArray();

        $filter_tables = array("banner_setups", "channel_conversations", "channel_lists", "channel_users",
            "conversation_files", "coupon_setups", "coupon_setup_vehicle_category", "discount_setups", "discount_setup_vehicle_category",
            "failed_jobs", "fare_biddings", "module_accesses", "notification_settings", "parcels", "parcel_categories", "parcel_fares",
            "parcel_fares_parcel_weights", "parcel_weights", "social_links", "trip_fares", "trip_requests", "trip_routes", "trip_status"
        , "vehicles", "vehicle_brands", "vehicle_categories", "vehicle_category_zone", "vehicle_models");

        $tables = array_intersect($tables, $filter_tables);

        return view('businessmanagement::admin.system-settings.clean-database', compact('tables'));

    }

    public function cleanDb(Request $request): RedirectResponse|Renderable
    {
        $this->authorize('business_edit');
        $tables = (array)$request['tables'];
        if (count($tables) < 1) {
            Toastr::error(NO_CHANGES_FOUND['message']);
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            Toastr::error(DEFAULT_FAIL_200['message']);
            return back();
        }

        Toastr::success(DEFAULT_DELETE_200['message']);
        return back();
    }


    private static function setEnvironmentValue($key, $value)
    {
        $env = app()->environmentFilePath();

        try {
            chmod($env, 777);
        }catch(Exception $exception){

        }
        $str = file_get_contents($env);

        if (is_bool(env($key))) {
            $oldValue = var_export(env($key), true);
        } else {
            $oldValue = env($key);
        }
        if (str_contains($str, $key)) {
            $str = str_replace("{$key}={$oldValue}", "{$key}={$value}", $str);

        } else {
            $str .= "{$key}={$value}\n";
        }
        $file = fopen(base_path('.env'), 'w');
        fwrite($file, $str);
        fclose($file);

        return $value;
    }
}
