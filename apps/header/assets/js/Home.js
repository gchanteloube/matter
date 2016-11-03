
Home = function(){};
Home.prototype = {
    eventListeners: function () {
        var This = this;

        $(window).on("load", function() {
            This.masonry = $('.cloth-grid').masonry({
                itemSelector: '.cloth-item'
            });

            $(window).scroll(function (event) {
                var scrollPosition = ($(window).scrollTop() / ($(document).height() - $(window).height())) * 100;
                if (scrollPosition > 90) {
                    This.loadArticles();
                }
            });
        });

        $('.cloth-grid').on({
            mouseenter: function () {
                $(this).find('.tips-cloth-item').fadeIn();
            },
            mouseleave: function () {
                $(this).find('.tips-cloth-item').fadeOut();
            }
        }, '.cloth-item');

        $('.cloth-grid').on({
            click: function () {
                if ($('.mask-details').is(':visible')) {
                    This.closeDetails();
                } else {
                    var articleId = $(this).attr('data-id');
                    This.openDetails(articleId);
                }
            }
        }, '.cloth-item');

        $('.i-want').click(function (e) {
            var articleId = $(this).parent().attr('data-id');

            $.ajax({
                type: "POST",
                data: {article_id: articleId},
                url: '../S/Home.addToWishlist',
                success: function (data) {
                    var response = $.parseJSON(data)[0];
                    if (response == 'Article added!') {
                        App.alert({
                            container: $('#alert_container'),
                            type: 'success', // [danger, warning, success, info]
                            message: 'This article have been added in your wishlist! You will be notificated when it goes on sale.',
                            closeInSeconds: 6,
                            icon: 'info'
                        });
                    } else {
                        App.alert({
                            container: $('#alert_container'),
                            type: 'danger', // [danger, warning, success, info]
                            message: 'Sorry, your article cannot be added in your wishlist. Please try again in several minutes',
                            closeInSeconds: 6,
                            icon: 'info'
                        });
                    }
                }
            });

            e.stopPropagation();
        });

        $('.comment-input').keypress(function(e) {
            if(e.which == 13) {
                var articleId = $('.container-details').attr('data-id');
                var comment = $('.comment-input').val();

                if (articleId != 0 && articleId != '' && comment != '') {
                    $.ajax({
                        type: "POST",
                        data: {article_id: articleId, comment: comment},
                        url: '../S/Home.addComment',
                        success: function (data) {
                            $('.comment-input').val('');

                            var response = $.parseJSON(data)[0];
                            if (response == 'Comment added!') {
                                var username = $('.container-details').attr('data-username');
                                $('.list-comments-details ul').prepend('<li><label class="owner-comments-details">' + username + '</label> ' + comment + '</li>');
                            } else {
                                App.alert({
                                    container: $('#alert_container'),
                                    type: 'danger', // [danger, warning, success, info]
                                    message: 'Sorry, your comment cannot be added. Please try again in several minutes',
                                    closeInSeconds: 6,
                                    icon: 'info'
                                });
                            }
                        }
                    });
                }
            }
        });
    },

    openDetails: function (articleId) {
        var This = this;

        $.ajax({
            type: "POST",
            data: {article_id: articleId},
            url: '../S/Home.details',
            success: function (data) {
                $('.mask-details').html(data);
                $('.mask-details').show();
                $('.container-details').show();

                var height = $('.container-details').height();
                $('.container-details').css({'margin-top': '-' + (height / 2) + 'px'});

                // Click out
                $('.mask-details').click(function () {
                    This.closeDetails();
                });
                $('.container-details').click(function (e) {
                    e.stopPropagation();
                });
            }
        });
    },

    closeDetails: function () {
        $('.mask-details').html('');
        $('.i-want-details').unbind();
        $('.comment-input').unbind();
        $('.mask-details').hide();

        $('.list-comments-details ul').html('');
        $('.container-details').hide();
    },

    loadAllComments: function (articleId) {
        $.ajax({
            type: "POST",
            data: {article_id: articleId},
            url: '../S/Home.getAllComments',
            success: function (data) {
                $('.mask-details').html(data);
                $('.mask-details').show();
                $('.container-details').show();

                var height = $('.container-details').height();
                $('.container-details').css({'margin-top': '-' + (height / 2) + 'px'});
            }
        });
    },

    loadArticles: function () {
        var This = this;

        if (This.mutexLoadArticles == true) {
            This.mutexLoadArticles = false;
            $.ajax({
                type: "POST",
                data: {offset: 0, limit: 12},
                url: '../S/Home.getArticles',
                success: function (data) {
                    var html = $.parseHTML(data);
                    var newArticles = '';
                    $.each(html, function (idx) {
                        if (idx >= 2 && idx % 2 == 0) {
                            newArticles += $(html[idx]).prop('outerHTML');
                        }
                    });

                    var adding = $(newArticles);
                    This.masonry.append(adding).masonry('appended', adding);

                    This.mutexLoadArticles = true;
                }
            });
        }
    },

    initDetails: function () {
        var This = this;

        $('.i-want-details').click(function (e) {
            var articleId = $(this).parent().parent().attr('data-id');

            $.ajax({
                type: "POST",
                data: {article_id: articleId},
                url: '../S/Home.addToWishlist',
                success: function (data) {
                    This.closeDetails();

                    var response = $.parseJSON(data)[0];
                    if (response == 'Article added!') {
                        App.alert({
                            container: $('#alert_container'),
                            type: 'success', // [danger, warning, success, info]
                            message: 'This article have been added in your wishlist! You will be notificated when it goes on sale.',
                            closeInSeconds: 6,
                            icon: 'info'
                        });
                    } else {
                        App.alert({
                            container: $('#alert_container'),
                            type: 'danger', // [danger, warning, success, info]
                            message: 'Sorry, your article cannot be added in your wishlist. Please try again in several minutes',
                            closeInSeconds: 6,
                            icon: 'info'
                        });
                    }
                }
            });

            e.stopPropagation();
        });

        $('.comment-input').keypress(function(e) {
            if(e.which == 13) {
                var articleId = $('.container-details').attr('data-id');
                var comment = $('.comment-input').val();

                if (articleId != 0 && articleId != '' && comment != '') {
                    $.ajax({
                        type: "POST",
                        data: {article_id: articleId, comment: comment},
                        url: '../S/Home.addComment',
                        success: function (data) {
                            $('.comment-input').val('');

                            var response = $.parseJSON(data)[0];
                            if (response == 'Comment added!') {
                                var username = $('.container-details').attr('data-username');
                                $('.list-comments-details ul').prepend('<li><label class="owner-comments-details">' + username + '</label> ' + comment + '</li>');
                            } else {
                                App.alert({
                                    container: $('#alert_container'),
                                    type: 'danger', // [danger, warning, success, info]
                                    message: 'Sorry, your comment cannot be added. Please try again in several minutes',
                                    closeInSeconds: 6,
                                    icon: 'info'
                                });
                            }
                        }
                    });
                }
            }
        });

        $('.more-comments-details').click(function () {
            var articleId = $('.container-details').attr('data-id');
            This.loadAllComments(articleId);
        });
    },

    init: function () {
        var This = this;
        This.mutexLoadArticles = true;

        this.eventListeners();
    }
};