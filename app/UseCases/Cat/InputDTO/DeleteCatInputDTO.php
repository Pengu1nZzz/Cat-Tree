<?php

namespace App\UseCases\Cat\InputDTO;

use App\UseCases\Base\DTO\BaseUseCaseDTO;

class DeleteCatInputDTO extends BaseUseCaseDTO
{
    public int $id;
}
