<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2019 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : '';
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $query = isset($_POST['query']) ? $_POST['query'] : '';
    $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;

    $gender = isset($_POST['gender']) ? $_POST['gender'] : -1;
    $online = isset($_POST['online']) ? $_POST['online'] : -1;
    $photo = isset($_POST['photo']) ? $_POST['photo'] : -1;
    $proMode = isset($_POST['pro']) ? $_POST['pro'] : -1;
    $ageFrom = isset($_POST['ageFrom']) ? $_POST['ageFrom'] : 13;
    $ageTo = isset($_POST['ageTo']) ? $_POST['ageTo'] : 110;

    $sex_orientation = isset($_POST['sex_orientation']) ? $_POST['sex_orientation'] : 0;

    $query = helper::clearText($query);
    $query = helper::escapeText($query);

    $userId = helper::clearInt($userId);

    $sex_orientation = helper::clearInt($sex_orientation);

    if ($gender != -1) $gender = helper::clearInt($gender);
    if ($online != -1) $online = helper::clearInt($online);
    if ($photo != -1) $photo = helper::clearInt($photo);
    if ($proMode != -1) $proMode = helper::clearInt($proMode);

    $ageFrom = helper::clearInt($ageFrom);
    $ageTo = helper::clearInt($ageTo);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $search = new find($dbo);
    $search->setRequestFrom($accountId);

    $result = $search->query($query, $userId, $gender, $online, $photo, $proMode, $ageFrom, $ageTo, $sex_orientation);

    echo json_encode($result);
    exit;
}
