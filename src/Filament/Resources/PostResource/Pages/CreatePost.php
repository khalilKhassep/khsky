<?php

namespace LaraZeus\Sky\Filament\Resources\PostResource\Pages;

use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\Sky\Filament\Resources\PostResource;
use Filament\Facades\Filament;
use App\Models\Panel;
class CreatePost extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = PostResource::class;

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
