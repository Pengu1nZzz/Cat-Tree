<?php

namespace App\UseCases\Cat;

use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\Models\Cat\DTO\CatDTO;
use App\UseCases\BaseUseCase;
use App\UseCases\Cat\InputDTO\GetCatInputDTO;

class GetCatUseCase extends BaseUseCase
{
    private ?CatDTO $outputDto = null;

    public function __construct(private readonly ICatDatabaseRepository $catDatabaseRepository)
    {
    }

    protected function getInputDTOClass(): ?string
    {
        return GetCatInputDTO::class;
    }

    public function getOutputDto(): ?CatDTO
    {
        return $this->outputDto;
    }

    public function execute(): void
    {
        /** @var GetCatInputDTO $inputDto */
        $inputDto = $this->inputDto;

        $this->outputDto = $this->catDatabaseRepository->findById($inputDto->id);
    }
}
