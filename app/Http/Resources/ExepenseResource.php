<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ExepenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'date' => $this->expense_date->translatedFormat('d M Y'),
            'category' => [
                'id' => $this->category->id,
                'title' => $this->category->title
            ],
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'note' => $this->note,
            'image' => $this->image,
            'imageUrl' => $this->image ? Storage::disk('public')->url($this->image) : null,
        ];
    }
}
