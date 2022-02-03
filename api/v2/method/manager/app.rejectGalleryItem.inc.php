<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2021 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */;

if (!defined("APP_SIGNATURE")) {

    header("Location: /");
    exit;
}

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : 0;
    $fromUserId = isset($_POST['fromUserId']) ? $_POST['fromUserId'] : 0;

    $itemId = helper::clearInt($itemId);
    $fromUserId = helper::clearInt($fromUserId);

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $admin = new admin($dbo);
    $admin->setId($accountId);

    if (!$admin->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array(
        "error" => false,
        "error_code" => ERROR_SUCCESS
    );

    $admin_info = $admin->get();

    if (!$admin_info['error'] && $admin_info['access_level'] != ADMIN_ACCESS_LEVEL_READ_ONLY_RIGHTS) {

        $gallery = new gallery($dbo);
        $gallery->setRequestFrom($fromUserId);
        $result = $gallery->reject($itemId);
        unset($gallery);
    }

    unset($admin_info);

    echo json_encode($result);
    exit;
}
