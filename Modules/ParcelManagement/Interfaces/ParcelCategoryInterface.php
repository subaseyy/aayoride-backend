<?php

namespace Modules\ParcelManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ParcelCategoryInterface extends BaseRepositoryInterface
{
    /**
     * Category wise parcels and its trip status
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $attributes
     * @return mixed
     */
    public function getCategorizedParcels(int $limit, int $offset, string $status_column, bool $dynamic_page = false, array $attributes = []):mixed;

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
