<?php

if (!defined('ABSPATH')) {
    exit;
}

function shcb_register_booking_post_type()
{
    $labels = [
        'name' => 'Hälsokontroll Bokningar',
        'singular_name' => 'Hälsokontroll Bokning',
        'menu_name' => 'Hälsokontroll Bokningar',
        'add_new' => 'Lägg till ny',
        'add_new_item' => 'Lägg till ny bokning',
        'edit_item' => 'Redigera bokning',
        'new_item' => 'Ny bokning',
        'view_item' => 'Visa bokning',
        'search_items' => 'Sök bokningar',
        'not_found' => 'Inga bokningar hittades',
        'not_found_in_trash' => 'Inga bokningar hittades i papperskorgen',
    ];

    $args = [
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => ['title'],
        'has_archive' => false,
        'rewrite' => false,
    ];

    register_post_type('health_booking', $args);
}


add_action('init', 'shcb_register_booking_post_type');
