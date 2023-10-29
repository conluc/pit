<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_cod"])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_OFF);

require "elementos.php";
?>

<html>
    <head>
        <title>MaxCakes - Reclamações</title>
        <link rel="stylesheet" href="style.css?a=22"/>
    </head>
    <body>
        <?php
            function sendComplaint() {
                $complaint = filter_input(INPUT_POST, "complaint");
                if (!$complaint) {
                    echo "Erro: Código do item a ser adicionado inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "insert into complaints(user_cod, complaint) values(?, ?)";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("is", $_SESSION["user_cod"], $complaint) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                echo "Reclamação enviada com sucesso.";
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Fazer Reclamações</h1>";
            echo "<br/>";
            
            echo "<div style='margin: auto; width: 900px;'>";
            
            if (filter_input(INPUT_POST, "sendcomplaint")) {
                sendComplaint();
            }
            
            echo "<div style='text-align: center;'>";
            
            echo "<form action='reclamacoes.php' method='post'>";
            echo "<h1>Reclamação:</h1>";
            echo "<textarea name='complaint' cols='60' rows='5' maxlength='255' required></textarea><br/>";
            echo "<input type='submit' name='sendcomplaint' value='Enviar'/>";
            echo "</form>";
            
            echo "</div>";
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>