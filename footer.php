<footer class="bg-dark text-white text-center py-3">
    <p>&copy; <?php echo date('Y'); ?> MyTheme. All Rights Reserved.</p>
</footer>
<?php wp_footer(); ?>
<div id="auth-popup" style="display:none;">
  <div class="auth-form">
    <h2>Регистрация</h2>
    <input type="text" id="reg_login" placeholder="Логин">
    <input type="email" id="reg_email" placeholder="Email">
    <input type="text" id="reg_name" placeholder="ФИО">
    <input type="password" id="reg_pass" placeholder="Пароль">
    <button id="register_user">Зарегистрироваться</button>

    <h2>Авторизация</h2>
    <input type="email" id="auth_email" placeholder="Email">
    <input type="password" id="auth_pass" placeholder="Пароль">
    <button id="login_user">Войти</button>

    <div id="auth-message"></div>
  </div>
</div>
</body>

</html>