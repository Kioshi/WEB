<?php

    function generateFromFile($template, ...$args)
    {
        
        $file = file_get_contents($template);
        return sprintf($file,...$args);
    }

    function generate($template, ...$args)
    {
        return sprintf($template,...$args);
    }

    function row()
    {
        return '<div class="row">%s</div>';
    }

    function col2()
    {
        return '<div class="col-md-2">
                    <a href="hry.php?id=123">
                        <img src="http://pingendo.github.io/pingendo-bootstrap/assets/placeholder.png" class="center-block img-circle img-responsive">
                        <p class="text-center">Hra %d</p>
                    </a>
                </div>';
    }

?>