<?php

namespace Tests\Feature;

use App\Enum\GenderEnum;
use App\Models\Cat\Cat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_cats_with_pagination(): void
    {
        Cat::factory()->count(20)->create();

        $response = $this->getJson('/api/v1/cats?page=1&per_page=15');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'gender',
                        'age',
                        'mother_id',
                        'mother',
                        'possible_fathers',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);

        $this->assertCount(15, $response->json('data'));
        $this->assertSame(20, $response->json('meta.total'));
    }

    public function test_can_filter_cats_by_age(): void
    {
        Cat::factory()->create(['name' => 'Kitten', 'age' => 1]);
        Cat::factory()->create(['name' => 'Adult', 'age' => 4]);
        Cat::factory()->create(['name' => 'Old', 'age' => 10]);

        $response = $this->getJson('/api/v1/cats?age=4');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Adult', $data[0]['name']);
        $this->assertSame(4, $data[0]['age']);
    }

    public function test_can_filter_cats_by_age_range(): void
    {
        Cat::factory()->create(['name' => 'Young', 'age' => 1]);
        Cat::factory()->create(['name' => 'Middle1', 'age' => 3]);
        Cat::factory()->create(['name' => 'Middle2', 'age' => 5]);
        Cat::factory()->create(['name' => 'Senior', 'age' => 9]);

        $response = $this->getJson('/api/v1/cats?min_age=3&max_age=5');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Middle1', $names);
        $this->assertContains('Middle2', $names);
        $this->assertNotContains('Young', $names);
        $this->assertNotContains('Senior', $names);
    }

    public function test_can_filter_cats_by_gender(): void
    {
        Cat::factory()->male()->create(['name' => 'Tom']);
        Cat::factory()->female()->create(['name' => 'Misty']);

        $response = $this->getJson('/api/v1/cats?gender=female');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Misty', $data[0]['name']);
        $this->assertSame(GenderEnum::FEMALE->value, $data[0]['gender']);
    }

    public function test_can_filter_cats_by_gender_and_age(): void
    {
        Cat::factory()->male()->create(['name' => 'Tom1', 'age' => 2]);
        Cat::factory()->male()->create(['name' => 'Tom2', 'age' => 4]);
        Cat::factory()->female()->create(['name' => 'Misty1', 'age' => 2]);
        Cat::factory()->female()->create(['name' => 'Misty2', 'age' => 4]);

        $response = $this->getJson('/api/v1/cats?gender=male&age=4');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Tom2', $data[0]['name']);
    }

    public function test_can_get_single_cat_with_mother_and_possible_fathers(): void
    {
        $mother = Cat::factory()->female()->create(['name' => 'MotherCat', 'age' => 4]);
        $father1 = Cat::factory()->male()->create(['name' => 'Father1', 'age' => 5]);
        $father2 = Cat::factory()->male()->create(['name' => 'Father2', 'age' => 6]);

        $kitten = Cat::factory()->create([
            'name' => 'Kitten',
            'gender' => GenderEnum::MALE->value,
            'age' => 0,
            'mother_id' => $mother->id,
        ]);
        $kitten->possibleFathers()->attach([$father1->id, $father2->id]);

        $response = $this->getJson("/api/v1/cats/{$kitten->id}");

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $kitten->id,
                    'name' => 'Kitten',
                    'gender' => GenderEnum::MALE->value,
                    'age' => 0,
                    'mother_id' => $mother->id,
                    'mother' => [
                        'id' => $mother->id,
                        'name' => 'MotherCat',
                        'gender' => GenderEnum::FEMALE->value,
                        'age' => 4,
                    ],
                ],
            ]);

        $fathers = $response->json('data.possible_fathers');
        $this->assertCount(2, $fathers);
        $fatherNames = collect($fathers)->pluck('name')->all();
        $this->assertContains('Father1', $fatherNames);
        $this->assertContains('Father2', $fatherNames);
    }

    public function test_returns_404_when_cat_not_found(): void
    {
        $response = $this->getJson('/api/v1/cats/999999');

        $response->assertNotFound();
    }

    public function test_can_create_cat_without_parents(): void
    {
        $payload = [
            'name' => 'Simba',
            'gender' => GenderEnum::MALE->value,
            'age' => 2,
        ];

        $response = $this->postJson('/api/v1/cats', $payload);

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'name' => 'Simba',
                    'gender' => GenderEnum::MALE->value,
                    'age' => 2,
                    'mother_id' => null,
                    'mother' => null,
                    'possible_fathers' => [],
                ],
            ]);

        $this->assertDatabaseHas('cats', [
            'name' => 'Simba',
            'gender' => GenderEnum::MALE->value,
            'age' => 2,
            'mother_id' => null,
        ]);
    }

    public function test_can_create_cat_with_mother_and_multiple_possible_fathers(): void
    {
        $mother = Cat::factory()->female()->create(['name' => 'Luna']);
        $father1 = Cat::factory()->male()->create(['name' => 'Milo']);
        $father2 = Cat::factory()->male()->create(['name' => 'Oliver']);

        $payload = [
            'name' => 'Kitten Leo',
            'gender' => GenderEnum::MALE->value,
            'age' => 0,
            'mother_id' => $mother->id,
            'possible_father_ids' => [$father1->id, $father2->id],
        ];

        $response = $this->postJson('/api/v1/cats', $payload);

        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'name' => 'Kitten Leo',
                    'gender' => GenderEnum::MALE->value,
                    'age' => 0,
                    'mother_id' => $mother->id,
                    'mother' => [
                        'id' => $mother->id,
                        'name' => 'Luna',
                    ],
                ],
            ]);

        $newCatId = $response->json('data.id');

        $this->assertDatabaseHas('cats', [
            'id' => $newCatId,
            'name' => 'Kitten Leo',
            'mother_id' => $mother->id,
        ]);

        $this->assertDatabaseHas('cat_fathers', [
            'cat_id' => $newCatId,
            'father_id' => $father1->id,
        ]);

        $this->assertDatabaseHas('cat_fathers', [
            'cat_id' => $newCatId,
            'father_id' => $father2->id,
        ]);
    }

    public function test_cannot_create_cat_with_male_as_mother(): void
    {
        $maleCat = Cat::factory()->male()->create(['name' => 'Felix']);

        $payload = [
            'name' => 'Invalid Kitten',
            'gender' => GenderEnum::FEMALE->value,
            'age' => 0,
            'mother_id' => $maleCat->id,
        ];

        $response = $this->postJson('/api/v1/cats', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mother_id']);
    }

    public function test_cannot_create_cat_with_female_as_father(): void
    {
        $femaleCat = Cat::factory()->female()->create(['name' => 'Cleo']);

        $payload = [
            'name' => 'Invalid Kitten',
            'gender' => GenderEnum::MALE->value,
            'age' => 0,
            'possible_father_ids' => [$femaleCat->id],
        ];

        $response = $this->postJson('/api/v1/cats', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['father_ids']);
    }

    public function test_cannot_create_cat_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/cats', [
            'name' => '',
            'gender' => 'unknown_gender',
            'age' => -5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'gender', 'age']);
    }

    public function test_can_update_cat(): void
    {
        $cat = Cat::factory()->female()->create(['name' => 'Original', 'age' => 1]);
        $mother = Cat::factory()->female()->create(['name' => 'New Mother']);
        $father = Cat::factory()->male()->create(['name' => 'New Father']);

        $payload = [
            'name' => 'Updated Name',
            'gender' => GenderEnum::FEMALE->value,
            'age' => 2,
            'mother_id' => $mother->id,
            'possible_father_ids' => [$father->id],
        ];

        $response = $this->putJson("/api/v1/cats/{$cat->id}", $payload);

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $cat->id,
                    'name' => 'Updated Name',
                    'age' => 2,
                    'mother_id' => $mother->id,
                ],
            ]);

        $this->assertDatabaseHas('cats', [
            'id' => $cat->id,
            'name' => 'Updated Name',
            'age' => 2,
            'mother_id' => $mother->id,
        ]);

        $this->assertDatabaseHas('cat_fathers', [
            'cat_id' => $cat->id,
            'father_id' => $father->id,
        ]);
    }

    public function test_cannot_set_cat_as_its_own_mother_or_father(): void
    {
        $cat = Cat::factory()->female()->create(['name' => 'Self Cat']);

        $responseMother = $this->putJson("/api/v1/cats/{$cat->id}", [
            'name' => 'Self Cat',
            'gender' => GenderEnum::FEMALE->value,
            'age' => 3,
            'mother_id' => $cat->id,
        ]);

        $responseMother->assertStatus(422)
            ->assertJsonValidationErrors(['mother_id']);

        $responseFather = $this->putJson("/api/v1/cats/{$cat->id}", [
            'name' => 'Self Cat',
            'gender' => GenderEnum::MALE->value,
            'age' => 3,
            'possible_father_ids' => [$cat->id],
        ]);

        $responseFather->assertStatus(422)
            ->assertJsonValidationErrors(['father_ids']);
    }

    public function test_can_delete_cat(): void
    {
        $cat = Cat::factory()->create();

        $response = $this->deleteJson("/api/v1/cats/{$cat->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('cats', ['id' => $cat->id]);
    }
}
