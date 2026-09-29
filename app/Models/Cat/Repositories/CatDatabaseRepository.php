<?php

namespace App\Models\Cat\Repositories;

use App\Enum\GenderEnum;
use App\Models\Base\DTO\PaginatedListDTO;
use App\Models\Cat\Cat;
use App\Models\Cat\Contracts\ICatDatabaseRepository;
use App\Models\Cat\DTO\CatDTO;
use Illuminate\Database\Eloquent\Builder;

class CatDatabaseRepository implements ICatDatabaseRepository
{
    public function paginate(
        int $page,
        int $perPage,
        ?int $age = null,
        ?int $minAge = null,
        ?int $maxAge = null,
        ?string $gender = null,
        ?int $motherId = null,
        ?int $fatherId = null,
        ?string $sortBy = null,
        ?string $sortDirection = null,
        ?string $search = null,
    ): PaginatedListDTO {
        $query = Cat::query()->with(['mother', 'possibleFathers']);

        if ($age !== null) {
            $query->where('age', $age);
        }

        if ($minAge !== null) {
            $query->where('age', '>=', $minAge);
        }

        if ($maxAge !== null) {
            $query->where('age', '<=', $maxAge);
        }

        if ($gender !== null) {
            $query->where('gender', $gender);
        }

        if ($motherId !== null) {
            $query->where('mother_id', $motherId);
        }

        if ($fatherId !== null) {
            $query->whereHas('possibleFathers', function (Builder $q) use ($fatherId) {
                $q->where('cats.id', $fatherId);
            });
        }

        if ($search !== null && $search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sortBy && $sortDirection) {
            $sortColumns = ['id', 'name', 'gender', 'age', 'created_at'];
            if (in_array($sortBy, $sortColumns, true)) {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->latest('id');
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $dto = new PaginatedListDTO();
        $dto->count = $paginator->total();
        $dto->currentPage = $paginator->currentPage();
        $dto->lastPage = $paginator->lastPage();
        $dto->perPage = $paginator->perPage();
        $dto->items = $paginator->getCollection()->map(fn (Cat $cat) => $this->mapCat($cat));

        return $dto;
    }

    public function findById(int $id): ?CatDTO
    {
        $cat = Cat::query()
            ->with(['mother', 'possibleFathers'])
            ->find($id);

        return $cat ? $this->mapCat($cat) : null;
    }

    public function create(CatDTO $dto): CatDTO
    {
        /** @var Cat $cat */
        $cat = Cat::query()->create([
            'name' => $dto->name,
            'gender' => $dto->gender,
            'age' => $dto->age,
            'mother_id' => $dto->motherId,
        ]);

        if (!empty($dto->possibleFatherIds)) {
            $cat->possibleFathers()->sync($dto->possibleFatherIds);
        }

        return $this->mapCat($cat->fresh(['mother', 'possibleFathers']));
    }

    public function update(int $id, CatDTO $dto): ?CatDTO
    {
        /** @var Cat|null $cat */
        $cat = Cat::query()->find($id);

        if (!$cat) {
            return null;
        }

        $cat->update([
            'name' => $dto->name,
            'gender' => $dto->gender,
            'age' => $dto->age,
            'mother_id' => $dto->motherId,
        ]);

        $cat->possibleFathers()->sync($dto->possibleFatherIds);

        return $this->mapCat($cat->fresh(['mother', 'possibleFathers']));
    }

    public function delete(int $id): void
    {
        Cat::query()->whereKey($id)->delete();
    }

    private function mapCat(Cat $cat, bool $withRelations = true): CatDTO
    {
        $dto = new CatDTO();
        $dto->fillFromModel($cat);
        $dto->gender = $cat->gender instanceof GenderEnum ? $cat->gender->value : (string) $cat->gender;
        $dto->age = (int) $cat->age;
        $dto->motherId = $cat->mother_id !== null ? (int) $cat->mother_id : null;
        $dto->createdAt = $cat->created_at?->toIso8601String();
        $dto->updatedAt = $cat->updated_at?->toIso8601String();

        if ($withRelations) {
            $dto->mother = $cat->relationLoaded('mother') && $cat->mother
                ? $this->mapCat($cat->mother, false)
                : null;

            if ($cat->relationLoaded('possibleFathers')) {
                $dto->possibleFathers = $cat->possibleFathers->map(fn (Cat $father) => $this->mapCat($father, false));
                $dto->possibleFatherIds = $cat->possibleFathers->pluck('id')->all();
            }
        }

        return $dto;
    }
}
