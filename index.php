<?php
  require_once('Twig-1.x/lib/Twig/Autoloader.php');
  require_once('User.php');
  require_once('Database.php');
  require_once('TablePage.php');
  require_once('DetailPage.php');
  require_once('AdminPage.php');
  require_once('LoansPage.php');
  require_once('LoginPage.php');

  $db = new Database();
  $user = new User($db);
  $twig = initTwig();

  $template = $twig->loadTemplate('index.htm');
  $template_params = array();
  $template_params["navbar"] = createNavBar();
  $template_params["body"] = createBody();
  echo $template->render($template_params);
  
  // Initiliaze Twig templating engine
  function initTwig()
  {
      Twig_Autoloader::register();
      $loader = new Twig_Loader_Filesystem('templates');
      return new Twig_Environment($loader);
  }
  
  // Create and render navigation bar
  function createNavBar()
  {
      global $twig, $user;
      $navbar = $twig->loadTemplate('navbar.htm');

      $template_params = array();
      $template_params["info"] = $user->getInfo();
      $template_params["role"] = $user->getRole();
      $template_params["page"] = currPage();
      $template_params["pageId"] = currPageId();
      echo $navbar->render($template_params);
  }

  // Create and render body
  function createBody()
  {
      global $twig, $user, $db;
      $result = '';
      if (isSet($_GET["logout"]))
      {
          $user->logout();
          header('Location: '.strtok($_SERVER["REQUEST_URI"],'?'));
          die;
      }
      else if (isSet($_GET["login"]))
      {
          $page = new LoginPage($user,$twig);
          $result .= $page->render();
      }
      else if (isSet($_GET["loans"]))
      {
          if ($user->isLogged())
              $page = new LoansPage($user, $db, $twig);
          else
              $page = new UnauthorizedPage($twig);
          $result .= $page->render();
      }
      else if (isSet($_GET["member"]))
      {
          if ($user->isLogged())
          {
              $page = new MemberPage($user, $db, $twig, $_GET["member"]);
              $result .= $page->render();
              $page = new LoansPage($user, $db, $twig, $_GET["member"]);
          }
          else
              $page = new UnauthorizedPage();
          $result .= $page->render();
      }
      else if (isSet($_GET["members"]))
      {
          if ($user->isLogged())
              $page = new MembersPage($db,$twig);
          else
              $page = new UnauthorizedPage($twig); 
          $result .= $page->render();
      }
      else if (isSet($_GET["game"]))
      {
          $page = new GamePage($user,$db,$twig,$_GET["game"]);
          if ($user->isLogged())
          {
              $result .= $page->render();
              $page = new LoansPage($user, $db, $twig, null, $_GET["game"]);
          }
          $result .= $page->render();
                
      }
      else if (isSet($_GET["admin"]))
      {
          if ($user->getRole() == 'ADMIN') 
              $page = new AdminPage($user, $db, $twig);
          else
              $page = new UnauthorizedPage($twig);
          $result .= $page->render();
      }
      else
      {
          $page = new GamesPage($db,$twig);
          $result .= $page->render();    
      }

      return $result;
    }
    
    // Helper for retrieve page name from GET
    function currPage()
    {
        if (isSet($_GET["members"]))
            return 'members';
        else if (isSet($_GET["member"]))
            return 'member';
        else if (isSet($_GET["loans"]))    
            return 'loans';
        else if (isSet($_GET["login"]))   
            return 'login';
        else if (isSet($_GET["admin"]))   
            return 'admin';
        else
            return 'games';
    }

    // Helper for retrieve id from GET
    function currPageId()
    {
        if (isSet($_GET["member"]))
            return $_GET["member"];
        else if (isSet($_GET["game"]))    
            return $_GET["game"];
        else 
            return 0;
    }

?>
