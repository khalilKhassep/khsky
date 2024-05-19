<?php

namespace LaraZeus\Sky\Models;

use Illuminate\Database\Eloquent\Builder;

trait PostScope
{
    /**
     * @param  Builder<Post>  $query
     */
    public function scopeSticky(Builder $query , string $type = 'post'): Builder
    {
        return $query->where('post_type', $type)
            ->whereNotNull('sticky_until')
            ->whereDate('sticky_until', '>=', now())
            ->whereDate('published_at', '<=', now());
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopeNotSticky(Builder $query , string $type = 'post'): Builder
    {
        return $query->where('post_type', $type)->where(function ($q) {
            return $q->whereDate('sticky_until', '<=', now())->orWhereNull('sticky_until');
        })
            ->whereDate('published_at', '<=', now());
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query , string $type = 'post'): Builder
    {
        return $query->where('post_type', $type)
            ->where('status', 'publish')
            ->whereDate('published_at', '<=', now());
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopeRelated(Builder $query, Post $post , string $type = 'post'): Builder
    {
        return $query->where('post_type', $type)
            ->withAnyTags($post->tags->pluck('name')->toArray(), 'category');
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopePage(Builder $query): Builder
    {
        return $query->where('post_type', 'page');
    }

    public function scopeEvent(Builder $builder) : Builder {
        return $builder->where('post_type', 'event');
    }


    public function scopeAdministration(Builder $builder) : Builder {
        return $builder->where('post_type', 'administration');
    }

    public function scopePartner(Builder $builder) :Builder {
        return $builder->where('post_type', 'partner');
    } 

    function scopeService(Builder $builder) : Builder {
        return $builder->where('post_type', 'service');
    }
    function scopeProduct(Builder $builder): Builder
    {
        return $builder->where('post_type', 'product');
    }
    function scopeActivites(Builder $builder): Builder
    {
        return $builder->where('post_type', 'activity');
    }
    function scopeHalls(Builder $builder): Builder
    {
        return $builder->where('post_type', 'hall');
    }

    function scopeType(Builder $builder ,string $type = 'post') : Builder {
        
        return $builder->where('post_type', $type);
    }
    /**
     * @param  Builder<Post>  $query
     */
    public function scopePosts(Builder $query): Builder
    {
        return $query->where('post_type', 'post');
    }

    /**
     * @param  Builder<Post>  $query
     * @param  ?string  $category
     */
    public function scopeForCategory(Builder $query, ?string $category = null): Builder
    {
        if ($category !== null) {
            return $query->where(
                function ($query) use ($category) {
                    $query->withAnyTags([$category], 'category');

                    return $query;
                }
            );
        }

        return $query;
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term !== null) {
            return $query->where(
                function ($query) use ($term) {
                    foreach (['title', 'slug', 'content', 'description'] as $attribute) {
                        $query->orWhere($attribute, 'like', "%{$term}%");
                    }

                    return $query;
                }
            );
        }

        return $query;
    }
}
