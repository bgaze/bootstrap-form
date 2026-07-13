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
    | Bootstrap version
    |--------------------------------------------------------------------------
    |
    | The Bootstrap version forms are rendered for: 4 or 5.
    | Defaults to 4 (fully backward compatible). Set to 5 to opt into Bootstrap 5
    | markup application wide. It can also be overridden per form, e.g.:
    |
    |     BF::open(['bootstrap_version' => 5])
    |
    */

    'bootstrap_version' => 4,

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
    | Group attributes
    |--------------------------------------------------------------------------
    |
    | Here you may specify application wide default attributes for input groups.
    | An array is expected, any other value will be ignored.
    | Note that the form group class will always be added if missing.
    |
    */

    'group' => [],

    /*
    |--------------------------------------------------------------------------
    | Error output
    |--------------------------------------------------------------------------
    |
    | Here you may specify whether all the errors of an input should be
    | displayed or just the first one.
    |
    */

    'show_all_errors' => false,

    /*
    |--------------------------------------------------------------------------
    | Valid feedback
    |--------------------------------------------------------------------------
    |
    | When enabled, after a submission that failed validation (an error bag is
    | present), fields that carry no error of their own are marked valid
    | (is-valid). Provide a per-field "success" option to also render a
    | valid-feedback message. Disabled by default.
    |
    */

    'show_valid_feedback' => false,

    /*
    |--------------------------------------------------------------------------
    | Bootstrap 4 layout options
    |--------------------------------------------------------------------------
    |
    | Layout options applied when rendering for Bootstrap 4. Component classes
    | (form-control, form-check, ...) are provided by the version driver and are
    | not configurable: only these layout-level, app-specific options are.
    |
    */

    'bootstrap4' => [

        // Whether Bootstrap custom style should be used by default when available.
        'custom' => false,

        // Horizontal forms: width of the left (labels) and right (fields) columns.
        'left_class' => 'col-lg-2 col-xl-3',
        'right_class' => 'col-lg-10 col-xl-9',

        // Horizontal forms: add an empty left column to checkboxes, radios and fields
        // without label to preserve alignment. Set false to disable, or a CSS class.
        'pull_right' => 'hidden-md-down col-lg-2 col-xl-3',

        // Inline forms: horizontal blank space between label and field (on labels).
        // Set false to disable. Does not apply to checkboxes/radios (needs CSS).
        'lspace' => 'mr-2',

        // Inline forms: horizontal blank space between form groups. False to disable.
        'hspace' => 'mr-3',

        // Inline forms: vertical blank space between form groups. False to disable.
        'vspace' => 'my-1',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bootstrap 5 layout options
    |--------------------------------------------------------------------------
    |
    | Same options, applied when rendering for Bootstrap 5. The `custom` option is
    | a no-op in Bootstrap 5 (custom controls were merged into the default styles),
    | and spacing utilities use the -e/-s suffixes (me-*, ms-*) instead of -r/-l.
    |
    */

    'bootstrap5' => [

        // Horizontal forms: width of the left (labels) and right (fields) columns.
        'left_class' => 'col-lg-2 col-xl-3',
        'right_class' => 'col-lg-10 col-xl-9',

        // Horizontal forms: add an empty left column to checkboxes, radios and fields
        // without label to preserve alignment. Set false to disable, or a CSS class.
        'pull_right' => 'd-none d-lg-block col-lg-2 col-xl-3',

        // Inline forms: horizontal blank space between label and field (on labels).
        // Set false to disable. Does not apply to checkboxes/radios (needs CSS).
        'lspace' => 'me-2',

        // Inline forms: horizontal blank space between form groups. False to disable.
        'hspace' => 'me-3',

        // Inline forms: vertical blank space between form groups. False to disable.
        'vspace' => 'my-1',
    ],
];
