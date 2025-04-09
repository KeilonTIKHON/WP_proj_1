<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="cont_container">
            <div class="container">
                <a class="navbar-brand" href="#">MyTheme</a>



                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'navbar-nav',
                        'container' => false
                    ));
                    ?>
                </div>


            </div>
            <div class="cart_Plus_menu">
                <div id="cart-icon" style="position: relative; top:8px; left:-10px; cursor: pointer;">
                    🛒 <span id="cart-count" style="font-weight: bold;">0</span>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </div>
        </div>

        <div id="cart-popup"
            style="display:none; position: absolute; top: 50px; right: 10px; background: #fff; border: 1px solid #ccc; padding: 20px; z-index: 1000;">
            <h3>Cart</h3>
            <div id="cart-items"></div>
            <div>Total: <span id="cart-total">0 $</span></div>
            <button id="checkout-btn">place an order</button>
        </div>
    </nav>