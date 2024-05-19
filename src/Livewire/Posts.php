<?php

namespace LaraZeus\Sky\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Posts extends Component
{
    use SearchHelpers;

    public function render(): View
    {
        $search = request('search');
        $category = request('category');
        $type = str_replace('/', '', request()->getRequestUri());
       
        
        if ($type === 'content' || $type == 'blog') 
            $type = 'post';
        

        $posts = config('zeus-sky.models.Post')::NotSticky($type)
            ->search($search)
            ->with(['tags', 'author', 'media'])
            ->type($type)
            ->forCategory($category)
            ->published($type)
            ->orderBy('published_at', 'desc')
            ->get();


        $pages = config('zeus-sky.models.Post')::query()
            ->page()
            ->whereDate('published_at', '<=', now())
            ->search($search)
            ->with(['tags', 'author', 'media'])
            ->forCategory($category)
            ->orderBy('published_at', 'desc')
            ->whereNull('parent_id')
            ->get();

        $pages = $this->highlightSearchResults($pages, $search);
        $posts = $this->highlightSearchResults($posts, $search);

        $recent = config('zeus-sky.models.Post')::query()
            ->posts()
            ->published()
            ->whereDate('published_at', '<=', now())
            ->with(['tags', 'author', 'media'])
            ->limit(config('zeus-sky.recentPostsLimit'))
            ->orderBy('published_at', 'desc')
            ->get();

        seo()
            ->site(config('zeus.site_title', 'Laravel'))
            ->title(__('Posts') . ' - ' . config('zeus.site_title'))
            ->description(__('Posts') . ' - ' . config('zeus.site_description') . ' ' . config('zeus.site_title'))
            ->rawTag('favicon', '<link rel="icon" type="image/x-icon" href="' . asset('favicon/favicon.ico') . '">')
            ->rawTag('<meta name="theme-color" content="' . config('zeus.site_color') . '" />')
            ->withUrl()
            ->twitter();

        return view(app('skyTheme') . '.home')
            ->with([
                'posts' => $posts,
                'pages' => $pages,
                'recent' => $recent,
                'tags' => config('zeus-sky.models.Tag')::withCount('postsPublished')
                    ->where('type', 'category')
                    ->get(),
                'stickies' => config('zeus-sky.models.Post')::with(['author', 'media'])->sticky()->published()->get(),
            ])
            ->layout(config('zeus.layout'));
    }
}
