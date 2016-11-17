<html> 
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://pingendo.github.io/pingendo-bootstrap/themes/default/bootstrap.css" rel="stylesheet" type="text/css">
    <?php 
        require_once('utils.php');
        /*include 'server.php';
        include 'table.php';
        include 'game.php';
        include 'user.php';
        include 'login.php';
        include 'registration.php';*/
    ?>
  </head>
  <body>
    <?php
      
      require_once('User.php');
      $user = new User();

      require_once 'Twig-1.x/lib/Twig/Autoloader.php';
      Twig_Autoloader::register();
      $loader = new Twig_Loader_Filesystem('templates');
      $twig = new Twig_Environment($loader);
      $navbar = $twig->loadTemplate('navbar.html');

      $template_params = array();
      $template_params["info"] = $user->getInfo();
      $template_params["page"] = currPage();
      $template_params["pageId"] = currPageId();
      echo $navbar->render($template_params);


    /*
      include_once 'main.php'; 

      if (isSet($_GET["users"]))
        showTable(false);
      else if (isSet($_GET["game"]))
        showDetail(true,$_GET["game"]);
      else if (isSet($_GET["user"]))    
        showDetail(false,$_GET["user"]);
      else if (isSet($_GET["borrows"]))    
        showBorrows();
      else if (isSet($_GET["login"]))   
        showLogin();     
      else if (isSet($_GET["registration"]))   
        showRegistration();     
      else
        showTable(true);
        */
    ?>
    <!--
    <div class="section">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <h1 class="text-center">
            <?php
              if (isSet($_GET["users"]))
                echo 'Uživatelé';
              else
                echo 'Hry';
              echo '<br>';
            ?>
            </h1>
          </div>
        </div>
        <?php
          for ($i = 0; $i <= 5; $i++) 
          {
            $columns = '';
            for ($j = 1; $j <= 4; $j++) 
              $columns .= generate(col3(),($i*6+$j));
            
            echo generate(row(),$columns);
          }
        ?>
      </div>
    </div>
-->  

  </body>
</html>
