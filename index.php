<html><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://pingendo.github.io/pingendo-bootstrap/themes/default/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="deskovky.css" rel="stylesheet" type="text/css">
    <?php 
      include 'server.php';
      include 'templates.php';
      ini_set('register_globals', 'on');
    ?>
  </head><body>
    <div class="navbar navbar-default navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">Deskovky</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a href="index.php">Hry</a>
            </li>
            <li>
              <a href="index.php?users">Uživatelé</a>
            </li>
            <li class="active">
              <a href="vypujcky.php">Výpůjčky<br></a>
            </li>
            <li class="loginbutton">
              <?php
                $username = getUserName();
                if (!$username)
                {
                  //$params = array_merge($_GET, array("login"=>""));
                  //echo basename($_SERVER['PHP_SELF']);
                  //echo '<a href="'.basename($_SERVER['PHP_SELF']).'?'.http_build_query($params).'">Login<br></a>';
                  echo '<a href="">Login<br></a>';
                }
                else
                  echo '<a href="index.php?user='.$username.'">'.$username.'<br></a>';
              ?>
            </li>
          </ul>
        </div>
      </div>
    </div>
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
          for ($i = 0; $i <= 10; $i++) 
          {
            $columns = '';
            for ($j = 1; $j <= 6; $j++) 
              $columns .= generate(col2(),($i*6+$j));
            
            echo generate(row(),$columns);
          }
        ?>
      </div>
    </div>
  

</body></html>