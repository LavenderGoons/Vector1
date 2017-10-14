$(function(){
    $('.forum-section').click(function (e) {
        var preview = $(this).find('.preview-content');
        preview.toggle();
    });
    $('#new-topic-btn, #blanket').click(function () {
       $('#blanket').toggle();
       $('.new-topic-modal').toggle();
       $('#new-topic-title').val('');
       $('#new-topic-content').val('');
    });
});
