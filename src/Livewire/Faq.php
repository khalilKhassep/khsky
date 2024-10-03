<?php

namespace LaraZeus\Sky\Livewire;

use Illuminate\View\View;
use LaraZeus\Sky\SkyPlugin;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Faq extends Component
{

    public array $faqs;
    public $test = "test";
    public function loadFaqsForCategoryBySlug(string $slug)
    {
        $model = SkyPlugin::get()->getModel('Faq');

        $this->faqs = $model::whereHas('tags', function (Builder $query) use ($slug) {
            $locale = app()->getLocale();
            return $query->where("slug->$locale", $slug)->where('type', 'faq');
        })->get()->toArray();

        return $this->faqs ;
    }

    public function render(): View
    {
        $model = SkyPlugin::get()->getModel('Faq');
        $cats = SkyPlugin::get()->getModel('Tag')::with('children')->where('type', 'faq')
            ->get();
        seo()
            ->site(config('zeus.site_title', 'Laravel'))
            ->title(__('FAQ') . ' - ' . config('zeus.site_title'))
            ->description(__('FAQs') . ' - ' . config('zeus.site_description') . ' ' . config('zeus.site_title'))
            ->rawTag('favicon', '<link rel="icon" type="image/x-icon" href="' . asset('favicon/favicon.ico') . '">')
            ->rawTag('<meta name="theme-color" content="' . config('zeus.site_color') . '" />')
            ->withUrl()
            ->twitter();

        return view(app('skyTheme') . '.addons.faq')
            ->with('cats', $cats)
            ->layout(config('zeus.layout'));
    }
}
