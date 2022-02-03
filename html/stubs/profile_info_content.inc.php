<ul class="collection">

    <?php


        if ($profileInfo['id'] == auth::getCurrentUserId() || $profileInfo['friend'] || $profileInfo['allowShowMyInfo'] == 0) {

            ?>

            <li class="collection-item">
                <h5 class="title"><?php echo $LANG['label-join-date']; ?></h5>
                <p><?php echo $profileInfo['createDate']; ?></p>
            </li>

            <?php

                if (strlen($profileInfo['location']) > 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-location']; ?></h5>
                        <p><?php echo $profileInfo['location']; ?></p>
                    </li>

                    <?php
                }
                ?>

                <?php

                    if (strlen($profileInfo['fb_page']) > 0) {

                        ?>

                        <li class="collection-item">
                            <h5 class="title"><?php echo $LANG['label-facebook-link']; ?></h5>
                            <p><a rel="nofollow" target="_blank" href="<?php echo $profileInfo['fb_page']; ?>"><?php echo $profileInfo['fb_page']; ?></a></p>
                        </li>

                        <?php
                    }
                ?>

                <?php

                    if (strlen($profileInfo['instagram_page']) > 0) {

                        ?>

                        <li class="collection-item">
                            <h5 class="title"><?php echo $LANG['label-instagram-link']; ?></h5>
                            <p><a rel="nofollow" target="_blank" href="<?php echo $profileInfo['instagram_page']; ?>"><?php echo $profileInfo['instagram_page']; ?></a></p>
                        </li>

                        <?php
                    }
                ?>

                <?php

                if (strlen($profileInfo['status']) > 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-status']; ?></h5>
                        <p><?php echo $profileInfo['status']; ?></p>
                    </li>

                    <?php
                }
                ?>

                <?php

                if (strlen($profileInfo['sex']) < 3) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-gender']; ?></h5>
                        <p>
                            <?php

                                switch ($profileInfo['sex']) {

                                    case 0: {

                                        echo $LANG['gender-male'];

                                        break;
                                    }

                                    case 1: {

                                        echo $LANG['gender-female'];

                                        break;
                                    }

                                    default: {

                                        echo $LANG['gender-secret'];

                                        break;
                                    }
                                }
                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['sex_orientation'] > 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-sex-orientation']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['sex_orientation']) {

                                case 1: {

                                    echo $LANG['sex-orientation-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['sex-orientation-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['sex-orientation-3'];

                                    break;
                                }

                                default : {

                                    echo $LANG['sex-orientation-4'];

                                    break;
                                }
                            }
                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['age'] > 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-age']; ?></h5>
                        <p><?php echo $profileInfo['age']; ?></p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['height'] > 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-height']; ?></h5>
                        <p><?php echo $profileInfo['height']." (".$LANG['label-cm'].")"; ?></p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['weight'] > 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-weight']; ?></h5>
                        <p><?php echo $profileInfo['weight']." (".$LANG['label-kg'].")"; ?></p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iStatus'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-relationship-status']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iStatus']) {

                                case 1: {

                                    echo $LANG['label-relationship-status-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-relationship-status-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-relationship-status-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-relationship-status-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-relationship-status-5'];

                                    break;
                                }

                                case 6: {

                                    echo $LANG['label-relationship-status-6'];

                                    break;
                                }

                                case 7: {

                                    echo $LANG['label-relationship-status-7'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-relationship-status-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iPoliticalViews'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-political-views']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iPoliticalViews']) {

                                case 1: {

                                    echo $LANG['label-political-views-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-political-views-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-political-views-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-political-views-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-political-views-5'];

                                    break;
                                }

                                case 6: {

                                    echo $LANG['label-political-views-6'];

                                    break;
                                }

                                case 7: {

                                    echo $LANG['label-political-views-7'];

                                    break;
                                }

                                case 8: {

                                    echo $LANG['label-political-views-8'];

                                    break;
                                }

                                case 9: {

                                    echo $LANG['label-political-views-9'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-political-views-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iWorldView'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-world-view']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iWorldView']) {

                                case 1: {

                                    echo $LANG['label-world-view-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-world-view-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-world-view-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-world-view-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-world-view-5'];

                                    break;
                                }

                                case 6: {

                                    echo $LANG['label-world-view-6'];

                                    break;
                                }

                                case 7: {

                                    echo $LANG['label-world-view-7'];

                                    break;
                                }

                                case 8: {

                                    echo $LANG['label-world-view-8'];

                                    break;
                                }

                                case 9: {

                                    echo $LANG['label-world-view-9'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-world-view-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iPersonalPriority'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-personal-priority']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iPersonalPriority']) {

                                case 1: {

                                    echo $LANG['label-personal-priority-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-personal-priority-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-personal-priority-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-personal-priority-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-personal-priority-5'];

                                    break;
                                }

                                case 6: {

                                    echo $LANG['label-personal-priority-6'];

                                    break;
                                }

                                case 7: {

                                    echo $LANG['label-personal-priority-7'];

                                    break;
                                }

                                case 8: {

                                    echo $LANG['label-personal-priority-8'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-personal-priority-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iImportantInOthers'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-important-in-others']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iImportantInOthers']) {

                                case 1: {

                                    echo $LANG['label-important-in-others-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-important-in-others-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-important-in-others-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-important-in-others-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-important-in-others-5'];

                                    break;
                                }

                                case 6: {

                                    echo $LANG['label-important-in-others-6'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-important-in-others-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iSmokingViews'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-smoking-views']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iSmokingViews']) {

                                case 1: {

                                    echo $LANG['label-smoking-views-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-smoking-views-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-smoking-views-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-smoking-views-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-smoking-views-5'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-smoking-views-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iAlcoholViews'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-alcohol-views']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iAlcoholViews']) {

                                case 1: {

                                    echo $LANG['label-alcohol-views-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-alcohol-views-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-alcohol-views-3'];

                                    break;
                                }

                                case 4: {

                                    echo $LANG['label-alcohol-views-4'];

                                    break;
                                }

                                case 5: {

                                    echo $LANG['label-alcohol-views-5'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-alcohol-views-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iLooking'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-profile-looking']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iLooking']) {

                                case 1: {

                                    echo $LANG['label-you-looking-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-you-looking-2'];

                                    break;
                                }

                                case 3: {

                                    echo $LANG['label-you-looking-3'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-you-looking-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

                <?php

                if ($profileInfo['iInterested'] != 0) {

                    ?>

                    <li class="collection-item">
                        <h5 class="title"><?php echo $LANG['label-profile-like']; ?></h5>
                        <p>
                            <?php

                            switch ($profileInfo['iInterested']) {

                                case 1: {

                                    echo $LANG['label-profile-you-like-1'];

                                    break;
                                }

                                case 2: {

                                    echo $LANG['label-profile-you-like-2'];

                                    break;
                                }

                                default: {

                                    echo $LANG['label-profile-you-like-0'];

                                    break;
                                }
                            }

                            ?>
                        </p>
                    </li>

                    <?php
                }

                ?>

            <?php

        } else {

            ?>

            <div class="card information-banner border-0">
                <div class="card-header">
                    <div class="card-body p-0">
                        <h5 class="m-0"><?php echo $LANG['label-info-error-permission']; ?></h5>
                    </div>
                </div>
            </div>

            <?php
        }
    ?>

</ul>