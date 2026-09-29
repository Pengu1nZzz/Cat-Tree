<?php

namespace App\Models\Cat;

use App\Enum\GenderEnum;
use Database\Factories\CatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Cat
 *
 * @property int $id
 * @property string $name
 * @property GenderEnum $gender
 * @property int $age
 * @property int|null $mother_id
 * @property-read Cat|null $mother
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Cat> $possibleFathers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Cat> $childrenAsMother
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Cat> $childrenAsPossibleFather
 */
class Cat extends Model
{
    use HasFactory;

    protected $table = 'cats';

    protected $fillable = [
        'name',
        'gender',
        'age',
        'mother_id',
    ];

    protected $casts = [
        'gender' => GenderEnum::class,
        'age' => 'integer',
        'mother_id' => 'integer',
    ];

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Cat::class, 'mother_id');
    }

    public function possibleFathers(): BelongsToMany
    {
        return $this->belongsToMany(Cat::class, 'cat_fathers', 'cat_id', 'father_id')
            ->withTimestamps();
    }

    public function childrenAsMother(): HasMany
    {
        return $this->hasMany(Cat::class, 'mother_id');
    }

    public function childrenAsPossibleFather(): BelongsToMany
    {
        return $this->belongsToMany(Cat::class, 'cat_fathers', 'father_id', 'cat_id')
            ->withTimestamps();
    }

    protected static function newFactory(): CatFactory
    {
        return CatFactory::new();
    }
}
