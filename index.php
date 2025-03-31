<?php get_header()?>

<?php
while (have_posts()) {
    the_post(); ?>
    <div>Hiii</div>
<?php }
?>

<?php get_footer() ?>