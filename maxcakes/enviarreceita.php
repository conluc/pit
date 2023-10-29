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
        <title>MaxCakes - Enviar Receita</title>
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php
            function printItemForm() {
                $mode = "";
                if (filter_input(INPUT_POST, "edititem") !== null){
                    $mode = "edit";
                } elseif (filter_input(INPUT_POST, "additem") !== null) {
                    $mode = "add";
                } else {
                    echo "Nenhum modo (Editar ou adicionar) válido.";
                    return;
                }
                
                echo "<p style='font-weight: bold; text-align: center'>Item:</p>";
                
                $name = ""; $image = ""; $price = 0.0;
                
                if ($mode === "edit") {
                    $cod = filter_input(INPUT_POST, "itemcod");
                    if (!$cod) {
                        echo "Erro: Nenhum codigo de item recebido.";
                        return;
                    }
                    
                    $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                    if ($sqlconn->connect_error) {
                        echo "Erro ao se conectar com o banco.";
                        return;
                    }
                    
                    $sql_query = "select name, image, price from items where cod = ?";

                    $sql_stmt = $sqlconn->prepare($sql_query);
                    if ($sql_stmt === false) {
                        echo "Erro ao preparar query.";
                        return;
                    }

                    if ($sql_stmt->bind_param("i", $cod) === false) {
                        echo "Erro ao vincular parametros da query.";
                        return;
                    }

                    if ($sql_stmt->execute() === false) {
                        echo "Erro ao executar query.";
                        return;
                    }

                    $sql_stmt->store_result();
                    if ($sql_stmt->num_rows === 0) {
                        echo "Erro: Nenhum item encontrado.";
                        return;
                    }

                    $sql_stmt->bind_result($name, $image, $price);
                    $sql_stmt->fetch();
                    
                    $sql_stmt->close();
                    $sqlconn->close();
                }
                
                echo "<form action='enviodereceita.php' method='post'>";
                
                if ($mode === "edit") {
                    echo "<input type='hidden' name='itemcod' value='$cod'/>";
                    echo "<input type='hidden' name='mode' value='edit'/>";
                } elseif ($mode === "add") { 
                    echo "<input type='hidden' name='mode' value='add'/>";
                }
                
                echo "<span style='float: left;'>Nome: </span>";
                echo "<input type='text' name='name' style='float: right; width: 150px' value='$name' required autofocus/><br/>";
                
                echo "<span style='float: left;'>Imagem: </span>";
                echo "<input type='text' name='image' style='float: right; width: 150px' value='$image' required/><br/>";
                
                echo "<span style='float: left;'>Preço: </span>";
                echo "<input type='number' name='price' style='float: right; width: 150px' value='$price' min='0' step='0.01' required/><br/>";
                
                echo "<br/><input type='submit' name='send' value='Enviar'/>";
                echo "</form>";
                
                if ($mode === "edit") {
                    echo "<form action='enviodereceita.php' method='post'>";
                    echo "<input type='hidden' name='itemcod' value='$cod'/>";
                    echo "<input type='submit' name='delete' value='Deletar'/>";
                    echo "</form>";
                }
            }
        
        
            printHeader();
            printMenu();
            
            echo "<div id='body-small'>";
            
            echo "<div style='margin: auto; width: 300px'>";
            printItemForm();
            
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>
