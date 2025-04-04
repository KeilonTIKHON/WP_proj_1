<?php get_header(); ?>

<div class="container mt-5">
    <h1 class="mb-4">Product catalogue</h1>

    <?php 
    // Получаем текущую страницу
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Настроим WP_Query с пагинацией
    $args = array(
        'post_type'      => 'product',  // тип записи (например, товары)
        'posts_per_page' => 8,          // количество постов на странице
        'paged'          => $paged,    // текущая страница
    );

    // Запускаем WP_Query
    $query = new WP_Query($args);
    

 if ($query->have_posts()) : ?>
        <div class="row">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <div class="product-card">
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title(); ?>">
                        </div>
                           
                                
                            
                        

                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h5>

                            <?php $price = get_field('price'); ?>
                            <?php if ($price) : ?>
                                <p class="card-text"><strong>Price:</strong> <?php echo esc_html($price); ?> </p>
                            <?php endif; ?>

                            <p class="card-text"><?php the_excerpt(); ?></p>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">Buy</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Пагинация -->
        <!-- Пагинация -->
        <div class="pagination mt-4">
            <?php 
            // Добавляем пагинацию
            the_posts_pagination( array(
                'mid_size'  => 2,              // Количество ссылок до и после текущей
                'prev_text' => __('&laquo; Назад', 'textdomain'), // Текст для кнопки "Назад"
                'next_text' => __('Вперед &raquo;', 'textdomain'),
                 // Текст для кнопки "Вперед"
            ) );
            ?>
        </div>
    <?php else : ?>
        <p>Товары не найдены.</p>
    <?php endif; ?>
    <?php wp_reset_postdata(); // Важно сбросить пост данные ?>
</div>

<?php get_footer(); ?>