window.App || ( window.App = {} );

App.hTimer = 0;
App.time_ms = 7000;

App.init = function() {

    if (App.hTimer) clearTimeout(App.hTimer);
    App.run();
};

App.run = function() {

    $.ajax({
        type: "POST",
        url: "/api/" + options.api_version + "/method/account.getSettings",
        data: "accountId=" + account.id + "&accessToken=" + account.accessToken,
        success: function(response) {

            if (response.error === false) {

                if (response.hasOwnProperty('notificationsCount')) {

                    if (response.notificationsCount < 1) {

                        $("span.notifications-badge").addClass("hidden");
                        $('span.notifications-primary-badge').text("");

                    } else {

                        $("span.notifications-badge").removeClass("hidden");
                        $('span.notifications-primary-badge').text(response.notificationsCount);
                    }
                }

                if (response.hasOwnProperty('messagesCount')) {

                    if (response.messagesCount < 1) {

                        $("span.messages-badge").addClass("hidden");
                        $('span.messages-primary-badge').text("");

                    }  else {

                        $("span.messages-badge").removeClass("hidden");
                        $('span.messages-primary-badge').text(response.messagesCount);
                    }
                }

                if (response.hasOwnProperty('guestsCount')) {

                    if (response.guestsCount < 1) {

                        $("div.guests-badge").addClass("hidden");
                        $('span.guests-count').text("");
                        $('span.guests-primary-badge').text("");

                    }  else {

                        $("div.guests-badge").removeClass("hidden");
                        $('span.guests-count').text(response.guestsCount);
                        $('span.guests-primary-badge').text(response.guestsCount);
                    }
                }

                if (response.hasOwnProperty('newMatchesCount')) {

                    if (response.newMatchesCount < 1) {

                        $("div.matches-badge").addClass("hidden");
                        $('span.matches-count').text("");

                    }  else {

                        $("div.matches-badge").removeClass("hidden");
                        $('span.matches-count').text(response.newMatchesCount);
                    }
                }

                if (response.hasOwnProperty('newFriendsCount')) {

                    if (response.newFriendsCount < 1) {

                        $("div.friends-badge").addClass("hidden");
                        $("span.friends-badge").addClass("hidden");
                        $('span.friends-primary-badge').text("");

                    }  else {

                        $("div.friends-badge").removeClass("hidden");
                        $("span.friends-badge").removeClass("hidden");
                        $('span.friends-primary-badge').text(response.newFriendsCount);
                    }
                }
            }
        },
        complete: function() {

            // Добавляем 4 секунд для следуещего обновления | add 4 seconds to next update
            App.time_ms = App.time_ms + 4000;

            App.hTimer = setTimeout(function() {

                App.init();

            }, App.time_ms);
        }
    });
};

App.setLanguage = function(language) {

    $('#langModal').modal('toggle');
    $.cookie("lang", language, { expires : 7, path: '/' });
    location.reload();
};

window.Gallery || (window.Gallery = {});

Gallery.add = function (itemImg, itemPreviewImg, itemOriginImg) {

    itemImg = $.trim(itemImg);

    $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + '/method/gallery.new',
        data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&imgUrl=" + itemImg + "&previewImgUrl=" + itemPreviewImg + "&originImgUrl=" + itemOriginImg,
        dataType: 'json',
        timeout: 30000,
        success: function(response) {

            location.reload();
        },
        error: function(xhr, type){

        }
    });
};

Gallery.remove = function (itemId) {

    $('div.gallery-item[data-id=' + itemId + ']').hide();

    if ($('div.card[data-id=' + itemId + ']').length != 0) {

        $('div.card[data-id=' + itemId + ']').hide();
    }

    $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + "/method/gallery.remove",
        data: 'accountId=' + account.id + '&accessToken=' + account.accessToken + '&itemId=' + itemId,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('div.gallery-item[data-id=' + itemId + ']').remove();

            if ($('div.card[data-id=' + itemId + ']').length != 0) {

                $('div.card[data-id=' + itemId + ']').remove();
            }

            if (options.pageId === "gallery" && response.hasOwnProperty('html')) {

                //
            }
        },
        error: function(xhr, type){

            $('div.gallery-item[data-id=' + itemId + ']').show();
        }
    });
};

Gallery.showReportDialog = function (itemId, itemType) {

    var html = '<div id="reportModal" class="modal fade">';
    html +=' <div class="modal-dialog modal-dialog-centered" role="document">';
    html += '<div class="modal-content">';
    html += '<div class="modal-header">';
    html += '<h5 class="modal-title" id="reportModal">' + strings.sz_action_report + '</h5>'
    html += '<button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '</div>'; // modal-header
    html += '<div class="modal-body">';

    html += '<a onclick="Gallery.sendReport(\'' + itemId + '\', \'' + itemType + '\', \'0\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_1 + '</a>';
    html += '<a onclick="Gallery.sendReport(\'' + itemId + '\', \'' + itemType + '\', \'1\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_2 + '</a>';
    html += '<a onclick="Gallery.sendReport(\'' + itemId + '\', \'' + itemType + '\', \'2\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_3 + '</a>';
    html += '<a onclick="Gallery.sendReport(\'' + itemId + '\', \'' + itemType + '\', \'3\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_4 + '</a>';

    html += '</div>'; // modal-body
    html += '<div class="modal-footer">';
    html += '<button type="button" class="btn blue" data-dismiss="modal">' + strings.sz_action_close + '</button>';
    html += '</div>';  // footer
    html += '</div>';  // modal-content
    html += '</div>';  // modal-dialog
    html += '</div>';  // reportModal
    $("#modal-section").html(html);
    $("#reportModal").modal();
};

Gallery.sendReport = function (itemId, itemType, abuseId) {

    // itemType = for next code updates

    $('#reportModal').modal('toggle');

    $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + '/method/gallery.report',
        data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&itemId=" + itemId + "&abuseId=" + abuseId,
        dataType: 'json',
        timeout: 30000,
        success: function(response) {

            //
        },
        error: function(xhr, type) {

            //
        }
    });
};

window.Spotlight || (window.Spotlight = {});

Spotlight.prepare = function () {

    var spotlight_dlg = $('#spotlight-dlg');

    spotlight_dlg.modal('show');
};

Spotlight.add = function () {

    var spotlight_dlg = $('#spotlight-dlg');

    spotlight_dlg.find(".modal-footer").addClass("hidden");
    spotlight_dlg.find(".spotlight-content").addClass("hidden");
    spotlight_dlg.find(".loader-content").removeClass("hidden");

    $.ajax({
        type: 'POST',
        url: '/api/' + options.api_version + '/method/spotlight.add',
        data: "accessToken=" + account.accessToken + "&accountId=" + account.id,
        dataType: 'json',
        timeout: 30000,
        success: function(response) {

            if (response.hasOwnProperty('error')) {

                if (response.error === false) {

                    if ($("div.spotlight").length > 0) {

                        $('div.spotlight').find('.user-photo').attr('onclick', '');
                        $('div.spotlight').find('.add-me-container').remove();

                    } else {

                        location.reload();
                    }
                }
            }

            spotlight_dlg.modal('toggle');
        },
        error: function(xhr, type) {

            spotlight_dlg.find(".modal-footer").removeClass("hidden");
            spotlight_dlg.find(".spotlight-content").removeClass("hidden");
            spotlight_dlg.find(".loader-content").addClass("hidden");
        }
    });
};

window.Items || (window.Items = {});

Items.more = function (url, offset) {

    if ($('button.loading-button').length > 0) {

        $('button.loading-button').attr("disabled", "disabled");
    }

    $.ajax({
        type: 'POST',
        url: url,
        data: 'itemId=' + offset + "&loaded=" + items_loaded,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            $('header.loading-banner').remove();

            if ($('.empty-list-banner').length > 0) {

                $('.empty-list-banner').remove();
            }

            if (response.hasOwnProperty('html')){

                $("div.items-view").append(response.html);

            } else {

                $("div.content-list-page").append("<header class=\"top-banner info-banner empty-list-banner\"></header>");
            }

            if (response.hasOwnProperty('html2')){

                $("div.items-container").append(response.html2);
            }

            items_loaded = response.items_loaded;
            items_all = response.items_all;
        },
        error: function(xhr, type) {

            if ($('button.loading-button').length > 0) {

                $('button.loading-button').removeAttr("disabled");
            }
        }
    });
};

window.Item || ( window.Item = {} );

Item.like = function (itemId, itemType) {

    $.ajax({
        type: 'POST',
        url: '/api/v2/method/gallery.like',
        data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&itemId=" + itemId + "&itemType=" + itemType,
        dataType: 'json',
        timeout: 30000,
        success: function(response){

            if (response.myLike) {

                $('.item-like-button[data-id=' + itemId + ']').addClass("active");

            } else {

                $('.item-like-button[data-id=' + itemId + ']').removeClass("active");
            }

            var likesCount = 0;
            var commentsCount = 0;

            if (response.hasOwnProperty('likesCount')) {

                likesCount = response.likesCount;
            }

            if (response.hasOwnProperty('commentsCount')) {

                commentsCount = response.commentsCount;
            }

            $('.likes-count[data-id=' + itemId + ']').text(likesCount);
            $('.comments-count[data-id=' + itemId + ']').text(commentsCount);

            if (likesCount == 0 && commentsCount == 0) {

                $('.item-counters[data-id=' + itemId + ']').addClass("gone");

            } else {

                $('.item-counters[data-id=' + itemId + ']').removeClass("gone");

                if (likesCount == 0) {

                    $('.item-likes-count[data-id=' + itemId + ']').addClass("gone");

                } else {

                    $('.item-likes-count[data-id=' + itemId + ']').removeClass("gone");
                }

                if (commentsCount == 0) {

                    $('.item-comments-count[data-id=' + itemId + ']').addClass("gone");

                } else {

                    $('.item-comments-count[data-id=' + itemId + ']').removeClass("gone");
                }
            }
        },
        error: function(xhr, type){

        }
    });
}