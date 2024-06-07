<?php

namespace LaraZeus\Sky\Filament\Resources\NavigationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use LaraZeus\Sky\Filament\Resources\NavigationResource;
use App\Traits\HasMultiablePanels;

class CreateNavigation extends CreateRecord
{
    // use CreateRecord\Concerns\Translatable, HasMultiablePanels {
    //     HasMultiablePanels::handleRecordCreation insteadof CreateRecord\Concerns\Translatable;
    // }

        use NavigationResource\Pages\Concerns\HandlesNavigationBuilder;

    protected static string $resource = NavigationResource::class;


}
