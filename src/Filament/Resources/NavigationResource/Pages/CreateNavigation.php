<?php

namespace LaraZeus\Sky\Filament\Resources\NavigationResource\Pages;

use App\Models\Panel;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\Sky\Filament\Resources\NavigationResource;

class CreateNavigation extends CreateRecord
{
    use NavigationResource\Pages\Concerns\HandlesNavigationBuilder;

    protected static string $resource = NavigationResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // attach cereated record to panel

        $panel = Panel::findByName(Filament::getCurrentPanel()->getId());

        $record = static::getModel()::create($data) ;

        $panel->posts()->attach($record->id);

        return $record ;
    }
}
