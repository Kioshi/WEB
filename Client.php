<?php

require_once('utils.php');
require_once('User.php');
require_once('Database.php');
require_once ('Twig-1.x/lib/Twig/Autoloader.php');

class Client
{
    private $user;
    private $db;    
    private $twig;

    public function __construct()
    {
        $this->user = new User();
        $this->db = new Database();
 
        $this->initTwig();
        $this->createNavBar();
        $this->createBody();
    }

    private function initTwig()
    {
        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('templates');
        $this->twig = new Twig_Environment($loader);

    }

    private function createBody()
    {

        if (isSet($_GET["logout"]))
        {
            $this->user->logout();
            header('Location: '.strtok($_SERVER["REQUEST_URI"],'?'));
            die;
        }
        else if (isSet($_GET["login"]))
        {
            $this->login();
        }
        else if (isSet($_GET["loans"]))
        {
            if ($this->user->isLogged())
                $this->loans();
            else
                $this->unauthorized();  
        }
        else if (isSet($_GET["member"]))
        {
            if ($this->user->isLogged())
                $this->detail('Člen', $this->db->getMember($_GET["member"]));
            else
                $this->unauthorized();
        }
        else if (isSet($_GET["members"]))
        {
            if ($this->user->isLogged())
                $this->table('Členové', 'member' ,$this->db->getMembers());
            else
                $this->unauthorized();        
        }
        else if (isSet($_GET["game"]))
        {
            $this->detail('Člen',$this->db->getGame($_GET["game"]));        
        }
        else
        {
            $this->table('Hry', 'game' ,$this->db->getGames());        
        }
    }


    private function createNavBar()
    {
        $navbar = $this->twig->loadTemplate('navbar.htm');

        $template_params = array();
        $template_params["info"] = $this->user->getInfo();
        $template_params["page"] = currPage();
        $template_params["pageId"] = currPageId();
        echo $navbar->render($template_params);
    }



    private function login()
    {
        echo "Login";
        $this->user->login('tester','test');
    }

    private function table($name, $link, $data)
    {
        $template = $this->twig->loadTemplate('table.htm');
        $template_params = array();
        $template_params["name"] = $name;
        $template_params["link"] = $link;
        $template_params["data"] = $data;
        $template_params["dataLenght"] = sizeof($data);
        echo $template->render($template_params);
    }

    private function loans()
    {
        echo "loans";
    }

    private function detail($name, $data)
    {
        echo "detail ".$name;
    }

    private function unauthorized()
    {
        $template = $this->twig->loadTemplate('unauthorized.htm');
        $template_params = array();
        echo $template->render($template_params);
    }
}



?>
