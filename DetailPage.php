<?php
require_once('Page.php');
require_once('Database.php');
require_once('User.php');

abstract class DetailPage extends Page
{
    protected $user;
    protected $db;
    protected $template;
    protected $id;

    public function __construct($user, $db, $twig, $id)
    {
        parent::__construct($twig);

        $this->db = $db;    
        $this->user = $user;
        $this->id = $id;
        $this->template = $twig->loadTemplate('detail.htm');
    }
}

class MemberPage extends DetailPage
{
    
    public function render()
    {
        $data = $this->db->getMember($this->id);
        $template_params = array();
        $template_params["pageName"] = 'Člen';
        $template_params["info"] = $data;
        return $this->template->render($template_params);
    }
    
}

class GamePage extends DetailPage
{
    
    public function render()
    {
        $data = $this->db->getGame($this->id);
        $template_params = array();
        $template_params["pageName"] = 'Hra';
        $template_params["info"] = $data;
        return $this->template->render($template_params);
    }
}


?>