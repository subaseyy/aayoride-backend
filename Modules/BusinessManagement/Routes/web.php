<?php

use Illuminate\Support\Facades\Route;
use Modules\BusinessManagement\Http\Controllers\Web\Admin\BusinessSetup\{BusinessInfoController,
    CustomerSettingController,
    DriverSettingController,
    TripFareSettingController
};
use Modules\BusinessManagement\Http\Controllers\Web\Admin\Configuration\{NotificationController,
    PaymentConfigController,
    ThirdPartyController
};
use Modules\BusinessManagement\Http\Controllers\Web\Admin\Configuration\SMSConfigController;
use Modules\BusinessManagement\Http\Controllers\Web\Admin\PagesMedia\LandingPageController;
use Modules\BusinessManagement\Http\Controllers\Web\Admin\PagesMedia\PagesMediaController;
use Modules\BusinessManagement\Http\Controllers\Web\Admin\SystemSettings\{LanguageController, SystemSettingsController};

#new route
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    Route::group(['prefix' => 'business', 'as' => 'business.'], function () {
        Route::group(['prefix' => 'setup', 'as' => 'setup.'], function () {
            Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\BusinessSetup\BusinessInfoController::class)->group(function () {
                Route::get('update-business-setting', 'updateBusinessSetting')->name('update-business-setting');
                Route::group(['prefix' => 'info', 'as' => 'info.'], function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::get('maintenance', 'maintenance')->name('maintenance');
                    Route::get('settings', 'settings')->name('settings');
                    Route::post('update-settings', 'updateSettings')->name('update-settings');
                });
            });

            Route::group(['prefix' => 'driver', 'as' => 'driver.'], function () {
                Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\BusinessSetup\DriverSettingController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                });
            });

            Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
                Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\BusinessSetup\CustomerSettingController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                });
            });

            Route::group(['prefix' => 'trip-fare', 'as' => 'trip-fare.'], function () {
                Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\BusinessSetup\TripFareSettingController::class)->group(function () {
                    Route::get('penalty', 'index')->name('penalty');
                    Route::get('trips', 'tripIndex')->name('trips');
                    Route::post('store', 'store')->name('store');
                    Route::group(['prefix' => 'cancellation-reason', 'as' =>'cancellation_reason.'],function (){
                        Route::post('store', 'storeCancellationReason')->name('store');
                        Route::get('edit/{id}', 'editCancellationReason')->name('edit');
                        Route::post('update/{id}', 'updateCancellationReason')->name('update');
                        Route::delete('delete/{id}', 'destroyCancellationReason')->name('delete');
                        Route::get('status', 'statusCancellationReason')->name('status');
                    });
                });
            });
        });

        Route::group(['prefix' => 'configuration', 'as' => 'configuration.'], function () {
            Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
                Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\Configuration\NotificationController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('store', 'store')->name('store');
                    Route::post('push/store', 'pushStore')->name('push-store');
                    Route::get('notification-settings', 'updateNotificationSettings')->name('notification-settings');
                });
            });
            Route::group(['prefix' => 'third-party', 'as' => 'third-party.'], function () {

                Route::group(['prefix' => 'payment-method', 'as' => 'payment-method.'], function () {
                    Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\Configuration\PaymentConfigController::class)->group(function () {
                        Route::get('/', 'paymentConfigGet')->name('index');
                        Route::put('update', 'paymentConfigSet')->name('update');
                    });
                });
                Route::group(['prefix' => 'sms-gateway', 'as' => 'sms-gateway.'], function () {
                    Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\Configuration\SMSConfigController::class)->group(function () {
                        Route::get('/', 'smsConfigGet')->name('index');
                        Route::put('update', 'smsConfigSet')->name('update');
                    });
                });

                Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\Configuration\ThirdPartyController::class)->group(function () {
                    Route::group(['prefix' => 'email-config', 'as' => 'email-config.'], function () {
                        Route::get('/', 'emailConfig')->name('index');
                        Route::post('update', 'updateEmailConfig')->name('update');
                    });

                    Route::group(['prefix' => 'recaptcha', 'as' => 'recaptcha.'], function () {
                        Route::get('/', 'recaptcha')->name('index');
                        Route::post('update', 'updateRecaptcha')->name('update');
                    });
                    Route::group(['prefix' => 'google-map', 'as' => 'google-map.'], function () {
                        Route::get('/', 'map')->name('index');
                        Route::post('update', 'updateMap')->name('update');
                    });
                });
            });

        });

        Route::group(['prefix' => 'pages-media', 'as' => 'pages-media.'], function () {
            Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\PagesMedia\PagesMediaController::class)->group(function () {
                Route::get('social-media', 'socialMedia')->name('social-media');
                Route::post('store-social-link', 'storeSocialLink')->name('store-social-link');
                Route::post('update-social-link/{id}', 'updateSocialLink')->name('update-social-link');
                Route::get('update-social-link-status', 'updateSocialStatus')->name('update-social-link-status');
                Route::delete('delete-social-link', 'deleteSocialLink')->name('delete-social-link');
                Route::get('log', 'log')->name('log');
                Route::group(['prefix' => 'business-page', 'as' => 'business-page.'], function () {
                    Route::get('/', 'businessPages')->name('index');
                    Route::post('update', 'businessPagesUpdate')->name('update');
                });
            });

            Route::group(['prefix' => 'landing-page', 'as' => 'landing-page.'], function () {
                Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\PagesMedia\LandingPageController::class)->group(function () {
                    Route::group(['prefix' => 'intro-section', 'as' => 'intro-section.'], function () {
                        Route::get('/', 'introSection')->name('index');
                        Route::post('update', 'updateIntroSection')->name('update');
                    });
                    Route::group(['prefix' => 'our-solutions', 'as' => 'our-solutions.'], function () {
                        Route::get('/', 'getOurSolutionsSectionView')->name('index');
                        Route::post('update-intro', 'updateOurSolutions')->name('update-intro');
                        Route::post('update', 'getAddOrUpdateOurSolutions')->name('update');
                        Route::get('status', 'statusOurSolutions')->name('status');
                        Route::get('edit/{id}', 'editOurSolutions')->name('edit');
                        Route::delete('delete/{id}', 'deleteOurSolutions')->name('delete');
                    });
                    Route::group(['prefix' => 'business-statistics', 'as' => 'business-statistics.'], function () {
                        Route::get('/', 'businessStatistics')->name('index');
                        Route::post('update', 'updateBusinessStatistics')->name('update');
                    });
                    Route::group(['prefix' => 'our-platform', 'as' => 'our-platform.'], function () {
                        Route::get('/', 'ourPlatform')->name('index');
                        Route::post('update', 'updateOurPlatform')->name('update');
                    });
                    Route::group(['prefix' => 'earn-money', 'as' => 'earn-money.'], function () {
                        Route::get('/', 'earnMoney')->name('index');
                        Route::post('update', 'updateEarnMoney')->name('update');
                    });
                    Route::group(['prefix' => 'testimonial', 'as' => 'testimonial.'], function () {
                        Route::get('/', 'testimonial')->name('index');
                        Route::post('update', 'updateTestimonial')->name('update');
                        Route::get('status', 'statusTestimonial')->name('status');
                        Route::get('edit/{id}', 'editTestimonial')->name('edit');
                        Route::delete('delete/{id}', 'deleteTestimonial')->name('delete');
                    });
                    Route::group(['prefix' => 'cta', 'as' => 'cta.'], function () {
                        Route::get('/', 'cta')->name('index');
                        Route::post('update', 'updateCta')->name('update');
                    });
                });
            });
        });

        Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\SystemSetting\SystemSettingController::class)->group(function () {
            Route::group(['prefix' => 'environment-setup', 'as' => 'environment-setup.'], function () {
                Route::get('/', 'envSetup')->name('index');
                Route::post('update', 'envUpdate')->name('update');
            });

            Route::group(['prefix' => 'clean-database', 'as' => 'clean-database.'], function () {
                Route::get('/', 'dbIndex')->name('index');
                Route::post('update', 'cleanDb')->name('clean');
            });
        });

        Route::group(['prefix' => 'languages', 'as' => 'languages.'], function () {
            Route::controller(\Modules\BusinessManagement\Http\Controllers\Web\New\Admin\SystemSetting\LanguageController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('add-new', 'store')->name('add-new');
                Route::get('update-status', 'updateStatus')->name('update-status');
                Route::get('update-default-status', 'updateDefaultStatus')->name('update-default-status');
                Route::get('delete/{lang}', 'delete')->name('delete');
                Route::get('translate/{lang}', 'translate')->name('translate');
                Route::post('update', 'update')->name('update');

                Route::post('translate-submit/{lang}', 'translateSubmit')->name('translate-submit');
                Route::any('auto-translate/{lang}', 'autoTranslate')->name('auto-translate');
            });
        });
    });
});
