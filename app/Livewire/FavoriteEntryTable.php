<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Entry;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class FavoriteEntryTable extends DataTableComponent
{
    protected $model = Entry::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTableAttributes([
            'class' => 'table-bordered table-striped table-hover text-nowrap',
        ]);
        $this->setDefaultSort('favorited_at', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->searchable()
                ->sortable(),

            LinkColumn::make('Title')
                ->searchable(fn(Builder $query, $searchTerm) => $query->orWhere('title', 'LIKE', "%{$searchTerm}%"))
                ->title(fn($row) => Str::of($row->title)->limit(70))
                ->location(fn($row) => route('entries.show', $row)),
            Column::make("Rooms", "rooms")
                ->searchable()
                ->sortable(),
            Column::make("Area", "area")
                ->searchable()
                ->sortable(),
            Column::make("District", "district")
                ->searchable()
                ->sortable(),
            Column::make("Price", "price")
                ->searchable()
                ->sortable()
                ->format(fn($value, Entry $row, Column $column) => number_format($value, 0, ',', ' ')),
            Column::make("Price per m2", "price_per_sqm")
                ->searchable()
                ->sortable()
                ->format(fn($value, Entry $row, Column $column) => number_format($value, 0, ',', ' ')),
            Column::make("Favorited at", "favorited_at")
                ->searchable()
                ->sortable()
                ->format(fn($value, Entry $row, Column $column) => $value->format('Y-m-d H:i')),

            Column::make("Created at", "created_at")
                ->searchable()
                ->sortable()
                ->format(fn($value, Entry $row, Column $column) => $value->format('Y-m-d')),
        ];
    }

    public function builder(): Builder
    {
        return Entry::query()->favorite()->select([
            'title',
            'created_at',
        ]);
    }
}
