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
        <title>MaxCakes - Depoimentos</title>
        <link rel="stylesheet" href="style.css?a=21"/>
    </head>
    <body>
        <?php
            function sendFeedback() {
                $feedback = filter_input(INPUT_POST, "feedback");
                if (!$feedback) {
                    echo "Erro: Código do item a ser adicionado inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "insert into feedbacks(user_cod, feedback) values(?, ?)";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("is", $_SESSION["user_cod"], $feedback) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            function printFeedbacks() {
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                        echo "Erro ao se conectar com o banco.";
                        return;
                }
                
                $email = ""; $feedback = "";
                $sql_query = "select users.email, feedbacks.feedback from users inner join feedbacks on users.cod = feedbacks.user_cod;";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                if ($sql_stmt->num_rows !== 0) {
                    $sql_stmt->bind_result($email, $feedback);
                    
                    echo "<table class='feedback-list'>";
                    while ($sql_stmt->fetch()) {
                        echo "<tr>";
                        
                        echo "<td style='width: 240px;'>$email</td>";
                        echo "<td>$feedback</td>";
                        
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Nenhum depoimento a ser exibido.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Depoimentos</h1>";
            echo "<br/>";
            
            echo "<div style='margin: auto; width: 900px;'>";
            
            if (filter_input(INPUT_POST, "sendfeedback")) {
                sendFeedback();
            }
            
            printFeedbacks();
            
            echo "<br/>";
            
            echo "<div style='text-align: center;'>";
            
            echo "<form action='depoimentos.php' method='post'>";
            echo "<h1>Fazer depoimento:</h1>";
            echo "<textarea name='feedback' cols='60' rows='5' maxlength='255' required></textarea><br/>";
            echo "<input type='submit' name='sendfeedback' value='Enviar'/>";
            echo "</form>";
            
            echo "</div>";
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>