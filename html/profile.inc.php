<?php

    /*!
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2021 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

    if (!defined("APP_SIGNATURE")) {

        header("Location: /");
        exit;
    }

    if (!auth::isSession()) {

        header('Location: /');
        exit;
    }

    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        auth::unsetSession();

        header('Location: /');
        exit;
    }

    $welcome_block = false;

    if (isset($_SESSION['welcome_block'])) {

        $welcome_block = true;

        unset($_SESSION['welcome_block']);
    }

    $profileId = $helper->getUserId($request[0]);

    $profile = new profile($dbo, $profileId);

    $profile->setRequestFrom(auth::getCurrentUserId());
    $profileInfo = $profile->get();

    if ($profileInfo['error'] === true) {

        header("Location: /");
        exit;
    }

    $myPage = false;

    if ($profileInfo['id'] == auth::getCurrentUserId()) {

        $account = new account($dbo, $profileInfo['id']);
        $account->setLastActive();
        unset($account);

        $myPage = true;

    } else {

        if (auth::getCurrentUserId() != 0 && auth::getCurrentUserGhostFeature() == 0) {

            $guests = new guests($dbo, $profileInfo['id']);
            $guests->setRequestFrom(auth::getCurrentUserId());

            $guests->add(auth::getCurrentUserId());
        }
    }

    // Cover

    $profileCoverUrl = $profileInfo['normalCoverUrl'];

    if (strlen($profileCoverUrl) == 0) {

        $profileCoverUrl = "/img/cover_none.png?x=1";
    }

    // Photo

    $profilePhotoUrl = $profileInfo['bigPhotoUrl'];
    $profileNormalPhotoUrl = $profileInfo['normalPhotoUrl'];

    if (strlen($profilePhotoUrl) == 0) {

        $profilePhotoUrl = $profileNormalPhotoUrl = "/img/profile_default_photo.png";
    }

    auth::newAuthenticityToken();

    $page_id = "profile";

    if ($myPage) {

        $page_id = "my-profile";
    }

    $css_files = array("my.css", "account.css");
    $page_title = $profileInfo['fullname']." | ".APP_TITLE;

    include_once("html/common/site_header.inc.php");

?>

<body class="profile-page">

    <?php

        include_once("html/common/site_topbar.inc.php");
    ?>


    <div class="wrap content-page">

        <div class="main-column row">

            <?php

                include_once("html/common/site_sidenav.inc.php");
            ?>

            <div class="col-lg-9 col-md-12" id="content">

                <?php

                if ($profileInfo['state'] != ACCOUNT_STATE_ENABLED) {

                    include_once("html/stubs/profile.php");

                } else {

                    ?>
                        <?php

                            if ($welcome_block) {

                                ?>
                                    <div class="card mb-3" id="welcome-block">
                                        <div class="card-header">
                                            <h3 class="card-title"><?php echo $LANG['label-welcome-title']; ?></h3>
                                            <h5 class="card-description"><?php echo $LANG['label-welcome-sub-title']; ?></h5>
                                        </div>
                                    </div>
                                <?php
                            }

                        ?>

                        <div class="main-content">

                            <?php

                            if ($myPage) {

                                ?>

                                <div class="upload-progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>

                                <?php
                            }
                            ?>


                            <div class="profile_cover" style="background-image: url(<?php echo $profileCoverUrl; ?>); background-position: <?php echo $profileInfo['coverPosition']; ?>">

                                <?php

                                if ($myPage) {

                                    ?>

                                    <div class="profile_add_cover profile-upload-actions">
                                        <span class="upload-button"><input type="file" id="photo-upload" name="uploaded_file"><?php echo $LANG['action-change-photo']; ?></span>
                                        <span class="upload-button"><input type="file" id="cover-upload" name="uploaded_file"><?php echo $LANG['page-profile-upload-cover']; ?></span>
                                    </div>

                                    <?php
                                }
                                ?>
                            </div>

                            <div id="addon_block">

                                <?php

                                if (auth::isSession() && $myPage) {

                                    ?>

                                    <a href="/account/settings" class="flat_btn noselect"><?php echo $LANG['action-edit-profile']; ?></a>

                                    <?php
                                }

                                if (!$myPage) {

                                    ?>

                                    <a data-action="block" data-toggle="modal" data-target="#profile-gift-dlg" class="flat_btn noselect gift-btn"><?php echo $LANG['label-gift']; ?></a>

                                    <?php

                                    if ($profileInfo['friend']) {

                                        ?>
                                        <a onclick="Friends.remove('<?php echo $profileInfo['id']; ?>'); return false;" class="flat_btn noselect friends-btn"><?php echo $LANG['action-remove-from-friends']; ?></a>
                                        <?php

                                    } else {

                                        if ($profileInfo['follow']) {

                                            ?>
                                            <a onclick="Friends.cancelRequest('<?php echo $profileInfo['id']; ?>'); return false;" class="flat_btn noselect friends-btn"><?php echo $LANG['action-cancel-friend-request']; ?></a>
                                            <?php

                                        } else {

                                            ?>
                                            <a onclick="Friends.sendRequest('<?php echo $profileInfo['id']; ?>'); return false;"  class="flat_btn noselect friends-btn" ><?php echo $LANG['action-add-to-friends']; ?></a>
                                            <?php
                                        }
                                    }
                                    ?>

                                    <?php

                                    if (!$profileInfo['myLike']) {

                                        ?>

                                        <a onclick="Profile.like('<?php echo $profileInfo['id']; ?>'); return false;" class="flat_btn noselect like-btn">Like</a>

                                        <?php

                                    }
                                    ?>

                                    <?php

                                    if ($profileInfo['allowMessages'] == 0 && !$profileInfo['friend']) {

                                        ?>

                                            <a data-toggle="modal" data-target="#profile-messages-not-allowed" href="javascript: void(0)" style="" class="flat_btn noselect"><?php echo $LANG['action-send-message']; ?></a>

                                        <?php

                                    } else {

                                        ?>
                                            <a href="/account/chat?chat_id=0&user_id=<?php echo $profileInfo['id']; ?>" style="" class="flat_btn noselect"><?php echo $LANG['action-send-message']; ?></a>
                                        <?php
                                    }

                                    ?>

                                    <a onclick="Report.showDialog('<?php echo $profileInfo['id']; ?>', '<?php echo REPORT_TYPE_PROFILE; ?>'); return false;" class="flat_btn noselect"><?php echo $LANG['action-report']; ?></a>

                                    <?php

                                    if ($profileInfo['blocked']) {

                                        ?>
                                        <a data-action="unblock" data-toggle="modal" data-target="#profile-unblock-dlg" onclick="Profile.getBlockBox('<?php echo $profileInfo['id']; ?>'); return false;" class="flat_btn noselect block-btn"><?php echo $LANG['action-unblock']; ?></a>
                                        <?php

                                    } else {

                                        ?>
                                        <a data-action="block" data-toggle="modal" data-target="#profile-block-dlg" onclick="Profile.getBlockBox('<?php echo $profileInfo['id']; ?>'); return false;" class="flat_btn noselect block-btn"><?php echo $LANG['action-block']; ?></a>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                }
                                ?>
                            </div>

                            <div class="profile-content standard-page">

                                <div class="user-info">

                                    <div class="">

                                        <div class="profile-user-photo-container">

                                            <span class="profile-photo-loader ">
                                                <div class="loader">
                                                    <i class="fa fa-circle-notch"></i>
                                                </div>
                                            </span>

                                            <a href="<?php echo $profileNormalPhotoUrl; ?>" class="profile_img_wrap profile-user-photo-link">
                                                <span alt="Photo" class="profile-user-photo user_image profile-user-photo-bg" style="background-image: url('<?php echo $profilePhotoUrl; ?>') " onclick="blueimp.Gallery($('.profile-user-photo-link')); return false"></span>
                                            </a>

                                            <?php

                                                $feelingImgUrl = "";

                                                if ($myPage && $profileInfo['feeling'] == 0) {

                                                    $feelingImgUrl = "/feelings/add.png";

                                                } else {

                                                    if ($profileInfo['feeling'] != 0) {

                                                        $feelingImgUrl = "/feelings/".$profileInfo['feeling'].".png";
                                                    }
                                                }

                                                if (strlen($feelingImgUrl) != 0) {

                                                    ?>
                                                        <span <?php if ($myPage) echo "data-action=\"block\" data-toggle=\"modal\" data-target=\"#profile-feeling-dlg\""; ?> class="profile-feeling-img" style="background-image: url('<?php echo $feelingImgUrl; ?>');"></span>
                                                    <?php
                                                }
                                            ?>

                                        </div>

                                        <div class="basic-info">
                                            <h1>
                                                <?php echo $profileInfo['fullname']; ?>
                                                <?php

                                                if ($profileInfo['verify'] == 1) {

                                                    ?>
                                                    <span class="user-badge user-verified-badge ml-1" rel="tooltip" title="<?php echo $LANG['label-account-verified']; ?>">
                                                            <i class="iconfont icofont-check-alt"></i>
                                                        </span>
                                                    <?php
                                                }
                                                ?>
                                            </h1>

                                            <h4 style="margin: 0">@<?php echo $profileInfo['username']; ?></h4>

                                            <?php

                                            if ($profileInfo['online']) {

                                                ?>
                                                <span class="info-item info-item-online">Online</span>
                                                <?php

                                            } else {

                                                if ($profileInfo['lastAuthorize'] == 0) {

                                                    ?>
                                                    <span class="info-item info-item-online">Offline</span>
                                                    <?php

                                                } else {

                                                    ?>
                                                    <span class="info-item info-item-online"><?php echo $profileInfo['lastAuthorizeTimeAgo']; ?></span>
                                                    <?php
                                                }
                                            }
                                            ?>

                                        </div>

                                    </div>
                                </div>



                                <!--   <div class="profile-content standard-page"> END-->
                            </div>

                        </div>

                        <?php

                        if ($profileInfo['giftsCount'] != 0) {

                            if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyGifts'] == 0) {

                                ?>
                                <div class="main-content">
                                    <div class="card border-0 mt-4 col-12 p-0" id="preview-gifts-block">
                                        <div class="card-header border-0">
                                            <h3 class="card-title"><i class="icofont icofont-gift mr-2"></i><span class="counter-button-title"><?php echo $LANG['page-gifts']; ?> <span id="stat_photos_count" class="counter-button-indicator"><?php echo $profileInfo['giftsCount']; ?></span></span></h3>
                                            <span class="action-link"><a href="/<?php echo $profileInfo['username']; ?>/gifts"><?php echo $LANG['action-show-all']; ?></a></span>
                                        </div>

                                        <div class="card-body p-2">
                                            <div class="grid-list gifts-list">

                                                <?php

                                                $gifts = new gift($dbo);
                                                $gifts->setRequestFrom($profileInfo['id']);
                                                $result = $gifts->get($profileInfo['id'], 0, 6);

                                                foreach ($result['items'] as $key => $value) {

                                                    draw::previewGiftItem($value, $profileInfo, $LANG, $helper);
                                                }
                                                ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php
                            }
                        }


                        if ($profileInfo['photosCount'] != 0) {

                            if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyGallery'] == 0) {

                                ?>
                                    <div class="main-content">
                                        <div class="card border-0 mt-4 col-12 p-0" id="preview-gallery-block">
                                            <div class="card-header border-0">
                                                <h3 class="card-title"><i class="icofont icofont-image mr-2"></i><span class="counter-button-title"><?php echo $LANG['page-gallery']; ?> <span id="stat_photos_count" class="counter-button-indicator"><?php echo $profileInfo['photosCount']; ?></span></span></h3>
                                                <span class="action-link"><a href="/<?php echo $profileInfo['username']; ?>/gallery"><?php echo $LANG['action-show-all']; ?></a></span>
                                            </div>

                                            <div class="card-body p-2">
                                                <div class="grid-list">

                                                    <?php

                                                    $gallery = new gallery($dbo);
                                                    $gallery->setRequestFrom($profileInfo['id']);
                                                    $result = $gallery->get(0, $profileInfo['id'], false, true, 1, 6);

                                                    foreach ($result['items'] as $key => $value) {

                                                        draw::previewGalleryItem($value, $LANG, $helper);
                                                    }
                                                    ?>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                <?php
                            }
                        }

                        if ($profileInfo['friendsCount'] != 0) {

                            if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyFriends'] == 0) {

                                ?>
                                <div class="main-content">
                                    <div class="card border-0 mt-4 col-12 p-0" id="preview-friends-block">
                                        <div class="card-header border-0">
                                            <h3 class="card-title"><i class="icofont icofont-users mr-2"></i><span class="counter-button-title"><?php echo $LANG['page-friends']; ?> <span id="stat_friends_count" class="counter-button-indicator"><?php echo $profileInfo['friendsCount']; ?></span></span></h3>
                                            <span class="action-link"><a href="/<?php echo $profileInfo['username']; ?>/friends"><?php echo $LANG['action-show-all']; ?></a></span>
                                        </div>

                                        <div class="card-body p-2">
                                            <div class="grid-list">

                                                <?php

                                                $friends = new friends($dbo, $profileInfo['id']);
                                                $friends->setRequestFrom($profileInfo['id']);
                                                $result = $friends->get(0, 6);

                                                foreach ($result['items'] as $key => $value) {

                                                    draw::previewFriendItem($value, $LANG, $helper);
                                                }
                                                ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php
                            }
                        }

                        if ($profileInfo['likesCount'] != 0) {

                            if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyLikes'] == 0) {

                                ?>
                                <div class="main-content">
                                    <div class="card border-0 mt-4 col-12 p-0" id="preview-likes-block">
                                        <div class="card-header border-0">
                                            <h3 class="card-title"><i class="icofont icofont-heart mr-2"></i><span class="counter-button-title"><?php echo $LANG['page-likes']; ?> <span id="stat_likes_count" class="counter-button-indicator"><?php echo $profileInfo['likesCount']; ?></span></span></h3>
                                            <span class="action-link"><a href="/<?php echo $profileInfo['username']; ?>/likes"><?php echo $LANG['action-show-all']; ?></a></span>
                                        </div>

                                        <div class="card-body p-2">
                                            <div class="grid-list">

                                                <?php

                                                $result = $profile->getFans(0, 6);

                                                foreach ($result['items'] as $key => $value) {

                                                    draw::previewPeopleItem($value, $LANG, $helper);
                                                }
                                                ?>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php
                            }
                        }

                        ?>

                        <div class="main-content profile-info-content">

                            <div class="standard-page">

                                <?php

                                include_once("html/stubs/profile_info_content.inc.php");
                                ?>
                            </div>
                        </div>
                    <?php
                }
                ?>

            </div>

        </div>

    </div>

    <?php

    if ($myPage) {

        ?>

        <div class="modal modal-form fade profile-feeling-dlg" id="profile-feeling-dlg" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title placeholder-title"><?php echo $LANG['label-select-feeling']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body pt-0">

                        <div class="loader-content p-10 m-10 d-block" style="height: 150px;">
                            <div class="loader">
                                <i class="ic icon-spin icon-spin"></i>
                            </div>
                        </div>

                        <div class="gifts-content">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $LANG['action-cancel']; ?></button>
                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    if (!$myPage && auth::getCurrentUserId() != 0) {

        ?>

        <div class="modal modal-form fade profile-gift-dlg" id="profile-gift-dlg" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title placeholder-title"><?php echo $LANG['label-select-gift']; ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body pt-0">

                        <div class="loader-content p-10 m-10 d-block" style="height: 150px;">
                            <div class="loader">
                                <i class="ic icon-spin icon-spin"></i>
                            </div>
                        </div>

                        <div class="gifts-content">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $LANG['action-cancel']; ?></button>
                        <button type="button" data-price="0" data-id="0" data-profile-id="<?php echo $profileInfo['id']; ?>" onclick="Gifts.send(this); return false;" disabled class="btn btn-primary"><?php echo $LANG['action-send']; ?></button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal modal-form fade profile-block-dlg" id="profile-block-dlg" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <form id="profile-block-form" action="/api/v2/method/blacklist.add" method="post">

                        <input type="hidden" name="accessToken" value="<?php echo auth::getAccessToken(); ?>">
                        <input type="hidden" name="accountId" value="<?php echo auth::getCurrentUserId(); ?>">

                        <input type="hidden" name="profileId" value="<?php echo $profileInfo['id']; ?>">
                        <input type="hidden" name="reason" value="">

                        <div class="modal-header">
                            <h5 class="modal-title placeholder-title"><?php echo $LANG['dlg-confirm-block-title']; ?></h5>
                            <button class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="error-summary alert alert-warning"><?php echo sprintf($LANG['msg-block-user-text'], "<strong>".$profileInfo['fullname']."</strong>", "<strong>".$profileInfo['fullname']."</strong>"); ?></div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $LANG['action-no']; ?></button>
                            <button type="button"  onclick="Profile.block('<?php echo $profileInfo['id']; ?>'); return false;" data-dismiss="modal" class="btn btn-primary"><?php echo $LANG['action-yes']; ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="modal modal-form fade profile-messages-not-allowed" id="profile-messages-not-allowed" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title placeholder-title"><?php echo $profileInfo['fullname']; ?></h5>
                        <button class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="error-summary alert alert-warning"><?php echo sprintf($LANG['label-messages-not-allowed'], "<strong>".$profileInfo['fullname']."</strong>"); ?></div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn blue" data-dismiss="modal"><?php echo $LANG['action-close']; ?></button>
                    </div>

                </div>
            </div>
        </div>

        <?php
    }
    ?>

    <?php

        include_once("html/common/site_footer.inc.php");
    ?>

        <script type="text/javascript">

            var $infobox = $('#info-box');

            $("#profile-feeling-dlg").on("show.bs.modal", function(e) {

                var $this = $(this);

                $(this).find(".modal-footer").addClass("hidden");
                $(this).find(".gifts-content").addClass("hidden");
                $(this).find(".loader-content").removeClass("hidden");
                $(this).find(".btn-primary").attr("disabled", "disabled").attr("data-id", "0").attr("data-price", "0");

                $(this).find(".gifts-content").load("/ajax/feelings/list", {limit: 25}, function() {

                    $this.find(".loader-content").addClass("hidden");
                    $this.find(".gifts-content").removeClass("hidden");
                    $this.find(".modal-footer").removeClass("hidden");
                });
            });

            $(document).on("click", ".feeling", function() {

                var $this = $(this);

                var feelingId = $this.attr('data-id');

                var feelingImgUrl = "/feelings/add.png";

                if (feelingId != 0) {

                    feelingImgUrl = $this.find('img').attr("src");
                }

                $('.profile-feeling-img').css("background-image", "url(" + feelingImgUrl + ")");

                $('#profile-feeling-dlg').modal('toggle');

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/account.setFeeling',
                    data: 'feeling=' + feelingId + "&accessToken=" + account.accessToken + "&accountId=" + account.id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        //
                    },
                    error: function(xhr, type) {

                        //
                    }
                });
            });

            $("#profile-gift-dlg").on("show.bs.modal", function(e) {

                var $this = $(this);

                $(this).find(".modal-footer").addClass("hidden");
                $(this).find(".gifts-content").addClass("hidden");
                $(this).find(".loader-content").removeClass("hidden");
                $(this).find(".btn-primary").attr("disabled", "disabled").attr("data-id", "0").attr("data-price", "0");

                $(this).find(".gifts-content").load("/ajax/gifts/list", {limit: 25}, function() {

                    $this.find(".loader-content").addClass("hidden");
                    $this.find(".gifts-content").removeClass("hidden");
                    $this.find(".modal-footer").removeClass("hidden");
                });
            });

            $(document).on("click", ".gift", function() {

                var $this = $(this);

                $('.gift').removeClass("active");

                $this.addClass("active");

                $("#profile-gift-dlg").find(".btn-primary").removeAttr("disabled").attr("data-id", $this.attr("data-id")).attr("data-price", $this.attr("data-price"));
            });

            window.Gifts || ( window.Gifts = {} );

            Gifts.send = function (element) {

                var $this = $(element);
                var $dlg = $("#profile-gift-dlg");

                $dlg.find(".modal-footer").addClass("hidden");
                $dlg.find(".gifts-content").addClass("hidden");
                $dlg.find(".loader-content").removeClass("hidden");

                if (parseInt($dlg.find('.account-balance').attr("data-balance"), 10) < parseInt($this.attr("data-price"), 10)) {

                    window.location = "/account/balance";

                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/gifts.send',
                    data: 'giftId=' + $this.attr("data-id") + '&giftTo=' + $this.attr("data-profile-id") + "&accessToken=" + account.accessToken + "&accountId=" + account.id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        if (response.hasOwnProperty('error')) {

                            if (response.error === false) {

                                location.reload();
                            }
                        }

                        $('#profile-gift-dlg').modal('toggle');
                    },
                    error: function(xhr, type) {

                        $dlg.find(".modal-footer").removeClass("hidden");
                        $dlg.find(".gifts-content").removeClass("hidden");
                        $dlg.find(".loader-content").addClass("hidden");
                    }
                });
            };

            window.Friends || ( window.Friends = {} );

            Friends.remove = function (profile_id) {

                $("a.friends-btn").text(strings.sz_action_add_to_friends);

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/friends.remove',
                    data: 'friendId=' + profile_id + "&accessToken=" + account.accessToken + "&accountId=" + account.id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("a.friends-btn").text(strings.sz_action_add_to_friends);
                        $("a.friends-btn").attr('onClick', 'Friends.sendRequest(\'' + profile_id +  '\'); return false;');
                    },
                    error: function(xhr, type){

                        $("a.friends-btn").text(strings.sz_action_remove_from_friends);
                    }
                });
            };

            Friends.cancelRequest = function (profile_id) {

                $("a.friends-btn").text(strings.sz_action_add_to_friends);

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/friends.sendRequest',
                    data: 'accountId=' + account.id + "&accessToken=" + account.accessToken + "&profileId=" + profile_id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("a.friends-btn").text(strings.sz_action_add_to_friends);
                        $("a.friends-btn").attr('onClick', 'Friends.sendRequest(\'' + profile_id +  '\'); return false;');
                    },
                    error: function(xhr, type){

                        $("a.friends-btn").text(strings.sz_action_cancel_friends_request);
                    }
                });
            };

            Friends.sendRequest = function (profile_id) {

                $("a.friends-btn").text(strings.sz_action_cancel_friends_request);

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/friends.sendRequest',
                    data: 'accountId=' + account.id + "&accessToken=" + account.accessToken + "&profileId=" + profile_id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("a.friends-btn").text(strings.sz_action_cancel_friends_request);
                        $("a.friends-btn").attr('onClick', 'Friends.cancelRequest(\'' + profile_id +  '\'); return false;');
                    },
                    error: function(xhr, type){

                        $("a.friends-btn").text(strings.sz_action_add_to_friends);
                    }
                });
            };

            window.Report || ( window.Report = {} );

            Report.showDialog = function (itemId, itemType) {

                var html = '<div id="reportModal" class="modal fade">';
                html +=' <div class="modal-dialog modal-dialog-centered" role="document">';
                html += '<div class="modal-content">';
                html += '<div class="modal-header">';
                html += '<h5 class="modal-title" id="reportModal">' + strings.sz_action_report + '</h5>'
                html += '<button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                html += '</div>'; // modal-header
                html += '<div class="modal-body">';

                html += '<a onclick="Report.send(\'' + itemId + '\', \'' + itemType + '\', \'0\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_1 + '</a>';
                html += '<a onclick="Report.send(\'' + itemId + '\', \'' + itemType + '\', \'1\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_2 + '</a>';
                html += '<a onclick="Report.send(\'' + itemId + '\', \'' + itemType + '\', \'2\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_3 + '</a>';
                html += '<a onclick="Report.send(\'' + itemId + '\', \'' + itemType + '\', \'3\'); return false;" class="box-menu-item" href="javascript:void(0)">' + strings.sz_report_reason_4 + '</a>';

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

            Report.send = function (itemId, itemType, abuseId) {

                // itemType = for next code updates

                $('#reportModal').modal('toggle');

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/profile.report',
                    data: 'accessToken=' + account.accessToken + "&accountId=" + account.id + "&profileId=" + itemId + "&reason=" + abuseId,
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

            window.Profile || ( window.Profile = {} );

            Profile.getReportBox = function(user_id, title) {

                var url = "/ajax/profile/method/report.php/?action=get-box&user_id=" + user_id;
                $.colorbox({width:"450px", href: url, title: title, fixed:true});
            };

            Profile.sendReport = function (profile_id, reason, access_token) {

                $.ajax({
                    type: 'POST',
                    url: '/ajax/profile/method/report.php',
                    data: 'profile_id=' + profile_id + "&reason=" + reason + "&access_token=" + access_token,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response) {

                        $.colorbox.close();

                    },
                    error: function(xhr, type){

                    }
                });
            };

            Profile.getBlockBox = function(profile_id) {

                var attr = $("a.block-btn").attr("data-action");

                if (typeof attr !== typeof undefined) {

                    if (attr === "block") {

                        $('#profile-block-dlg').modal('show');

                    } else {

                        $("a.block-btn").text(strings.sz_action_block);
                        $("a.block-btn").attr("data-action", "block");

                        Profile.unBlock(profile_id);
                    }
                }
            };

            Profile.block = function(profile_id) {

                $("a.block-btn").text(strings.sz_action_unblock);
                $("a.block-btn").attr("data-action", "unblock");

                $.ajax({
                    type: 'POST',
                    url: "/api/" + options.api_version + "/method/blacklist.add",
                    data: "accountId=" + account.id + "&accessToken=" + account.accessToken + "&profileId=" + profile_id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function (response) {


                    },
                    error: function (xhr, type) {

                        $("a.block-btn").text(strings.sz_action_block);
                        $("a.block-btn").attr("data-action", "block");
                    }
                });
            };


            Profile.unBlock = function(profile_id) {

                $.ajax({
                    type: 'POST',
                    url: "/api/" + options.api_version + "/method/blacklist.remove",
                    data: "accountId=" + account.id + "&accessToken=" + account.accessToken + "&profileId=" + profile_id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function (response) {


                    },
                    error: function (xhr, type) {

                    }
                });
            };

            Profile.like = function (profile_id) {

                $("a.like-btn").hide();

                $.ajax({
                    type: 'POST',
                    url: '/api/' + options.api_version + '/method/profile.like',
                    data: 'profileId=' + profile_id + "&accessToken=" + account.accessToken + "&accountId=" + account.id,
                    dataType: 'json',
                    timeout: 30000,
                    success: function(response){

                        $("a.like-btn").remove();
                    },
                    error: function(xhr, type){

                        $("a.like-btn").show();
                    }
                });
            };

            $("#photo-upload").fileupload({
                formData: {accountId: <?php echo auth::getCurrentUserId(); ?>, accessToken: "<?php echo auth::getAccessToken(); ?>", imgType: 0},
                name: 'image',
                url: "/api/" + options.api_version + "/method/profile.uploadImg",
                dropZone:  '',
                dataType: 'json',
                singleFileUploads: true,
                multiple: false,
                maxNumberOfFiles: 1,
                maxFileSize: constants.MAX_FILE_SIZE,
                acceptFileTypes: "", // or regex: /(jpeg)|(jpg)|(png)$/i
                "files":null,
                minFileSize: null,
                messages: {
                    "maxNumberOfFiles":"Maximum number of files exceeded",
                    "acceptFileTypes":"File type not allowed",
                    "maxFileSize": "File is too big",
                    "minFileSize": "File is too small"},
                process: true,
                start: function (e, data) {

                    console.log("start");

                    $('div.upload-progress').css("display", "block");
                    $('div.profile-upload-actions').addClass('hidden');

                    $("#photo-upload").trigger('start');
                },
                processfail: function(e, data) {

                    console.log("processfail");

                    if (data.files.error) {

                        $infobox.find('#info-box-message').text(data.files[0].error);
                        $infobox.modal('show');
                    }
                },
                progressall: function (e, data) {

                    console.log("progressall");

                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    $('div.upload-progress').find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
                },
                done: function (e, data) {

                    console.log("done");

                    var result = jQuery.parseJSON(data.jqXHR.responseText);

                    if (result.hasOwnProperty('error')) {

                        if (result.error === false) {

                            if (result.hasOwnProperty('lowPhotoUrl')) {

                                $("span.profile-user-photo").css("background-image", "url(" + result.lowPhotoUrl + ")");
                                $("span.avatar").css("background-image", "url(" + result.lowPhotoUrl + ")");
                                $("a.profile-user-photo-link").attr("href", result.originPhotoUrl);
                                $("img.profile-photo-avatar").attr("src", result.lowPhotoUrl);

                                $('#welcome-block').remove();
                            }

                        } else {

                            $infobox.find('#info-box-message').text(result.error_description);
                            $infobox.modal('show');
                        }
                    }

                    $("#photo-upload").trigger('done');
                },
                fail: function (e, data) {

                    console.log(data.errorThrown);
                },
                always: function (e, data) {

                    console.log("always");

                    $('div.upload-progress').css("display", "none");
                    $('div.profile-upload-actions').removeClass('hidden');

                    $("#photo-upload").trigger('always');
                }
            });

            $("#cover-upload").fileupload({
                formData: {accountId: <?php echo auth::getCurrentUserId(); ?>, accessToken: "<?php echo auth::getAccessToken(); ?>", imgType: 1},
                name: 'image',
                url: "/api/" + options.api_version + "/method/profile.uploadImg",
                dropZone:  '',
                dataType: 'json',
                singleFileUploads: true,
                multiple: false,
                maxNumberOfFiles: 1,
                maxFileSize: constants.MAX_FILE_SIZE,
                acceptFileTypes: "", // or regex: /(jpeg)|(jpg)|(png)$/i
                "files":null,
                minFileSize: null,
                messages: {
                    "maxNumberOfFiles": "Maximum number of files exceeded",
                    "acceptFileTypes": "File type not allowed",
                    "maxFileSize": "File is too big",
                    "minFileSize": "File is too small"},
                process: true,
                start: function (e, data) {

                    console.log("start");

                    $('div.upload-progress').css("display", "block");
                    $('div.profile-upload-actions').addClass('hidden');
                },
                processfail: function(e, data) {

                    console.log("processfail");

                    if (data.files.error) {

                        $infobox.find('#info-box-message').text(data.files[0].error);
                        $infobox.modal('show');
                    }
                },
                progressall: function (e, data) {

                    console.log("progressall");

                    var progress = parseInt(data.loaded / data.total * 100, 10);

                    $('div.upload-progress').find('.progress-bar').attr('aria-valuenow', progress).css('width', progress + '%').text(progress + '%');
                },
                done: function (e, data) {

                    console.log("done");

                    var result = jQuery.parseJSON(data.jqXHR.responseText);

                    if (result.hasOwnProperty('error')) {

                        if (result.error === false) {

                            if (result.hasOwnProperty('normalCoverUrl')) {

                                $("div.profile_cover").css("background-image", "url(" + result.normalCoverUrl + ")");
                            }

                        } else {

                            $infobox.find('#info-box-message').text(result.error_description);
                            $infobox.modal('show');
                        }
                    }
                },
                fail: function (e, data) {

                    console.log("always");
                },
                always: function (e, data) {

                    console.log("always");

                    $('div.upload-progress').css("display", "none");
                    $('div.profile-upload-actions').removeClass('hidden');
                }

            });

        </script>

</body>
</html>