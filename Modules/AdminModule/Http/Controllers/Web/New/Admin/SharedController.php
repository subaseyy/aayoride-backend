<?php

namespace Modules\AdminModule\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\AdminModule\Service\Interface\AdminNotificationServiceInterface;
use Modules\BusinessManagement\Service\Interface\BusinessSettingServiceInterface;
use Modules\UserManagement\Service\Interface\AppNotificationServiceInterface;

class SharedController extends BaseController
{
    protected $adminNotificationService;
    protected $businessSettingService;

    public function __construct(AdminNotificationServiceInterface $adminNotificationService, BusinessSettingServiceInterface $businessSettingService)
    {
        parent::__construct($adminNotificationService);
        $this->adminNotificationService = $adminNotificationService;
        $this->businessSettingService = $businessSettingService;
    }

    public function getNotifications()
    {
        $notification = $this->adminNotificationService->getBy(criteria: ['is_seen' => false], orderBy: ['created_at' => 'desc']);
        return response()->json(view('adminmodule::partials._notifications', compact('notification'))->render());
    }

    public function seenNotification(Request $request)
    {
        $notification = $this->adminNotificationService->update(id: $request->id, data: ['is_seen' => true]);
        return response()->json($notification);
    }

    public function lang($locale)
    {
        $direction = 'ltr';
        $languages = $this->businessSettingService->findOneBy(criteria: ['key_name' => SYSTEM_LANGUAGE])?->value ?? [['code' => 'en', 'direction' => 'ltr']];
        foreach ($languages as $data) {
            if ($data['code'] == $locale) {
                $direction = $data['direction'] ?? 'ltr';
            }
        }
        session()->put('locale', $locale);
        Session::put('direction', $direction);
        return redirect()->back();
    }
}
