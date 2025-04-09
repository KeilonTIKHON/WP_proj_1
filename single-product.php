<?php get_header() ?>

<?php
while (have_posts()) {
    the_post(); ?>
    <div><?php ?>
        <h1>This is a product</h1>
        <h2><?php the_title() ?></h2>
        <div class="test">
            <div class="prod-image">

                <?php the_post_thumbnail(); ?>

            </div>
            <?php
            $price = get_field("price");
            $descr = get_field("description");
            $size = get_field("size");
            $color = get_field("color");

            ?>
            <div>
                
                <button onclick="addToCart('<?php echo get_the_ID(); ?>', '<?php the_title(); ?>', <?php echo get_post_meta(get_the_ID(), 'price', true); ?>)" class="add_to_cart">Add to cart</button>
            </div>

            <div class="meta_cont">
                <div class="meta_child_cont">
                    <?php
                    echo "Price: " . $price  . " $";
                    ?>
                </div>
                <div class="meta_child_cont">
                    <?php
                    echo "" . $descr;
                    ?>
                </div>
                <div class="meta_child_cont">
                    Availavle sizes: <br>
                    <?php
                    if (is_array($size)) {
                        foreach ($size as $size_key => $size_value) {
                            echo $size_value . "<br>";
                        }
                    }
                    //echo $size;
                    // ?>
                </div>
                <div class="meta_child_cont">
                    <?php
                    echo "Colors: " . $color;
                    ?>
                </div>

            </div>
            <div>
                <?php
                
                if (comments_open() || get_comments_number()) {
                    comments_template(); 
                }
                ?>
            </div>

            <?php





            //if ($value) {
            //echo wp_kses_post($value);
            //} else {
            //echo 'empty';
            // }
            //the_title();
            //the_content();
            ?>
        </div>
    </div>
<?php }
?>
<?php get_footer() ?>