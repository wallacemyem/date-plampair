<?php

/*!
 * https://racconsquare.com
 * racconsquare@gmail.com
 *
 * Copyright 2012-2021 Demyanchuk Dmitry (racconsquare@gmail.com)
 */

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $hash = isset($_POST['hash']) ? $_POST['hash'] : '';

    $appType = isset($_POST['appType']) ? $_POST['appType'] : 2; // 2 = APP_TYPE_ANDROID
    $fcm_regId = isset($_POST['fcm_regId']) ? $_POST['fcm_regId'] : '';
    $lang = isset($_POST['lang']) ? $_POST['lang'] : '';

    $facebookId = isset($_POST['facebookId']) ? $_POST['facebookId'] : '';

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $referrer = isset($_POST['referrer']) ? $_POST['referrer'] : 0;

    $photoUrl = isset($_POST['photo']) ? $_POST['photo'] : '';

    $user_sex = isset($_POST['sex']) ? $_POST['sex'] : 0;
    $user_year = isset($_POST['year']) ? $_POST['year'] : 2000;
    $user_month = isset($_POST['month']) ? $_POST['month'] : 1;
    $user_day = isset($_POST['day']) ? $_POST['day'] : 1;

    $u_age = isset($_POST['age']) ? $_POST['age'] : 0;
    $u_sex_orientation = isset($_POST['sex_orientation']) ? $_POST['sex_orientation'] : 0;

    $language = isset($_POST['language']) ? $_POST['language'] : '';

    $clientId = helper::clearInt($clientId);
    $appType = helper::clearInt($appType);

    $referrer = helper::clearInt($referrer);

    $user_sex = helper::clearInt($user_sex);
    $user_year = helper::clearInt($user_year);
    $user_month = helper::clearInt($user_month);
    $user_day = helper::clearInt($user_day);

    $u_age = helper::clearInt($u_age);
    $u_sex_orientation = helper::clearInt($u_sex_orientation);

    $facebookId = helper::clearText($facebookId);

    $username = helper::clearText($username);
    $fullname = helper::clearText($fullname);
    $password = helper::clearText($password);
    $email = helper::clearText($email);
    $photoUrl = helper::clearText($photoUrl);
    $language = helper::clearText($language);

    $facebookId = helper::escapeText($facebookId);
    $username = helper::escapeText($username);
    $fullname = helper::escapeText($fullname);
    $password = helper::escapeText($password);
    $email = helper::escapeText($email);
    $photoUrl = helper::escapeText($photoUrl);
    $language = helper::escapeText($language);

    $lang = helper::clearText($lang);
    $lang = helper::escapeText($lang);

    $fcm_regId = helper::clearText($fcm_regId);
    $fcm_regId = helper::escapeText($fcm_regId);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_CLIENT_ID, "Error client Id.");
    }

    if ($hash !== md5(md5($username).CLIENT_SECRET)) {

        api::printError(ERROR_CLIENT_SECRET, "Error hash.");
    }

    $result = array("error" => true);

    $account = new account($dbo);
    $result = $account->signup($username, $fullname, $password, $email, $user_sex, $user_year, $user_month, $user_day, $u_age, $u_sex_orientation, $language);
    unset($account);

    if (!$result['error']) {

        $account = new account($dbo);
        $account->setLastActive();
        $result = $account->signin($username, $password);
        unset($account);

        if (!$result['error']) {

            $auth = new auth($dbo);
            $result = $auth->create($result['accountId'], $clientId, $appType, $fcm_regId, $lang);

            if (!$result['error']) {

                $account = new account($dbo, $result['accountId']);

                // refsys

                if ($referrer != 0) {

                    $settings = new settings($dbo);
                    $app_settings = $settings->get();
                    unset($settings);

                    $ref = new refsys($dbo);
                    $ref->setRequestFrom($account->getId());
                    $ref->setBonus($app_settings['defaultReferralBonus']['intValue']);
                    $ref->setReferrer($referrer);

                    unset($ref);
                }

                if (strlen($facebookId) != 0) {

                    $helper = new helper($dbo);

                    if ($helper->getUserIdByFacebook($facebookId) == 0) {

                        $account->setFacebookId($facebookId);
                    }

                } else {

                    $account->setFacebookId("");
                }

                $result['account'] = array();

                array_push($result['account'], $account->get());
            }
        }
    }

    echo json_encode($result);
    exit;
}
