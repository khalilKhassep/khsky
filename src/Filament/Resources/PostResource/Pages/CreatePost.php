<?php

namespace LaraZeus\Sky\Filament\Resources\PostResource\Pages;

use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\Sky\Filament\Resources\PostResource;
use App\Traits\HasMultiablePanels;
class CreatePost extends CreateRecord
{
    use CreateRecord\Concerns\Translatable,HasMultiablePanels{
        HasMultiablePanels::handleRecordCreation insteadof CreateRecord\Concerns\Translatable;
    }

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

   
}
