<?php

namespace LaraZeus\Sky\Filament\Resources\PageResource\Pages;

use App\Models\Panel;
use Filament\Facades\Filament;
use Filament\Actions\LocaleSwitcher;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\Sky\Filament\Resources\PageResource;

class CreatePage extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
    protected function handleRecordCreation(array $data): Model
    {
        // attach cereated record to panel

        $panel = Panel::findByName(Filament::getCurrentPanel()->getId());

        $record = static::getModel()::create($data) ;

        $panel->posts()->attach($record->id);

        return $record ;
    }
}
