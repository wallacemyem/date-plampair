<?php

/*!
 * ifsoft.co.uk
 *
 * http://ifsoft.com.ua, https://ifsoft.co.uk, https://raccoonsquare.com
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2020 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

class support extends db_connect
{

	private $requestFrom = 0;

	public function __construct($dbo = NULL)
    {
		parent::__construct($dbo);
	}

    public function count()
    {
        $stmt = $this->db->prepare("SELECT count(*) FROM support WHERE removeAt = 0");
        $stmt->execute();

        return $number_of_rows = $stmt->fetchColumn();
    }

    public function removeTicket($ticketId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $currentTime = time();

        $stmt = $this->db->prepare("UPDATE support SET removeAt = (:removeAt) WHERE id = (:ticketId)");
        $stmt->bindParam(":ticketId", $ticketId, PDO::PARAM_INT);
        $stmt->bindParam(":removeAt", $currentTime, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function createTicket($accountId, $email, $subject, $text, $clientId = 0)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $currentTime = time();
        $ip_addr = helper::ip_addr();
        $u_agent = helper::u_agent();

        $stmt = $this->db->prepare("INSERT INTO support (clientId, accountId, email, subject, text, createAt, ip_addr, u_agent) value (:clientId, :accountId, :email, :subject, :text, :createAt, :ip_addr, :u_agent)");
        $stmt->bindParam(":clientId", $clientId, PDO::PARAM_INT);
        $stmt->bindParam(":accountId", $accountId, PDO::PARAM_INT);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":subject", $subject, PDO::PARAM_STR);
        $stmt->bindParam(":text", $text, PDO::PARAM_STR);
        $stmt->bindParam(":createAt", $currentTime, PDO::PARAM_INT);
        $stmt->bindParam(":ip_addr", $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(":u_agent", $u_agent, PDO::PARAM_STR);

        if ($stmt->execute()) {

            $result = array("error" => false,
                            "error_code" => ERROR_SUCCESS);
        }

        return $result;
    }

    public function get($ticketId)
    {
        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $stmt = $this->db->prepare("SELECT * FROM support WHERE id = (:ticketId) LIMIT 1");
        $stmt->bindParam(":ticketId", $ticketId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                $row = $stmt->fetch();

                $result = array("error" => false,
                                "error_code" => ERROR_SUCCESS,
                                "id" => $row['id'],
                                "clientId" => $row['clientId'],
                                "accountId" => $row['accountId'],
                                "email" => $row['email'],
                                "subject" => htmlspecialchars_decode(stripslashes($row['subject'])),
                                "text" => htmlspecialchars_decode(stripslashes($row['text'])),
                                "reply" => htmlspecialchars_decode(stripslashes($row['reply'])),
                                "replyAt" => $row['replyAt'],
                                "replyFrom" => $row['replyFrom'],
                                "removeAt" => $row['removeAt'],
                                "createAt" => $row['createAt'],
                                "u_agent" => $row['u_agent'],
                                "ip_addr" => $row['ip_addr']);
            }
        }

        return $result;
    }

    public function getTickets()
    {
        $tickets = array("error" => false,
                        "error_code" => ERROR_SUCCESS,
                        "id" => 0,
                        "tickets" => array());

        $stmt = $this->db->prepare("SELECT * FROM support WHERE removeAt = 0 ORDER BY id DESC");

        if ($stmt->execute()) {

            if ($stmt->rowCount() > 0) {

                while ($row = $stmt->fetch()) {

                    array_push($tickets['tickets'], $this->get($row['id']));

                    $tickets['id'] = $row['id'];
                }
            }
        }

        return $tickets;
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

