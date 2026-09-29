<?php

use App\Enum\UseCaseSystemNamesEnum;
use App\UseCases\Cat\CreateCatUseCase;
use App\UseCases\Cat\DeleteCatUseCase;
use App\UseCases\Cat\GetCatUseCase;
use App\UseCases\Cat\ListCatsUseCase;
use App\UseCases\Cat\UpdateCatUseCase;

return [
    'mapping' => [
        UseCaseSystemNamesEnum::CAT_LIST->value => ListCatsUseCase::class,
        UseCaseSystemNamesEnum::CAT_GET->value => GetCatUseCase::class,
        UseCaseSystemNamesEnum::CAT_CREATE->value => CreateCatUseCase::class,
        UseCaseSystemNamesEnum::CAT_UPDATE->value => UpdateCatUseCase::class,
        UseCaseSystemNamesEnum::CAT_DELETE->value => DeleteCatUseCase::class,
    ],
];
