<?php
require_once('Page.php');
require_once('User.php');
require_once('Database.php');

class LoansPage extends Page
{
    protected $user;
    protected $db;
    protected $template;
    protected $gameId;
    protected $memberId;

    public function __construct($user, $db, $twig, $memberId = null, $gameId = null)
    {
        parent::__construct($twig);

        $this->user = $user;
        $this->db = $db;
        $this->gameId = $gameId;
        $this->memberId = $memberId;
        $this->template = $twig->loadTemplate('loans.htm');
    }

    public function render()
    {
        $template_params = array();
        if ($this->gameId == null && $this->memberId == null)
            $template_params["pageName"] = 'Výpůjčky';
        $data = $this->db->getLoans($this->memberId,$this->gameId);
        $template_params["data"] = $data;
        $template_params["dataLenght"] = sizeof($data);
        return $this->template->render($template_params);
    }
}


?>