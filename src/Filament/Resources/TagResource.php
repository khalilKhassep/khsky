<?php

namespace LaraZeus\Sky\Filament\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaraZeus\Sky\Filament\Resources\TagResource\Pages;
use LaraZeus\Sky\Models\Tag;
use LaraZeus\Sky\SkyPlugin;

class TagResource extends SkyResource
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 5;

    public static function getModel(): string
    {
        return SkyPlugin::get()->getModel('Tag');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('Tag Name'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, $state) {
                                $slug = Str::slug($state);
                                $tagModel = !is_null(SkyPlugin::get()->getModel('Tag')::findBySlug($slug, 'faq'))
                                    ? SkyPlugin::get()->getModel('Tag')::findBySlug($slug, 'faq')->exists()
                                    : false;
                                if ($tagModel) {
                                    $incementalslug = $state . '-' . SkyPlugin::get()->getModel('Tag')::where('slug', 'like', '%' . Str::slug($state) . '%')
                                        //->where('type', 'faq')
                                        ->get()->count() + 1;

                                    $set('slug', Str::slug($incementalslug));
                                } else {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->unique(ignorable: fn(?Model $record): ?Model => $record)
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->columnSpan(2)
                            ->options(SkyPlugin::get()->getTagTypes()),
                        Select::make('template')
                            ->options([
                                'default' => 'default',
                                'members' => 'members'
                            ]),
                        Select::make('parent_id')
                            ->relationship(name: 'parent', titleAttribute: "name")
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->name)

                            ->label(__('Parent')),
                        Select::make(__('Panel'))
                            ->multiple()
                            ->preload()
                            ->relationship('panels', titleAttribute: 'panel_name')
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('name')->toggleable()->searchable()->sortable(),
                TextColumn::make('type')->toggleable()->searchable()->sortable(),
                TextColumn::make('slug')->toggleable()->searchable()->sortable(),
                TextColumn::make('items_count')
                    ->toggleable()
                    ->getStateUsing(
                        function (Tag $record): int {
                            // get class methods
                
                            $methods = get_class_methods($record);

                            if (in_array($record->type, $methods))
                                return $record->{$record->type}->count();

                            if (in_array($record->type, config('zeus-sky.tags_type')))
                                return $record->category()->count();

                            return 0;

                        }

                    ),
                TextColumn::make('panels.panel_name')
                    ->label(__('Panel')),

            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(SkyPlugin::get()->getTagTypes())
                    ->label(__('type')),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make('edit'),
                    DeleteAction::make('delete'),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\TagResource\RelationManagers\ChildrenRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): string
    {
        return __('Tag');
    }

    public static function getPluralLabel(): string
    {
        return __('Tags');
    }

    public static function getNavigationLabel(): string
    {
        return __('Tags');
    }
}
