$(function(){
    //Show forum post preview
    $('.forum-section').click(function (e) {
        var preview = $(this).find('.preview-content');
        preview.toggle();
    });
    //Display new topic modal
    $('#new-topic-btn, #blanket').click(function () {
       $('#blanket').toggle();
       $('.form-modal').toggle();
       $('#new-topic-title').val('');
       $('#new-topic-content').val('');
    });
    //Display the image url modal
    $('.profile-img-wrapper').click(function(){
        $('#blanket').toggle();
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
});