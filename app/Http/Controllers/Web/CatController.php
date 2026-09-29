<?php

namespace App\Http\Controllers\Web;

use App\Enum\GenderEnum;
use App\Enum\UseCaseSystemNamesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cat\StoreCatRequest;
use App\Http\Requests\Cat\UpdateCatRequest;
use App\Models\Cat\Cat;
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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CatController extends Controller
{
    public function __construct(private readonly UseCaseFactory $useCaseFactory)
    {
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function index(Request $request): View
    {
        /** @var ListCatsUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_LIST->value);

        $inputDto = new ListCatsInputDTO();
        $inputDto->page = max(1, $request->integer('page', 1));
        $inputDto->perPage = max(1, min(100, $request->integer('per_page', 10)));
        $inputDto->age = $request->has('age') && $request->input('age') !== null && $request->input('age') !== '' ? $request->integer('age') : null;
        $inputDto->minAge = $request->has('min_age') && $request->input('min_age') !== null && $request->input('min_age') !== '' ? $request->integer('min_age') : null;
        $inputDto->maxAge = $request->has('max_age') && $request->input('max_age') !== null && $request->input('max_age') !== '' ? $request->integer('max_age') : null;
        $inputDto->gender = $request->filled('gender') ? $request->string('gender')->trim()->toString() : null;
        $inputDto->motherId = $request->filled('mother_id') ? $request->integer('mother_id') : null;
        $inputDto->fatherId = $request->filled('father_id') ? $request->integer('father_id') : null;
        $inputDto->search = $request->filled('search') ? $request->string('search')->trim()->toString() : null;
        $inputDto->sortBy = $request->filled('sort_by') ? $request->string('sort_by')->trim()->toString() : null;
        $inputDto->sortDirection = $request->filled('sort_direction') ? $request->string('sort_direction')->trim()->toString() : null;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $result = $useCase->getOutputDto();

        $paginator = new LengthAwarePaginator(
            items: $result->items,
            total: $result->count,
            perPage: $result->perPage,
            currentPage: $result->currentPage,
            options: [
                'path' => route('cats.index'),
                'query' => $request->query(),
            ]
        );

        $allMothers = Cat::query()->where('gender', GenderEnum::FEMALE)->orderBy('name')->get();
        $allFathers = Cat::query()->where('gender', GenderEnum::MALE)->orderBy('name')->get();

        return view('cats.index', [
            'cats' => $paginator,
            'filters' => [
                'age' => $inputDto->age,
                'min_age' => $inputDto->minAge,
                'max_age' => $inputDto->maxAge,
                'gender' => $inputDto->gender,
                'mother_id' => $inputDto->motherId,
                'father_id' => $inputDto->fatherId,
                'search' => $inputDto->search,
                'sort_by' => $inputDto->sortBy,
                'sort_direction' => $inputDto->sortDirection,
            ],
            'allMothers' => $allMothers,
            'allFathers' => $allFathers,
        ]);
    }

    public function create(): View
    {
        $mothers = Cat::query()->where('gender', GenderEnum::FEMALE)->orderBy('name')->get();
        $fathers = Cat::query()->where('gender', GenderEnum::MALE)->orderBy('name')->get();

        return view('cats.create', [
            'mothers' => $mothers,
            'fathers' => $fathers,
        ]);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function store(StoreCatRequest $request): RedirectResponse
    {
        /** @var CreateCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_CREATE->value);

        $inputDto = new CreateCatInputDTO();
        $inputDto->name = $request->string('name')->trim()->toString();
        $inputDto->gender = $request->string('gender')->trim()->toString();
        $inputDto->age = $request->integer('age');
        $inputDto->motherId = $request->filled('mother_id') ? $request->integer('mother_id') : null;
        $inputDto->possibleFatherIds = $request->getFatherIds();

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        /** @var CatDTO $created */
        $created = $useCase->getOutputDto();

        return redirect()->route('cats.show', $created->id)
            ->with('success', "Кошка \"{$created->name}\" успешно добавлена!");
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function show(int $id): View
    {
        /** @var GetCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_GET->value);

        $inputDto = new GetCatInputDTO();
        $inputDto->id = $id;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $cat = $useCase->getOutputDto();

        if (!$cat) {
            abort(404, 'Кошка не найдена.');
        }

        // Fetch children
        $children = Cat::query()
            ->where('mother_id', $id)
            ->orWhereHas('possibleFathers', function ($q) use ($id) {
                $q->where('cats.id', $id);
            })
            ->with(['mother', 'possibleFathers'])
            ->get();

        return view('cats.show', [
            'cat' => $cat,
            'children' => $children,
        ]);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function edit(int $id): View
    {
        /** @var GetCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_GET->value);

        $inputDto = new GetCatInputDTO();
        $inputDto->id = $id;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        $cat = $useCase->getOutputDto();

        if (!$cat) {
            abort(404, 'Кошка не найдена.');
        }

        $mothers = Cat::query()
            ->where('gender', GenderEnum::FEMALE)
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();

        $fathers = Cat::query()
            ->where('gender', GenderEnum::MALE)
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();

        return view('cats.edit', [
            'cat' => $cat,
            'mothers' => $mothers,
            'fathers' => $fathers,
        ]);
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function update(int $id, UpdateCatRequest $request): RedirectResponse
    {
        /** @var UpdateCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_UPDATE->value);

        $inputDto = new UpdateCatInputDTO();
        $inputDto->id = $id;
        $inputDto->name = $request->string('name')->trim()->toString();
        $inputDto->gender = $request->string('gender')->trim()->toString();
        $inputDto->age = $request->integer('age');
        $inputDto->motherId = $request->filled('mother_id') ? $request->integer('mother_id') : null;
        $inputDto->possibleFatherIds = $request->getFatherIds();

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        /** @var CatDTO|null $updated */
        $updated = $useCase->getOutputDto();

        if (!$updated) {
            abort(404, 'Кошка не найдена.');
        }

        return redirect()->route('cats.show', $updated->id)
            ->with('success', "Данные кошки \"{$updated->name}\" успешно обновлены!");
    }

    /**
     * @throws UseCaseNotFoundException
     * @throws BindingResolutionException
     */
    public function destroy(int $id): RedirectResponse
    {
        /** @var DeleteCatUseCase $useCase */
        $useCase = $this->useCaseFactory->createUseCase(UseCaseSystemNamesEnum::CAT_DELETE->value);

        $inputDto = new DeleteCatInputDTO();
        $inputDto->id = $id;

        $useCase->setInputDTO($inputDto);
        $useCase->execute();

        return redirect()->route('cats.index')
            ->with('success', 'Кошка успешно удалена.');
    }
}
