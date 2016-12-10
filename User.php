<?php

require_once('Database.php');
session_start();

class User
{
    private $userName;
    private $session;
    private $db;

    public function __construct($db)
    {
        $this->userName = isSet($_SESSION["userName"]) ? $_SESSION["userName"] : null;
        $this->session = isSet($_SESSION["session"]) ? $_SESSION["session"] : null;
        $this->db = $db;
    }

    public function login($userName, $password)
    {
        $this->logout(false);
        $this->session = $this->db->login($userName,$password);
        if ($this->session)
        {
            $this->userName = $userName;
            $_SESSION["userName"] = $userName;
            $_SESSION["session"] = $this->session;
            return true;
        }
        return false;
    }

    public function register($userName, $password, $memberId)
    {
        $res = $this->db->register($userName, $password, $memberId);
        if ($res == 1)
            $_SESSION["userName"] = $userName;

        return $res;
    }

    public function logout($force = true)
    {
        if ($force && $this->userName && $this->session)
            $this->db->logout($this->userName);

        $this->userName = null;
        $this->session = null;
        unset($_SESSION["userName"]);
        unset($_SESSION["session"]);
    }

    public function isLogged()
    {
        if ($this->session)
            return $this->db->isLogged($this->userName,$this->session);
        else
            return false;
    }

    // Retrieve user info from DB or return default
    public function getInfo()
    {
        return $this->userName ? $this->db->getInfo($this->userName) : array('name' => 'Login', 'id' => -1);
    }

    // Retrieve user role from DB
    public function getRole()
    {
        return $this->userName ? $this->db->getRole($this->userName) : '';
    }
}


?>
