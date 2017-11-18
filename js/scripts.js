$(function(){
    //Show forum post preview
    $('.forum-section').click(function (e) {
        if (e.target != $(this).find('a.post-link')[0] && e.target != $(this).find('img')[0]) {
            var preview = $(this).find('.preview-content');
            preview.toggle();
        }
    });

    //Display new topic modal
    $('#new-topic-btn, #post-blanket').click(function () {
        $('#post-blanket').toggle();
    //    $('.form-modal').css('display','inline-flex');
        $('.form-modal').toggle();
        $('#new-topic-title').val('');
        $('#new-topic-content').val('');
    });

    $('#new-comment-btn, #comment-blanket').click(function () {
        $('#comment-blanket').toggle();
        // $('#comment-modal').css('display','inline-flex');
        $('.form-modal').toggle();
        $('#new-comment-title').val('');
     });

    //Display the image url modal
    $('.profile-img-wrapper, #profile-image-blanket, .user-img-wrapper, #user-image-blanket').click(function(){
        $('.blanket').toggle();
        // $('.form-modal').css('display','inline-flex');
        $('.form-modal').toggle();
    });
    
    //Get url from modal and update img/form input
    $('#image-url-btn').click(function () {
        $('#blanket').toggle();
        $('.form-modal').toggle();
        var img_url = $('#profile-image-input').val();
        $('#image_url').val(img_url);
        $('.profile-img-wrapper > img').attr('src', img_url);
    });

    $('#sidebar-header').click(function(){
        location.href='user.php';
    });

    $('#signout, .signout-item').click(function () {
        $.post('include/controller.php', {command: "signout"}, function(data_in){
            var data_obj = JSON.parse(data_in);
            if(data_obj.result == "success") {
                location.href='login.php';
            }
        });
    })

    $('#delete-user').click(function(){
        $('#delete-user-blanket').toggle();
        $('#delete-user-modal').toggle();
    });
});