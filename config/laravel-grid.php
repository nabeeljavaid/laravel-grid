<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CSS Framework
    |--------------------------------------------------------------------------
    |
    | Controls which CSS framework the grid views use.
    | Options: 'bootstrap' (default), 'tailwind'
    |
    */
    'css_framework' => env('GRID_CSS_FRAMEWORK', 'bootstrap'),

    /*
    |--------------------------------------------------------------------------
    | Pagination Style
    |--------------------------------------------------------------------------
    |
    | Controls how the page navigator is rendered in the grid footer.
    |
    | Options:
    |   'INPUT'  — current page as a text input field (default)
    |   'SELECT' — current page as a dropdown select
    |   'LINKS'  — Laravel's built-in pagination links
    |
    */
    'pagination_style' => env('GRID_PAGINATION_STYLE', 'INPUT'),

    /*
    |--------------------------------------------------------------------------
    | Records Per Page Style
    |--------------------------------------------------------------------------
    |
    | Controls how the records-per-page selector is rendered in the grid footer.
    |
    | Options:
    |   'SELECT' — dropdown select (default)
    |   'INPUT'  — text input field
    |   'LINKS'  — anchor links for each option
    |
    */
    'records_per_page_style' => env('GRID_RECORDS_PER_PAGE_STYLE', 'SELECT'),

    /*
    |--------------------------------------------------------------------------
    | Records Per Page Options
    |--------------------------------------------------------------------------
    |
    | The list of options shown in the records-per-page selector.
    |
    */
    'records_per_page' => [25, 50, 100, 200, 500],

    /*
    |--------------------------------------------------------------------------
    | Default Limit
    |--------------------------------------------------------------------------
    |
    | The default number of records per page used when no limit is present
    | in the current request or session.
    |
    */
    'default_limit' => env('GRID_DEFAULT_LIMIT', 25),

    /*
    |--------------------------------------------------------------------------
    | Serial Number
    |--------------------------------------------------------------------------
    |
    | Whether to show the # serial number column by default.
    | Can be overridden per-grid via $grid->setSerialNumber(false).
    |
    */
    'serial_number' => true,

];
