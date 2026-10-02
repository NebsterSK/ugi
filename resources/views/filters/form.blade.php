@use('App\Enums\Location')
@use('App\Enums\PropertyType')

@php
/** @var App\Models\Filter $filter */

$selectedLocations = old('locations', $filter->locations?->map->value->all() ?? []);
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $filter->name) }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="property_type" class="form-label">Property type</label>
    <select id="property_type" name="property_type" class="form-select @error('property_type') is-invalid @enderror" required>
        @foreach(PropertyType::cases() as $propertyType)
            <option value="{{ $propertyType->value }}" @selected(old('property_type', $filter->property_type?->value) === $propertyType->value)>{{ $propertyType->label() }}</option>
        @endforeach
    </select>
    @error('property_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <span class="form-label d-block">Locations</span>
    @foreach(Location::cases() as $location)
        <div class="form-check form-check-inline">
            <input type="checkbox" id="location-{{ $location->value }}" name="locations[]" value="{{ $location->value }}" class="form-check-input @error('locations') is-invalid @enderror" @checked(in_array($location->value, $selectedLocations))>
            <label for="location-{{ $location->value }}" class="form-check-label">{{ $location->label() }}</label>
        </div>
    @endforeach
    @error('locations')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    @error('locations.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="price_from" class="form-label">Price from (€)</label>
        <input type="number" id="price_from" name="price_from" min="0" step="1000" class="form-control @error('price_from') is-invalid @enderror" value="{{ old('price_from', $filter->price_from) }}">
        @error('price_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="price_to" class="form-label">Price to (€)</label>
        <input type="number" id="price_to" name="price_to" min="0" step="1000" class="form-control @error('price_to') is-invalid @enderror" value="{{ old('price_to', $filter->price_to) }}">
        @error('price_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="area_from" class="form-label">Area from (m2)</label>
        <input type="number" id="area_from" name="area_from" min="0" class="form-control @error('area_from') is-invalid @enderror" value="{{ old('area_from', $filter->area_from) }}">
        @error('area_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-check form-switch mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" role="switch" @checked(old('is_active', $filter->is_active))>
    <label for="is_active" class="form-check-label">Active</label>
</div>

<button type="submit" class="btn btn-primary">Save</button>

<a href="{{ route('filters.index') }}" class="btn btn-outline-secondary">Cancel</a>
