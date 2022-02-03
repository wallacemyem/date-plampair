<?php

    /*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

?>

    <?php

        if (auth::isSession()) {

            ?>
            <div class="modal modal-form fade spotlight-dlg" id="spotlight-dlg" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title placeholder-title"><?php echo $LANG['page-spotlight']; ?></h5>
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

                            <div class="spotlight-content">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $LANG['action-cancel']; ?></button>
                            <button type="button" onclick="Spotlight.add(this); return false;" class="btn btn-primary"><?php echo $LANG['action-accept']; ?></button>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
    ?>

    <div class="modal modal-form fade" id="info-box" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title placeholder-title" id="info-box-title"><?php echo APP_TITLE; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>

                <div class="modal-body">

                    <div id="info-box-message" class="error-summary alert alert-danger"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $LANG['action-close']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="langModal" tabindex="-1" role="dialog" aria-labelledby="langModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="langModal"><?php echo $LANG['page-language']; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php

                    foreach ($LANGS as $name => $val) {

                        echo "<a onclick=\"App.setLanguage('$val'); return false;\" class=\"box-menu-item \" href=\"javascript:void(0)\">$name</a>";
                    }

                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn blue" data-dismiss="modal"><?php echo $LANG['action-close']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls " style="display: none;">
        <div class="slides" style="width: 2048px;"></div>
        <h3 class="title hidden"></h3>
        <a class="prev text-light">‹</a>
        <a class="next text-light">›</a>
        <a class="close text-light">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>

    <div id="modal-section"></div>

    <div id="main-footer">
        <div class="wrap">

            <ul id="footer-nav">
                <li><a href="/about"><?php echo $LANG['footer-about']; ?></a></li>
                <li><a href="/terms"><?php echo $LANG['footer-terms']; ?></a></li>
                <li><a href="/gdpr"><?php echo $LANG['footer-gdpr']; ?></a></li>
                <li><a href="/support"><?php echo $LANG['footer-support']; ?></a></li>
                <li><a class="lang_link" href="javascript:void(0)" data-toggle="modal" data-target="#langModal"><?php echo $LANG['lang-name']; ?></a></li>

                <li id="footer-copyright">
                    © <?php echo APP_YEAR; ?> <?php echo APP_TITLE; ?>
                </li>
            </ul>

        </div>
    </div>


    <script type="text/javascript" src="/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="/js/my.js"></script>

    <script type="text/javascript" src="/js/jquery.autosize.js"></script>
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap-slider.min.js"></script>
    <script type="text/javascript" src="/js/load-image.all.min.js"></script>
    <script type="text/javascript" src="/js/jquery.ui.widget.js"></script>
    <script type="text/javascript" src="/js/jquery.iframe-transport.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload-process.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload-image.js"></script>
    <script type="text/javascript" src="/js/jquery.fileupload-validate.js"></script>
    <script type="text/javascript" src="/js/blueimp-gallery.js"></script>
    <script type="text/javascript" src="/js/sidenav.min.js"></script>

    <script type="text/javascript">

        var options = {

            pageId: "<?php echo $page_id; ?>",
            api_version: "<?php echo API_VERSION; ?>"
        };

        var constants = {
            MAX_FILE_SIZE: 3145728,
            IMAGE_FILE_MAX_SIZE: <?php echo IMAGE_FILE_MAX_SIZE; ?>,
            VIDEO_FILE_MAX_SIZE: <?php echo VIDEO_FILE_MAX_SIZE; ?>
        };

        var account = {

            id: "<?php echo auth::getCurrentUserId(); ?>",
            username: "<?php echo auth::getCurrentUserLogin(); ?>",
            accessToken: "<?php echo auth::getAccessToken(); ?>",
            balance: "<?php echo auth::getCurrentUserBalance(); ?>"
        };

        var strings = {

            sz_action_block: "<?php echo $LANG['action-block']; ?>",
            sz_action_unblock: "<?php echo $LANG['action-unblock']; ?>",
            sz_action_close: "<?php echo $LANG['action-close']; ?>",
            sz_action_report: "<?php echo $LANG['action-report']; ?>",
            sz_report_reason_1: "<?php echo $LANG['label-profile-report-reason-1']; ?>",
            sz_report_reason_2: "<?php echo $LANG['label-profile-report-reason-2']; ?>",
            sz_report_reason_3: "<?php echo $LANG['label-profile-report-reason-3']; ?>",
            sz_report_reason_4: "<?php echo $LANG['label-profile-report-reason-4']; ?>",
            sz_report_reason_5: "<?php echo $LANG['label-profile-report-reason-5']; ?>",
            sz_action_remove_from_friends: "<?php echo $LANG['action-remove-from-friends']; ?>",
            sz_action_cancel_friends_request: "<?php echo $LANG['action-cancel-friend-request']; ?>",
            sz_action_add_to_friends: "<?php echo $LANG['action-add-to-friends']; ?>",
            sz_message_empty_list: "<?php echo $LANG['label-empty-list']; ?>"
        };

    </script>

    <script type="text/javascript" src="/js/common.js"></script>

    <script type="text/javascript">

        <?php

            if (auth::isSession()) {

            ?>

                App.init();

            <?php
        }

        ?>

        window.App || ( window.App = {} );

    </script>