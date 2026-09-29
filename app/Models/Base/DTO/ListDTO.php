<?php

namespace App\Models\Base\DTO;
use Illuminate\Support\Collection;
class ListDTO
{
    /**
     * Item collection
     * @var Collection
     */
    public Collection $items;

    /**
     * Total count value
     * @var int
     */
    public int $count;
}
