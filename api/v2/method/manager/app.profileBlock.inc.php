<?php

/*!
 * https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2021 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */;

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $profileId = isset($_POST['profileId']) ? $_POST['profileId'] : 0;

    $profileId = helper::clearInt($profileId);

    $result = array(
        "error" => true,
        "error_code" => ERROR_UNKNOWN
    );

    $admin = new admin($dbo);
    $admin->setId($accountId);

    if (!$admin->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $admin_info = $admin->get();

    if (!$admin_info['error'] && $admin_info['access_level'] < ADMIN_ACCESS_LEVEL_MODERATOR_RIGHTS) {

        // Set state

        $account = new account($dbo, $profileId);
        $account->setState(ACCOUNT_STATE_BLOCKED);
        unset($account);

        // Remove Avatar and cover

        $moderator = new moderator($dbo);
        $moderator->rejectPhoto($profileId);
        $moderator->rejectCover($profileId);
        unset($moderator);

        // Remove from spotlight

        $spotlight = new spotlight($dbo);
        $spotlight->delete($profileId);
        unset($spotlight);

        // Remove all gallery items

        $gallery = new gallery($dbo);
        $gallery->setRequestFrom($profileId);
        $gallery->removeAll();
        unset($gallery);

        // Close all authorizations

        $auth = new auth($dbo);
        $auth->removeAll($profileId);

        $result = array(
            "error" => false,
            "error_code" => ERROR_SUCCESS
        );
    }

    echo json_encode($result);
    exit;
}
