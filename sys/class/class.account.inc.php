<?php

/*!
 * https://racconsquare.com
 * racconsquare@gmail.com
 *
 * Copyright 2012-2021 Demyanchuk Dmitry (racconsquare@gmail.com)
 */

class account extends db_connect
{
    private $id = 0;

    public function __construct($dbo = NULL, $accountId = 0)
    {

        parent::__construct($dbo);

        $this->setId($accountId);
    }

    public function signup($username, $fullname, $password, $email, $sex, $year, $month, $day, $u_age, $u_sex_orientation, $language = '')
    {

        $result = array("error" => true);

        $helper = new helper($this->db);

        if (!helper::isCorrectLogin($username)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 0,
                            "error_description" => "Incorrect login");

            return $result;
        }

        if ($helper->isLoginExists($username)) {

            $result = array("error" => true,
                            "error_code" => ERROR_LOGIN_TAKEN,
                            "error_type" => 0,
                            "error_description" => "Login already taken");

            return $result;
        }

        if (empty($fullname)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 3,
                            "error_description" => "Empty user full name");

            return $result;
        }

        if (!helper::isCorrectPassword($password)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 1,
                            "error_description" => "Incorrect password");

            return $result;
        }

        if (!helper::isCorrectEmail($email)) {

            $result = array("error" => true,
                            "error_code" => ERROR_UNKNOWN,
                            "error_type" => 2,
                            "error_description" => "Wrong email");

            return $result;
        }

        if ($helper->isEmailExists($email)) {

            $result = array("error" => true,
                            "error_code" => ERROR_EMAIL_TAKEN,
                            "error_type" => 2,
                            "error_description" => "User with this email is already registered");

            return $result;
        }

        if ($sex < 0 || $sex > 2) {

            // 0 = male
            // 1= female
            // 2= secret

            $sex = 2; // Default gender. 2 = secret
        }

        if ($u_sex_orientation > 4 || $u_sex_orientation < 1) {

            $u_sex_orientation = 1; // Default sex orientation. 1 = Heterosexual
        }

        if ($u_age > 110 || $u_age < 18) {

            $u_age = 18; // Default age. 18 = 18 years
        }

        $ip_addr = helper::ip_addr();

        $settings = new settings($this->db);
        $app_settings = $settings->get();
        unset($settings);

        if ($app_settings['allowMultiAccountsFunction']['intValue'] == 0) {

            if ($this->checkMultiAccountsByIp($ip_addr)) {

                $result = array("error" => true,
                                "error_code" => 500,
                                "error_type" => 4,
                                "error_description" => "User with this ip is already registered");

                return $result;
            }
        }

        $salt = helper::generateSalt(3);
        $passw_hash = md5(md5($password).$salt);
        $currentTime = time();

        $accountModerateAt = 0;
        $accountPostModerateAt = $currentTime;

        $accountState = ACCOUNT_STATE_ENABLED;
        $default_user_balance = $app_settings['defaultBalance']['intValue'];
        $default_user_admob = $app_settings['admob']['intValue'];
        $default_free_messages_count = $app_settings['defaultFreeMessagesCount']['intValue'];
        $default_allow_messages = $app_settings['defaultAllowMessages']['intValue'];
        $default_user_language = "en";

        $stmt = $this->db->prepare("INSERT INTO users (free_messages_count, language, admob, state, login, fullname, passw, email, salt, balance, bYear, bMonth, bDay, sex, u_age, sex_orientation, regtime, accountModerateAt, accountPostModerateAt, allowMessages, ip_addr) value (:free_messages_count, :language, :admob, :state, :username, :fullname, :password, :email, :salt, :balance, :bYear, :bMonth, :bDay, :sex, :age, :sex_orientation, :createAt, :accountModerateAt, :accountPostModerateAt, :allowMessages, :ip_addr)");
        $stmt->bindParam(":free_messages_count", $default_free_messages_count, PDO::PARAM_INT);
        $stmt->bindParam(":language", $default_user_language, PDO::PARAM_STR);
        $stmt->bindParam(":admob", $default_user_admob, PDO::PARAM_INT);
        $stmt->bindParam(":state", $accountState, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":password", $passw_hash, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
        $stmt->bindParam(":balance", $default_user_balance, PDO::PARAM_INT);
        $stmt->bindParam(":bYear", $year, PDO::PARAM_INT);
        $stmt->bindParam(":bMonth", $month, PDO::PARAM_INT);
        $stmt->bindParam(":bDay", $day, PDO::PARAM_INT);
        $stmt->bindParam(":sex", $sex, PDO::PARAM_INT);
        $stmt->bindParam(":age", $u_age, PDO::PARAM_INT);
        $stmt->bindParam(":sex_orientation", $u_sex_orientation, PDO::PARAM_INT);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":accountModerateAt", $accountModerateAt, PDO::PARAM_INT);
        $stmt->bindParam(":accountPostModerateAt", $accountPostModerateAt, PDO::PARAM_INT);
        $stmt->bindParam(":allowMessages", $default_allow_messages, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $this->setId($this->db->lastInsertId());

            $result = array("error" => false,
                            'error_code' => ERROR_SUCCESS,
                            'error_description' => 'SignUp Success!',
                            'accountId' => $this->getId(),
                            'username' => $username,
                            'fullname' => $fullname,
                            'password' => $password,
                            'balance' => $default_user_balance,
                            'free_messages_count' => $default_free_messages_count);

            return $result;
        }

        return $result;
    }

    public function signin($username, $password)
    {
        $access_data = array('error' => true);

        $username = helper::clearText($username);
        $password = helper::clearText($password);

        $stmt = $this->db->prepare("SELECT salt FROM users WHERE login = (:username) LIMIT 1");
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            $passw_hash = md5(md5($password).$row['salt']);

            $stmt2 = $this->db->prepare("SELECT id, state, fullname, lowPhotoUrl, verify, pro, free_messages_count FROM users WHERE login = (:username) AND passw = (:password) LIMIT 1");
            $stmt2->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt2->bindParam(":password", $passw_hash, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $row2 = $stmt2->fetch();

                $access_data = array("error" => false,
                                     "error_code" => ERROR_SUCCESS,
                                     "accountId" => $row2['id'],
                                     "fullname" => $row2['fullname'],
                                     "photoUrl" => $row2['lowPhotoUrl'],
                                     "verify" => $row2['verify'],
                                     "pro" => $row2['pro'],
                                     "free_messages_count" => $row2['free_messages_count']);
            }
        }

        return $access_data;
    }

    public function logout($accountId, $accessToken)
    {
        $auth = new auth($this->db);
        $auth->remove($accountId, $accessToken);
    }

    public function checkMultiAccountsByIp($ip_addr)
    {
        $createAt = time() - 12 * 3600; // 6 hours

        $stmt = $this->db->prepare("SELECT id FROM users WHERE ip_addr = (:ip_addr) AND regtime > (:regtime) LIMIT 1");
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(":regtime", $createAt, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                return true;
            }
        }

        return false;
    }

    public function setPassword($password, $newPassword)
    {
        $result = array('error' => true,
                        'error_code' => ERROR_UNKNOWN);

        if (!helper::isCorrectPassword($password)) {

            return $result;
        }

        if (!helper::isCorrectPassword($newPassword)) {

            return $result;
        }

        $stmt = $this->db->prepare("SELECT salt FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            $passw_hash = md5(md5($password).$row['salt']);

            $stmt2 = $this->db->prepare("SELECT id FROM users WHERE id = (:accountId) AND passw = (:password) LIMIT 1");
            $stmt2->bindParam(":accountId", $this->id, PDO::PARAM_INT);
            $stmt2->bindParam(":password", $passw_hash, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $this->newPassword($newPassword);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS);
            }
        }

        return $result;
    }

    public function newPassword($password)
    {
        $newSalt = helper::generateSalt(3);
        $newHash = md5(md5($password).$newSalt);

        $stmt = $this->db->prepare("UPDATE users SET passw = (:newHash), salt = (:newSalt) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":newHash", $newHash, PDO::PARAM_STR);
        $stmt->bindParam(":newSalt", $newSalt, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function setWeight($u_weight)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET u_weight = (:u_weight) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":u_weight", $u_weight, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getWeight()
    {
        $stmt = $this->db->prepare("SELECT u_weight FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['weight'];
        }

        return 0;
    }

    public function setHeight($u_height)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET u_height = (:u_height) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":u_height", $u_height, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getHeight()
    {
        $stmt = $this->db->prepare("SELECT u_height FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['height'];
        }

        return 0;
    }

    public function setAge($u_age)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET u_age = (:u_age) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":u_age", $u_age, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getAge()
    {
        $stmt = $this->db->prepare("SELECT u_age FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['age'];
        }

        return 0;
    }

    public function setSexOrientation($u_sex_orientation)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET sex_orientation = (:sex_orientation) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":sex_orientation", $u_sex_orientation, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getSexOrientation()
    {
        $stmt = $this->db->prepare("SELECT sex_orientation FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['sex_orientation'];
        }

        return 0;
    }

    public function setSex($sex)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET sex = (:sex) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":sex", $sex, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getSex()
    {
        $stmt = $this->db->prepare("SELECT sex FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['sex'];
        }

        return 0;
    }

    public function setBirth($year, $month, $day)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET bYear = (:bYear), bMonth = (:bMonth), bDay = (:bDay) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":bYear", $year, PDO::PARAM_INT);
        $stmt->bindParam(":bMonth", $month, PDO::PARAM_INT);
        $stmt->bindParam(":bDay", $day, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setAdmob($admob)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET admob = (:mode) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":mode", $admob, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getAdmob()
    {
        $stmt = $this->db->prepare("SELECT admob FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['admob'];
        }

        return 0;
    }

    public function setGhost($ghost)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $ghost_create_at = 0;

        if ($ghost != 0) {

            $ghost_create_at = time();
        }

        $stmt = $this->db->prepare("UPDATE users SET ghost = (:ghost), ghost_create_at = (:ghost_create_at) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":ghost", $ghost, PDO::PARAM_INT);
        $stmt->bindParam(":ghost_create_at", $ghost_create_at, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getGhost()
    {
        $stmt = $this->db->prepare("SELECT ghost FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['ghost'];
        }

        return 0;
    }

    public function setPro($pro)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $pro_create_at = 0;

        if ($pro != 0) {

            $pro_create_at = time();
        }

        $stmt = $this->db->prepare("UPDATE users SET pro = (:pro), pro_create_at = (:pro_create_at) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":pro", $pro, PDO::PARAM_INT);
        $stmt->bindParam(":pro_create_at", $pro_create_at, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getPro()
    {
        $stmt = $this->db->prepare("SELECT pro FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['pro'];
        }

        return 0;
    }

    public function setVip($vip)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $vip_create_at = 0;

        if ($vip != 0) {

            $vip_create_at = time();
        }

        $stmt = $this->db->prepare("UPDATE users SET vip = (:vip), vip_create_at = (:vip_create_at) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":vip", $vip, PDO::PARAM_INT);
        $stmt->bindParam(":vip_create_at", $vip_create_at, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getVip()
    {
        $stmt = $this->db->prepare("SELECT vip FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['vip'];
        }

        return 0;
    }

    public function setBalance($balance)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET balance = (:balance) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":balance", $balance, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getBalance()
    {
        $stmt = $this->db->prepare("SELECT balance FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['balance'];
        }

        return 0;
    }

    public function updateOtpVerification($otpPhoneNumber, $otpVerified)
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN
        );

        $stmt = $this->db->prepare("UPDATE users SET otpPhone = (:otpPhone), otpVerified = (:otpVerified) WHERE id = (:accountId)");
        $stmt->bindParam(":otpPhone", $otpPhoneNumber, PDO::PARAM_STR);
        $stmt->bindParam(":otpVerified", $otpVerified, PDO::PARAM_INT);
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array(
                'error' => false,
                'error_code' => ERROR_SUCCESS
            );
        }

        return $result;
    }

    public function setFreeMessagesCount($count)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET free_messages_count = (:free_messages_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":free_messages_count", $count, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getFreeMessagesCount()
    {
        $stmt = $this->db->prepare("SELECT free_messages_count FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['free_messages_count'];
        }

        return 0;
    }

    public function setRating($rating)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET rating = (:rating) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":rating", $rating, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getRating()
    {
        $stmt = $this->db->prepare("SELECT rating FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['rating'];
        }

        return 0;
    }

    public function setGiftsCount($giftsCount)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET gifts_count = (:gifts_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":gifts_count", $giftsCount, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getGiftsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM gifts WHERE giftTo = (:accountId) AND removeAt = 0");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setPhotosCount($photosCount)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET photos_count = (:photos_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":photos_count", $photosCount, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getPhotosCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM photos WHERE fromUserId = (:fromUserId) AND removeAt = 0 AND moderateAt > 0");
        $stmt->bindParam(":fromUserId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function updateCounters()
    {
        $photosCount = $this->getPhotosCount();
        $giftsCount = $this->getGiftsCount();
        $likesCount = $this->getLikesCount();
        $friendsCount = $this->getFriendsCount();
        $matches_count = $this->getMatchesCount();

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET photos_count = (:photos_count), gifts_count = (:gifts_count), likes_count = (:likes_count), friends_count = (:friends_count), matches_count = (:matches_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":photos_count", $photosCount, PDO::PARAM_INT);
        $stmt->bindParam(":gifts_count", $giftsCount, PDO::PARAM_INT);
        $stmt->bindParam(":likes_count", $likesCount, PDO::PARAM_INT);
        $stmt->bindParam(":friends_count", $friendsCount, PDO::PARAM_INT);
        $stmt->bindParam(":matches_count", $matches_count, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setFacebookId($fb_id)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET fb_id = (:fb_id) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":fb_id", $fb_id, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getFacebookId()
    {
        $stmt = $this->db->prepare("SELECT fb_id FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['fb_id'];
        }

        return 0;
    }

    public function setFacebookPage($fb_page)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET fb_page = (:fb_page) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":fb_page", $fb_page, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getFacebookPage()
    {
        $stmt = $this->db->prepare("SELECT fb_page FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['fb_page'];
        }

        return '';
    }

    public function setInstagramPage($instagram_page)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET my_page = (:my_page) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":my_page", $instagram_page, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getInstagramPage()
    {
        $stmt = $this->db->prepare("SELECT my_page FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['my_page'];
        }

        return '';
    }

    public function setEmail($email)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $helper = new helper($this->db);

        if (!helper::isCorrectEmail($email)) {

            return $result;
        }

        if ($helper->isEmailExists($email)) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE users SET email = (:email) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getEmail()
    {
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['email'];
        }

        return '';
    }

    public function setUsername($username)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $helper = new helper($this->db);

        if (!helper::isCorrectLogin($username)) {

            return $result;
        }

        if ($helper->isLoginExists($username)) {

            return $result;
        }

        $stmt = $this->db->prepare("UPDATE users SET login = (:login) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":login", $username, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getUsername()
    {
        $stmt = $this->db->prepare("SELECT login FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['login'];
        }

        return '';
    }

    public function setLocation($location)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET country = (:country) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":country", $location, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getLocation()
    {
        $stmt = $this->db->prepare("SELECT country FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['country'];
        }

        return '';
    }

    public function setGeoLocation($lat, $lng)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET lat = (:lat), lng = (:lng) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":lat", $lat, PDO::PARAM_STR);
        $stmt->bindParam(":lng", $lng, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getGeoLocation()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT lat, lng FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS,
                            'lat' => $row['lat'],
                            'lng' => $row['lng']);
        }

        return $result;
    }

    public function getLikesCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM profile_likes WHERE toUserId = (:toUserId) AND removeAt = 0");
        $stmt->bindParam(":toUserId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setLikesCount($likesCount)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET likes_count = (:likes_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":likes_count", $likesCount, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getFriendsCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM friends WHERE friendTo = (:profileId) AND removeAt = 0");
        $stmt->bindParam(":profileId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setFriendsCount($friendsCount)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET friends_count = (:friends_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":friends_count", $friendsCount, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getMatchesCount()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM matches WHERE u_matchTo = (:u_matchTo) AND removeAt = 0");
        $stmt->bindParam(":u_matchTo", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setMatchesCount($matchesCount)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET matches_count = (:matches_count) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":matches_count", $matchesCount, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setStatus($status)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET status = (:status) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":status", $status, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getStatus()
    {
        $stmt = $this->db->prepare("SELECT status FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['status'];
        }

        return '';
    }

    public function restorePointCreate($email, $clientId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $restorePointInfo = $this->restorePointInfo();

        if ($restorePointInfo['error'] === false) {

            return $restorePointInfo;
        }

        $currentTime = time();	// Current time

        $u_agent = helper::u_agent();
        $ip_addr = helper::ip_addr();

        $hash = md5(uniqid(rand(), true));

        $stmt = $this->db->prepare("INSERT INTO restore_data (accountId, hash, email, clientId, createAt, u_agent, ip_addr) value (:accountId, :hash, :email, :clientId, :createAt, :u_agent, :ip_addr)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":clientId", $clientId, PDO::PARAM_INT);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS,
                            'accountId' => $this->id,
                            'hash' => $hash,
                            'email' => $email);
        }

        return $result;
    }

    public function restorePointInfo()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM restore_data WHERE accountId = (:accountId) AND removeAt = 0 LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS,
                            'accountId' => $row['accountId'],
                            'hash' => $row['hash'],
                            'email' => $row['email']);
        }

        return $result;
    }

    public function restorePointRemove()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $removeAt = time();

        $stmt = $this->db->prepare("UPDATE restore_data SET removeAt = (:removeAt) WHERE accountId = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $removeAt, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function deactivation($password)
    {

        $result = array('error' => true,
                        'error_code' => ERROR_UNKNOWN);

        if (!helper::isCorrectPassword($password)) {

            return $result;
        }

        $stmt = $this->db->prepare("SELECT salt FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch();
            $passw_hash = md5(md5($password) . $row['salt']);

            $stmt2 = $this->db->prepare("SELECT id FROM users WHERE id = (:accountId) AND passw = (:password) LIMIT 1");
            $stmt2->bindParam(":accountId", $this->id, PDO::PARAM_INT);
            $stmt2->bindParam(":password", $passw_hash, PDO::PARAM_STR);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {

                $this->setState(ACCOUNT_STATE_DISABLED);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS);
            }
        }

        return $result;
    }

    public function setLanguage($language)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET language = (:language) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":language", $language, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getLanguage()
    {
        $stmt = $this->db->prepare("SELECT language FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['language'];
        }

        return 'en';
    }

    public function setVerify($verify)
    {
        $result = array('error' => false,
                        'error_code' => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET verify = (:verify) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":verify", $verify, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setFeeling($feeling)
    {
        $result = array('error' => false,
                        'error_code' => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET feeling = (:feeling) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":feeling", $feeling, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function setAllowPhotosComments($allowPhotosComments)
    {
        $stmt = $this->db->prepare("UPDATE users SET allowPhotosComments = (:allowPhotosComments) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowPhotosComments", $allowPhotosComments, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllowPhotosComments()
    {
        $stmt = $this->db->prepare("SELECT allowPhotosComments FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowPhotosComments'];
        }

        return 0;
    }

    public function setFullname($fullname)
    {
        if (strlen($fullname) == 0) {

            return;
        }

        $stmt = $this->db->prepare("UPDATE users SET fullname = (:fullname) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function setAllowMessages($allowMessages)
    {
        $stmt = $this->db->prepare("UPDATE users SET allowMessages = (:allowMessages) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowMessages", $allowMessages, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllowMessages()
    {
        $stmt = $this->db->prepare("SELECT allowMessages FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowMessages'];
        }

        return 0;
    }

    public function setAllowComments($allowComments)
    {
        $stmt = $this->db->prepare("UPDATE users SET allowComments = (:allowComments) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowComments", $allowComments, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAllowComments()
    {
        $stmt = $this->db->prepare("SELECT allowComments FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowComments'];
        }

        return 0;
    }


    public function setState($accountState)
    {

        $stmt = $this->db->prepare("UPDATE users SET state = (:accountState) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":accountState", $accountState, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getState()
    {
        $stmt = $this->db->prepare("SELECT state FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['state'];
        }

        return 0;
    }

    public function setPrivacySettings($allowShowMyLikes, $allowShowMyGifts, $allowShowMyFriends, $allowShowMyGallery, $allowShowMyInfo)
    {
        $stmt = $this->db->prepare("UPDATE users SET allowShowMyLikes = (:allowShowMyLikes), allowShowMyGifts = (:allowShowMyGifts), allowShowMyFriends = (:allowShowMyFriends), allowShowMyGallery = (:allowShowMyGallery), allowShowMyInfo = (:allowShowMyInfo)  WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowShowMyLikes", $allowShowMyLikes, PDO::PARAM_INT);
        $stmt->bindParam(":allowShowMyGifts", $allowShowMyGifts, PDO::PARAM_INT);
        $stmt->bindParam(":allowShowMyFriends", $allowShowMyFriends, PDO::PARAM_INT);
        $stmt->bindParam(":allowShowMyGallery", $allowShowMyGallery, PDO::PARAM_INT);
        $stmt->bindParam(":allowShowMyInfo", $allowShowMyInfo, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getPrivacySettings()
    {
        $result = array(
            "error" => true,
            "error_code" => ERROR_UNKNOWN
        );

        $stmt = $this->db->prepare("SELECT allowShowMyLikes, allowShowMyGifts, allowShowMyFriends, allowShowMyGallery, allowShowMyInfo FROM users WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS,
                            "allowShowMyLikes" => $row['allowShowMyLikes'],
                            "allowShowMyGifts" => $row['allowShowMyGifts'],
                            "allowShowMyFriends" => $row['allowShowMyFriends'],
                            "allowShowMyGallery" => $row['allowShowMyGallery'],
                            "allowShowMyInfo" => $row['allowShowMyInfo']);
        }

        return $result;
    }

    public function setLastActive()
    {
        $time = time();

        $stmt = $this->db->prepare("UPDATE users SET last_authorize = (:last_authorize) WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":last_authorize", $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function setLastNotifyView()
    {
        $time = time();

        $stmt = $this->db->prepare("UPDATE users SET last_notify_view = (:last_notify_view) WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":last_notify_view", $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLastNotifyView()
    {
        $stmt = $this->db->prepare("SELECT last_notify_view FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['last_notify_view'];
            }
        }

        $time = time();

        return $time;
    }

    public function setLastGuestsView()
    {
        $time = time();

        $stmt = $this->db->prepare("UPDATE users SET last_guests_view = (:last_guests_view) WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":last_guests_view", $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLastGuestsView()
    {
        $stmt = $this->db->prepare("SELECT last_guests_view FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['last_guests_view'];
            }
        }

        $time = time();

        return $time;
    }

    public function setLastMatchesView()
    {
        $time = time();

        $stmt = $this->db->prepare("UPDATE users SET last_matches_view = (:last_matches_view) WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":last_matches_view", $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLastMatchesView()
    {
        $stmt = $this->db->prepare("SELECT last_matches_view FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['last_matches_view'];
            }
        }

        $time = time();

        return $time;
    }

    public function setLastFriendsView()
    {
        $time = time();

        $stmt = $this->db->prepare("UPDATE users SET last_friends_view = (:last_friends_view) WHERE id = (:id)");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":last_friends_view", $time, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getLastFriendsView()
    {
        $stmt = $this->db->prepare("SELECT last_friends_view FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['last_friends_view'];
            }
        }

        $time = time();

        return $time;
    }

    public function get()
    {
        $result = array("error" => true,
                        "error_code" => ERROR_ACCOUNT_ID);

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $notifications_count = 0;
                $messages_count = 0;
                $matches_count = 0;
                $matches_count = 0;
                $guests_count = 0;
                $friends_count = 0;

                $online = false;

                $current_time = time();

                if ($row['last_authorize'] != 0 && $row['last_authorize'] > ($current_time - 15 * 60)) {

                    $online = true;
                }

                $time = new language($this->db);

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "admob" => $row['admob'],
                                "ghost" => $row['ghost'],
                                "ghost_create_at" => $row['ghost_create_at'],
                                "vip" => $row['vip'],
                                "vip_create_at" => $row['vip_create_at'],
                                "pro" => $row['pro'],
                                "pro_create_at" => $row['pro_create_at'],
                                "feeling" => $row['feeling'],
                                "gcm" => $row['gcm'],
                                "balance" => $row['balance'],
                                "free_messages_count" => $row['free_messages_count'],
                                "fb_id" => $row['fb_id'],
                                "rating" => $row['rating'],
                                "state" => $row['state'],
                                "regtime" => $row['regtime'],
                                "ip_addr" => $row['ip_addr'],
                                "username" => $row['login'],
                                "fullname" => stripcslashes($row['fullname']),
                                "location" => stripcslashes($row['country']),
                                "status" => stripcslashes($row['status']),
                                "fb_page" => stripcslashes($row['fb_page']),
                                "instagram_page" => stripcslashes($row['my_page']),
                                "verify" => $row['verify'],
                                "verified" => $row['verify'],
                                "otpPhone" => $row['otpPhone'],
                                "otpVerified" => $row['otpVerified'],
                                "email" => $row['email'],
                                "emailVerify" => $row['emailVerify'],
                                "sex" => $row['sex'],
                                "sex_orientation" => $row['sex_orientation'],
                                "age" => $row['u_age'],
                                "height" => $row['u_height'],
                                "weight" => $row['u_weight'],
                                "year" => $row['bYear'],
                                "month" => $row['bMonth'],
                                "day" => $row['bDay'],
                                "lat" => $row['lat'],
                                "lng" => $row['lng'],
                                "language" => $row['language'],
                                "lowPhotoUrl" => $row['lowPhotoUrl'],
                                "normalPhotoUrl" => $row['normalPhotoUrl'],
                                "bigPhotoUrl" => $row['normalPhotoUrl'],
                                "coverUrl" => $row['normalCoverUrl'],
                                "originCoverUrl" => $row['originCoverUrl'],
                                "iStatus" => $row['iStatus'],
                                "iPoliticalViews" => $row['iPoliticalViews'],
                                "iWorldView" => $row['iWorldView'],
                                "iPersonalPriority" => $row['iPersonalPriority'],
                                "iImportantInOthers" => $row['iImportantInOthers'],
                                "iSmokingViews" => $row['iSmokingViews'],
                                "iAlcoholViews" => $row['iAlcoholViews'],
                                "iLooking" => $row['iLooking'],
                                "iInterested" => $row['iInterested'],
                                "allowShowMyInfo" => $row['allowShowMyInfo'],
                                "allowShowMyGallery" => $row['allowShowMyGallery'],
                                "allowShowMyFriends" => $row['allowShowMyFriends'],
                                "allowShowMyLikes" => $row['allowShowMyLikes'],
                                "allowShowMyGifts" => $row['allowShowMyGifts'],
                                "allowShowMyAge" => $row['allowShowMyAge'],
                                "allowShowMySexOrientation" => $row['allowShowMySexOrientation'],
                                "allowShowOnline" => $row['allowShowOnline'],
                                "allowPhotosComments" => $row['allowPhotosComments'],
                                "allowComments" => $row['allowComments'],
                                "allowMessages" => $row['allowMessages'],
                                "allowLikesGCM" => $row['allowLikesGCM'],
                                "allowMatchesGCM" => $row['allowMatchesGCM'],
                                "allowGiftsGCM" => $row['allowGiftsGCM'],
                                "allowCommentsGCM" => $row['allowCommentsGCM'],
                                "allowFollowersGCM" => $row['allowFollowersGCM'],
                                "allowMessagesGCM" => $row['allowMessagesGCM'],
                                "allowCommentReplyGCM" => $row['allowCommentReplyGCM'],
                                "lastAuthorize" => $row['last_authorize'],
                                "lastAuthorizeDate" => date("Y-m-d H:i:s", $row['last_authorize']),
                                "lastAuthorizeTimeAgo" => $time->timeAgo($row['last_authorize']),
                                "online" => $online,
                                "friendsCount" => $row['friends_count'],
                                "photosCount" => $row['photos_count'],
                                "likesCount" => $row['likes_count'],
                                "matchesCount" => $row['matches_count'],
                                "giftsCount" => $row['gifts_count'],
                                "notificationsCount" => $notifications_count,
                                "guestsCount" => $guests_count,
                                "newFriendsCount" => $friends_count,
                                "newMatchesCount" => $matches_count,
                                "messagesCount" => $messages_count,
                                "photoCreateAt" => $row['photoCreateAt'],
                                "photoModerateAt" => $row['photoModerateAt'],
                                "accountModerateAt" => $row['accountModerateAt'],
                                "accountPostModerateAt" => $row['accountPostModerateAt'],
                                "accountRejectModerateAt" => $row['accountRejectModerateAt'],
                                "coverPostModerateAt" => $row['coverPostModerateAt'],
                                "coverModerateUrl" => $row['coverModerateUrl'],
                                "photoPostModerateAt" => $row['photoPostModerateAt'],
                                "photoModerateUrl" => $row['photoModerateUrl'],
                                "lastNotifyView" => $row['last_notify_view'],
                                "lastMatchesView" => $row['last_matches_view'],
                                "lastGuestsView" => $row['last_guests_view'],
                                "lastFriendsView" => $row['last_friends_view']);

                unset($time);
            }
        }

        return $result;
    }

    public function edit($fullname)
    {
        $result = array("error" => true);

        $stmt = $this->db->prepare("UPDATE users SET fullname = (:fullname) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false);
        }

        return $result;
    }

    public function setPhoto($array_data)
    {
        $stmt = $this->db->prepare("UPDATE users SET originPhotoUrl = (:originPhotoUrl), normalPhotoUrl = (:normalPhotoUrl), bigPhotoUrl = (:bigPhotoUrl), lowPhotoUrl = (:lowPhotoUrl), photoModerateUrl = '' WHERE id = (:account_id)");
        $stmt->bindParam(":account_id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":originPhotoUrl", $array_data['originPhotoUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":normalPhotoUrl", $array_data['normalPhotoUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":bigPhotoUrl", $array_data['bigPhotoUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":lowPhotoUrl", $array_data['lowPhotoUrl'], PDO::PARAM_STR);

        $stmt->execute();
    }

    public function setCover($array_data)
    {
        $stmt = $this->db->prepare("UPDATE users SET originCoverUrl = (:originCoverUrl), normalCoverUrl = (:normalCoverUrl), coverModerateUrl = '' WHERE id = (:account_id)");
        $stmt->bindParam(":account_id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":originCoverUrl", $array_data['originCoverUrl'], PDO::PARAM_STR);
        $stmt->bindParam(":normalCoverUrl", $array_data['normalCoverUrl'], PDO::PARAM_STR);

        $stmt->execute();
    }

    public function setCoverPosition($position)
    {
        $stmt = $this->db->prepare("UPDATE users SET coverPosition = (:coverPosition) WHERE id = (:account_id)");
        $stmt->bindParam(":account_id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":coverPosition", $position, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function setBackgroundUrl($url)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET originCoverUrl = (:originCoverUrl), normalCoverUrl = (:normalCoverUrl) WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":originCoverUrl", $url, PDO::PARAM_STR);
        $stmt->bindParam(":normalCoverUrl", $url, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function getBackgroundUrl()
    {
        $stmt = $this->db->prepare("SELECT originCoverUrl FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['originCoverUrl'];
            }
        }

        return '';
    }

    public function getAccessLevel($user_id)
    {
        $stmt = $this->db->prepare("SELECT access_level FROM users WHERE id = (:id) LIMIT 1");
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                return $row['access_level'];
            }
        }

        return 0;
    }

    public function setAccessLevel($access_level)
    {
        $stmt = $this->db->prepare("UPDATE users SET access_level = (:access_level) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":access_level", $access_level, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function set_iStatus($status)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iStatus = (:iStatus) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iStatus", $status, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iStatus()
    {
        $stmt = $this->db->prepare("SELECT iStatus FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iStatus'];
        }

        return 0;
    }

    public function set_iPoliticalViews($politicalViews)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iPoliticalViews = (:iPoliticalViews) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iPoliticalViews", $politicalViews, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iPoliticalViews()
    {
        $stmt = $this->db->prepare("SELECT iPoliticalViews FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iPoliticalViews'];
        }

        return 0;
    }

    public function set_iWorldView($worldView)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iWorldView = (:iWorldView) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iWorldView", $worldView, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iWorldView()
    {
        $stmt = $this->db->prepare("SELECT iWorldView FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iWorldView'];
        }

        return 0;
    }

    public function set_iPersonalPriority($personalPriority)
{
    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $stmt = $this->db->prepare("UPDATE users SET iPersonalPriority = (:iPersonalPriority) WHERE id = (:accountId)");
    $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
    $stmt->bindParam(":iPersonalPriority", $personalPriority, PDO::PARAM_STR);

    if ($stmt->execute()) {

        $result = array('error' => false,
            'error_code' => ERROR_SUCCESS);
    }

    return $result;
}

    public function get_iPersonalPriority()
    {
        $stmt = $this->db->prepare("SELECT iPersonalPriority FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iPersonalPriority'];
        }

        return 0;
    }

    public function set_iImportantInOthers($importantInOthers)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iImportantInOthers = (:iImportantInOthers) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iImportantInOthers", $importantInOthers, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iImportantInOthers()
    {
        $stmt = $this->db->prepare("SELECT iImportantInOthers FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iImportantInOthers'];
        }

        return 0;
    }

    public function set_iSmokingViews($smokingViews)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iSmokingViews = (:iSmokingViews) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iSmokingViews", $smokingViews, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iSmokingViews()
    {
        $stmt = $this->db->prepare("SELECT iSmokingViews FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iSmokingViews'];
        }

        return 0;
    }

    public function set_iAlcoholViews($alcoholViews)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iAlcoholViews = (:iAlcoholViews) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iAlcoholViews", $alcoholViews, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iAlcoholViews()
    {
        $stmt = $this->db->prepare("SELECT iAlcoholViews FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iAlcoholViews'];
        }

        return 0;
    }

    public function set_iLooking($looking)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iLooking = (:iLooking) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iLooking", $looking, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iLooking()
    {
        $stmt = $this->db->prepare("SELECT iLooking FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iLooking'];
        }

        return 0;
    }

    public function set_iInterested($interested)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET iInterested = (:iInterested) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":iInterested", $interested, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_iInterested()
    {
        $stmt = $this->db->prepare("SELECT iInterested FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['iInterested'];
        }

        return 0;
    }

    public function set_allowShowMyBirthday($allowShowMyBirthday)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("UPDATE users SET allowShowMyBirthday = (:allowShowMyBirthday) WHERE id = (:accountId)");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":allowShowMyBirthday", $allowShowMyBirthday, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array('error' => false,
                            'error_code' => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get_allowShowMyBirthday()
    {
        $stmt = $this->db->prepare("SELECT allowShowMyBirthday FROM users WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $row = $stmt->fetch();

            return $row['allowShowMyBirthday'];
        }

        return 0;
    }

    public function setId($accountId)
    {
        $this->id = $accountId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setAnonymousQuestions($anonymousQuestions)
    {
        $result = array("error" => true);

        $stmt = $this->db->prepare("UPDATE users SET anonymousQuestions = (:anonymousQuestions) WHERE id = (:accountId) LIMIT 1");
        $stmt->bindParam(":accountId", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":anonymousQuestions", $anonymousQuestions, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->execute()) {

            $result = array("error" => false);
        }

        return $result;
    }
}

