<?php

namespace Modules\ChattingManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\ChattingManagement\Entities\ChannelList;
use Modules\ChattingManagement\Interfaces\ChannelListInterface;

class ChannelListRepository implements ChannelListInterface
{
    public function __construct(
        private ChannelList $list
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
        $query = $this->list
            ->query()
            ->with(['channel_users.user', 'last_channel_conversations.conversation_files'])
            ->whereHas('channel_users', function ($query) use ($attributes) {
                $query->where(['user_id' => $attributes['user_id']]);})
            ->orderBy('updated_at', 'DESC');
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
        return $this->list
            ->query()
            ->when($column, function ($query) use($column, $attributes) {
                $query->whereIn($column, $attributes['value']);
            })
            ->when(array_key_exists('channel_users', $attributes), function ($query) use ($attributes){
                $query->whereHas('channel_users', function ($query) use ($attributes) {
                    $query->where(['user_id' => $attributes['to']]);});
            })
            ->latest()
            ->first();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $channel = $this->list;
        DB::transaction(function () use ($channel, $attributes) {
            $channel->save();
            $channel->channel_users()->createMany([
                [
                    'user_id' => $attributes['user_id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'user_id' => $attributes['to'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        });

        return $channel;

    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $list = $this->getBy(column: 'id', value: '', attributes: [
            'value' => [$id],
        ]);
        $list->updated_at = now();
        $list->save();
        return $list;
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
