<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, https://ifsoft.co.uk, https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $distance = isset($_POST['distance']) ? $_POST['distance'] : 1000;
    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $liked = isset($_POST['liked']) ? $_POST['liked'] : 1;
    $matches = isset($_POST['matches']) ? $_POST['matches'] : 1;

    $sex = isset($_POST['sex']) ? $_POST['sex'] : 3; // 3 = any

    $sex_orientation = isset($_POST['sex_orientation']) ? $_POST['sex_orientation'] : 0;

    $lat = isset($_POST['lat']) ? $_POST['lat'] : '0.000000';
    $lng = isset($_POST['lng']) ? $_POST['lng'] : '0.000000';

    $distance = helper::clearInt($distance);
    $itemId = helper::clearInt($itemId);

    $liked = helper::clearInt($liked);
    $matches = helper::clearInt($matches);

    $sex = helper::clearInt($sex);

    $sex_orientation = helper::clearInt($sex_orientation);

    $lat = helper::clearText($lat);
    $lat = helper::escapeText($lat);

    $lng = helper::clearText($lng);
    $lng = helper::escapeText($lng);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $account = new account($dbo, $accountId);

    if (strlen($lat) > 0 && strlen($lng) > 0 && $itemId == 0) {

        $result = $account->setGeoLocation($lat, $lng);
    }

    $hotgame = new hotgame($dbo);
    $hotgame->setRequestFrom($accountId);

    $result = $hotgame->get($itemId, $lat, $lng, $distance, $sex, $sex_orientation, $liked, $matches);

    echo json_encode($result);
    exit;
}
