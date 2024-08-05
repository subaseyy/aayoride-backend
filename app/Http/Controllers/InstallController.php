<?php

namespace App\Http\Controllers;

use App\Traits\ActivationClass;
use App\Traits\UnloadedHelpers;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserAccount;
use Modules\UserManagement\Entities\UserLevel;
use Ramsey\Uuid\Uuid;

class InstallController extends Controller
{
    use ActivationClass, UnloadedHelpers;

    public function step0(): Factory|View|Application
    {
        return view('installation.step0');
    }

    public function step1(Request $request): View|Factory|RedirectResponse|Application
    {
        if (Hash::check('step_1', $request['token'])) {
            //extensions
            $permission['curl'] = function_exists('curl_version');
            $permission['bcmath'] = extension_loaded('bcmath');
            $permission['ctype'] = extension_loaded('ctype');
            $permission['json'] = extension_loaded('json');
            $permission['mbstring'] = extension_loaded('mbstring');
            $permission['openssl'] = extension_loaded('openssl');
            $permission['pdo'] = defined('PDO::ATTR_DRIVER_NAME');
            $permission['tokenizer'] = extension_loaded('tokenizer');
            $permission['xml'] = extension_loaded('xml');
            $permission['zip'] = extension_loaded('zip');
            $permission['fileinfo'] = extension_loaded('fileinfo');
            $permission['gd'] = extension_loaded('gd');
            $permission['sodium'] = extension_loaded('sodium');

            //file permissions
            $permission['module_file_permission'] = is_writable(base_path('modules_statuses.json'));
            $permission['env_file_write_perm'] = is_writable(base_path('.env'));
            $permission['routes_file_write_perm'] = is_writable(base_path('app/Providers/RouteServiceProvider.php'));
            return view('installation.step1', compact('permission'));
        }
        session()->flash('error', 'Access denied!');
        return redirect()->route('step0');
    }

    public function step2(Request $request): View|Factory|RedirectResponse|Application
    {
        if (Hash::check('step_2', $request['token'])) {
            return view('installation.step2');
        }
        session()->flash('error', 'Access denied!');
        return redirect()->route('step0');
    }

    public function step3(Request $request): View|Factory|RedirectResponse|Application
    {
        if (Hash::check('step_3', $request['token'])) {
            return view('installation.step3');
        }
        session()->flash('error', 'Access denied!');
        return redirect()->route('step0');
    }

    public function step4(Request $request): View|Factory|RedirectResponse|Application
    {
        if (Hash::check('step_4', $request['token'])) {
            return view('installation.step4');
        }
        session()->flash('error', 'Access denied!');
        return redirect()->route('step0');
    }

    public function step5(Request $request): View|Factory|RedirectResponse|Application
    {
        if (Hash::check('step_5', $request['token'])) {
            return view('installation.step5');
        }
        session()->flash('error', 'Access denied!');
        return redirect()->route('step0');
    }

    public function purchaseCode(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|regex:/^\S*$/u',
            'purchase_key' => 'required|regex:/^\S*$/u',
        ]);

        $this->setEnvironmentValue('SOFTWARE_ID', 'MTAwMDAwMDA=');
        $this->setEnvironmentValue('BUYER_USERNAME', $request['username']);
        $this->setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);

        $post = [
            'name' => $request['name'],
            'email' => $request['email'],
            'username' => str_replace(' ', '_', $request['username']),
            'purchase_key' => str_replace(' ', '', $request['purchase_key']),
            'domain' => preg_replace("#^[^:/.]*[:/]+#i", "", url('/')),
        ];
        $response = $this->dmvf($post);

        return redirect($response.'?token='.bcrypt('step_3'));
    }

    public function systemSettings(Request $request): View|Factory|RedirectResponse|Application
    {
        $request->validate([
            'password' => 'same:confirm_password'
        ]);

        if (!Hash::check('step_6', $request['token'])) {
            session()->flash('error', 'Access denied!');
            return redirect()->route('step0');
        }

        $user = User::create([
            'id' => Uuid::uuid4(),
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'user_type' => 'super-admin',
            'password' => bcrypt($request['password']),
            'phone' => $request['phone'],
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        UserAccount::query()->create([
            'user_id' => $user->id
        ]);
        $customerLevel = UserLevel::create([
            'sequence'=>1,
            'name'=>"Level 1",
            'reward_type'=>"loyalty_points",
            'reward_amount'=>10,
            'image'=>asset('assets/images/logo.png'),
            'targeted_ride'=>10,
            'targeted_ride_point'=>10,
            'targeted_amount'=>10,
            'targeted_amount_point'=>10,
            'targeted_cancel'=>10,
            'targeted_cancel_point'=>10,
            'targeted_review'=>10,
            'targeted_review_point'=>10,
            'user_type'=>CUSTOMER,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
        $driverLevel = UserLevel::create([
            'sequence'=>1,
            'name'=>"Level 1",
            'reward_type'=>"loyalty_points",
            'reward_amount'=>10,
            'image'=>asset('assets/images/logo.png'),
            'targeted_ride'=>10,
            'targeted_ride_point'=>10,
            'targeted_amount'=>10,
            'targeted_amount_point'=>10,
            'targeted_cancel'=>10,
            'targeted_cancel_point'=>10,
            'targeted_review'=>10,
            'targeted_review_point'=>10,
            'user_type'=>DRIVER,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);

        $previousRouteServiceProvider = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvider = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvider, $previousRouteServiceProvider);

        $modules = ['AdminModule','AuthManagement','BusinessManagement','ChattingManagement','FareManagement',
            'Gateways','ParcelManagement','PromotionManagement','ReviewModule','TransactionManagement','TripManagement',
            'UserManagement','VehicleManagement','ZoneManagement',
        ];
        foreach ($modules as $module) {
            Artisan::call('module:enable', ['module' => $module]);
        }
        return view('installation.step6');
    }

    public function databaseInstallation(Request $request): Redirector|Application|RedirectResponse
    {
        if (self::check_database_connection($request->DB_HOST, $request->DB_DATABASE, $request->DB_USERNAME, $request->DB_PASSWORD)) {
            $url = preg_replace('/^https?:\/\//', '', URL::to('/'));

            // Remove www.
            $url = preg_replace('/^www\./', '', $url);
            $key = base64_encode(random_bytes(32));
            $output = 'APP_NAME=DriveMond' . time() . '
                    APP_ENV=live
                    APP_MODE=live
                    APP_KEY=base64:' . $key . '
                    APP_DEBUG=false
                    APP_INSTALL=true
                    APP_LOG_LEVEL=debug
                    APP_URL=' . URL::to('/') . '

                    DB_CONNECTION=mysql
                    DB_HOST=' . $request->DB_HOST . '
                    DB_PORT=3306
                    DB_DATABASE=' . $request->DB_DATABASE . '
                    DB_USERNAME=' . $request->DB_USERNAME . '
                    DB_PASSWORD=' . $request->DB_PASSWORD . '

                    BROADCAST_DRIVER=reverb
                    CACHE_DRIVER=file
                    SESSION_DRIVER=file
                    SESSION_LIFETIME=60
                    QUEUE_DRIVER=sync

                    AWS_ENDPOINT=
                    AWS_ACCESS_KEY_ID=
                    AWS_SECRET_ACCESS_KEY=
                    AWS_DEFAULT_REGION=us-east-1
                    AWS_BUCKET=

                    REDIS_HOST=127.0.0.1
                    REDIS_PASSWORD=null
                    REDIS_PORT=6379

                    PUSHER_APP_ID=drivemond
                    PUSHER_APP_KEY=drivemond
                    PUSHER_APP_SECRET=drivemond
                    PUSHER_APP_CLUSTER=mt1
                    PUSHER_HOST='. $url .'
                    PUSHER_PORT=6001
                    PUSHER_SCHEME="http"


                    VITE_APP_NAME="${APP_NAME}"
                    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
                    VITE_PUSHER_HOST="${PUSHER_HOST}"
                    VITE_PUSHER_PORT="${PUSHER_PORT}"
                    VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
                    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

                    REVERB_APP_ID=drivemond
                    REVERB_APP_KEY=drivemond
                    REVERB_APP_SECRET=drivemond
                    REVERB_HOST='. $url .'
                    REVERB_PORT=6001
                    REVERB_SCHEME="http"
                    REVERB_SSL_CERT_PATH=""
                    REVERB_SSL_KEY_PATH=""

                    MIX_REVERB_APP_KEY="${REVERB_APP_KEY}"
                    MIX_REVERB_HOST="${REVERB_HOST}"
                    MIX_REVERB_PORT="${REVERB_PORT}"
                    MIX_REVERB_SCHEME="${REVERB_SCHEME}"

                    PURCHASE_CODE=' . session('purchase_key') . '
                    BUYER_USERNAME=' . session('username') . '
                    SOFTWARE_ID=MTAwMDAwMDA=

                    SOFTWARE_VERSION=1.6
                    ';
            $file = fopen(base_path('.env'), 'w');
            fwrite($file, $output);
            fclose($file);

            $path = base_path('.env');
            if (file_exists($path)) {
                return redirect()->route('step4', ['token' => $request['token']]);
            } else {
                session()->flash('error', 'Database error!');
                return redirect()->route('step3', ['token' => bcrypt('step_3')]);
            }
        } else {
            session()->flash('error', 'Database host error!');
            return redirect()->route('step3', ['token' => bcrypt('step_3')]);
        }
    }

    public function importSql(): Redirector|RedirectResponse|Application
    {
        try {
            $sql_path = base_path('installation/backup/database.sql');
            DB::unprepared(file_get_contents($sql_path));
            return redirect()->route('step5', ['token' => bcrypt('step_5')]);
        } catch (\Exception $exception) {
            session()->flash('error', 'Your database is not clean, do you want to clean database then import?');
            return back();
        }
    }

    public function forceImportSql(): Redirector|RedirectResponse|Application
    {
        try {
            Artisan::call('db:wipe', ['--force' => true]);
            $sql_path = base_path('installation/backup/database.sql');
            DB::unprepared(file_get_contents($sql_path));
            return redirect()->route('step5', ['token' => bcrypt('step_5')]);
        } catch (\Exception $exception) {
            session()->flash('error', 'Check your database permission!');
            return back();
        }
    }

    function check_database_connection($db_host = "", $db_name = "", $db_user = "", $db_pass = ""): bool
    {
        try {
            if (@mysqli_connect($db_host, $db_user, $db_pass, $db_name)) {
                return true;
            } else {
                return false;
            }
        }catch(\Exception $exception){
            return false;
        }
    }
}
