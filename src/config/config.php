<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Blade directives
    |--------------------------------------------------------------------------
    |
    | Here you may specify if blade directives must be enabled.
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
    | Horizontal forms
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default options for the forizontal form layout.
    |
    */

    // You can add an empty left column to checkboxes, radios and fields without 
    // label to preserve fields alignment.
    // Set to false to disable this feature, or set the HTML class attribute to use.
    'pull_right' => 'hidden-md-down col-lg-2 col-xl-3',

    // The width of left column (labels).
    'left_class' => 'col-lg-2 col-xl-3',

    // The width of right column (fields).
    'right_class' => 'col-lg-10 col-xl-9',

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
