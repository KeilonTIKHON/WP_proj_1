<?php get_header()?>

<div>404))))))))))</div>
<?php
while (have_posts()) {
    the_post(); ?>
    <div>
        <h3><a href="<?php the_permalink() ?>"><?php the_title() ?> </a> <div>GHBRJK</div></h3>
    </div>
<?php }
?>

<?php get_footer() ?>