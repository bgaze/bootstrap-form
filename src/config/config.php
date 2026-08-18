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
    | Blade components
    |--------------------------------------------------------------------------
    |
    | Here you may specify if the Blade x-components must be enabled. When true,
    | the "bf" component namespace is registered, exposing tags such as
    | <x-bf::text/>, <x-bf::form/>, etc. The BF facade and Blade directives
    | remain available regardless of this setting.
    |
    */

    'components' => true,

    /*
    |--------------------------------------------------------------------------
    | Bootstrap version
    |--------------------------------------------------------------------------
    |
    | The Bootstrap version forms are rendered for: 4 or 5.
    | Defaults to 5. Set to 4 for legacy Bootstrap 4 markup (still fully
    | supported for backward compatibility). It can also be overridden per form
    | or per field, e.g.:
    |
    |     BF::open(['bootstrap_version' => 4])
    |
    */

    'bootstrap_version' => 5,

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
    |
    | Declaring a "class" here takes over the group styling: it replaces the
    | "group_class" default of the version sections below (and the inline spacing),
    | keeping only the classes the Bootstrap driver requires. It can be set per form
    | and per field the same way, e.g. BF::open(['group' => ['class' => 'mb-4']]).
    | Set this option to false to drop the group wrapper entirely.
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
    | Required mark
    |--------------------------------------------------------------------------
    |
    | The mark appended to the label of any field carrying the HTML "required"
    | attribute. HTML is accepted verbatim (e.g. ' <span class="text-danger">*</span>'),
    | so spacing/markup lives in the value itself; the default already includes a
    | leading space. Set to false to disable the feature.
    |
    | It can be overridden per form or per field, e.g.:
    |
    |     BF::open(['required_mark' => false])
    |     BF::text('email', 'Email', null, ['required' => true, 'required_mark' => ' (required)'])
    |
    | On a choice collection (checkboxes / radios) the mark is applied to the
    | global label only, never to the individual choice labels.
    |
    */

    'required_mark' => ' *',

    /*
    |--------------------------------------------------------------------------
    | Content escaping
    |--------------------------------------------------------------------------
    |
    | The content sinks — label, help, success and the prepend/append addons —
    | emit their value as raw HTML, so markup can be injected deliberately.
    | Enable this option to escape them instead. Disabled by default, so nothing
    | changes for an existing application.
    |
    | It can be overridden per form or per field, e.g.:
    |
    |     BF::open(['escape' => true])
    |     BF::text('q', 'Q', null, ['escape' => true])
    |
    | A value implementing Htmlable (an HtmlString, a Blade slot) is markup by
    | construction: it is never escaped, whatever this option says. That is the
    | per-value opt-out. Escaping content that comes from user input, the database
    | or translation files at the application boundary remains the safe habit,
    | enabled or not.
    |
    */

    'escape' => false,

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

        // Default class(es) of every form group wrapper. False for none. Configured
        // here only: to override it, style the group itself ('group' => ['class' => ...]),
        // which can be set per form and per field.
        'group_class' => 'form-group',

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

        // Default class(es) of every form group wrapper. False for none. Configured
        // here only: to override it, style the group itself ('group' => ['class' => ...]),
        // which can be set per form and per field.
        'group_class' => 'mb-3',

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
