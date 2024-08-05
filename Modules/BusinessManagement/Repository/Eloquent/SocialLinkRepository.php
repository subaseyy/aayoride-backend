<?php

namespace Modules\BusinessManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Modules\BusinessManagement\Entities\SocialLink;
use Modules\BusinessManagement\Repository\SocialLinkRepositoryInterface;

class SocialLinkRepository extends BaseRepository implements SocialLinkRepositoryInterface
{
    public function __construct(SocialLink $model)
    {
        parent::__construct($model);
    }
}
