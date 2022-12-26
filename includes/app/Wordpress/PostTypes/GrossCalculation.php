<?php

namespace GrossCalculator\Wordpress\PostTypes;

class GrossCalculation
{
    private const CPT_NAME = 'gross_calculation';

    public static function register()
    {
        $labels = array(
            'name' => _x('Wyliczenia brutto', 'Post Type General Name', 'GC'),
            'singular_name' => _x('Wyliczenie brutto', 'Post Type Singular Name', 'GC'),
            'menu_name' => __('Gross Calculations', 'GC'),
            'name_admin_bar' => __('Wyliczenie brutto', 'GC'),
            'archives' => __('Wyliczenia brutto', 'GC'),
            'attributes' => __('Atrybuty wyliczeń brutto', 'GC'),
            'parent_item_colon' => __('Parent Item:', 'GC'),
            'all_items' => __('Wyliczenia brutto', 'GC'),
            'add_new_item' => __('Dodaj nowe wyliczenie brutto', 'GC'),
            'add_new' => __('Dodaj nowe', 'GC'),
            'new_item' => __('Nowe wyliczenie brutto', 'GC'),
            'edit_item' => __('Edytuj wyliczenie brutto', 'GC'),
            'update_item' => __('Aktualizuj wyliczenie brutto', 'GC'),
            'view_item' => __('Zobacz wyliczenie brutto', 'GC'),
            'view_items' => __('Zobacz wyliczenia brutto', 'GC'),
            'search_items' => __('Wyszukaj w wyliczeniach brutto', 'GC'),
            'not_found' => __('Nie znaleziono', 'GC'),
            'not_found_in_trash' => __('Nie znaleziono w koszu', 'GC'),
            'featured_image' => __('Obrazek wyrózniony', 'GC'),
            'set_featured_image' => __('Ustaw obrazek wyrózniony', 'GC'),
            'remove_featured_image' => __('Usuń obrazek wyrózniony', 'GC'),
            'use_featured_image' => __('Uzyj jako obrazek wyrózniony', 'GC'),
            'insert_into_item' => __('Insert into Gross Calculation', 'GC'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'GC'),
            'items_list' => __('Items list', 'GC'),
            'items_list_navigation' => __('Items list navigation', 'GC'),
            'filter_items_list' => __('Filter items list', 'GC'),
        );
        $args = array(
            'label' => __('Gross Calculation', 'GC'),
            'description' => __('Single calculation created by Gross Calculator shortcode.', 'GC'),
            'labels' => $labels,
            'supports' => array('custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 100,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
            ),
            'map_meta_cap' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-money-alt',
        );
        register_post_type(self::CPT_NAME, $args);
    }

    public static function getGrossCalculationCPTName()
    {
        return self::CPT_NAME;
    }

}
