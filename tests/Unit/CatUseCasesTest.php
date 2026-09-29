<?php

namespace Tests\Unit;

use App\Models\Base\DTO\PaginatedListDTO;
use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\Models\Cat\DTO\CatDTO;
use App\UseCases\Cat\CreateCatUseCase;
use App\UseCases\Cat\DeleteCatUseCase;
use App\UseCases\Cat\GetCatUseCase;
use App\UseCases\Cat\InputDTO\CreateCatInputDTO;
use App\UseCases\Cat\InputDTO\DeleteCatInputDTO;
use App\UseCases\Cat\InputDTO\GetCatInputDTO;
use App\UseCases\Cat\InputDTO\ListCatsInputDTO;
use App\UseCases\Cat\InputDTO\UpdateCatInputDTO;
use App\UseCases\Cat\ListCatsUseCase;
use App\UseCases\Cat\UpdateCatUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;

class CatUseCasesTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_list_cats_use_case(): void
    {
        $repository = Mockery::mock(ICatDatabaseRepository::class);
        $paginatedResult = new PaginatedListDTO();
        $paginatedResult->count = 1;
        $paginatedResult->currentPage = 1;
        $paginatedResult->lastPage = 1;
        $paginatedResult->perPage = 15;
        $paginatedResult->items = collect([new CatDTO()]);

        $repository->shouldReceive('paginate')
            ->once()
            ->with(1, 15, 3, null, null, 'female', null, null, null, null, null)
            ->andReturn($paginatedResult);

        $useCase = new ListCatsUseCase($repository);
        $inputDto = new ListCatsInputDTO();
        $inputDto->page = 1;
        $inputDto->perPage = 15;
        $inputDto->age = 3;
        $inputDto->gender = 'female';

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $this->assertSame($paginatedResult, $useCase->getOutputDto());
    }

    public function test_get_cat_use_case(): void
    {
        $repository = Mockery::mock(ICatDatabaseRepository::class);
        $catDto = new CatDTO();
        $catDto->id = 10;
        $catDto->name = 'Kitty';

        $repository->shouldReceive('findById')
            ->once()
            ->with(10)
            ->andReturn($catDto);

        $useCase = new GetCatUseCase($repository);
        $inputDto = new GetCatInputDTO();
        $inputDto->id = 10;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $this->assertSame($catDto, $useCase->getOutputDto());
    }

    public function test_create_cat_use_case(): void
    {
        $repository = Mockery::mock(ICatDatabaseRepository::class);
        $catDto = new CatDTO();
        $catDto->id = 1;
        $catDto->name = 'Milo';
        $catDto->gender = 'male';
        $catDto->age = 2;

        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (CatDTO $dto) {
                return $dto->name === 'Milo' && $dto->gender === 'male' && $dto->age === 2;
            }))
            ->andReturn($catDto);

        $useCase = new CreateCatUseCase($repository);
        $inputDto = new CreateCatInputDTO();
        $inputDto->name = 'Milo';
        $inputDto->gender = 'male';
        $inputDto->age = 2;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $this->assertSame($catDto, $useCase->getOutputDto());
    }

    public function test_update_cat_use_case(): void
    {
        $repository = Mockery::mock(ICatDatabaseRepository::class);
        $catDto = new CatDTO();
        $catDto->id = 1;
        $catDto->name = 'Milo Updated';

        $repository->shouldReceive('update')
            ->once()
            ->with(1, Mockery::on(function (CatDTO $dto) {
                return $dto->name === 'Milo Updated';
            }))
            ->andReturn($catDto);

        $useCase = new UpdateCatUseCase($repository);
        $inputDto = new UpdateCatInputDTO();
        $inputDto->id = 1;
        $inputDto->name = 'Milo Updated';
        $inputDto->gender = 'male';
        $inputDto->age = 3;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $this->assertSame($catDto, $useCase->getOutputDto());
    }

    public function test_delete_cat_use_case(): void
    {
        $repository = Mockery::mock(ICatDatabaseRepository::class);

        $repository->shouldReceive('delete')
            ->once()
            ->with(5);

        $useCase = new DeleteCatUseCase($repository);
        $inputDto = new DeleteCatInputDTO();
        $inputDto->id = 5;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $this->assertTrue(true);
    }
}
