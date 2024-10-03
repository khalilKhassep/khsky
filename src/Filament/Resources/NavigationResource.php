<?php

namespace LaraZeus\Sky\Filament\Resources;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use LaraZeus\Sky\Models\Navigation;
use LaraZeus\Sky\SkyPlugin;
use Illuminate\Database\Eloquent\Model;

class NavigationResource extends SkyResource
{
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?int $navigationSort = 99;

    protected static bool $showTimestamps = true;

    public static function disableTimestamps(bool $condition = true): void
    {
        static::$showTimestamps = !$condition;
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Section::make('')->schema([
                    TextInput::make('name')
                        ->label(__('zeus-sky::filament-navigation.attributes.name'))
                        ->reactive()
                        ->debounce()
                        ->required(),
                    ViewField::make('items')
                        ->label(__('zeus-sky::filament-navigation.attributes.items'))
                        ->default([])
                        ->view('zeus::filament.navigation-builder'),
                ])
                    ->columnSpan([
                        12,
                        'lg' => 8,
                    ]),
                Group::make([
                    Section::make('')->schema([
                        Select::make('handle')
                            ->label(__('Location'))
                            ->options(function (?Model $record) {

                                /**
                                 * Filters the options array based on the handles retrieved from the model.
                                 *
                                 * This function defines a list of menu options and retrieves the handles 
                                 * from the model while excluding any global scopes. It then filters the 
                                 * options array to return only those keys that are not present in the 
                                 * handles array.
                                 *
                                 * The function works as follows:
                                 * 
                                 * 1. **Options Array**: 
                                 *    An associative array of menu handles and their corresponding translated 
                                 *    labels is defined. The keys represent the unique handles for each menu, 
                                 *    while the values are the translated labels for display purposes.
                                 * 
                                 * 2. **Retrieve Handles**:
                                 *    The function retrieves the handles from the model using Eloquent, 
                                 *    excluding any global scopes. This produces an array of handles that 
                                 *    are currently in use.
                                 * 
                                 * 3. **Filter Logic**:
                                 *    - The keys of the options array are compared against the handles array 
                                 *      using `array_diff()`. This identifies which keys in the options array 
                                 *      are not present in the handles array.
                                 *    - The result of `array_diff()` is then flipped using `array_flip()` to 
                                 *      create an associative array for easier key checking.
                                 * 
                                 * 4. **Return Value**:
                                 *    The function returns a filtered array of the options where the keys 
                                 *    are only those that are not present in the handles array. In effect, 
                                 *    this allows the function to return menu options that are not currently 
                                 *    being used.
                                 *
                                 * Example:
                                 * Given an options array:
                                 * 
                                 * [
                                 *     'main-header-menu' => __('Header menu'),
                                 *     'main-sommod-header-menu' => __('Header menu Sommod'),
                                 *     'footer-menu-1' => __('Footer 1'),
                                 *     'footer-menu-2' => __('Footer 2'),
                                 *     'footer-menu-3' => __('Footer 3'),
                                 * ]
                                 * 
                                 * And a handles array:
                                 * 
                                 * ['main-header-menu', 'main-sommod-header-menu', 'footer-menu-1']
                                 * 
                                 * The function will return:
                                 * 
                                 * [
                                 *     'footer-menu-2' => 'Footer 2',
                                 *     'footer-menu-3' => 'Footer 3',
                                 * ]
                                 *
                                 * @return array An associative array of menu options that are not currently 
                                 *               in use based on the handles retrieved from the model.
                                 */
                                $options = [
                                    'main-header-menu' => __('Header menu'),
                                    'main-sommod-header-menu' => __('Header menu Sommod'),
                                    'footer-menu-1' => __('Footer 1'),
                                    'footer-menu-2' => __('Footer 2'),
                                    'footer-menu-3' => __('Footer 3'),
                                ];

                                $handles = array_values(static::getModel()::withoutGlobalScope(\App\Models\Scopes\PanelScope::class)->get()->pluck('handle')->toArray());

                                $notUsedOptions = array_filter($options, function ($i) use ($options, $handles) {
                                    $used = array_flip(array_diff(array_keys($options), $handles));
                                    return array_key_exists($i, $used);

                                }, ARRAY_FILTER_USE_KEY);

                                if (is_null($record)) {
                                    return $notUsedOptions;
                                }
                                return array_merge($notUsedOptions, [$record->handle => $record->name]);


                            })
                            ->required(),
                        Select::make(__('Panel'))
                            ->multiple()
                            ->relationship('panels', titleAttribute: 'panel_name')
                            ->preload(),
                        //->unique(column: 'handle', ignoreRecord: true),
                        View::make('zeus::filament.card-divider')
                            ->visible(static::$showTimestamps),
                        Placeholder::make('created_at')
                            ->label(__('zeus-sky::filament-navigation.attributes.created_at'))
                            ->visible(static::$showTimestamps)
                            ->content(fn(?Navigation $record) => $record ? $record->created_at->translatedFormat(Table::$defaultDateTimeDisplayFormat) : new HtmlString('&mdash;')),
                        Placeholder::make('updated_at')
                            ->label(__('zeus-sky::filament-navigation.attributes.updated_at'))
                            ->visible(static::$showTimestamps)
                            ->content(fn(?Navigation $record) => $record ? $record->updated_at->translatedFormat(Table::$defaultDateTimeDisplayFormat) : new HtmlString('&mdash;')),
                    ]),
                ])
                    ->columnSpan([
                        12,
                        'lg' => 4,
                    ]),
            ])
            ->columns(12);
    }

    public static function getLabel(): string
    {
        return __('Navigation');
    }

    public static function getPluralLabel(): string
    {
        return __('Navigations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Navigations');
    }

    public static function canAccess(): bool
    {
        return true;
        //return filament()->getCurrentPanel()->getId() === 'admin';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('zeus-sky::filament-navigation.attributes.name'))
                    ->searchable(),
                TextColumn::make('handle')
                    ->label(__('zeus-sky::filament-navigation.attributes.handle'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('zeus-sky::filament-navigation.attributes.created_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('zeus-sky::filament-navigation.attributes.updated_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('panels.panel_name')
                    ->label(__('Panel')),

            ])
            ->actions([
                EditAction::make()
                    ->icon(null),
                DeleteAction::make()
                    ->icon(null),
            ])
            ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => NavigationResource\Pages\ListNavigations::route('/'),
            'create' => NavigationResource\Pages\CreateNavigation::route('/create'),
            'edit' => NavigationResource\Pages\EditNavigation::route('/{record}'),
        ];
    }

    public static function getModel(): string
    {
        return SkyPlugin::get()->getModel('Navigation');
    }
}
