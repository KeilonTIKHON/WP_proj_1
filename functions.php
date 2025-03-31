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
