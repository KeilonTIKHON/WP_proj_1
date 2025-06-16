<?php
/**
 * Template Name: Личный кабинет
 */
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();

$current_user = wp_get_current_user();
$rest_nonce = wp_create_nonce('wp_rest');
?>

<div class="user-cabinet">
    <h2>Hi, <?php echo esc_html($current_user->display_name); ?>!</h2>
    <p>Email: <?php echo esc_html($current_user->user_email); ?></p>

    <h3>Your orders:</h3>
    <div id="user-orders">
        <p>Loading...</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetch('/wp-json/myapi/v1/orders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': '<?php echo esc_js($rest_nonce); ?>'
            },
            body: JSON.stringify({
                email: '<?php echo esc_js($current_user->user_email); ?>'
            })
        })
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('user-orders');
                container.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    container.innerHTML = '<p>You have no orders.</p>';
                    return;
                }

                const list = document.createElement('ul');
                data.forEach(order => {
                    const item = document.createElement('li');
                    console.log(order);
                    item.innerHTML = `
    <div><strong>ID:</strong> ${order.id}</div>
    <div><strong>Date:</strong> ${order.date}</div>
    <div><strong>Products:</strong> ${
        Array.isArray(order.products) && order.products.length > 0
            ? '<ul>' + order.products.map(p => `<li>${p.title} — ${p.qty} шт.</li>`).join('') + '</ul>'
            : 'No Data'
    }</div>
    <div><strong>Status:</strong> ${order.status || 'Not chosen'}</div>
    </br>
`;
                    list.appendChild(item);
                });

                container.appendChild(list);
            })
            .catch(err => {
                console.error('REST error', err);
                document.getElementById('user-orders').innerHTML = '<p>Error loading orders.</p>';
            });
    });
</script>

<?php get_footer(); ?>