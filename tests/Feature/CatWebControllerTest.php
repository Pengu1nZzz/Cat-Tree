<?php

namespace Tests\Feature;

use App\Enum\GenderEnum;
use App\Models\Cat\Cat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatWebControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_cats_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('cats.index'));
    }

    public function test_index_displays_cats_and_filters(): void
    {
        Cat::factory()->create(['name' => 'Barsik', 'gender' => GenderEnum::MALE, 'age' => 3]);
        Cat::factory()->create(['name' => 'Murka', 'gender' => GenderEnum::FEMALE, 'age' => 5]);

        $response = $this->get(route('cats.index'));

        $response->assertOk();
        $response->assertSee('Barsik');
        $response->assertSee('Murka');
        $response->assertSee('Список кошек');
    }

    public function test_index_filters_by_gender_and_age(): void
    {
        Cat::factory()->create(['name' => 'Barsik', 'gender' => GenderEnum::MALE, 'age' => 3]);
        Cat::factory()->create(['name' => 'Murka', 'gender' => GenderEnum::FEMALE, 'age' => 5]);

        $response = $this->get(route('cats.index', ['gender' => 'female', 'age' => 5]));

        $response->assertOk();
        $response->assertSee('Murka');
        $response->assertDontSee('Barsik');
    }

    public function test_create_renders_form_with_available_parents(): void
    {
        $mother = Cat::factory()->create(['name' => 'QueenMother', 'gender' => GenderEnum::FEMALE, 'age' => 4]);
        $father = Cat::factory()->create(['name' => 'KingFather', 'gender' => GenderEnum::MALE, 'age' => 4]);

        $response = $this->get(route('cats.create'));

        $response->assertOk();
        $response->assertSee('Добавить новую кошку');
        $response->assertSee('QueenMother');
        $response->assertSee('KingFather');
    }

    public function test_store_creates_cat_and_redirects(): void
    {
        $mother = Cat::factory()->create(['gender' => GenderEnum::FEMALE, 'age' => 4]);
        $father1 = Cat::factory()->create(['gender' => GenderEnum::MALE, 'age' => 3]);
        $father2 = Cat::factory()->create(['gender' => GenderEnum::MALE, 'age' => 5]);

        $response = $this->post(route('cats.store'), [
            'name' => 'KittenOne',
            'gender' => 'female',
            'age' => 0,
            'mother_id' => $mother->id,
            'father_ids' => [$father1->id, $father2->id],
        ]);

        $cat = Cat::where('name', 'KittenOne')->first();
        $this->assertNotNull($cat);
        $this->assertEquals($mother->id, $cat->mother_id);
        $this->assertCount(2, $cat->possibleFathers);

        $response->assertRedirect(route('cats.show', $cat->id));
        $response->assertSessionHas('success');
    }

    public function test_show_displays_cat_details_mother_fathers_and_offspring(): void
    {
        $mother = Cat::factory()->create(['name' => 'MamaCat', 'gender' => GenderEnum::FEMALE, 'age' => 5]);
        $father = Cat::factory()->create(['name' => 'PapaCat', 'gender' => GenderEnum::MALE, 'age' => 6]);

        $cat = Cat::factory()->create([
            'name' => 'MainCat',
            'gender' => GenderEnum::FEMALE,
            'age' => 2,
            'mother_id' => $mother->id,
        ]);
        $cat->possibleFathers()->attach($father->id);

        $kitten = Cat::factory()->create([
            'name' => 'BabyKitten',
            'gender' => GenderEnum::MALE,
            'age' => 0,
            'mother_id' => $cat->id,
        ]);

        $response = $this->get(route('cats.show', $cat->id));

        $response->assertOk();
        $response->assertSee('MainCat');
        $response->assertSee('MamaCat');
        $response->assertSee('PapaCat');
        $response->assertSee('BabyKitten');
    }

    public function test_edit_renders_edit_form(): void
    {
        $cat = Cat::factory()->create(['name' => 'OldName', 'gender' => GenderEnum::MALE, 'age' => 2]);

        $response = $this->get(route('cats.edit', $cat->id));

        $response->assertOk();
        $response->assertSee('Редактировать данные кошки');
        $response->assertSee('OldName');
    }

    public function test_update_modifies_cat_and_redirects(): void
    {
        $cat = Cat::factory()->create(['name' => 'InitialName', 'gender' => GenderEnum::MALE, 'age' => 2]);

        $response = $this->put(route('cats.update', $cat->id), [
            'name' => 'UpdatedName',
            'gender' => 'male',
            'age' => 3,
        ]);

        $response->assertRedirect(route('cats.show', $cat->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cats', [
            'id' => $cat->id,
            'name' => 'UpdatedName',
            'age' => 3,
        ]);
    }

    public function test_destroy_deletes_cat_and_redirects(): void
    {
        $cat = Cat::factory()->create(['name' => 'ToDelete']);

        $response = $this->delete(route('cats.destroy', $cat->id));

        $response->assertRedirect(route('cats.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('cats', ['id' => $cat->id]);
    }
}
