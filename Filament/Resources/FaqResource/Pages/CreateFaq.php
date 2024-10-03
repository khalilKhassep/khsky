<?php

namespace LaraZeus\Sky\Filament\Resources\FaqResource\Pages;

use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\Sky\Filament\Resources\FaqResource;
use App\Traits\HasMultiablePanels;
class CreateFaq extends CreateRecord
{
    use CreateRecord\Concerns\Translatable, HasMultiablePanels {
        HasMultiablePanels::handleRecordCreation insteadof CreateRecord\Concerns\Translatable;
    }
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
