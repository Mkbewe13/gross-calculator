<?php

namespace GrossCalculator\Wordpress\PostTypes;

class GrossCalculation
{
    private const CPT_NAME = 'gross_calculation';

    public static function register()
    {
        $labels = array(
            'name' => _x('Gross Calculations', 'Post Type General Name', 'GC'),
            'singular_name' => _x('Gross Calculation', 'Post Type Singular Name', 'GC'),
            'menu_name' => __('Gross Calculations', 'GC'),
            'name_admin_bar' => __('Gross Calculation', 'GC'),
            'archives' => __('Gross Calculation Archives', 'GC'),
            'attributes' => __('Gross Calculation Attributes', 'GC'),
            'parent_item_colon' => __('Parent Item:', 'GC'),
            'all_items' => __('All Gross Calculations', 'GC'),
            'add_new_item' => __('Add New Gross Calculation', 'GC'),
            'add_new' => __('Add New', 'GC'),
            'new_item' => __('New Gross Calculation', 'GC'),
            'edit_item' => __('Edit Gross Calculation', 'GC'),
            'update_item' => __('Update Gross Calculation', 'GC'),
            'view_item' => __('View Gross Calculation', 'GC'),
            'view_items' => __('View Gross Calculations', 'GC'),
            'search_items' => __('Search Gross Calculation', 'GC'),
            'not_found' => __('Not found', 'GC'),
            'not_found_in_trash' => __('Not found in Trash', 'GC'),
            'featured_image' => __('Featured Image', 'GC'),
            'set_featured_image' => __('Set featured image', 'GC'),
            'remove_featured_image' => __('Remove featured image', 'GC'),
            'use_featured_image' => __('Use as featured image', 'GC'),
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
            'menu_position' => 5,
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
        );
        register_post_type(self::CPT_NAME, $args);
    }

    public static function getGrossCalculationCPTName(){
        return self::CPT_NAME;
    }

}
