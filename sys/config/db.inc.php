<?php

    /*!
     * raccoonsquare.com
     *
     * https://raccoonsquare.com
     * raccoonsquare@gmail.com
     *
     * Copyright 2012-2021 Demyanchuk Dmitry (raccoonsquare@gmail.com)
     */

$C = array();
$B = array();

$B['APP_DEMO'] = true;                                      //true = enable demo version mode

$B['APP_MESSAGES_COUNTERS'] = true;                         //true = show new messages counters
$B['APP_MYSQLI_EXTENSION'] = true;                          //if on the server is not installed mysqli extension, set to false
$B['FACEBOOK_AUTHORIZATION'] = true;                        //false = Do not show buttons Login/Signup with Facebook | true = allow display buttons Login/Signup with Facebook

$C['COMPANY_URL'] = "https://palmpair.com";                 //Company URL

$B['APP_PATH'] = "app";
$B['APP_VERSION'] = "1";
$B['APP_NAME'] = "Palm Pair";
$B['APP_TITLE'] = "Palm Pair";
$B['APP_VENDOR'] = "palmpair.com";
$B['APP_YEAR'] = "2022";
$B['APP_AUTHOR'] = "Wallace Myem"; 
$B['APP_SUPPORT_EMAIL'] = "team@brickservers.ng";
$B['APP_AUTHOR_PAGE'] = "wallace";

$B['APP_HOST'] = "palmpair.com";                 //edit to your domain, example (WARNING - without http:// and www): yourdomain.com
$B['APP_URL'] = "https://palmpair.com";           //edit to your domain url, example (WARNING - with http:// or https://): http://yourdomain.com

$B['TEMP_PATH'] = "tmp/";                                //don`t edit this option
$B['PHOTO_PATH'] = "photo/";                             //don`t edit this option
$B['COVER_PATH'] = "cover/";                             //don`t edit this option
$B['CHAT_IMAGE_PATH'] = "chat_images/";                  //don`t edit this option
$B['GIFTS_PATH'] = "gifts/";                             //don`t edit this option
$B['MY_PHOTOS_PATH'] = "gallery/";                       //don`t edit this option
$B['VIDEO_PATH'] = "video/";                             //don`t edit this option
$B['STICKERS_PATH'] = "stickers/";                       //don`t edit this option

$B['GOOGLE_PLAY_LINK'] = "https://play.google.com/store/apps/";

$B['CLIENT_ID'] = 1;                               //Client ID | Integer | For identify the application | Example: 12567
$B['CLIENT_SECRET'] = "wFt4*KBoNN_kjSdG13m1k3k=";        //Client Secret | String | Text value for identify the application | Example: wFt4*KBoNN_kjSdG13m1k3k=

// Stripe settings | Settings from Cross-Platform Mobile Payments | See documentation

$B['STRIPE_PUBLISHABLE_KEY'] = "pk_test_Fv4E5L3N8d45p3NcpyxhGYzW6r";
$B['STRIPE_SECRET_KEY'] = "sk_test_hLnPfu0vdl457M5pZInquCtvbK";

// Push notifications settings | For sending FCM (Firebase Cloud Messages) | https://raccoonsquare.com/help/how_to_create_fcm_android/

$B['GOOGLE_API_KEY'] = "AAAAkHFX7b4:APA91bFCOYXCf6sBKTzUH4BqhhO8";
$B['GOOGLE_SENDER_ID'] = "345345345345";

$B['FIREBASE_API_KEY'] = $B['GOOGLE_API_KEY'];
$B['FIREBASE_SENDER_ID'] = $B['GOOGLE_SENDER_ID'];

// Firebase project id need for OTP verification

$B['FIREBASE_PROJECT_ID'] = "dating-app-456456";

// Facebook settings | For login/signup with facebook | https://raccoonsquare.com/help/how_to_create_facebook_application_and_get_app_id_and_app_secret/

$B['FACEBOOK_APP_ID'] = "345345345345";
$B['FACEBOOK_APP_SECRET'] = "e961f4ead1ddf6ef08f1e4as91690be56b24";

// Recaptcha settings | Create you keys for reCAPTCHA v3 | https://www.google.com/recaptcha/admin/create

$B['RECAPTCHA_SITE_KEY'] = "6LfE6lEeAAAAADqKeJumhZ7oFwziXgyDEcmkaWMw";
$B['RECAPTCHA_SECRET_KEY'] = "6LfE6lEeAAAAAO8Wb4ASIZTCn5jBUORIB-9Y4bLm";

// SMTP Settings | For password recovery

$B['SMTP_HOST'] = 'yousite.com';                         //SMTP host
$B['SMTP_AUTH'] = true;                                     //SMTP auth (Enable SMTP authentication)
$B['SMTP_SECURE'] = 'tls';                                  //SMTP secure (Enable TLS encryption, `ssl` also accepted)
$B['SMTP_PORT'] = 587;                                      //SMTP port (TCP port to connect to)
$B['SMTP_EMAIL'] = 'support@yousite.com';                     //SMTP email
$B['SMTP_USERNAME'] = 'support@yousite.com';                  //SMTP username
$B['SMTP_PASSWORD'] = 'password';                      //SMTP password

//Please edit database data

$C['DB_HOST'] = "ls-9ce89b3e1caad919c133fdfe42120ee67c177d05.c5wvqj6cyfz1.eu-west-3.rds.amazonaws.com";                                //localhost or your db host
$C['DB_USER'] = "palmpair";                             //your db user
$C['DB_PASS'] = "z38qZs^2";                         //your db password
$C['DB_NAME'] = "palmpair";                             //your db name


$C['DEFAULT_BALANCE'] = 10;                                    // Default user balance in credits (Is charged during the user registration)

$C['ERROR_SUCCESS'] = 0;

$C['ERROR_UNKNOWN'] = 100;
$C['ERROR_ACCESS_TOKEN'] = 101;

$C['ERROR_LOGIN_TAKEN'] = 300;
$C['ERROR_EMAIL_TAKEN'] = 301;
$C['ERROR_FACEBOOK_ID_TAKEN'] = 302;

$C['ERROR_ACCOUNT_ID'] = 400;

$C['DISABLE_LIKES_GCM'] = 0;
$C['ENABLE_LIKES_GCM'] = 1;

$C['DISABLE_COMMENTS_GCM'] = 0;
$C['ENABLE_COMMENTS_GCM'] = 1;

$C['DISABLE_FOLLOWERS_GCM'] = 0;
$C['ENABLE_FOLLOWERS_GCM'] = 1;

$C['DISABLE_MESSAGES_GCM'] = 0;
$C['ENABLE_MESSAGES_GCM'] = 1;

$C['DISABLE_GIFTS_GCM'] = 0;
$C['ENABLE_GIFTS_GCM'] = 1;

$C['SEX_MALE'] = 0;
$C['SEX_FEMALE'] = 1;
$C['SEX_ANY'] = 2;

$C['USER_CREATED_SUCCESSFULLY'] = 0;
$C['USER_CREATE_FAILED'] = 1;
$C['USER_ALREADY_EXISTED'] = 2;
$C['USER_BLOCKED'] = 3;
$C['USER_NOT_FOUND'] = 4;
$C['USER_LOGIN_SUCCESSFULLY'] = 5;
$C['EMPTY_DATA'] = 6;
$C['ERROR_API_KEY'] = 7;

$C['NOTIFY_TYPE_LIKE'] = 0;
$C['NOTIFY_TYPE_FOLLOWER'] = 1;
$C['NOTIFY_TYPE_MESSAGE'] = 2;
$C['NOTIFY_TYPE_COMMENT'] = 3;
$C['NOTIFY_TYPE_COMMENT_REPLY'] = 4;
$C['NOTIFY_TYPE_FRIEND_REQUEST_ACCEPTED'] = 5;
$C['NOTIFY_TYPE_GIFT'] = 6;

$C['NOTIFY_TYPE_IMAGE_COMMENT'] = 7;
$C['NOTIFY_TYPE_IMAGE_COMMENT_REPLY'] = 8;
$C['NOTIFY_TYPE_IMAGE_LIKE'] = 9;

$C['GCM_NOTIFY_CONFIG'] = 0;
$C['GCM_NOTIFY_SYSTEM'] = 1;
$C['GCM_NOTIFY_CUSTOM'] = 2;
$C['GCM_NOTIFY_LIKE'] = 3;
$C['GCM_NOTIFY_ANSWER'] = 4;
$C['GCM_NOTIFY_QUESTION'] = 5;
$C['GCM_NOTIFY_COMMENT'] = 6;
$C['GCM_NOTIFY_FOLLOWER'] = 7;
$C['GCM_NOTIFY_PERSONAL'] = 8;
$C['GCM_NOTIFY_MESSAGE'] = 9;
$C['GCM_NOTIFY_COMMENT_REPLY'] = 10;
$C['GCM_FRIEND_REQUEST_INBOX'] = 11;
$C['GCM_FRIEND_REQUEST_ACCEPTED'] = 12;
$C['GCM_NOTIFY_GIFT'] = 14;
$C['GCM_NOTIFY_SEEN'] = 15;
$C['GCM_NOTIFY_TYPING'] = 16;
$C['GCM_NOTIFY_URL'] = 17;

$C['GCM_NOTIFY_IMAGE_COMMENT_REPLY'] = 18;
$C['GCM_NOTIFY_IMAGE_COMMENT'] = 19;
$C['GCM_NOTIFY_IMAGE_LIKE'] = 20;

$C['ACCOUNT_STATE_ENABLED'] = 0;
$C['ACCOUNT_STATE_DISABLED'] = 1;
$C['ACCOUNT_STATE_BLOCKED'] = 2;
$C['ACCOUNT_STATE_DEACTIVATED'] = 3;

$C['ACCOUNT_TYPE_USER'] = 0;
$C['ACCOUNT_TYPE_GROUP'] = 1;

$C['GALLERY_ITEM_TYPE_IMAGE'] = 0;
$C['GALLERY_ITEM_TYPE_VIDEO'] = 1;

$C['ADMIN_ACCESS_LEVEL_FULL'] = 0;
$C['ADMIN_ACCESS_LEVEL_MODERATOR'] = 1;
$C['ADMIN_ACCESS_LEVEL_GUEST'] = 2;

$LANGS = array();
$LANGS['English'] = "en";
$LANGS['Русский'] = "ru";

