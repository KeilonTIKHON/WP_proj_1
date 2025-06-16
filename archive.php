<?php get_header()?>

<h2><?php the_archive_title() ?></h2>


<?php
while (have_posts()) {
    the_post(); ?>
    <div>
        <h3><a href="<?php the_permalink() ?>"><?php the_title() ?> </a> <div>GHBRJK</div></h3>
    </div>
<?php }
?>