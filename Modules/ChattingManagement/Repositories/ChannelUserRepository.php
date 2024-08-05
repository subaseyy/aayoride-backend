<?php

namespace Modules\ChattingManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\ChattingManagement\Entities\ChannelUser;
use Modules\ChattingManagement\Interfaces\ChannelUserInterface;

class ChannelUserRepository implements ChannelUserInterface
{
    public function __construct(
        private ChannelUser $user
    )
    {
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return LengthAwarePaginator|array|Collection
     */
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $query = $this->user
            ->query()
            ->where(['user_id' => $attributes['user_id']]);
        if ($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }
        return $query->paginate($limit);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        // TODO: Implement getBy() method.
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        // TODO: Implement store() method.
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $oparator = $attributes['is_read'] ? '=' : '!=';
        $user = $this->user->query()
            ->where('channel_id', $attributes['channel_id'])
            ->where('user_id', $oparator, $attributes['user_id'])
            ->firstOrFail();
        $user->is_read = $attributes['is_read'];
        $user->save();

        return $user;
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }
}
