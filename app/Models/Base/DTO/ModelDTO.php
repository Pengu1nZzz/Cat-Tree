<?php

namespace App\Models\Base\DTO;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelDTO
 * @package App\Models\Base\DTO
 */
abstract class ModelDTO
{

    /**
     * Convert snake to camel case
     *
     * @param string $input
     * @return string
     */
    protected function snakeToCamel(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
    }

    /**
     * Convert camel to snake case
     *
     * @param string $input
     * @return string
     */
    protected function camelToSnake(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    /**
     * Get field list
     *
     * @return array
     */
    protected function getFields(): array
    {
        $result = [];
        $reflection = new \ReflectionClass($this);

        foreach ($reflection->getProperties() as $property) {
            $result[] = $property->getName();
        }

        return $result;
    }

    /**
     * Fill model InputDTO instance
     *
     * @param Model $model
     * @return void
     */
    public function fillFromModel(Model $model): void
    {
        $fields = $this->getFields();
        $exceptAutoFields = $this->exceptAutoFields ?? [];

        foreach ($fields as $field) {
            if (in_array($field, $exceptAutoFields)) {
                continue;
            }

            $modelFieldName = $this->camelToSnake($field);

            if (isset($model->{$modelFieldName})) {
                $value = $model->{$modelFieldName};
                if ($value instanceof \BackedEnum) {
                    $value = $value->value;
                }
                $this->{$field} = $value;
            }
        }
    }
}
