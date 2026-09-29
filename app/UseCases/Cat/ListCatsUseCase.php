<?php

namespace App\UseCases\Cat;

use App\Models\Base\DTO\PaginatedListDTO;
use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\UseCases\BaseUseCase;
use App\UseCases\Cat\InputDTO\ListCatsInputDTO;

class ListCatsUseCase extends BaseUseCase
{
    private PaginatedListDTO $outputDto;

    public function __construct(private readonly ICatDatabaseRepository $catDatabaseRepository)
    {
    }

    protected function getInputDTOClass(): ?string
    {
        return ListCatsInputDTO::class;
    }

    public function getOutputDto(): PaginatedListDTO
    {
        return $this->outputDto;
    }

    public function execute(): void
    {
        /** @var ListCatsInputDTO $inputDto */
        $inputDto = $this->inputDto;

        $this->outputDto = $this->catDatabaseRepository->paginate(
            page: $inputDto->page,
            perPage: $inputDto->perPage,
            age: $inputDto->age,
            minAge: $inputDto->minAge,
            maxAge: $inputDto->maxAge,
            gender: $inputDto->gender,
            motherId: $inputDto->motherId,
            fatherId: $inputDto->fatherId,
            sortBy: $inputDto->sortBy,
            sortDirection: $inputDto->sortDirection,
            search: $inputDto->search,
        );
    }
}
