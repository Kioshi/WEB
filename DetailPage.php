<?php
require_once('Page.php');
require_once('Database.php');
require_once('User.php');

abstract class DetailPage extends Page
{
    protected $user;
    protected $db;
    protected $template;
    protected $formTemplate;
    protected $id;
    protected $type;

    public function __construct($user, $db, $twig, $id)
    {
        parent::__construct($twig);

        $this->db = $db;    
        $this->user = $user;
        $this->id = $id;
        $this->template = $twig->loadTemplate('detail.htm');
        $this->formTemplate = $twig->loadTemplate('loanForm.htm');

        $this->processPOST();
    }
    
    private function processPOST()
    {
        $this->type = 0;
        if (isSet($_POST["formLoan"]))
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

class MemberPage extends DetailPage
{
    
    public function render()
    {
        $data = $this->db->getMember($this->id);
        $template_params = array();
        $template_params["pageName"] = 'Člen';
        $template_params["info"] = $data;
        $result = $this->template->render($template_params);
        if ($this->user->isLogged() && ($this->user->getInfo()['id'] == $this->id || $this->user->getRole() == 'ADMIN'))
            $result .= $this->loanForm($data['name']);
        
        return $result;
    }

    private function loanForm($name)
    {        
        $template_params = array();
        $template_params["type"] = $this->type;
        $closets = $this->db->getClosets();
        $template_params["closets"] = $closets;
        $template_params["closetsLenght"] = sizeof($closets);
        $members = array(array('nazev'=>$name, 'id' => $this->id));
        $template_params["members"] = $members;
        $template_params["membersLenght"] = sizeof($members);
        $games = $this->db->getGames();
        $template_params["games"] = $games;
        $template_params["gamesLenght"] = sizeof($games);
        return $this->formTemplate->render($template_params);
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