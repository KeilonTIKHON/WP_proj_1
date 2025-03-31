<?php get_header()?>

<?php
while (have_posts()) {
    the_post(); ?>
    <div>
        <h2><?php the_title() ?></h2>
        <div class="test">
            <?php the_content() ?>
        </div>
    </div>
<?php }
?>