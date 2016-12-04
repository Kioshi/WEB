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
                $this->loans($this->db->getLoans($this->user,null,null),"Výpůjčky");
            else
                $this->unauthorized();  
        }
        else if (isSet($_GET["member"]))
        {
            if ($this->user->isLogged())
            {
                $this->detail('Člen', $this->db->getMember($_GET["member"]));
                $this->loans($this->db->getLoans($this->user,$_GET["member"],null));
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
                $this->loans($this->db->getLoans($this->user,null,$_GET["game"]));
                  
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
}

?>
