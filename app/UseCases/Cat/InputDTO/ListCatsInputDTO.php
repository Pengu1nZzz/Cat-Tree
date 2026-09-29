<?php

namespace App\UseCases\Cat\InputDTO;

use App\UseCases\Base\DTO\BaseUseCaseDTO;

class ListCatsInputDTO extends BaseUseCaseDTO
{
    public int $page = 1;

    public int $perPage = 15;

    public ?int $age = null;

    public ?int $minAge = null;

    public ?int $maxAge = null;

    public ?string $gender = null;

    public ?int $motherId = null;

    public ?int $fatherId = null;

    public ?string $search = null;

    public ?string $sortBy = null;

    public ?string $sortDirection = null;
}
