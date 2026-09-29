<?php

namespace App\Http\Requests\Cat;

use App\Enum\GenderEnum;
use App\Models\Cat\Cat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', Rule::enum(GenderEnum::class)],
            'age' => ['required', 'integer', 'min:0', 'max:100'],
            'mother_id' => [
                'nullable',
                'integer',
                'exists:cats,id',
            ],
            'father_ids' => ['nullable', 'array'],
            'father_ids.*' => ['integer', 'distinct', 'exists:cats,id'],
            'possible_father_ids' => ['nullable', 'array'],
            'possible_father_ids.*' => ['integer', 'distinct', 'exists:cats,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $catId = (int) $this->route('id');

            $motherId = $this->input('mother_id');
            if ($motherId) {
                if ((int) $motherId === $catId) {
                    $validator->errors()->add('mother_id', 'A cat cannot be its own mother.');
                }

                $mother = Cat::find($motherId);
                if ($mother && $mother->gender !== GenderEnum::FEMALE) {
                    $validator->errors()->add('mother_id', 'The selected mother must be a female cat.');
                }
            }

            $fatherIds = $this->getFatherIds();
            if (!empty($fatherIds)) {
                if (in_array($catId, array_map('intval', $fatherIds), true)) {
                    $validator->errors()->add('father_ids', 'A cat cannot be its own father.');
                }

                $invalidFathers = Cat::whereIn('id', $fatherIds)
                    ->where('gender', '!=', GenderEnum::MALE->value)
                    ->pluck('id')
                    ->all();

                if (!empty($invalidFathers)) {
                    $validator->errors()->add('father_ids', 'All possible fathers must be male cats.');
                }
            }
        });
    }

    public function getFatherIds(): array
    {
        if ($this->has('possible_father_ids')) {
            return (array) $this->input('possible_father_ids', []);
        }

        return (array) $this->input('father_ids', []);
    }
}
