<?php

add_action('wp_ajax_add_to_cart', 'handle_add_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'handle_add_to_cart');

add_action('wp_ajax_update_cart_qty', 'handle_update_cart_qty');
add_action('wp_ajax_nopriv_update_cart_qty', 'handle_update_cart_qty');

add_action('wp_ajax_remove_from_cart', 'handle_remove_from_cart');
add_action('wp_ajax_nopriv_remove_from_cart', 'handle_remove_from_cart');

add_action('wp_ajax_process_order', 'handle_process_order');
add_action('wp_ajax_nopriv_process_order', 'handle_process_order');
add_action('rest_api_init', function () {
    register_rest_route('myapi/v1', '/orders', [
        'methods' => 'POST',
        'callback' => 'get_user_orders_by_email',
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ]);
});
function get_user_orders_by_email(WP_REST_Request $request)
{
    $current_user = wp_get_current_user();
    $requested_email = sanitize_email($request->get_param('email'));

    if ($current_user->user_email !== $requested_email) {
        return new WP_REST_Response(['message' => 'Unauthorized.'], 403);
    }

    $query = new WP_Query([
        'post_type' => 'filed_order',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'email',
                'value' => $requested_email,
                'compare' => '='
            ]
        ]
    ]);

    $orders = [];

    if ($query->have_posts()) {
        foreach ($query->posts as $post) {
            $products_data = get_post_meta($post->ID, 'products_id', true);
            $product_list = [];

            if (!empty($products_data) && is_array($products_data)) {
                foreach ($products_data as $item) {
                    $product = get_post($item['product_id']);
                    if ($product) {
                        $product_list[] = [
                            'title' => $product->post_title,
                            'qty' => intval($item['qty']),
                        ];
                    }
                }
            }

            $orders[] = [
                'id' => $post->ID,
                'date' => get_the_date('', $post),
                'products' => $product_list,
                'status' => get_post_meta($post->ID, 'order_status', true),
            ];
        }
    }

    return rest_ensure_response($orders);
}

function handle_process_order()
{
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $cart = json_decode(stripslashes($_POST['cart']), true);

    $order_data = array(
        'post_type' => 'filed_order',
        'post_status' => 'publish',
        'post_title' => 'Order from ' . $name,
    );

    $order_id = wp_insert_post($order_data);

    if ($order_id && !is_wp_error($order_id)) {
        update_field('name', $name, $order_id);
        update_field('email', $email, $order_id);

        // Товары из корзины
        $product_ids = [];

        if (!empty($cart) && is_array($cart)) {
            foreach ($cart as $item) {
                $product_ids[] = [
                    'product_id' => intval($item['id']),
                    'qty' => intval($item['qty'])
                ];
            }


            update_post_meta($order_id, 'products_id', $product_ids);
        }

        wp_send_json_success(['order_id' => $order_id]);
    } else {
        wp_send_json_error(['message' => 'Issue with creating zapis']);
    }

    wp_die();
}


function handle_add_to_cart()
{
    $product_id = intval($_POST['id']);
    $qty = intval($_POST['qty']);
    $cart = isset($_COOKIE['cart']) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];

    $found = false;

    foreach ($cart as &$item) {
        if ($item['id'] == $product_id) {
            $item['qty'] += $qty;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $product = get_post($product_id);
        $price = get_post_meta($product_id, 'price', true);
        $cart[] = [
            'id' => $product_id,
            'name' => $product->post_title,
            'price' => floatval($price),
            'qty' => $qty,
        ];
    }

    setcookie('cart', json_encode($cart), time() + 3600 * 24 * 7, "/");
    wp_send_json_success($cart);
}


function handle_update_cart_qty()
{
    $product_id = intval($_POST['id']);
    $qty = intval($_POST['qty']);
    $cart = isset($_COOKIE['cart']) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];

    foreach ($cart as &$item) {
        if ($item['id'] == $product_id) {
            $item['qty'] = $qty;
            break;
        }
    }

    setcookie('cart', json_encode($cart), time() + 3600 * 24 * 7, "/");
    wp_send_json_success($cart);
}


function handle_remove_from_cart()
{
    $product_id = intval($_POST['id']);
    $cart = isset($_COOKIE['cart']) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];

    $cart = array_filter($cart, function ($item) use ($product_id) {
        return $item['id'] != $product_id;
    });


    $cart = array_values($cart);

    setcookie('cart', json_encode($cart), time() + 3600 * 24 * 7, "/");
    wp_send_json_success($cart);
}
add_filter('wp_nav_menu_items', function ($items, $args) {
    if (is_user_logged_in()) {
        $items .= '<li><a href="' . esc_url(site_url('/user-cabinet')) . '">Личный кабинет</a></li>';
    }
    return $items;
}, 10, 2);
add_filter('rwmb_meta_boxes', 'order_products_metabox');
function order_products_metabox($meta_boxes)
{
    $meta_boxes[] = [
        'title' => 'Order details',
        'post_types' => ['filed_order'],
        'fields' => [
            [
                'id' => 'products_id',
                'type' => 'custom_html',
                'std' => '',
                'callback' => function ($field) {
                    $products = get_post_meta(get_the_ID(), 'products_id', true);

                    if (empty($products)) {
                        echo '<p>No data on ordered products.</p>';
                        return;
                    }

                    echo '<ul>';
                    foreach ($products as $item) {
                        $product = get_post($item['product_id']);
                        echo '<li>' . esc_html($product->post_title) . ' — ' . intval($item['qty']) . ' шт.</li>';
                    }
                    echo '</ul>';
                }
            ]
        ]
    ];

    return $meta_boxes;
}
function my_theme_setup()
{

    register_nav_menus(array(
        'main-menu' => __('Main Menu', 'my-theme'),
        'footer-menu' => __('Footer Menu', 'my-theme'),
    ));


    add_theme_support('post-thumbnails');


    add_theme_support('title-tag');
}
function mytheme_setup()
{
    add_theme_support('post-thumbnails');
    add_image_size('medium', 300, 200, true);
}
add_action('after_setup_theme', 'mytheme_setup');
add_action('rest_api_init', function () {
    register_rest_route('myapi/v1', '/orders', [
        'methods' => 'GET',
        'callback' => 'get_user_orders',
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ]);
});

function get_user_orders(WP_REST_Request $request)
{
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;

    // Пример: заказы — кастомный пост-тип "order"
    $orders = get_posts([
        'post_type' => 'order',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'order_email',
                'value' => $email,
                'compare' => '='
            ]
        ]
    ]);

    $data = [];

    foreach ($orders as $order) {
        $data[] = [
            'id' => $order->ID,
            'date' => get_the_date('', $order),
            'total' => get_post_meta($order->ID, 'order_total', true),
            'status' => get_post_meta($order->ID, 'order_status', true),
        ];
    }

    return rest_ensure_response($data);
}

function custom_product_query($query)
{
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('product')) {

        $query->set('posts_per_page', 8);
    }
}

add_action('pre_get_posts', 'custom_product_query');

function mytheme_enqueue_styles()
{
    // Подключаем Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');

    // Подключаем основной стиль темы
    wp_enqueue_style('theme-style', get_stylesheet_uri());
    wp_enqueue_style('main-style', get_stylesheet_uri());

    // Подключаем Bootstrap JS + Popper.js
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_styles');
function enqueue_cart_script()
{
    wp_enqueue_script('js-cookie', 'https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js', array(), null, true);
    wp_enqueue_script('cart-js', get_template_directory_uri() . '/js/cart.js', array('jquery', 'js-cookie'), null, true);
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        wp_localize_script('cart-js', 'CurrentUserData', [
            'email' => esc_js($current_user->user_email)
        ]);
    } else {
        wp_localize_script('cart-js', 'CurrentUserData', [
            'email' => ''
        ]);
    }
    wp_localize_script('cart-js', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_cart_script');

function mytheme_register_menus()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mytheme')
    ));
}

add_action('after_setup_theme', 'mytheme_register_menus');
add_action('init', function () {
    add_post_type_support('product', 'comments');
});

function register_custom_post_type_product()
{
    $labels = array(
        'name' => 'Products',
        'singular_name' => 'Product',
        'menu_name' => 'Products',
        'name_admin_bar' => 'Product',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Product',
        'new_item' => 'New Product',
        'edit_item' => 'Edit Product',
        'view_item' => 'View Product',
        'all_items' => 'All Products',
        'search_items' => 'Search Products',
        'not_found' => 'No products found',
        'not_found_in_trash' => 'No products found in Trash'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'product'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
    );

    register_post_type('product', $args);
}
add_action('init', 'register_custom_post_type_product');

function set_product_per_page($query)
{
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('product')) {
        $query->set('posts_per_page', 12);
    }
}
add_action('pre_get_posts', 'set_product_per_page');

add_action('wp_ajax_nopriv_ajax_register_user', 'ajax_register_user');
function custom_product_pagination_query($query)
{
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('product')) {
        $query->set('posts_per_page', 8);
        $query->set('paged', (get_query_var('paged')) ? get_query_var('paged') : 1);
    }
}
add_action('pre_get_posts', 'custom_product_pagination_query');
function ajax_register_user()
{
    check_ajax_referer('auth_nonce', 'nonce');

    $login = sanitize_user($_POST['login']);
    $email = sanitize_email($_POST['email']);
    $pass = $_POST['pass'];
    $name = sanitize_text_field($_POST['name']);

    if (username_exists($login) || email_exists($email)) {
        wp_send_json(['success' => false, 'message' => 'User already exists']);
    }

    $user_id = wp_create_user($login, $pass, $email);
    wp_update_user(['ID' => $user_id, 'display_name' => $name]);

    wp_send_json(['success' => true, 'message' => 'SignUp compleate']);
}

add_action('wp_ajax_nopriv_ajax_login_user', 'ajax_login_user');

function ajax_login_user()
{
    check_ajax_referer('auth_nonce', 'nonce');

    $creds = [
        'user_login' => $_POST['email'],
        'user_password' => $_POST['pass'],
        'remember' => true
    ];

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        wp_send_json(['success' => false, 'message' => 'Invalid login or password']);
    } else {
        wp_send_json(['success' => true, 'message' => 'Welcome!']);
    }
}


function enqueue_auth_scripts()
{
    wp_enqueue_script('auth-js', get_template_directory_uri() . '/js/auth.js', ['jquery'], null, true);
    wp_localize_script('auth-js', 'auth_ajax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('auth_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_auth_scripts');
function register_custom_post_type_order()
{
    $labels = array(
        'name' => 'Orders',
        'singular_name' => 'Order',
        'menu_name' => 'Orders',
        'name_admin_bar' => 'Order',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Order',
        'new_item' => 'New Order',
        'edit_item' => 'Edit Order',
        'view_item' => 'View Order',
        'all_items' => 'All Orders',
        'search_items' => 'Search Orders',
        'not_found' => 'No Orders found',
        'not_found_in_trash' => 'No Orders found in Trash'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'order'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
    );

    register_post_type('filed_order', $args);
}
add_action('init', 'register_custom_post_type_order');

function register_product_sidebar()
{
    register_sidebar([
        'name' => 'product sidebar',
        'id' => 'product_sidebar',
        'title' => 'Top products',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'register_product_sidebar');
require get_template_directory() . '/product-rating-widget.php';

// Регистрируем виджет
function register_product_rating_widget()
{
    register_widget('Product_Rating_Widget');
}
add_action('widgets_init', 'register_product_rating_widget');



