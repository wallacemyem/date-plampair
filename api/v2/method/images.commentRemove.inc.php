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

    $commentId = isset($_POST['commentId']) ? $_POST['commentId'] : 0;

    $accountId = helper::clearInt($accountId);

    $commentId = helper::clearInt($commentId);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $images = new images($dbo);
    $images->setRequestFrom($accountId);

    $commentInfo = $images->commentsInfo($commentId);

    if ($commentInfo['fromUserId'] == $accountId) {

        $images->commentsRemove($commentId);

    } else {

        $photos = new photos($dbo);
        $photos->setRequestFrom($accountId);

        $imageInfo = $photos->info($commentInfo['imageId']);

        if ($imageInfo['fromUserId'] == $accountId) {

            $images->commentsRemove($commentId);
        }
    }

    unset($comments);
    unset($post);

    echo json_encode($result);
    exit;
}
