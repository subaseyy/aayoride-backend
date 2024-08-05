<?php

namespace Modules\ReviewModule\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\ReviewModule\Entities\Review;
use Modules\ReviewModule\Interfaces\ReviewInterface;

class ReviewRepository implements ReviewInterface
{
    public function __construct(
        private Review $review
    )
    {}

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
        $query = $this->review
            ->query()
            ->when(!empty($relations), function ($query) use ($relations){
                $query->with($relations);
            })
            ->when(array_key_exists('column', $attributes), fn ($query) =>
                $query->where($attributes['column'], $attributes['value'])
            )
            ->when(array_key_exists('column_name', $attributes), fn ($query) =>
                $query->where($attributes['column_name'], $attributes['column_value'])
            )
            ->when(array_key_exists('whereBetween', $attributes), fn($query) =>
                $query->whereBetween('created_at', $attributes['whereBetween'])
            )
            ->has('trip')
            ->latest();

        if ($dynamic_page) {

            return $query->paginate(perPage: $limit, page: $offset);
        }

        return $query->paginate(paginationLimit());
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return mixed|Model
     */
    public function getBy(string $column, int|string $value, array $attributes = []): mixed
    {
        return $this->review->query()
            ->when(($attributes['relations'] ?? null), fn ($query) => $query->with($attributes['relations']))
            ->when($column && $value, fn ($query) => $query->where($column, $value))
            ->when(($attributes['column'] ?? null), fn($query) => $query->where($attributes['column'], $attributes['value']))->first();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function store(array $attributes): Model
    {
        $images = [];
        if (array_key_exists('images', $attributes)) {
            foreach ($attributes['images'] as $image) {
                $images[] = fileUploader('review/', 'png', $image);
            }
        }
        $review = $this->review;
        $review->trip_request_id = $attributes['ride_request_id'];
        $review->given_by = $attributes['given_by'];
        $review->received_by = $attributes['received_by'];
        $review->trip_type = $attributes['trip_type'];
        $review->rating = $attributes['rating'];
        $review->feedback = $attributes['feedback'];
        $review->images = $images;
        $review->save();

        return $review;
    }

    /**
     * @param array $attributes
     * @param string $id
     * @return Model
     */
    public function update(array $attributes, string $id): Model
    {
        $review = $this->getBy(column: 'id', value: $id);
        $images = [];
        if (array_key_exists('images', $attributes)) {
            if ($review->images) {
                foreach ($review->images as $img)
                    fileRemover('review/', $img);
            }
            foreach ($attributes['images'] as $image) {
                $images[] = fileUploader('review/', 'png', $image);
            }
        }

        array_key_exists('rating', $attributes) ? $review->rating = $attributes['rating'] : null;
        array_key_exists('feedback', $attributes) ? $review->feedback = $attributes['feedback'] : null;
        array_key_exists('is_saved', $attributes) ? $review->is_saved = $attributes['is_saved'] : null;
        $review->images = $images;
        $review->save();

        return $review;
    }

    /**
     * @param string $id
     * @return Model
     */
    public function destroy(string $id): Model
    {
        $review = $this->review->query()->where('id', $id)->first();
        $review?->delete();

        return $review;
    }
}
