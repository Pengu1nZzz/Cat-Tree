<?php

namespace App\UseCases\Cat\InputDTO;

use App\UseCases\Base\DTO\BaseUseCaseDTO;

class CreateCatInputDTO extends BaseUseCaseDTO
{
    public string $name;

    public string $gender;

    public int $age;

    public ?int $motherId = null;

    /**
     * @var array<int>
     */
    public array $possibleFatherIds = [];
}
