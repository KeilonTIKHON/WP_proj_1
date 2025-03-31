<?php

function my_theme_setup() {
    // Регистрируем меню
    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'my-theme'),
        'footer-menu' => __('Footer Menu', 'my-theme'),
    ));

    // Включаем поддержку миниатюр записей
    add_theme_support('post-thumbnails');

    // Включаем поддержку <title> в <head>
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'my_theme_setup');

function my_theme_enqueue_styles() {
    // Подключаем Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');

    // Подключаем основной стиль темы
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Подключаем Bootstrap JS + Popper.js
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function mytheme_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme')
    ));
}
add_action('after_setup_theme', 'mytheme_register_menus');

function register_custom_post_type_product() {
    $labels = array(
        'name'               => 'Products',
        'singular_name'      => 'Product',
        'menu_name'          => 'Products',
        'name_admin_bar'     => 'Product',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Product',
        'new_item'           => 'New Product',
        'edit_item'          => 'Edit Product',
        'view_item'          => 'View Product',
        'all_items'          => 'All Products',
        'search_items'       => 'Search Products',
        'not_found'          => 'No products found',
        'not_found_in_trash' => 'No products found in Trash'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'product'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
    );

    register_post_type('product', $args);
}
add_action('init', 'register_custom_post_type_product');
