<?php

    /*!
     * ifsoft.co.uk
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk, https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */


    if (!$auth->authorize(auth::getCurrentUserId(), auth::getAccessToken())) {

        header('Location: /');
        exit;
    }

    $accountId = auth::getCurrentUserId();

    $account = new account($dbo, $accountId);

    $error = false;
    $send_status = false;
    $fullname = "";

    if (auth::isSession()) {

        $ticket_email = "";
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $gender = isset($_POST['gender']) ? $_POST['gender'] : 0;

        $u_age = isset($_POST['u_age']) ? $_POST['u_age'] : 0;
        $u_sex_orientation = isset($_POST['u_sex_orientation']) ? $_POST['u_sex_orientation'] : 0;

        $u_height = isset($_POST['u_height']) ? $_POST['u_height'] : 0;
        $u_weight = isset($_POST['u_weight']) ? $_POST['u_weight'] : 0;

        $day = isset($_POST['day']) ? $_POST['day'] : 0;
        $month = isset($_POST['month']) ? $_POST['month'] : 0;
        $year = isset($_POST['year']) ? $_POST['year'] : 0;

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $location = isset($_POST['location']) ? $_POST['location'] : '';
        $facebook_page = isset($_POST['facebook_page']) ? $_POST['facebook_page'] : '';
        $instagram_page = isset($_POST['instagram_page']) ? $_POST['instagram_page'] : '';

        $iStatus = isset($_POST['iStatus']) ? $_POST['iStatus'] : 0;
        $politicalViews = isset($_POST['politicalViews']) ? $_POST['politicalViews'] : 0;
        $worldViews = isset($_POST['worldViews']) ? $_POST['worldViews'] : 0;
        $personalPriority = isset($_POST['personalPriority']) ? $_POST['personalPriority'] : 0;
        $importantInOthers = isset($_POST['importantInOthers']) ? $_POST['importantInOthers'] : 0;
        $smokingViews = isset($_POST['smokingViews']) ? $_POST['smokingViews'] : 0;
        $alcoholViews = isset($_POST['alcoholViews']) ? $_POST['alcoholViews'] : 0;
        $lookingViews = isset($_POST['lookingViews']) ? $_POST['lookingViews'] : 0;
        $interestedViews = isset($_POST['interestedViews']) ? $_POST['interestedViews'] : 0;

        $gender = helper::clearInt($gender);

        $u_age = helper::clearInt($u_age);
        $u_sex_orientation = helper::clearInt($u_sex_orientation);
        $u_height = helper::clearInt($u_height);
        $u_weight = helper::clearInt($u_weight);

        $day = helper::clearInt($day);
        $month = helper::clearInt($month);
        $year = helper::clearInt($year);

        $fullname = helper::clearText($fullname);
        $fullname = helper::escapeText($fullname);

        $status = helper::clearText($status);
        $status = helper::escapeText($status);

        $location = helper::clearText($location);
        $location = helper::escapeText($location);

        $facebook_page = helper::clearText($facebook_page);
        $facebook_page = helper::escapeText($facebook_page);

        $instagram_page = helper::clearText($instagram_page);
        $instagram_page = helper::escapeText($instagram_page);

        $iStatus = helper::clearInt($iStatus);
        $politicalViews = helper::clearInt($politicalViews);
        $worldViews = helper::clearInt($worldViews);
        $personalPriority = helper::clearInt($personalPriority);
        $importantInOthers = helper::clearInt($importantInOthers);
        $smokingViews = helper::clearInt($smokingViews);
        $alcoholViews = helper::clearInt($alcoholViews);
        $lookingViews = helper::clearInt($lookingViews);
        $interestedViews = helper::clearInt($interestedViews);

        if (auth::getAuthenticityToken() !== $token) {

            $error = true;
        }

        if (!$error) {

            if (helper::isCorrectFullname($fullname)) {

                $account->edit($fullname);
            }

            if ($u_age > 17 && $u_age < 111) {

                $account->setAge($u_age);
            }

            if ($u_sex_orientation > 0 && $u_sex_orientation < 5) {

                $account->setSexOrientation($u_sex_orientation);
            }

            if ($u_height > -1 && $u_height < 300) {

                $account->setHeight($u_height);
            }

            if ($u_weight > -1 && $u_weight < 300) {

                $account->setWeight($u_weight);
            }

            $account->setSex($gender);
            $account->setBirth($year, $month, $day);
            $account->setStatus($status);
            $account->setLocation($location);

            $account->set_iStatus($iStatus);
            $account->set_iPoliticalViews($politicalViews);
            $account->set_iWorldView($worldViews);
            $account->set_iPersonalPriority($personalPriority);
            $account->set_iImportantInOthers($importantInOthers);
            $account->set_iSmokingViews($smokingViews);
            $account->set_iAlcoholViews($alcoholViews);
            $account->set_iLooking($lookingViews);
            $account->set_iInterested($interestedViews);

            if (helper::isValidURL($facebook_page)) {

                $account->setFacebookPage($facebook_page);

            } else {

                $account->setFacebookPage("");
            }

            if (helper::isValidURL($instagram_page)) {

                $account->setInstagramPage($instagram_page);

            } else {

                $account->setInstagramPage("");
            }

            header("Location: /account/settings?error=false");
            exit;
        }

        header("Location: /account/settings?error=true");
        exit;
    }

    $account->setLastActive();

    $accountInfo = $account->get();

    auth::newAuthenticityToken();

    $page_id = "settings_profile";

    $css_files = array("main.css", "my.css");
    $page_title = $LANG['page-settings']." | ".APP_TITLE;

    include_once("html/common/site_header.inc.php");

?>

<body class="settings-page">

    <?php

        include_once("html/common/site_topbar.inc.php");
    ?>

    <div class="wrap content-page">

        <div class="main-column row">

            <?php

                include_once("html/common/site_sidenav.inc.php");
            ?>

            <div class="col-lg-9 col-md-12" id="content">

                <div class="main-content">

                    <div class="standard-page">

                        <h1><?php echo $LANG['page-profile-settings']; ?></h1>

                        <div class="tab-container">
                            <nav class="tabs">
                                <a href="/account/settings"><span class="tab active"><?php echo $LANG['page-profile-settings']; ?></span></a>
                                <a href="/account/settings/privacy"><span class="tab"><?php echo $LANG['page-privacy-settings']; ?></span></a>
                                <a href="/account/balance"><span class="tab"><?php echo $LANG['page-balance']; ?></span></a>
                                <a href="/account/settings/services"><span class="tab"><?php echo $LANG['label-services']; ?></span></a>
                                <a href="/account/settings/password"><span class="tab"><?php echo $LANG['label-password']; ?></span></a>
                                <a href="/account/settings/referrals"><span class="tab"><?php echo $LANG['page-referrals']; ?></span></a>
                                <a href="/account/settings/blacklist"><span class="tab"><?php echo $LANG['page-blacklist']; ?></span></a>
                                <a href="/account/settings/otp"><span class="tab"><?php echo $LANG['page-otp']; ?></span></a>
                                <a href="/account/settings/deactivation"><span class="tab"><?php echo $LANG['page-deactivate-account']; ?></span></a>
                            </nav>
                        </div>

                        <form accept-charset="UTF-8" action="/account/settings" autocomplete="off" class="edit_user" id="settings-form" method="post">

                            <input autocomplete="off" type="hidden" name="authenticity_token" value="<?php echo auth::getAuthenticityToken(); ?>">

                            <div class="tabbed-content">

                                <?php

                                if ( isset($_GET['error']) ) {

                                    switch ($_GET['error']) {

                                        case "true" : {

                                            ?>

                                            <div class="errors-container" style="margin-top: 15px;">
                                                <ul>
                                                    <?php echo $LANG['msg-error-unknown']; ?>
                                                </ul>
                                            </div>

                                            <?php

                                            break;
                                        }

                                        default: {

                                            ?>

                                            <div class="success-container" style="margin-top: 15px;">
                                                <ul>
                                                    <b><?php echo $LANG['label-thanks']; ?></b>
                                                    <br>
                                                    <?php echo $LANG['label-settings-saved']; ?>
                                                </ul>
                                            </div>

                                            <?php

                                            break;
                                        }
                                    }
                                }
                                ?>

                                <div class="errors-container" style="margin-top: 15px; <?php if (!$error) echo "display: none"; ?>">
                                    <ul>
                                        <?php echo $LANG['ticket-send-error']; ?>
                                    </ul>
                                </div>

                                <div class="tab-pane active form-table">

                                    <div class="profile-basics form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-settings-main-section-title']; ?></h2>
                                            <p class="info"><?php echo $LANG['label-settings-main-section-sub-title']; ?></p>
                                        </div>

                                        <div class="form-cell">
                                            <input id="fullname" name="fullname" placeholder="<?php echo $LANG['label-fullname']; ?>" maxlength="64" type="text" value="<?php echo $accountInfo['fullname']; ?>">
                                            <input id="location" name="location" placeholder="<?php echo $LANG['label-location']; ?>" maxlength="64" type="text" value="<?php echo $accountInfo['location']; ?>">
                                            <input id="facebook_page" name="facebook_page" placeholder="<?php echo $LANG['label-facebook-link']; ?>" maxlength="255" type="text" value="<?php echo $accountInfo['fb_page']; ?>">
                                            <input id="instagram_page" name="instagram_page" placeholder="<?php echo $LANG['label-instagram-link']; ?>" maxlength="255" type="text" value="<?php echo $accountInfo['instagram_page']; ?>">
                                            <textarea placeholder="<?php echo $LANG['label-status']; ?>" id="status" name="status" maxlength="400"><?php echo $accountInfo['status']; ?></textarea>

                                        </div>
                                    </div>

                                    <div class="profile-basics form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-height']." (".$LANG['label-cm'].")"; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <input id="u_height" type="number" size="3" name="u_height" value="<?php echo $accountInfo['height']; ?>">
                                        </div>
                                    </div>

                                    <div class="profile-basics form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-weight']." (".$LANG['label-kg'].")"; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <input id="u_weight" type="number" size="3" name="u_weight" value="<?php echo $accountInfo['weight']; ?>">
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-age']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="u_age" name="u_age" class="selectBox">
                                                    <option disabled value="0" <?php if ($accountInfo['age'] < 18) echo "selected=\"selected\""; ?>><?php echo $LANG['label-select-age']; ?></option>

                                                    <?php

                                                        for ($i = 18; $i <= 110; $i++) {

                                                            if ($i == $accountInfo['age']) {

                                                                echo "<option value=\"$i\" selected=\"selected\">$i</option>";

                                                            } else {

                                                                echo "<option value=\"$i\">$i</option>";
                                                            }
                                                        }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-gender']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="gender" name="gender" class="selectBox">
                                                    <option value="2" <?php if ($accountInfo['sex'] != SEX_FEMALE && $accountInfo['sex'] != SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-secret']; ?></option>
                                                    <option value="0" <?php if ($accountInfo['sex'] == SEX_MALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-male']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['sex'] == SEX_FEMALE) echo "selected=\"selected\""; ?>><?php echo $LANG['gender-female']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-sex-orientation']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="u_sex_orientation" name="u_sex_orientation" class="selectBox">
                                                    <option disabled value="0" <?php if ($accountInfo['sex_orientation'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-select-sex-orientation']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['sex_orientation'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['sex_orientation'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['sex_orientation'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['sex_orientation'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['sex-orientation-4']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-relationship-status']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="iStatus" name="iStatus" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iStatus'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iStatus'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iStatus'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iStatus'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iStatus'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iStatus'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-5']; ?></option>
                                                    <option value="6" <?php if ($accountInfo['iStatus'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-6']; ?></option>
                                                    <option value="7" <?php if ($accountInfo['iStatus'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['label-relationship-status-7']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-political-views']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="politicalViews" name="politicalViews" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iPoliticalViews'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iPoliticalViews'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iPoliticalViews'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iPoliticalViews'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iPoliticalViews'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iPoliticalViews'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-5']; ?></option>
                                                    <option value="6" <?php if ($accountInfo['iPoliticalViews'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-6']; ?></option>
                                                    <option value="7" <?php if ($accountInfo['iPoliticalViews'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-7']; ?></option>
                                                    <option value="8" <?php if ($accountInfo['iPoliticalViews'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-8']; ?></option>
                                                    <option value="9" <?php if ($accountInfo['iPoliticalViews'] == 9) echo "selected=\"selected\""; ?>><?php echo $LANG['label-political-views-9']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-world-view']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="worldViews" name="worldViews" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iWorldView'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iWorldView'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iWorldView'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iWorldView'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iWorldView'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iWorldView'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-5']; ?></option>
                                                    <option value="6" <?php if ($accountInfo['iWorldView'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-6']; ?></option>
                                                    <option value="7" <?php if ($accountInfo['iWorldView'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-7']; ?></option>
                                                    <option value="8" <?php if ($accountInfo['iWorldView'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-8']; ?></option>
                                                    <option value="9" <?php if ($accountInfo['iWorldView'] == 9) echo "selected=\"selected\""; ?>><?php echo $LANG['label-world-view-9']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-personal-priority']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="personalPriority" name="personalPriority" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iPersonalPriority'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iPersonalPriority'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iPersonalPriority'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iPersonalPriority'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iPersonalPriority'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iPersonalPriority'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-5']; ?></option>
                                                    <option value="6" <?php if ($accountInfo['iPersonalPriority'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-6']; ?></option>
                                                    <option value="7" <?php if ($accountInfo['iPersonalPriority'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-7']; ?></option>
                                                    <option value="8" <?php if ($accountInfo['iPersonalPriority'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['label-personal-priority-8']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-important-in-others']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="importantInOthers" name="importantInOthers" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iImportantInOthers'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iImportantInOthers'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iImportantInOthers'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iImportantInOthers'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iImportantInOthers'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iImportantInOthers'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-5']; ?></option>
                                                    <option value="6" <?php if ($accountInfo['iImportantInOthers'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['label-important-in-others-6']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-smoking-views']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="smokingViews" name="smokingViews" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iSmokingViews'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-smoking-views-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iSmokingViews'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-smoking-views-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iSmokingViews'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-smoking-views-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iSmokingViews'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-smoking-views-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iSmokingViews'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-smoking-views-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iSmokingViews'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-smoking-views-5']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-alcohol-views']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="alcoholViews" name="alcoholViews" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iAlcoholViews'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-alcohol-views-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iAlcoholViews'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-alcohol-views-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iAlcoholViews'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-alcohol-views-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iAlcoholViews'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-alcohol-views-3']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['iAlcoholViews'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['label-alcohol-views-4']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['iAlcoholViews'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['label-alcohol-views-5']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-you-looking']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="lookingViews" name="lookingViews" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iLooking'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-looking-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iLooking'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-looking-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iLooking'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-looking-2']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['iLooking'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-looking-3']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-you-like']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="interestedViews" name="interestedViews" class="selectBox">
                                                    <option value="0" <?php if ($accountInfo['iInterested'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-like-0']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['iInterested'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-like-1']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['iInterested'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['label-you-like-2']; ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="link-preference form-row">
                                        <div class="form-cell left">
                                            <h2><?php echo $LANG['label-birth-date']; ?></h2>
                                        </div>

                                        <div class="form-cell">
                                            <div class="opt-in">
                                                <select id="day" name="day" class="selectBox" style="width: 30%;">

                                                    <?php

                                                    for ($day = 1; $day <= 31; $day++) {

                                                        if ($day == $accountInfo['day']) {

                                                            echo "<option value=\"$day\" selected=\"selected\">$day</option>";

                                                        } else {

                                                            echo "<option value=\"$day\">$day</option>";
                                                        }
                                                    }
                                                    ?>

                                                </select>

                                                <select id="month" name="month" class="selectBox" style="width: 30%;">
                                                    <option value="0" <?php if ($accountInfo['month'] == 0) echo "selected=\"selected\""; ?>><?php echo $LANG['month-jan']; ?></option>
                                                    <option value="1" <?php if ($accountInfo['month'] == 1) echo "selected=\"selected\""; ?>><?php echo $LANG['month-feb']; ?></option>
                                                    <option value="2" <?php if ($accountInfo['month'] == 2) echo "selected=\"selected\""; ?>><?php echo $LANG['month-mar']; ?></option>
                                                    <option value="3" <?php if ($accountInfo['month'] == 3) echo "selected=\"selected\""; ?>><?php echo $LANG['month-apr']; ?></option>
                                                    <option value="4" <?php if ($accountInfo['month'] == 4) echo "selected=\"selected\""; ?>><?php echo $LANG['month-may']; ?></option>
                                                    <option value="5" <?php if ($accountInfo['month'] == 5) echo "selected=\"selected\""; ?>><?php echo $LANG['month-june']; ?></option>
                                                    <option value="6" <?php if ($accountInfo['month'] == 6) echo "selected=\"selected\""; ?>><?php echo $LANG['month-july']; ?></option>
                                                    <option value="7" <?php if ($accountInfo['month'] == 7) echo "selected=\"selected\""; ?>><?php echo $LANG['month-aug']; ?></option>
                                                    <option value="8" <?php if ($accountInfo['month'] == 8) echo "selected=\"selected\""; ?>><?php echo $LANG['month-sept']; ?></option>
                                                    <option value="9" <?php if ($accountInfo['month'] == 9) echo "selected=\"selected\""; ?>><?php echo $LANG['month-oct']; ?></option>
                                                    <option value="10" <?php if ($accountInfo['month'] == 10) echo "selected=\"selected\""; ?>><?php echo $LANG['month-nov']; ?></option>
                                                    <option value="11" <?php if ($accountInfo['month'] == 11) echo "selected=\"selected\""; ?>><?php echo $LANG['month-dec']; ?></option>
                                                </select>

                                                <select id="year" name="year" class="selectBox" style="width: 30%;">

                                                    <?php

                                                    $current_year = date("Y");

                                                    for ($year = 1915; $year <= $current_year; $year++) {

                                                        if ($year == $accountInfo['year']) {

                                                            echo "<option value=\"$year\" selected=\"selected\">$year</option>";

                                                        } else {

                                                            echo "<option value=\"$year\">$year</option>";
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <input style="margin-top: 25px" class="red" name="commit" type="submit" value="<?php echo $LANG['action-save']; ?>">

                        </form>
                    </div>


                </div>

            </div>

        </div>

    </div>


        <?php

            include_once("html/common/site_footer.inc.php");
        ?>

</body>
</html>