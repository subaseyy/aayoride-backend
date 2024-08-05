<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Modules\UserManagement\Repository\OtpVerificationRepositoryInterface;
use Modules\UserManagement\Service\Interface\OtpVerificationServiceInterface;

class OtpVerificationService extends BaseService implements OtpVerificationServiceInterface
{
    protected $otpVerificationRepository;

    public function __construct(OtpVerificationRepositoryInterface $otpVerificationRepository)
    {
        parent::__construct($otpVerificationRepository);
        $this->otpVerificationRepository = $otpVerificationRepository;
    }

    // Add your specific methods related to OtpVerificationService here
}
