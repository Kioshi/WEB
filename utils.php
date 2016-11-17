<?php

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
        else
            return 'games';
    }
    
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

