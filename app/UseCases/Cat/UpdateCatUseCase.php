<?php

namespace App\UseCases\Cat;

use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\Models\Cat\DTO\CatDTO;
use App\UseCases\BaseUseCase;
use App\UseCases\Cat\InputDTO\UpdateCatInputDTO;

class UpdateCatUseCase extends BaseUseCase
{
    private ?CatDTO $outputDto = null;

    public function __construct(private readonly ICatDatabaseRepository $catDatabaseRepository)
    {
    }

    protected function getInputDTOClass(): ?string
    {
        return UpdateCatInputDTO::class;
    }

    public function getOutputDto(): ?CatDTO
    {
        return $this->outputDto;
    }

    public function execute(): void
    {
        /** @var UpdateCatInputDTO $inputDto */
        $inputDto = $this->inputDto;

        $catDto = new CatDTO();
        $catDto->id = $inputDto->id;
        $catDto->name = $inputDto->name;
        $catDto->gender = $inputDto->gender;
        $catDto->age = $inputDto->age;
        $catDto->motherId = $inputDto->motherId;
        $catDto->possibleFatherIds = $inputDto->possibleFatherIds;

        $this->outputDto = $this->catDatabaseRepository->update($inputDto->id, $catDto);
    }
}
