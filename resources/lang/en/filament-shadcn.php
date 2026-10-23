<?php

return [

    'appearance' => [
        'navigation_label' => 'Appearance',
        'title' => 'Appearance',
        'subheading' => 'Set the panel style, colors and layout. Changes apply to every user.',
        'sections' => [
            'style' => 'Style',
            'style_description' => 'Component shape, radius and density.',
            'colors' => 'Colors',
            'colors_description' => 'Base color, primary color and status colors.',
            'layout' => 'Layout',
            'layout_description' => 'Sidebar and sign-in page.',
            'typography' => 'Typography',
            'typography_description' => 'Fonts from Bunny Fonts (or the provider set in config).',
            'advanced' => 'Advanced',
            'advanced_description' => 'Override individual shadcn/ui tokens, e.g. copied from ui.shadcn.com/themes or tweakcn.',
        ],
        'styles' => [
            'vega' => 'Vega — classic',
            'nova' => 'Nova — compact',
            'maia' => 'Maia — soft',
            'lyra' => 'Lyra — sharp',
            'mira' => 'Mira — dense',
        ],
        'fields' => [
            'style' => 'Component style',
            'radius' => 'Radius',
            'radius_helper' => 'Leave empty to follow the style. In rem, 0 – 1.5.',
            'density' => 'Density',
            'density_helper' => 'Leave empty to follow the style. 1 = normal.',
            'base_color' => 'Base color',
            'primary_mode' => 'Primary color',
            'primary_mode_default' => 'Default (black / white)',
            'primary_mode_named' => 'Pick a color',
            'primary_mode_custom' => 'Custom color code',
            'primary_named' => 'Color',
            'primary_custom' => 'Color code',
            'danger' => 'Danger',
            'success' => 'Success',
            'warning' => 'Warning',
            'info' => 'Info',
            'sidebar' => 'Sidebar',
            'sidebar_inset' => 'Inset — content as a card',
            'sidebar_sidebar' => 'Classic — divider line',
            'sidebar_floating' => 'Floating — sidebar and content as cards',
            'auth_layout' => 'Sign-in page',
            'auth_muted' => 'Muted background, centered card',
            'auth_split' => 'Form + image',
            'auth_plain' => 'Plain',
            'auth_image' => 'Sign-in image',
            'font_sans' => 'Main font',
            'font_mono' => 'Monospace font',
            'font_helper' => 'Font name, e.g. Geist, Inter, Plus Jakarta Sans. Leave empty to follow the style.',
            'tokens_light' => 'Light mode tokens',
            'tokens_dark' => 'Dark mode tokens',
            'token_key' => 'Token',
            'token_value' => 'Value',
        ],
        'actions' => [
            'save' => 'Save',
            'reset' => 'Reset to defaults',
            'reset_confirm' => 'All appearance settings will return to the defaults from config.',
        ],
        'validation' => [
            'token' => 'Token :key contains a value that is not allowed. Use a color (oklch(), #hex), a size, calc() or var().',
        ],

        'notifications' => [
            'saved' => 'Appearance saved',
            'saved_body' => 'Reload the page if some parts have not changed yet.',
            'reset' => 'Appearance reset to defaults',
        ],
    ],

    'auth' => [
        'login' => [
            'heading' => 'Welcome back',
            'subheading' => 'Sign in to your :brand account',
            'register_prompt' => 'Don\'t have an account?',
            'register' => 'Sign up',
        ],
        'terms' => 'By continuing, you agree to our :terms and :privacy.',
        'terms_label' => 'Terms of Service',
        'privacy_label' => 'Privacy Policy',
    ],

    'shell' => [
        'toggle_sidebar' => 'Toggle sidebar',
        'breadcrumbs' => 'Breadcrumbs',
    ],

    'showcase' => [
        'dashboard_label' => 'Sample dashboard',
        'form_label' => 'Sample form',
        'navigation_label' => 'Component showcase',
        'title' => 'Component showcase',
        'subheading' => 'Every Filament component on one page, for checking the theme.',
    ],

    'components' => [
        'navigation_label' => 'shadcn components',
        'title' => 'shadcn components',
        'subheading' => 'Blade components <x-shadcn::…> for needs beyond Filament’s built-in components.',
    ],

    'ui' => [
        'carousel' => [
            'label' => 'Carousel',
            'previous' => 'Previous slide',
            'next' => 'Next slide',
        ],
        'command' => [
            'placeholder' => 'Type a command or search…',
            'empty' => 'No results found.',
        ],
        'resizable' => [
            'label' => 'Resize panel',
        ],
        'spinner' => [
            'label' => 'Loading',
        ],
        'message_scroller' => [
            'latest' => 'Jump to latest message',
        ],
        'questionnaire' => [
            'submit' => 'Submit',
            'next' => 'Next',
            'previous' => 'Back',
            'skip' => 'Skip',
            'required' => 'This question is required.',
            'progress' => 'Question :current of :total',
        ],
    ],

];
