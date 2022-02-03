<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2021 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $query = isset($_POST['query']) ? $_POST['query'] : '';
    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;

    $gender = isset($_POST['gender']) ? $_POST['gender'] : -3;
    $online = isset($_POST['online']) ? $_POST['online'] : 0;
    $photo = isset($_POST['photo']) ? $_POST['photo'] : 0;
    $proMode = isset($_POST['pro']) ? $_POST['pro'] : 0;
    $ageFrom = isset($_POST['ageFrom']) ? $_POST['ageFrom'] : 13;
    $ageTo = isset($_POST['ageTo']) ? $_POST['ageTo'] : 110;

    $sex_orientation = isset($_POST['sex_orientation']) ? $_POST['sex_orientation'] : 0;

    $distance = isset($_POST['distance']) ? $_POST['distance'] : 24855;

    $lat = isset($_POST['lat']) ? $_POST['lat'] : '39.9199';
    $lng = isset($_POST['lng']) ? $_POST['lng'] : '32.8543';

    $query = helper::clearText($query);
    $query = helper::escapeText($query);

    $itemId = helper::clearInt($itemId);

    $sex_orientation = helper::clearInt($sex_orientation);

    $gender = helper::clearInt($gender);
    $online = helper::clearInt($online);
    $photo = helper::clearInt($photo);
    $proMode = helper::clearInt($proMode);

    $ageFrom = helper::clearInt($ageFrom);
    $ageTo = helper::clearInt($ageTo);

    $distance = helper::clearInt($distance);

    $lat = helper::clearText($lat);
    $lat = helper::escapeText($lat);

    $lng = helper::clearText($lng);
    $lng = helper::escapeText($lng);

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $find = new find($dbo);
    $find->setRequestFrom($accountId);

    $result = $find->start($query, $itemId, $gender, $online, $photo, $proMode, $ageFrom, $ageTo, $sex_orientation, $distance, $lat, $lng);

    echo json_encode($result);
    exit;
}
