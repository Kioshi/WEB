<?php

require_once('Page.php');
require_once('Database.php');

abstract class TablePage extends Page
{
    protected $db;
    protected $template;

    public function __construct($db, $twig)
    {
        parent::__construct($twig);

        $this->db = $db;
        $this->template = $twig->loadTemplate('table.htm');
    }
}

class GamesPage extends TablePage
{
    public function render()
    {        
        $template_params = array();
        $template_params["pageName"] = 'Hry';
        $template_params["link"] = 'game';
        $data = $this->db->getGames();
        $template_params["data"] = $data;
        $template_params["dataLenght"] = sizeof($data);

        return $this->template->render($template_params);
    }
}

class MembersPage extends TablePage
{
    public function render()
    {
        $template_params = array();
        $template_params["pageName"] = 'Členové';
        $template_params["link"] = 'member';
        $data = $this->db->getMembers();
        $template_params["data"] = $data;
        $template_params["dataLenght"] = sizeof($data);

        return $this->template->render($template_params);        
    }
}

?>
