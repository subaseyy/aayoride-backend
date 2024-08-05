<?php

namespace Modules\BusinessManagement\Service;

use App\Service\BaseService;
use Modules\BusinessManagement\Repository\SocialLinkRepositoryInterface;
use Modules\BusinessManagement\Service\Interface\SocialLinkServiceInterface;

class SocialLinkService extends BaseService implements SocialLinkServiceInterface
{
    protected $socialLinkRepository;

    public function __construct(SocialLinkRepositoryInterface $socialLinkRepository)
    {
        parent::__construct($socialLinkRepository);
        $this->socialLinkRepository = $socialLinkRepository;
    }
}
