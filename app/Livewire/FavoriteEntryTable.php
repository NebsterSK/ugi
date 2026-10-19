<?php

namespace App\Livewire;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
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
        $this->setTdAttributes(fn (Column $column, Entry $row): array => $column->isField('price_per_sqm')
            ? ['style' => '--bs-table-bg: '.$row->price_per_sqm_background]
            : []);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            LinkColumn::make('Title')
                ->searchable(fn (Builder $query, $searchTerm) => $query->orWhere('title', 'LIKE', "%{$searchTerm}%"))
                ->title(fn ($row) => Str::of($row->title)->limit(70))
                ->location(fn ($row) => route('entries.show', $row)),
            Column::make('Rooms', 'rooms')
                ->searchable()
                ->sortable(),
            Column::make('Area', 'area')
                ->searchable()
                ->sortable(),
            Column::make('District', 'district')
                ->searchable()
                ->sortable(),
            Column::make('Price', 'price')
                ->searchable()
                ->sortable()
                ->format(fn ($value, Entry $row, Column $column) => number_format($value, 0, ',', ' ')),
            Column::make('Price per m2', 'price_per_sqm')
                ->searchable()
                ->sortable()
                ->format(fn ($value, Entry $row, Column $column) => '<span'.($row->is_price_per_sqm_extreme ? ' class="fw-bold"' : '').'>'.number_format($value, 0, ',', ' ').'</span>')
                ->html(),
            Column::make('Favorited at', 'favorited_at')
                ->searchable()
                ->sortable()
                ->format(fn ($value, Entry $row, Column $column) => $value->format('Y-m-d H:i')),

            Column::make('Created at', 'created_at')
                ->searchable()
                ->sortable()
                ->format(fn ($value, Entry $row, Column $column) => $value->format('Y-m-d')),
        ];
    }

    public function builder(): Builder
    {
        return Entry::favorite()->select([
            'title',
            'created_at',
        ]);
    }
}
