<?php

namespace App\Models\Base\DTO;

final class PaginatedListDTO extends ListDTO
{
    public int $currentPage;

    public int $lastPage;

    public int $perPage;
}
