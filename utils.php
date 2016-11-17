<?php

    function currPage()
    {
        if (isSet($_GET["users"]))
            return 'users';
        else if (isSet($_GET["user"]))
            return 'user';
        else if (isSet($_GET["loans"]))    
            return 'loans';
        else if (isSet($_GET["login"]))   
            return 'login';
        else
            return 'games';
    }
    
    function currPageId()
    {
        if (isSet($_GET["user"]))
            return $_GET["user"];
        else if (isSet($_GET["game"]))    
            return $_GET["game"];
        else 
            return 0;
    }

?>

