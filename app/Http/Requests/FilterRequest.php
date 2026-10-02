<?php

namespace App\Http\Requests;

use App\Enums\Location;
use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'property_type' => [
                'required',
                Rule::enum(PropertyType::class),
            ],
            'locations' => [
                'required',
                'array',
                'min:1',
            ],
            'locations.*' => [
                'distinct',
                Rule::enum(Location::class),
            ],
            'price_from' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'price_to' => [
                'nullable',
                'integer',
                'min:0',
                Rule::when($this->filled('price_from'), 'gte:price_from'),
            ],
            'area_from' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'locations.required' => 'Select at least one location.',
            'price_to.gte' => 'Price to must be greater than or equal to price from.',
        ];
    }

    /**
     * @return array{name: string, property_type: string, locations: array<int, string>, price_from: int|null, price_to: int|null, area_from: int|null, is_active: bool}
     */
    public function filterAttributes(): array
    {
        return [
            'name' => $this->validated('name'),
            'property_type' => $this->validated('property_type'),
            'locations' => $this->validated('locations'),
            'price_from' => $this->validated('price_from'),
            'price_to' => $this->validated('price_to'),
            'area_from' => $this->validated('area_from'),
            'is_active' => $this->boolean('is_active'),
        ];
    }
}
