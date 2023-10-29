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
        <title>MaxCakes - Favoritos</title>
        <link rel="stylesheet" href="style.css?a=6"/>
    </head>
    <body>
        <?php
            function addToFavorites() {
                $item_cod = filter_input(INPUT_POST, "item_cod");
                if (!$item_cod) {
                    echo "Erro: Código do item a ser adicionado inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                        echo "Erro ao se conectar com o banco.";
                        return;
                }
                
                $sql_pre_query = "select * from favorites where user_cod = ? and item_cod = ?";
                
                $sql_pre_stmt = $sqlconn->prepare($sql_pre_query);
                if ($sql_pre_stmt === false) {
                    echo "Erro ao preparar pré-query.";
                    return;
                }
                
                if ($sql_pre_stmt->bind_param("ii", $_SESSION["user_cod"], $item_cod) === false) {
                    echo "Erro ao vincular parametros da pré-query.";
                    return;
                }
                
                if ($sql_pre_stmt->execute() === false) {
                    echo "Erro ao executar pré-query.";
                    return;
                }
                
                $sql_pre_stmt->store_result();
                if ($sql_pre_stmt->num_rows !== 0) {
                    return;
                }
                $sql_pre_stmt->close();
                
                $sql_query = "insert into favorites(user_cod, item_cod) values(?, ?)";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("ii", $_SESSION["user_cod"], $item_cod) === false) {
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
        
            function removeFromFavorites() {
                $item_cod = filter_input(INPUT_POST, "item_cod");
                if (!$item_cod) {
                    echo "Erro: Código do item a ser removido inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                        echo "Erro ao se conectar com o banco.";
                        return;
                }
                
                $sql_query = "delete from favorites where user_cod = ? and item_cod = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("ii", $_SESSION["user_cod"], $item_cod) === false) {
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
        
            function printFavorites() {
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                        echo "Erro ao se conectar com o banco.";
                        return;
                }
                
                $item_cod = 0; $item_name = "";
                $sql_query = "select favorites.item_cod, items.name from favorites inner join items on favorites.item_cod = items.cod"
                            . " where favorites.user_cod = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("i", $_SESSION["user_cod"]) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                if ($sql_stmt->num_rows !== 0) {
                    $sql_stmt->bind_result($item_cod, $item_name);
                
                    echo "<table class = 'item-list'>";
                
                    while ($sql_stmt->fetch()) {
                        echo "<tr>";
                        
                        echo "<td>$item_name</td>";
                        
                        echo "<td style='width: 40px;'><form action='carrinho.php' method='post'>";
                        echo "<input type='hidden' name='item_cod' value='$item_cod'/>";
                        echo "<input type='submit' name='addtocart' value=' ' title='Adicionar ao carrinho'"
                            . " style='background: url(add_to_cart.png); background-size: contain; width: 32; height: 32;'/>";
                        echo "</form></td>";
                        
                        echo "<td style='width: 40px;'><form action='favoritos.php' method='post'>";
                        echo "<input type='hidden' name='item_cod' value='$item_cod'/>";
                        echo "<input type='submit' name='removefromfavorites' value=' ' title='Remover dos favoritos'"
                            . " style='background: url(remove.png); background-size: contain; width: 32; height: 32;'/>";
                        echo "</form></td>";
                        
                        echo "</tr>";
                    }
                
                    echo "</table>";
                } else {
                    echo "Nenhum item salvo como favoritos.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Favoritos</h1>";
            
            echo "<div style='margin: auto; width: 900px'>";
            
            if (filter_input(INPUT_POST, "addtofavorites")) {
                addToFavorites();
            } elseif (filter_input(INPUT_POST, "removefromfavorites")) {
                removeFromFavorites();
            }
            
            printFavorites();
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>