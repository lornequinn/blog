<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Slug column
    |--------------------------------------------------------------------------
    |
    | Posts and Tags are looked up by slug. Override if a consumer needs a
    | different column for slug routing (rare).
    |
    */
    'slug_column' => 'slug',

    /*
    |--------------------------------------------------------------------------
    | Status values
    |--------------------------------------------------------------------------
    |
    | Posts can be in one of these states. The publishing pipeline reads from
    | here so consumers can extend with their own states if needed.
    |
    */
    'statuses' => [
        'draft',
        'scheduled',
        'published',
    ],

    /*
    |--------------------------------------------------------------------------
    | Post page layout
    |--------------------------------------------------------------------------
    |
    | null renders the post as a standalone HTML page. Set a Blade layout
    | (e.g. 'layouts.app') to render the post inside your site's layout:
    | the article is placed in `layout_section` and the post title in a
    | 'title' section. Override the view entirely with
    | `vendor:publish --tag=blog-core-views` if you need more control.
    |
    */
    'layout' => null,

    'layout_section' => 'content',

];
