<?php

namespace App\Models\Cat\DTO;

use App\Models\Base\DTO\ModelDTO;
use Illuminate\Support\Collection;

class CatDTO extends ModelDTO
{
    protected array $exceptAutoFields = [
        'mother',
        'possibleFathers',
        'possibleFatherIds',
    ];

    public ?int $id = null;

    public string $name;

    public string $gender;

    public int $age;

    public ?int $motherId = null;

    public ?CatDTO $mother = null;

    /**
     * @var array<int>
     */
    public array $possibleFatherIds = [];

    /**
     * @var Collection<int, CatDTO>|null
     */
    public ?Collection $possibleFathers = null;

    public ?string $createdAt = null;

    public ?string $updatedAt = null;
}
