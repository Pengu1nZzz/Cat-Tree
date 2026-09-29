<?php

namespace App\Http\Controllers\Api\V1;

use App\Enum\UseCaseSystemNamesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cat\StoreCatRequest;
use App\Http\Requests\Cat\UpdateCatRequest;
use App\Models\Base\DTO\PaginatedListDTO;
use App\Models\Cat\DTO\CatDTO;
use App\UseCases\Base\Exceptions\UseCaseNotFoundException;
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
use App\UseCases\UseCaseFactory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;

class CatController extends Controller
{
    public function __construct(private readonly UseCaseFactory $useCaseFactory)
    {
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function index(): JsonResponse
    {
        /** @var ListCatsUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_LIST->value);

        $inputDto = new ListCatsInputDTO();
        $inputDto->page = max(1, request()->integer('page', 1));
        $inputDto->perPage = max(1, min(100, request()->integer('per_page', 15)));
        $inputDto->age = request()->has('age') && request()->input('age') !== null ? request()->integer('age') : null;
        $inputDto->minAge = request()->has('min_age') && request()->input('min_age') !== null ? request()->integer('min_age') : null;
        $inputDto->maxAge = request()->has('max_age') && request()->input('max_age') !== null ? request()->integer('max_age') : null;
        $inputDto->gender = request()->filled('gender') ? request()->string('gender')->trim()->toString() : null;
        $inputDto->motherId = request()->has('mother_id') && request()->input('mother_id') !== null ? request()->integer('mother_id') : null;
        $inputDto->fatherId = request()->has('father_id') && request()->input('father_id') !== null ? request()->integer('father_id') : null;
        $inputDto->search = request()->filled('search') ? request()->string('search')->trim()->toString() : null;
        $inputDto->sortBy = request()->filled('sort_by') ? request()->string('sort_by')->trim()->toString() : null;
        $inputDto->sortDirection = request()->filled('sort_direction') ? request()->string('sort_direction')->trim()->toString() : null;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $result = $useCase->getOutputDto();

        return response()->json([
            'data' => $result->items->map(fn (CatDTO $cat) => $this->serializeCat($cat))->values(),
            'meta' => $this->serializeMeta($result),
        ]);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function show(int $id): JsonResponse
    {
        /** @var GetCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_GET->value);

        $inputDto = new GetCatInputDTO();
        $inputDto->id = $id;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $cat = $useCase->getOutputDto();

        if (!$cat) {
            abort(404, 'Cat not found.');
        }

        return response()->json([
            'data' => $this->serializeCat($cat),
        ]);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function store(StoreCatRequest $request): JsonResponse
    {
        /** @var CreateCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_CREATE->value);

        $inputDto = new CreateCatInputDTO();
        $inputDto->name = $request->string('name')->trim()->toString();
        $inputDto->gender = $request->string('gender')->trim()->toString();
        $inputDto->age = $request->integer('age');
        $inputDto->motherId = $request->has('mother_id') && $request->input('mother_id') !== null
            ? $request->integer('mother_id')
            : null;
        $inputDto->possibleFatherIds = $request->getFatherIds();

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        return response()->json([
            'data' => $this->serializeCat($useCase->getOutputDto()),
        ], 201);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function update(UpdateCatRequest $request, int $id): JsonResponse
    {
        /** @var UpdateCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_UPDATE->value);

        $inputDto = new UpdateCatInputDTO();
        $inputDto->id = $id;
        $inputDto->name = $request->string('name')->trim()->toString();
        $inputDto->gender = $request->string('gender')->trim()->toString();
        $inputDto->age = $request->integer('age');
        $inputDto->motherId = $request->has('mother_id') && $request->input('mother_id') !== null
            ? $request->integer('mother_id')
            : null;
        $inputDto->possibleFatherIds = $request->getFatherIds();

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $cat = $useCase->getOutputDto();

        if (!$cat) {
            abort(404, 'Cat not found.');
        }

        return response()->json([
            'data' => $this->serializeCat($cat),
        ]);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var DeleteCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_DELETE->value);

        $inputDto = new DeleteCatInputDTO();
        $inputDto->id = $id;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        return response()->json([], 204);
    }

    private function serializeMeta(PaginatedListDTO $dto): array
    {
        return [
            'current_page' => $dto->currentPage,
            'last_page' => $dto->lastPage,
            'per_page' => $dto->perPage,
            'total' => $dto->count,
        ];
    }

    private function serializeCat(CatDTO $cat): array
    {
        return [
            'id' => $cat->id,
            'name' => $cat->name,
            'gender' => $cat->gender,
            'age' => $cat->age,
            'mother_id' => $cat->motherId,
            'mother' => $cat->mother ? [
                'id' => $cat->mother->id,
                'name' => $cat->mother->name,
                'gender' => $cat->mother->gender,
                'age' => $cat->mother->age,
            ] : null,
            'possible_fathers' => $cat->possibleFathers
                ? $cat->possibleFathers->map(fn (CatDTO $father) => [
                    'id' => $father->id,
                    'name' => $father->name,
                    'gender' => $father->gender,
                    'age' => $father->age,
                ])->values()->all()
                : [],
            'created_at' => $cat->createdAt,
            'updated_at' => $cat->updatedAt,
        ];
    }
}
