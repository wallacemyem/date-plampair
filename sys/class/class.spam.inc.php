<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, https://ifsoft.co.uk, https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class spam extends db_connect
{
	private $requestFrom = 0;
    private $language = 'en';

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

	// Get created chats count for last 30 minutes

    public function getChatsCount()
    {
        $testTime = time() - 1800; // 30 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM chats WHERE fromUserId = (:profileId) AND removeAt = 0 AND createAt > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    // Get user see profiles count for last 5 minutes

    public function getGuestsCount()
    {
        $testTime = time() - 300; // 5 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM guests WHERE guestId = (:profileId) AND removeAt = 0 AND createAt > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    // Get created comments count for last 30 minutes

    public function getCommentsCount()
    {
        $testTime = time() - 1800; // 30 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM images_comments WHERE fromUserId = (:profileId) AND removeAt = 0 AND createAt > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    // Get user like profiles count for last 30 minutes

    public function getProfileLikesCount()
    {
        $testTime = time() - 1800; // 30 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM profile_likes WHERE fromUserId = (:profileId) AND removeAt = 0 AND createAt > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    // Get user send gifts count for last 30 minutes

    public function getSendGiftsCount()
    {
        $testTime = time() - 1800; // 30 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM gifts WHERE giftFrom = (:profileId) AND removeAt = 0 AND createAt > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    // Get user send friend requests count for last 30 minutes

    public function getSendFriendRequestsCount()
    {
        $testTime = time() - 1800; // 30 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM profile_followers WHERE follower = (:profileId) AND create_at > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    // Get user like gallery items count for last 30 minutes

    public function getGalleryLikesCount()
    {
        $testTime = time() - 1800; // 30 minutes

        $stmt = $this->db->prepare("SELECT count(*) FROM images_likes WHERE fromUserId = (:profileId) AND removeAt = 0 AND createAt > (:testTime)");
        $stmt->bindParam(":profileId", $this->requestFrom, PDO::PARAM_INT);
        $stmt->bindParam(":testTime", $testTime, PDO::PARAM_INT);
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setRequestFrom($requestFrom)
    {
        $this->requestFrom = $requestFrom;
    }

    public function getRequestFrom()
    {
        return $this->requestFrom;
    }
}
