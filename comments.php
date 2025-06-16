<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            printf(
                _n('One comment', '%s comments', get_comments_number(), 'your-theme-textdomain'),
                number_format_i18n(get_comments_number())
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
            ]);
            ?>
        </ol>

        <?php the_comments_navigation(); ?>
    <?php endif; ?>

    <?php
    
    if (!comments_open() && get_comments_number()) :
        ?>
        <p class="no-comments"><?php _e('Comments closed.', 'your-theme-textdomain'); ?></p>
    <?php endif; ?>

    <?php
    comment_form([
        'title_reply'          => __('Leave a comment', 'your-theme-textdomain'),
        'label_submit'         => __('Send', 'your-theme-textdomain'),
        'comment_notes_before' => '',
        'comment_notes_after'  => '',
    ]);
    ?>
</div>