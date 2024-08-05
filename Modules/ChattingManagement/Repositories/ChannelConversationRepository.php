<?php

namespace Modules\ChattingManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\ChattingManagement\Entities\ChannelConversation;
use Modules\ChattingManagement\Interfaces\ChannelConversationInterface;

class ChannelConversationRepository implements ChannelConversationInterface
{

    public function __construct(
        private ChannelConversation $conversation
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
        $query = $this->conversation::query()
            ->when(!empty($relations), fn($query) => $query->with($relations))
            ->where(['channel_id' => $attributes['channel_id']])
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes){
                $query->with($attributes['relations']);
            })
            ->whereHas('channel.channel_users', function ($query) use ($attributes) {
                $query->where(['user_id' => $attributes['user_id']]);})
            ->latest();
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
        $conversation = $this->conversation;
        $conversation->channel_id = $attributes['channel_id'];
        $conversation->message = $attributes['message'] ?? null;
        $conversation->user_id = $attributes['user_id'];
        $conversation->save();
        if (array_key_exists('files', $attributes)) {
            foreach ($attributes['files'] as $file) {
                $extension = $file->getClientOriginalExtension();
                $conversation->conversation_files()->create([
                        'file_name' => fileUploader('conversation/', $extension, $file),
                        'file_type' => $extension,
                ]);
            }
        }
        return $conversation;
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
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
