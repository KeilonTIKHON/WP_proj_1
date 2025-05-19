<?php get_header() ?>


    <div>
        <h1>This is a page</h1>
        <h2><?php the_title() ?></h2>
        <div class="test">
            <?php the_content() ?>
        </div>
    </div>

<?php get_footer() ?>