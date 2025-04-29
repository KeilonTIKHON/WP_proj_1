<?php

class Product_Rating_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'product_rating_widget',
            'Рейтинг товаров',
            ['description' => 'Показывает 5 товаров с самым высоким рейтингом.']
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $query = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 5,
            'meta_key'       => 'raiting',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        ]);

        if ($query->have_posts()) {
            echo '<ul class="product-rating-list">';
            while ($query->have_posts()) {
                $query->the_post();
                $rating = get_post_meta(get_the_ID(), 'raiting', true);
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> — ' . esc_html($rating) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Товары не найдены.</p>';
        }

        wp_reset_postdata();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Рейтинг товаров';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Заголовок:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance          = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}
