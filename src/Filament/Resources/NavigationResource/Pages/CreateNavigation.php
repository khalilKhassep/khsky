<?php

namespace LaraZeus\Sky\Filament\Resources\NavigationResource\Pages;

use App\Models\Panel;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use LaraZeus\Sky\Filament\Resources\NavigationResource;
use App\Traits\HasMultiablePanels;

class CreateNavigation extends CreateRecord
{
    // use HasMultiablePanels {
    //     HasMultiablePanels::handleRecordCreation insteadof CreateRecord\Concerns\Translatable;
    // }

    use NavigationResource\Pages\Concerns\HandlesNavigationBuilder;

    protected static string $resource = NavigationResource::class;

    protected function handleRecordCreation(array $data) : Model {
        $record = parent::handleRecordCreation($data);

        // get panel 

        $panel = Panel::findByName(filament()->getCurrentPanel()->getId());

        $recordName = str(class_basename($record))->lcfirst()->plural()->value();

        if (method_exists($panel, $recordName)) {

            $panel->$recordName()->attach($record->id);
        }
        // eles return $record without attaching record to panel which means there is no method exists on the panel
        // go to add method ;
        // $panel->posts()->attach($record->id);

        return $record;
    }


}
