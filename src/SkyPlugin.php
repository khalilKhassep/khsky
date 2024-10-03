<?php

namespace LaraZeus\Sky;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use LaraZeus\Sky\Filament\Resources\FaqResource;
use LaraZeus\Sky\Filament\Resources\LibraryResource;
use LaraZeus\Sky\Filament\Resources\NavigationResource;
use LaraZeus\Sky\Filament\Resources\PageResource;
use LaraZeus\Sky\Filament\Resources\PostResource;
use LaraZeus\Sky\Filament\Resources\TagResource;
use Filament\Forms\Components\Select;

final class SkyPlugin implements Plugin
{
    use Configuration;
    use EvaluatesClosures;

    public function getId(): string
    {
        return 'zeus-sky';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasPostResource()) {
            $panel->resources([PostResource::class]);
        }

        if ($this->hasPageResource()) {
            $panel->resources([PageResource::class]);
        }

        if ($this->hasFaqResource()) {
            $panel->resources([FaqResource::class]);
        }

        if ($this->hasLibraryResource()) {
            $panel->resources([LibraryResource::class]);
        }

        if ($this->hasTagResource()) {
            $panel->resources([TagResource::class]);
        }

        if ($this->hasNavigationResource()) {
            $panel->resources([NavigationResource::class]);
            // dd(static::get()->getItemTypes());

        }
    }

    public static function make(): static
    {
        return new self();
    }

    public static function get(): static
    {
        // @phpstan-ignore-next-line
        return filament('zeus-sky');
    }

    public function boot(Panel $panel): void
    {
        // $this->itemType(__('Catoery'), [
        //     Select::make('category_id')
        //         ->searchable()
        //         ->options(function () {
        //             return SkyPlugin::get()->getModel('Tag')::whereIn('type', SkyPlugin::get()->getModel('Tag')::getTypes())->pluck('name', 'id')->map(fn($tag) => preg_replace('/\n/', '', $tag));
        //         })
        // ], 'category');

        // $this->itemType(__('Sommod Routes'), [
        //     Select::make('sommod_routes')
        //         ->label(__('Sommod Routes'))
        //         ->options([
        //             'front.somoud.home' => __('Somoud Home'),
        //         ])
        // ]);

        // $this->itemType(__('Collection'), [
        //     Select::make('collection')
        //         ->searchable()
        //         ->options(function () {
        //             return [
        //                 'event' => __('Events'),
        //                 'activity' => __('Activities'),
        //                 'administration' => __('Administrations'),
        //                 'hall' => __('Halls'),
        //                 'partners' => __('Partners'),
        //                 'product' => __('Products'),
        //                 'service' => __('Services'),
        //                 'supporters' => __('Supporters'),
        //                 'cource' => __('Cources'),
        //                 'initiative' => __('Initiatives'),
        //                 'blogs' => __('All News'),
        //                 'dashboard' => __('Library'),
        //                 'faq' => __('Faqs'),

        //             ];
        //         })
        // ], 'collection');
    }
}
