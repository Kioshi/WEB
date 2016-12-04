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
                $this->loans($this->db->getLoans(null,null),"Výpůjčky");
            else
                $this->unauthorized();  
        }
        else if (isSet($_GET["member"]))
        {
            if ($this->user->isLogged())
            {
                $this->detail('Člen', $this->db->getMember($_GET["member"]));
                $this->loans($this->db->getLoans($_GET["member"],null));
            }
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
            $this->detail('Hra',$this->db->getGame($_GET["game"])); 
            if ($this->user->isLogged()) 
                $this->loans($this->db->getLoans(null,$_GET["game"]));
                  
        }
        else if (isSet($_GET["admin"]))
        {
            if ($this->user->getRole() == 'ADMIN') 
                $this->administration();
            else
                $this->unauthorized();        
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
        $template_params["role"] = $this->user->getRole();
        $template_params["page"] = currPage();
        $template_params["pageId"] = currPageId();
        echo $navbar->render($template_params);
    }

    private function login()
    {
        if (isSet($_POST["username"]) && isSet($_POST["password"]))
        {
            if ($this->user->login($_POST["username"],$_POST["password"]))
            {
                header('Location: '.strtok($_SERVER["REQUEST_URI"],'?'));
                die;
            }
            else
            {
                $this->showLogin(true);
            }
        }
        else
        {
            $this->showLogin(false);
        }
    }

    private function showLogin($failed)
    {
        $template = $this->twig->loadTemplate('login.htm');
        $template_params = array();
        $template_params["failed"] = $failed;
        echo $template->render($template_params);
    }

    private function table($name, $link, $data)
    {
        $template = $this->twig->loadTemplate('table.htm');
        $template_params = array();
        $template_params["pageName"] = $name;
        $template_params["link"] = $link;
        $template_params["data"] = $data;
        $template_params["dataLenght"] = sizeof($data);
        echo $template->render($template_params);
    }

    private function loans($data, $pageName = null)
    {
        $navbar = $this->twig->loadTemplate('loans.htm');

        $template_params = array();
        if ($pageName != null)
            $template_params["pageName"] = $pageName;
        $template_params["data"] = $data;
        $template_params["dataLenght"] = sizeof($data);
        echo $navbar->render($template_params);
    }

    private function detail($name, $data)
    {
        $template = $this->twig->loadTemplate('detail.htm');
        $template_params = array();
        $template_params["pageName"] = $name;
        $template_params["info"] = $data;
        echo $template->render($template_params);
    }

    private function unauthorized()
    {
        $template = $this->twig->loadTemplate('unauthorized.htm');
        $template_params = array();
        echo $template->render($template_params);
    }

    private function administration()
    {
        $type = 0;
        if (isSet($_POST["jmeno"]))
        {
            if ($this->db->addMember($_POST))
                $type = 1;
            else
                $type = 2;
        }
        else if (isSet($_POST["nazev"]))
        {
            if ($this->db->addGame($_POST))
                $type = 3;
            else
                $type = 4;
        }

        $this->showForms($type);
    }

    private function showForms($type)
    {
        $template = $this->twig->loadTemplate('admin.htm');
        $template_params = array();
        $template_params["type"] = $type;
        $closets = $this->db->getClosets();
        $template_params["closets"] = $closets;
        $template_params["closetsLenght"] = sizeof($closets);
        $members = $this->db->getMembers();
        $template_params["members"] = $members;
        $template_params["membersLenght"] = sizeof($members);
        $games = $this->db->getGames();
        $template_params["games"] = $games;
        $template_params["gamesLenght"] = sizeof($games);
        echo $template->render($template_params);
    }
}

?>
