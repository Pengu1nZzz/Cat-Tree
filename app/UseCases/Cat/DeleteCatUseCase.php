<?php

namespace App\UseCases\Cat;

use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\UseCases\BaseUseCase;
use App\UseCases\Cat\InputDTO\DeleteCatInputDTO;

class DeleteCatUseCase extends BaseUseCase
{
    public function __construct(private readonly ICatDatabaseRepository $catDatabaseRepository)
    {
    }

    protected function getInputDTOClass(): ?string
    {
        return DeleteCatInputDTO::class;
    }

    public function execute(): void
    {
        /** @var DeleteCatInputDTO $inputDto */
        $inputDto = $this->inputDto;

        $this->catDatabaseRepository->delete($inputDto->id);
    }
}
