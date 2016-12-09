<?php
require_once('Page.php');
require_once('User.php');
require_once('Database.php');

class AdminPage extends Page
{
    protected $user;
    protected $db;
    protected $template;
    protected $type;

    public function __construct($user, $db, $twig)
    {
        parent::__construct($twig);

        $this->user = $user;
        $this->db = $db;
        $this->template = $this->twig->loadTemplate('admin.htm');

        $this->processPOST();
    }

    public function render()
    {
        $template_params = array();
        $template_params["type"] = $this->type;
        $closets = $this->db->getClosets();
        $template_params["closets"] = $closets;
        $template_params["closetsLenght"] = sizeof($closets);
        $members = $this->db->getMembers();
        $template_params["members"] = $members;
        $template_params["membersLenght"] = sizeof($members);
        $games = $this->db->getGames();
        $template_params["games"] = $games;
        $template_params["gamesLenght"] = sizeof($games);
        return $this->template->render($template_params);
    }
    
    private function processPOST()
    {
        $this->type = 0;
        if (isSet($_POST["jmeno"]))
        {
            if ($this->db->addMember($_POST))
                $this->type = 1;
            else
                $this->type = 2;
        }
        else if (isSet($_POST["nazev"]))
        {
            if ($this->db->addGame($_POST))
                $this->type = 3;
            else
                $this->type = 4;
        }
        else if (isSet($_POST["skrin"]))
        {
            if ($this->db->addCloset($_POST))
                $this->type = 8;
            else
                $this->type = 9;
        }
        else if (isSet($_POST["user"]))
        {
            if ($this->db->checkLoan($_POST))
            {
                if ($this->db->addLoan($_POST))
                    $this->type = 5;
                else
                    $this->type = 6;
            }
            else
                $this->type = 7;
        }
    }
}


?>