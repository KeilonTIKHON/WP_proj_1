jQuery(document).ready(function($) {
    $('#open-auth-popup').on('click', function () {
        $('#auth-popup').toggle();
    });

    $('#register_user').on('click', function () {
        $.post(auth_ajax.ajaxurl, {
            action: 'ajax_register_user',
            nonce: auth_ajax.nonce,
            login: $('#reg_login').val(),
            email: $('#reg_email').val(),
            name: $('#reg_name').val(),
            pass: $('#reg_pass').val()
        }, function (res) {
            $('#auth-message').text(res.message);
            if (res.success) location.reload();
        });
    });

    $('#login_user').on('click', function () {
        $.post(auth_ajax.ajaxurl, {
            action: 'ajax_login_user',
            nonce: auth_ajax.nonce,
            email: $('#auth_email').val(),
            pass: $('#auth_pass').val()
        }, function (res) {
            $('#auth-message').text(res.message);
            if (res.success) location.reload();
        });
    });
});
