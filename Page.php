<?php

require_once('Twig-1.x/lib/Twig/Autoloader.php');

abstract class Page
{
    protected $twig;
    public function __construct($twig)
    {  
        $this->twig = $twig;
    }

    // Virtual function for rendering page
    abstract public function render();
}

class UnauthorizedPage extends Page
{    
    public function render()
    {
        $template = $this->twig->loadTemplate('unauthorized.htm');
        $template_params = array();
        return $template->render($template_params);
    }
}

?>