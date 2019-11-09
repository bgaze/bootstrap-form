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
    | Custom fields
    |--------------------------------------------------------------------------
    |
    | Here you may specify if Bootstrap custom style should be used by default
    | when available.
    |
    */

    'custom' => false,

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
    // Set to false to disable this feature, or set the HTML class to add.
    'pull_right' => 'hidden-md-down col-lg-2 col-xl-3',

    // The width of left column (labels).
    'left_class' => 'col-lg-2 col-xl-3',

    // The width of right column (fields).
    'right_class' => 'col-lg-10 col-xl-9',

    /*
    |--------------------------------------------------------------------------
    | Inline forms
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default options for the inline form layout.
    |
    */

    // The horizontal blank space between fields.
    // Set to false to disable this feature, or set the HTML class to add on 
    // fields and labels.
    'hspace' => 'mr-2',

    // The vertical blank space between fields.
    // Set to false to disable this feature, or set the HTML class to add on 
    // fields and labels.
    'vspace' => 'my-1',

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
