<?php

namespace Modules\PromotionManagement\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "time_period" => $this->time_period,
            "display_position" => $this->display_position,
            "redirect_link" => $this->redirect_link,
            "banner_group" => $this->banner_group,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "image" => $this->image,
        ];
    }
}
