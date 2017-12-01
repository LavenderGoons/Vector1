$(function(){

    load_section_listeners();
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
        $('.form-modal').toggle();
        $('#new-comment-title').val('');
    });

    //Display the image url modal
    $('.profile-img-wrapper, #profile-image-blanket').click(function(){
        $('#profile-image-blanket').toggle();
        $('#image-url-modal').toggle();
    });

    $('.user-img-wrapper, #user-image-blanket').click(function(){
        $('#user-image-blanket').toggle();
        $('#image-url-modal').toggle();
    });

    //Get url from modal and update img/form input
    $('#image-url-btn').click(function () {
        $('#profile-image-blanket').toggle();
        $('#image-url-modal').toggle();
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

    $('#delete-user, #delete-user-blanket').click(function(){
        $('#delete-user-blanket').toggle();
        $('#delete-user-modal').toggle();
    });

    $('#change-pass, #change-pass-blanket').click(function(){
        $('#change-pass-blanket').toggle();
        $('#change-pass-modal').toggle();
    });

    $('#select-user-content').change(function(){
        var options = getUrlParameters();
        options['user_sort'] = $(this).val();
        options['page'] = getPageName();
        options['limit'] = 50;
        options['category'] = 'all';
        if(!options['user']) {
            options['user'] = '';
        }
        var data = {"command":"load_posts", "options":options};
        get_more_posts('.user-content-box', data, true);
    });

    //When scroll is near bottom load more comments or posts
    var nearBottom = false;
    $('.content').scroll(function () {
        if($(this).attr('id') == 'content-user') {
            return false;
        }
        if(($(this).scrollTop() + $('.content').outerHeight() >= document.getElementsByClassName('content')[0].scrollHeight - 50) && !nearBottom) {
            nearBottom = true;
            var options = getUrlParameters();
            options['page'] = getPageName();

            options = get_last_item(options);

            //Dont send request if the last ID is loaded
            if(options['last_post_id'] === 'undefined' || options['last_post_id'] == 1) {
                return false;
            }
            if(options['last_comment_id'] === 'undefined' || options['last_comment_id'] == 1) {
                return false;
            }

            //Check for category, and add all if not set
            if(!options.hasOwnProperty('category')) {
                options['category'] = 'all';
            }
            //Prepare the data for php controller
            var data = {"command":"load_posts", "options":options};
            get_more_posts('.content', data, false);
        }
    });

    //Send ajax request for more posts
    function get_more_posts(content_area, options, replace) {
        //Send post request and display results       
        $.post('include/controller.php', options, function(data){
            //Reset near bottom after new content is added
            nearBottom = false;
            //Parse the PHP json encode to JSON object
            var objData = JSON.parse(data);
            //Access and display the content
            if(replace) {
                $(content_area).html(objData.content);
            } else {
                $(content_area).append(objData.content);
            }
            //Load the listeners on the new forum posts
            if(options.options.page == 'index' && objData.content.length != 0) {
                load_section_listeners();
            }
        });
    }

    //Get the last comment/post to load from
    function get_last_item(options) {
        //If on the index page or user sort posts page get the last post to fetch from
        if($('.forum-section').length > 0) {
            var last_post_id = $('.forum-section').last().attr('data-post-id');
            options['last_post_id'] = last_post_id;
        }

        //If on the post page or user sort comment page get the last comment to fetch from
        if($('.post-comment').length > 0) {
            var last_comment_id = $('.post-comment').last().attr('data-comment-id');
            options['last_comment_id'] = last_comment_id;
        }
        return options;
    }
});

/* OPERATION FUNCTIONS */

//Get the current page form the URL
function getPageName() {
    var sPageURL = decodeURIComponent(window.location.pathname);
    var page = sPageURL.substr(sPageURL.lastIndexOf('/')+1);
    page = page.substring(0, page.indexOf('.'));
    return page;
}

//Get the URL parameters and add them as object attributes
function getUrlParameters() {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    var options = {};
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        options[sParameterName[0]] = sParameterName[1];
    }
    return options;
};

//Get the post content
function get_post_content(post_id, post_area) {
    $.post('include/controller.php', {"command":"get_content", "options":{"post_id":post_id}}, function(data){
        var objData = JSON.parse(data);
        $(post_area).find('p').html(objData.content);
        $(post_area).find('img').attr('src', objData.image_url);
    });
}

//LOAD THE FORUM POST LISTENER
function load_section_listeners() {
    //Show forum post preview
    $('.forum-section').off();
    $('.forum-section').click(function (e) {
        if (e.target != $(this).find('a.post-link')[0] && e.target != $(this).find('img')[0]) {
            var preview = $(this).find('.preview-content');
            var post_id = $(this).attr('data-post-id');
            preview.toggle();
            if($('.forum-section[data-post-id='+post_id+'] > .preview-content > p').html().length == 0) {
                get_post_content(post_id, '.forum-section[data-post-id='+post_id+'] > .preview-content');
            }
        }
    });
}