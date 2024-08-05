<?php

namespace Modules\ParcelManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ParcelWeightInterface extends BaseRepositoryInterface
{
    /**
    * Download functionalities
    * @param array $attributes
    * @return mixed
    */
    public function download(array $attributes = []):mixed;


    public function trashed(array $attributes);

    public function restore(string $id);

    public function permanentDelete(string $id);

}
