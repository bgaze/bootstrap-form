<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blade directives
    |--------------------------------------------------------------------------
    |
    | Here you may specify if blade directives should be enabled.
    |
    */

    'blade_directives' => true,

    /*
    |--------------------------------------------------------------------------
    | Form layout
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default form layout for the open method.
    | Available layouts are: vertical | horizontal | inline.
    |
    */

    'layout' => 'vertical',

    /*
    |--------------------------------------------------------------------------
    | Horizontal form default sizing
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default widths of the columns if you're using
    | the horizontal form layout. You can use the Bootstrap grid classes as you
    | wish.
    |
    */

    'left_column_class'  => 'col-lg-2 col-xl-3',
    'right_column_class' => 'col-lg-10 col-xl-9',
    
    /*
    |--------------------------------------------------------------------------
    | Error output
    |--------------------------------------------------------------------------
    |
    | Here you may specify the whether all the errors of an input should be
    | displayed or just the first one.
    |
    */

    'show_all_errors' => false,
];
