<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, https://ifsoft.co.uk, https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

class draw extends db_connect
{
	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    static function friendItem($profile, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['friendUserPhoto']) != 0) {

            $profilePhotoUrl = $profile['friendUserPhoto'];
        }

        ?>

        <div class="cardview-item">
            <div class="card-body">

                <a class="user-photo" href="/<?php echo $profile['friendUserUsername']; ?>">
                    <div class="cardview-img cardview-img-container">
                        <span class="card-loader-container">
                            <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                        </span>
                        <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')"></span>
                    </div>
                </a>



                <?php

                if ($profile['friendUserOnline']) {

                    ?>
                        <i class="online-label"></i>
                    <?php

                } else {

                    ?>
                        <span class="card-counter black noselect cardview-item-badge" original-title="<?php echo $LANG['label-last-seen']; ?>"><?php echo $profile['timeAgo']; ?></span>
                    <?php
                }
                ?>

                <div class="cardview-item-footer" style="position: relative;">
                    <h4 class="cardview-item-title-header">
                        <a class="cardview-item-title" href="/<?php echo $profile['friendUserUsername']; ?>">
                            <?php echo $profile['friendUserFullname']; ?>
                        </a>
                        <?php
                            if ($profile['friendUserVerify']) {

                                ?>
                                    <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified">
                                        <i class="iconfont icofont-check-alt"></i>
                                    </span>
                                <?php
                            }
                        ?>
                    </h4>
                    <?php
                        if (strlen($profile['friendLocation']) > 0) {

                            ?>
                                <div class="gray-text"><?php echo $profile['friendLocation']; ?></div>
                            <?php
                        }
                    ?>

                </div>

            </div>
        </div>

        <?php
    }

    static function guestItem($profile, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['guestUserPhoto']) != 0) {

            $profilePhotoUrl = $profile['guestUserPhoto'];
        }

        ?>

        <div class="cardview-item">
            <div class="card-body">

                <a class="user-photo" href="/<?php echo $profile['guestUserUsername']; ?>">
                    <div class="cardview-img cardview-img-container">
                        <span class="card-loader-container">
                            <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                        </span>
                        <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')"></span>
                    </div>
                </a>


                <span class="card-counter black noselect cardview-item-badge" original-title="<?php echo $LANG['label-last-visit']; ?>"><?php echo $profile['timeAgo']; ?></span>

                <?php

                    if ($profile['guestUserOnline']) {

                        ?>
                            <i class="online-label"></i>
                        <?php
                    }
                ?>

                <div class="cardview-item-footer" style="position: relative;">
                    <h4 class="cardview-item-title-header">
                        <a class="cardview-item-title" href="/<?php echo $profile['guestUserUsername']; ?>">
                            <?php echo $profile['guestUserFullname']; ?>
                        </a>
                        <?php
                        if ($profile['guestUserVerify']) {

                            ?>
                                <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified">
                                    <i class="iconfont icofont-check-alt"></i>
                                </span>
                            <?php
                        }
                        ?>
                    </h4>
                    <?php
                    if (strlen($profile['guestUserLocation']) > 0) {

                        ?>
                        <div class="gray-text"><?php echo $profile['guestUserLocation']; ?></div>
                        <?php
                    }
                    ?>

                </div>

            </div>
        </div>

        <?php
    }

    static function peopleItem($profile, $LANG, $helper = null)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['lowPhotoUrl']) != 0) {

            $profilePhotoUrl = $profile['lowPhotoUrl'];
        }

        ?>

        <li class="card-item classic-item">
            <a href="/<?php echo $profile['username']; ?>" class="card-body">
                    <span class="card-header">
                        <img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"/>
                        <?php if ($profile['online']) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                        <div class="card-content">
                            <span class="card-title"><?php echo $profile['fullname']; ?>

                                <?php

                                if ($profile['verify']) {

                                    ?>
                                    <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified">
                                        <i class="iconfont icofont-check-alt"></i>
                                    </span>
                                    <?php
                                }
                                ?>
                            </span>
                            <span class="card-username">@<?php echo $profile['username']; ?></span>

                            <?php

                            if (strlen($profile['location']) > 0) {

                                ?>
                                <span class="card-location"><?php echo $profile['location']; ?></span>
                                <?php
                            }

                            if ($profile['online']) {

                                ?>
                                <span class="card-counter green">Online</span>
                                <?php

                            } else {

                                ?>
                                <span title="<?php echo $LANG['label-last-seen']; ?>" class="card-counter black"><?php echo $profile['lastAuthorizeTimeAgo']; ?></span>
                                <?php
                            }
                            ?>
                        </div>
                    </span>
            </a>
        </li>

        <?php
    }

    static function blackListItem($profile, $LANG, $helper = null)
    {
        ?>

        <li class="card-item classic-item" data-id="<?php echo $profile['id']; ?>">
            <a href="/<?php echo $profile['blockedUserUsername']; ?>" class="card-body">
                <span class="card-header">
                    <img class="card-icon" src="<?php echo $profile['blockedUserPhotoUrl']; ?>"/>
                    <div class="card-content">
                        <span class="card-title"><?php echo $profile['blockedUserFullname']; ?>

                            <?php

                            if ($profile['blockedUserVerify']) {

                                ?>
                                <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified">
                                    <i class="iconfont icofont-check-alt"></i>
                                </span>
                                <?php
                            }
                            ?>
                        </span>
                        <span class="card-username">@<?php echo $profile['blockedUserUsername']; ?></span>

                        <?php

                        if ($profile['blockedUserOnline']) {

                            ?>
                            <span class="card-date">Online</span>
                            <?php
                        }
                        ?>

                        <span class="card-action">
                            <span class="card-act negative" onclick="BlackList.remove('<?php echo $profile['id']; ?>', '<?php echo $profile['blockedUserId']; ?>'); return false;"><?php echo $LANG['action-unblock']; ?></span>
                        </span>

                        <span class="card-counter blue"><?php echo $profile['timeAgo']; ?></span>
                    </div>
                </span>
            </a>
        </li>

        <?php
    }

    static function messageItem($message, $userInfo, $LANG, $helper = null)
    {
        $profileInfo = array("username" => "", "fullname" => "", "photoUrl" => "", "online" => false);

        if ($message['fromUserId'] == auth::getCurrentUserId()) {

            $profileInfo['username'] = auth::getCurrentUserLogin();
            $profileInfo['fullname'] = auth::getCurrentUserFullname();
            $profileInfo['photoUrl'] = auth::getCurrentUserPhotoUrl();
            $profileInfo['online'] = true;

        } else {

            $profileInfo['username'] = $userInfo['username'];
            $profileInfo['fullname'] = $userInfo['fullname'];
            $profileInfo['photoUrl'] = $userInfo['lowPhotoUrl'];
            $profileInfo['online'] = $userInfo['online'];
        }

        if (strlen($profileInfo['photoUrl']) == 0) {

            $profileInfo['photoUrl'] = "/img/profile_default_photo.png";
        }

        $time = new language(NULL, $LANG['lang-code']);

        $seen = false;

        if ($message['fromUserId'] == auth::getCurrentUserId() && $message['seenAt'] != 0 ) {

            $seen = true;
        }

        ?>

        <li class="card-item default-item message-item <?php if ($message['fromUserId'] == auth::getCurrentUserId()) echo "message-item-right"; ?>" data-id="<?php echo $message['id']; ?>">
            <div class="card-body">
                <span class="card-header">
                    <a href="/<?php echo $profileInfo['username']; ?>"><img class="card-icon" src="<?php echo $profileInfo['photoUrl']; ?>"/></a>
                    <?php if ($profileInfo['online'] && $message['fromUserId'] != auth::getCurrentUserId()) echo "<span title=\"Online\" class=\"card-online-icon\"></span>"; ?>
                    <div class="card-content">

                        <?php

                        if ($message['stickerId'] != 0) {

                            ?>
                                <img class="sticker-img" style="" alt="sticker-img" src="<?php echo $message['stickerImgUrl']; ?>">
                            <?php

                        } else {

                            ?>
                            <span class="card-status-text">

                                    <?php

                                    if (strlen($message['message']) > 0) {

                                        ?>
                                            <span class="card-status-text-message">
                                                <?php echo $message['message']; ?>
                                            </span>
                                        <?php
                                    }

                                    if (strlen($message['imgUrl']) > 0) {

                                        ?>
                                            <img class="post-img" data-href="<?php echo $message['imgUrl']; ?>" onclick="blueimp.Gallery($(this)); return false" alt="post-img" src="<?php echo $message['imgUrl']; ?>">
                                        <?php
                                    }

                                    ?>

                                    </span>
                            <?php
                        }
                        ?>

                        <span class="card-date">
                            <?php echo $time->timeAgo($message['createAt']); ?>
                            <span class="time green" style="<?php if (!$seen) echo 'display: none'; ?>" data-my-id="<?php echo $LANG['label-seen']; ?>"><?php echo $LANG['label-seen']; ?></span>
                        </span>

                    </div>
                </span>
            </div>
        </li>

        <?php
    }

    static function peopleCardviewItem($profile, $LANG, $counter = false, $counter_text = "", $counter_hint = "", $counter_color = "")
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['lowPhotoUrl']) != 0) {

            $profilePhotoUrl = $profile['lowPhotoUrl'];
        }

        ?>

        <div class="cardview-item">
            <div class="card-body">

                <a class="user-photo" href="/<?php echo $profile['username']; ?>">
                    <div class="cardview-img cardview-img-container">
                        <span class="card-loader-container">
                            <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                        </span>
                        <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')"></span>
                    </div>
                </a>

                <?php

                    if ($counter) {

                        ?>
                            <span class="card-counter <?php echo $counter_color; ?> noselect cardview-item-badge" original-title="<?php echo $counter_hint; ?>"><?php echo $counter_text; ?></span>
                        <?php
                    }
                ?>

                <?php if ($profile['online']) echo "<i class=\"online-label\"></i>"; ?>

                <div class="cardview-item-footer" style="position: relative;">
                    <h4 class="cardview-item-title-header">
                        <a class="cardview-item-title" href="/<?php echo $profile['username']; ?>">
                            <?php echo $profile['fullname']; ?>
                        </a>
                        <?php
                            if ($profile['verify']) {

                                ?>
                                <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified">
                                    <i class="iconfont icofont-check-alt"></i>
                                </span>
                                <?php
                            }
                        ?>
                    </h4>
                    <?php
                        if (strlen($profile['location']) > 0) {

                            ?>
                                <div class="gray-text"><?php echo $profile['location']; ?></div>
                            <?php
                        }
                    ?>

                </div>

            </div>
        </div>

        <?php
    }

    static function image($post, $LANG, $helper = null, $showComments = false)
    {
        $fromUserPhoto = "/img/profile_default_photo.png";

        if (strlen($post['owner']['lowPhotoUrl']) != 0) {

            $fromUserPhoto = $post['owner']['lowPhotoUrl'];
        }

        $time = new language(NULL, $LANG['lang-code']);

        ?>

        <div class="card custom-list-item post-item" data-id="<?php echo $post['id']; ?>">

            <li class="item-content">

            <div class="mb-2 item-header">

                <a href="/<?php echo $post['owner']['username']; ?>" class="item-logo" style="background-image:url(<?php echo $fromUserPhoto; ?>)"></a>

                <div class="dropdown">
                    <a class="mb-sm-0 item-menu" data-toggle="dropdown">
                        <i class="iconfont icofont-curved-down"></i>
                    </a>

                    <div class="dropdown-menu">

                        <?php

                        if ((auth::isSession() && $post['owner']['id'] == auth::getCurrentUserId())) {

                            ?>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="Gallery.remove('<?php echo $post['id']; ?>'); return false;"><?php echo $LANG['action-remove']; ?></a>
                            <?php

                        } else {

                            ?>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="Gallery.showReportDialog('<?php echo $post['id']; ?>', '<?php echo REPORT_TYPE_GALLERY_ITEM; ?>'); return false;"><?php echo $LANG['action-report']; ?></a>
                            <?php
                        }

                        ?>

                    </div>
                </div>

                <?php

                if ($post['owner']['online']) echo "<span title=\"Online\" class=\"item-logo-online\"></span>";

                ?>

                <a href="/<?php echo $post['owner']['username']; ?>" class="custom-item-link post-item-fullname"><?php echo $post['owner']['fullname']; ?></a>
                <?php

                if ($post['owner']['verified'] == 1) {

                    ?>
                    <span class="user-badge user-verified-badge ml-1" rel="tooltip" title="Verified account"><i class="iconfont icofont-check-alt"></i></span>
                    <?php


                }
                ?>

                <span class="post-item-time"><a href="/<?php echo $post['owner']['username']; ?>/gallery/<?php echo $post['id']; ?>"><?php echo $time->timeAgo($post['createAt']); ?></a></span>

            </div>

            <div class="item-meta post-item-content">

                <p class="post-text mx-2"><?php echo $post['comment']; ?></p>

                <?php

                if ($post['itemType'] == ITEM_TYPE_IMAGE && strlen($post['imgUrl'])) {

                    ?>
                    <img class="post-img" data-href="<?php echo $post['imgUrl']; ?>" onclick="blueimp.Gallery($(this)); return false" style="" alt="post-img" src="<?php echo $post['imgUrl']; ?>">
                    <?php

                } else {

                    if ($post['itemType'] == ITEM_TYPE_VIDEO && strlen($post['videoUrl']) > 0) {

                        ?>

                        <video width = "100%" height = "auto" style="max-height: 300px" controls>
                            <source src="<?php echo $post['videoUrl']; ?>" type="video/mp4">
                        </video>

                        <?php
                    }
                }
                ?>

                <div class="item-counters <?php if ($post['likesCount'] == 0 && $post['commentsCount'] == 0) echo 'gone' ?>" data-id="<?php echo $post['id']; ?>">
                    <a class="item-likes-count <?php if ($post['likesCount'] == 0) echo 'gone'; ?>" data-id="<?php echo $post['id']; ?>" href="/<?php echo $post['owner']['username']; ?>/gallery/<?php echo $post['id']; ?>/people"><?php echo $LANG['label-likes']; ?>: <span class="likes-count" data-id="<?php echo $post['id']; ?>"><?php echo $post['likesCount']; ?></span></a>
                    <a class="item-comments-count <?php if ($post['commentsCount'] == 0) echo 'gone'; ?>" data-id="<?php echo $post['id']; ?>" href="/<?php echo $post['owner']['username']; ?>/gallery/<?php echo $post['id']; ?>"><?php echo $LANG['label-comments']; ?>: <span class="comments-count" data-id="<?php echo $post['id']; ?>"><?php echo $post['commentsCount']; ?></span></a>
                </div>

                <div class="item-footer">
                    <div class="item-footer-container">
                            <span class="item-footer-button">
                                <a class="item-like-button item-footer-button <?php if ($post['myLike']) echo "active"; ?>" onclick="Item.like('<?php echo $post['id']; ?>', '<?php echo ITEM_TYPE_GALLERY; ?>'); return false;" data-id="<?php echo $post['id']; ?>">
                                    <i class="iconfont icofont-heart mr-1"></i>
                                    <?php echo $LANG['action-like']; ?>
                                </a>
                            </span>

                    </div>
                </div>

            </div

            </li>

        </div>

        <?php
    }

    static function galleryItem($photo, $LANG, $helper, $preview = false, $advanced = false)
    {

        ?>

        <div class="gallery-item <?php if ($advanced) echo 'gallery-advanced-item'; ?>" data-id="<?php echo $photo['id']; ?>">

            <div class="item-inner">

                <?php

                    if (!$preview) {

                        if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $photo['owner']['id']) {

                            ?>

                            <span title="<?php echo $LANG['action-remove']; ?>" class="action" onclick="Gallery.remove('<?php echo $photo['id']; ?>'); return false;"><i class="icon icofont icofont-close-line"></i></span>

                            <?php

                        } else {

                            ?>
                            <span title="<?php echo $LANG['action-report']; ?>" class="action" onclick="Gallery.showReportDialog('<?php echo $photo['id']; ?>', '<?php echo REPORT_TYPE_GALLERY_ITEM; ?>'); return false;"><i class="icon icofont icofont-flag"></i></span>
                            <?php
                        }
                    }

                    $previewImg = $photo['previewImgUrl'];

                    if (strlen($photo['previewVideoImgUrl']) != 0) {

                        $previewImg = $photo['previewVideoImgUrl'];
                    }

                    if (strlen($photo['videoUrl']) != 0) {

                        $previewImg = "/img/video_preview.jpg";

                        if ($photo['previewImgUrl']) {

                            $previewImg = $photo['previewImgUrl'];
                        }
                    }

                ?>

                <!--     onclick="blueimp.Gallery($(this)); return false"           -->

                <!--        <?php //echo $photo['originImgUrl']; ?>        -->

                <a class="" href="/<?php echo $photo['owner']['username']; ?>/gallery/<?php echo $photo['id']; ?>" >

                    <span class="card-loader-container">
                        <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                    </span>

                    <div class="gallery-item-preview" style="background-image:url(<?php echo $previewImg; ?>)">

                        <?php

                            if (strlen($photo['videoUrl']) != 0 && strlen($photo['previewImgUrl']) != 0) {

                                ?>
                                    <span class="video-play"></span>
                                <?php
                            }

                            if (!$preview) {

                                if ($photo['moderateAt'] == 0) {

                                    ?>
                                        <span class="info-badge warning"><i class="icon icofont icofont-info-circle"></i> <?php echo $LANG['label-wait-moderation']; ?></span>
                                    <?php

                                } else {

                                    ?>
                                        <span class="info-badge black"><i class="icon icofont icofont-clock-time"></i> <?php echo $photo['timeAgo']; ?></span>
                                    <?php
                                }

                                ?>

                                <?php
                            }

                        ?>

                    </div>
                </a>

                <?php

                    if ($advanced) {

                        $profilePhotoUrl = "/img/profile_default_photo.png";

                        if (strlen($photo['owner']['lowPhotoUrl']) != 0) {

                            $profilePhotoUrl = $photo['owner']['lowPhotoUrl'];
                        }

                        ?>
                        <div class="p-0">

                            <div class="card-item classic-item default-item p-2">
                                <div class="card-body p-0">
                                    <span class="card-header">
                                        <a href="/<?php echo $photo['owner']['username']; ?>"><img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"></a>

                                        <?php

                                            if ($photo['owner']['online']) {

                                                ?>
                                                    <span title="Online" class="card-online-icon"></span>
                                                <?php
                                            }
                                        ?>

                                        <div class="card-content">
                                            <span class="card-title">
                                                <a href="/<?php echo $photo['owner']['username']; ?>"><?php echo $photo['owner']['fullname']; ?></a>

                                                <?php

                                                    if ($photo['owner']['verified']) {

                                                        ?>
                                                            <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified"><i class="iconfont icofont-check-alt"></i></span>
                                                        <?php
                                                    }
                                                ?>

                                            </span>
                                            <span class="card-username">@<?php echo $photo['owner']['username']; ?></span>
                                        </div>
                                    </span>
                                </div>
                            </div>

                        </div>
                        <?php
                    }
                ?>

            </div>
        </div>

        <?php
    }

    static function previewGalleryItem($photo, $LANG, $helper)
    {

        ?>

        <div class="gallery-item col-3 col-lg-2 col-md-2 col-sm-3" data-id="<?php echo $photo['id']; ?>">

            <div class="item-inner">

                <?php

                $previewImg = $photo['previewImgUrl'];

                if (strlen($photo['previewVideoImgUrl']) != 0) {

                    $previewImg = $photo['previewVideoImgUrl'];
                }

                if (strlen($photo['videoUrl']) != 0) {

                    $previewImg = "/img/video_preview.jpg";
                }

                if (strlen($photo['videoUrl']) != 0 && strlen($photo['previewImgUrl']) != 0) {

                    $previewImg = $photo['previewImgUrl'];
                }

                ?>


                <a class="" href="/<?php echo $photo['owner']['username']; ?>/gallery/<?php echo $photo['id']; ?>">

                    <span class="card-loader-container">
                        <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                    </span>

                    <div class="gallery-item-preview" style="background-image:url(<?php echo $previewImg; ?>)">

                        <?php

                            if (strlen($photo['videoUrl']) != 0 && strlen($photo['previewImgUrl']) != 0) {

                                ?>
                                    <span class="video-play"></span>
                                <?php
                            }
                        ?>

                    </div>

                </a>

            </div>
        </div>

        <?php
    }

    static function previewFriendItem($item, $LANG, $helper)
    {
        ?>

        <div class="gallery-item col-3 col-lg-2 col-md-2 col-sm-3" data-id="<?php echo $item['friendUserId']; ?>">

            <div class="item-inner">

                <?php

                $previewImg = "/img/profile_default_photo.png";

                if (strlen($item['friendUserPhoto']) != 0) {

                    $previewImg = $item['friendUserPhoto'];
                }

                ?>


                <a class="" href="/<?php echo $item['friendUserUsername']; ?>" title="<?php echo $item['friendUserFullname']; ?>">

                    <span class="card-loader-container">
                        <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                    </span>

                    <div class="gallery-item-preview" style="background-image:url(<?php echo $previewImg; ?>)">

                    </div>

                </a>

            </div>
        </div>

        <?php
    }

    static function previewPeopleItem($item, $LANG, $helper)
    {
        ?>

        <div class="gallery-item col-3 col-lg-2 col-md-2 col-sm-3" data-id="<?php echo $item['id']; ?>">

            <div class="item-inner">

                <?php

                $previewImg = "/img/profile_default_photo.png";

                if (strlen($item['lowPhotoUrl']) != 0) {

                    $previewImg = $item['lowPhotoUrl'];
                }

                ?>


                <a class="" href="/<?php echo $item['username']; ?>" title="<?php echo $item['fullname']; ?>">

                    <span class="card-loader-container">
                        <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                    </span>

                    <div class="gallery-item-preview" style="background-image:url(<?php echo $previewImg; ?>)">

                    </div>

                </a>

            </div>
        </div>

        <?php
    }

    static function giftItem($item, $LANG, $helper, $preview = false, $advanced = false)
    {

        ?>

        <div class="gallery-item <?php if ($advanced) echo 'gallery-advanced-item'; ?>" data-id="<?php echo $item['id']; ?>">

            <div class="item-inner">

                <?php

                if (!$preview) {

                    if (auth::getCurrentUserId() != 0 && auth::getCurrentUserId() == $item['giftTo']) {

                        ?>

                        <span title="<?php echo $LANG['action-remove']; ?>" class="action" onclick="Gifts.remove('<?php echo $item['id']; ?>'); return false;"><i class="icon icofont icofont-close-line"></i></span>

                        <?php
                    }
                }

                $previewImg = $item['imgUrl'];

                ?>


                <span>

                    <span class="card-loader-container">
                        <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                    </span>

                    <div class="gallery-item-preview" style="background-image:url(<?php echo $previewImg; ?>)">

                        <?php

                        if (!$preview) {

                            ?>

                            <span class="info-badge black"><i class="icon icofont icofont-clock-time"></i> <?php echo $item['timeAgo']; ?></span>

                            <?php
                        }

                        ?>

                    </div>
                </span>

                <?php

                if ($advanced) {

                    $profilePhotoUrl = "/img/profile_default_photo.png";

                    if (strlen($item['giftFromUserPhoto']) != 0) {

                        $profilePhotoUrl = $item['giftFromUserPhoto'];
                    }

                    ?>
                    <div class="p-0">

                        <div class="card-item classic-item default-item p-2">
                            <div class="card-body p-0">
                                    <span class="card-header">
                                        <a href="/<?php echo $item['giftFromUserUsername']; ?>"><img class="card-icon" src="<?php echo $profilePhotoUrl; ?>"></a>

                                        <?php

                                            if ($item['giftFromUserOnline']) {

                                                ?>
                                                    <span title="Online" class="card-online-icon"></span>
                                                <?php
                                            }
                                        ?>

                                        <div class="card-content">
                                            <span class="card-title">
                                                <a href="/<?php echo $item['giftFromUserUsername']; ?>"><?php echo $item['giftFromUserFullname']; ?></a>

                                                <?php

                                                    if ($item['giftFromUserVerified']) {

                                                        ?>
                                                            <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified"><i class="iconfont icofont-check-alt"></i></span>
                                                        <?php
                                                    }
                                                ?>

                                            </span>
                                            <span class="card-username">@<?php echo $item['giftFromUserUsername']; ?></span>
                                        </div>
                                    </span>
                            </div>
                        </div>

                    </div>
                    <?php
                }
                ?>

            </div>
        </div>

        <?php
    }

    static function previewGiftItem($item, $profileInfo, $LANG, $helper)
    {

        ?>

        <div class="gift-item col p-0" data-id="<?php echo $item['id']; ?>">

            <div class="item-inner">

                <a class="" href="/<?php echo $profileInfo['username']; ?>/gifts">

                    <span class="card-loader-container">
                        <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                    </span>

                    <div class="gallery-item-preview" >
                        <img src="<?php echo $item['imgUrl']; ?>" style="">
                    </div>
                </a>

            </div>
        </div>

        <?php
    }

    static function spotlightSideNavItem($profile, $LANG, $add_me = false)
    {
        $profilePhotoUrl = "/img/profile_default_photo.png";

        if (strlen($profile['lowPhotoUrl']) != 0) {

            $profilePhotoUrl = $profile['lowPhotoUrl'];
        }

        ?>

            <div class="cardview-item" id="<?php echo $profile['id']; ?>">
                <div class="card-body">

                    <a class="user-photo" href="/<?php echo $profile['username']; ?>" onclick="<?php if ($add_me) { echo "Spotlight.prepare(); return false;"; } ?>">
                        <div class="cardview-img cardview-img-container">
                            <span class="card-loader-container">
                                <div class="loader"><i class="ic icon-spin icon-spin"></i></div>
                            </span>

                            <span class="cardview-img" style="background-image: url('<?php echo $profilePhotoUrl; ?>')"></span>

                            <?php

                                if ($profile['online']) {

                                    ?>
                                    <span title="Online" class="card-online-icon"></span>
                                    <?php
                                }

                                if ($profile['verified'] == 1) {

                                    ?>
                                    <span title="<?php echo $LANG['label-account-verified']; ?>" class="verified"><i class="iconfont icofont-check-alt p-0"></i></span>
                                    <?php
                                }
                            ?>

                            <?php

                                if ($add_me) {

                                    ?>
                                    <span class="add-me-container">
                                        <div class="icon"><i class="iconfont icofont-plus-circle"></i></div>
                                    </span>
                                    <?php
                                }
                            ?>
                        </div>
                    </a>

                    <div class="cardview-item-footer px-0 text-center" style="position: relative;">
                        <h4 class="cardview-item-title-header">
                            <a class="cardview-item-title" href="/<?php echo $profile['username']; ?>">
                                <?php echo $profile['fullname']; ?>
                            </a>
                        </h4>
                    </div>

                </div>
            </div>

        <?php
    }
}
