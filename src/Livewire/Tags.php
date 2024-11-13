<?php

namespace LaraZeus\Sky\Livewire;

use Illuminate\View\View;
use LaraZeus\Sky\Models\Tag;
use LaraZeus\Sky\SkyPlugin;
use Livewire\Component;

class Tags extends Component
{
    public string $type;

    public string $slug;

    protected string $post_type;

    protected ?string $template = 'default';
    public ?Tag $tag;

    protected $load = false;
    public function mount(string $type, string $slug): void
    {
        $this->type = $type;
        $this->slug = $slug;
        $this->post_type = $type;

         

        if ($this->type == 'category') {
            $this->post_type = 'post';
        }

       
        $this->tag = config('zeus-sky.models.Tag')::findBySlug($slug, $type);

        filled($this->tag->template) ? $this->template = $this->tag->template : '';

        
        abort_if($this->tag === null, 404);
    }

    public function render(): View
    {
        seo()
            ->site(config('zeus.site_title', 'Laravel'))
            ->title($this->tag->name . ' - ' . config('zeus.site_title'))
            ->description(__('Show All posts in') . ' ' . $this->tag->name . ' - ' . config('zeus.site_description') . ' ' . config('zeus.site_title'))
            ->rawTag('favicon', '<link rel="icon" type="image/x-icon" href="' . asset('favicon/favicon.ico') . '">')
            ->rawTag('<meta name="theme-color" content="' . config('zeus.site_color') . '" />')
            ->withUrl()
            ->twitter();

        return view(app('skyTheme') . '.partial.templates.tag-'.$this->template)
            ->with([
                'posts' => $this->tag->postsPublished($this->post_type)->get(),
            ])
            ->layout(config('zeus.layout'));
    }
}
