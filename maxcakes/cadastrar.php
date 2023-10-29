<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_cod"])) {
    header("Location: conta.php");
    exit();
}

require "elementos.php";
?>

<html>
    <head>
        <title>MaxCakes - Cadastrar</title>
        <link rel="stylesheet" href="style.css?i=122"/>
    </head>
    <body>
        <?php
            printHeader();
            printMenu();
            
            echo "<div id='body-small'>";
            echo "<h1>Cadastrar conta</h1>";
            
            echo "<form action='cadastro.php' method='post'>";
            
            echo "<div style='margin: auto; width: 300px'>";
            
            echo "<span style='float: left;'>Email: </span>";
            echo "<input type='email' name='email' style='float: right; width: 150px' required autofocus/><br/>";
            
            echo "<span style='float: left;'>Senha: </span>";
            echo "<input type='password' name='password' style='float: right; width: 150px' required/><br/>";
            
            echo "<span style='float: left;'>Confirmar senha: </span>";
            echo "<input type='password' name='confirm_password' style='float: right; width: 150px' required/><br/>";
            
            echo "<br/>";
            echo "<span style='float: left;'>Tipo de conta: </span><br/>";
            echo "<input type='radio' name='type' value='client' checked required/> Usuário<br/>";
            echo "<input type='radio' name='type' value='employee' required/> Funcionário<br/>";
            
            echo "<br/>";
            echo "<input type='submit' name='submit_data' value='Cadastrar'/>";
            
            echo "</div>";
            
            echo "</form>";
            
            echo "</div>";
        ?>
    </body>
</html>