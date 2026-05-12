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

];
