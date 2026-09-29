<?php

namespace App\Models\Cat\Contracts;

use App\Models\Base\DTO\PaginatedListDTO;
use App\Models\Cat\DTO\CatDTO;

interface ICatDatabaseRepository
{
    public function paginate(
        int $page,
        int $perPage,
        ?int $age = null,
        ?int $minAge = null,
        ?int $maxAge = null,
        ?string $gender = null,
        ?int $motherId = null,
        ?int $fatherId = null,
        ?string $sortBy = null,
        ?string $sortDirection = null,
        ?string $search = null,
    ): PaginatedListDTO;

    public function findById(int $id): ?CatDTO;

    public function create(CatDTO $dto): CatDTO;

    public function update(int $id, CatDTO $dto): ?CatDTO;

    public function delete(int $id): void;
}
