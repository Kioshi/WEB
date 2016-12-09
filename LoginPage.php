<?php
require_once('Page.php');
require_once('User.php');

class LoginPage extends Page
{
    protected $user;
    protected $template;
    protected $failed;

    public function __construct($user, $twig)
    {
        parent::__construct($twig);

        $this->user = $user;
        $this->template = $twig->loadTemplate('login.htm');
        
        $this->login();
    }

    public function render()
    {
        $template_params = array();
        $template_params["failed"] = $this->failed;
        return $this->template->render($template_params);
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
                $this->failed = true;
        }
        else
            $this->failed = false;
    }

}


?>