hideChat(0);
$(document).ready(function () {
    $(document).on('click', '#prime', function () {
        toggleFab();
    });
    $(document).on('click', '#chat_close_loader', function () {
        toggleFab();
    });
    $(document).on('click', '#chat_first_screen', function () {
        hideChat(1);
    });
    $(document).on('click', '#chat_second_screen', function () {
        hideChat(2);
    });
    $(document).on('click', '#btn_file_chat', function () {
        $('#chooseFileChat').trigger('click');
    });

    $(document).on('click', '#chat_fullscreen_loader', function () {
        $('.fullscreen').toggleClass('zmdi-window-maximize');
        $('.fullscreen').toggleClass('zmdi-window-restore');
        $('.chat').toggleClass('chat_fullscreen');
        $('#prime').toggleClass('is-hide');
        
        $('.header_img').toggleClass('change_img');
        $('.img_container').toggleClass('change_img');
        $('.chat_header').toggleClass('chat_header2');
        $('.fab_field').toggleClass('fab_field2');
        $('.chat_converse').toggleClass('chat_converse2');
    });
});

function toggleFab() {
    $('.prime').toggleClass('zmdi-comment-outline');
    $('.prime').toggleClass('zmdi-close');
    $('.prime').toggleClass('is-active');
    $('.prime').toggleClass('is-visible');
    $('#prime').toggleClass('is-float');
    $('.chat').toggleClass('is-visible');
    $('.fab').toggleClass('is-visible');
}

function hideChat(hide) {
    switch (hide) {
        case 0:
            $('#chat_converse').css('display', 'none');
            $('#chat_body').css('display', 'none');
            $('#chat_form').css('display', 'none');
            $('.chat_login').css('display', 'block');
            $('#chat_fullscreen').css('display', 'none');
            break;
        case 1:
            $('#chat_converse').css('display', 'block');
            $('#chat_body').css('display', 'none');
            $('#chat_form').css('display', 'none');
            $('.chat_login').css('display', 'none');
            break;
        case 2:
            $('#chat_converse').css('display', 'none');
            $('#chat_body').css('display', 'block');
            $('#chat_form').css('display', 'none');
            $('.chat_login').css('display', 'none');
            break;
    }
}

