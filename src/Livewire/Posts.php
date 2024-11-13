<?php

namespace LaraZeus\Sky\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Scopes\PanelScope;
use App\Models\Scopes\ContentProviderScope;

class Posts extends Component
{
    use SearchHelpers;
    use WithPagination;

    public function render(): View
    {
        $type = null;
        //$load = false;
        if(isset(request()->query()['page'])) {
            //$load = true;
            $type = preg_replace('([?].*)', '', request()->getRequestUri());
        }
        $search = request('search');
        $category = request('category');
        $type = str_replace('/', '', !is_null($type) ? $type : request()->getRequestUri());


        if ($type === 'content' || $type == 'blog')
            $type = 'post';

        
           $posts = config('zeus-sky.models.Post')::NotSticky($type) ;
        // dd(session()->all());
           if(session()->has('council_load')) {
              $posts->withoutGlobalScopes(); 
           } 
           $posts = $posts 
            ->search($search)
            ->with(['tags', 'author', 'media'])
            ->type($type)
            ->forCategory($category)
            ->published($type)
            ->orderBy('published_at', 'desc')
            
            ->get();




        $pages = config('zeus-sky.models.Post')::query()
            //->sommod($load)
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
            //->sommod($load)
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
