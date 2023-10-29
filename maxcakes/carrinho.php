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
        <title>MaxCakes - Carrinho de Compras</title>
        <link rel="stylesheet" href="style.css?a=43"/>
    </head>
    <body>
        <?php
            function updateQuantity() {
                $item_cod = filter_input(INPUT_POST, "item_cod");
                if (!$item_cod) {
                    echo "Erro: Código do item a ser atualizado inválido.";
                    return;
                }
                $item_quantity = filter_input(INPUT_POST, "item_quantity");
                if (!$item_quantity || $item_quantity > 50 || $item_quantity < 0) {
                    echo "Erro: Quantidade do item a ser atualizado inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "update cart set quantity = ? where user_cod = ? and item_cod = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("iii", $item_quantity, $_SESSION["user_cod"], $item_cod) === false) {
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
        
            function removeFromCart() {
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
                
                $sql_query = "delete from cart where user_cod = ? and item_cod = ?";
                
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
        
            function addToCart() {
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
                
                $sql_pre_query = "select * from cart where user_cod = ? and item_cod = ?";
                
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
                
                $sql_query = "insert into cart(user_cod, item_cod, quantity) values(?, ?, 1)";
                
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
        
            function printCart() {
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $item_cod = 0; $item_quantity = 0; $item_name = "";
                $sql_query = "select cart.item_cod, cart.quantity, items.name from cart inner join items on cart.item_cod = items.cod"
                            . " where cart.user_cod = ?";
                
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
                    $sql_stmt->bind_result($item_cod, $item_quantity, $item_name);
                
                    echo "<table class = 'item-list'>";
                
                    while ($sql_stmt->fetch()) {
                        echo "<tr>";
                        
                        echo "<td>$item_name</td>";
                        
                        echo "<td style='width: 140px;'><form action='carrinho.php' method='post'>";
                        echo "<input type='hidden' name='item_cod' value='$item_cod'/>";
                        echo "Qtd.";
                        echo "<input type='number' name='item_quantity' value='$item_quantity' min='1' max='50' style='width: 50px;'/>";
                        echo "<input type='submit' name='updatequantity' value='Atualizar' title='Atualizar quantidade'/>";
                        echo "</form></td>";
                        
                        echo "<td style='width: 40px;'><form action='carrinho.php' method='post'>";
                        echo "<input type='hidden' name='item_cod' value='$item_cod'/>";
                        echo "<input type='submit' name='removefromcart' value=' ' title='Remover do carrinho'"
                            . " style='background: url(remove.png); background-size: contain; width: 32; height: 32;'/>";
                        echo "</form></td>";
                        
                        echo "</tr>";
                    }
                
                    echo "</table>";
                    
                    echo "</br>";
                    echo "<a href='finalizarcompra.php' style='float: right;'><button>Finalizar compra</button></a>";
                } else {
                    echo "Nenhum item salvo no carrinho.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Carrinho de Compras</h1>";
            
            echo "<div style='margin: auto; width: 900px'>";
            
            if (filter_input(INPUT_POST, "addtocart")) {
                addToCart();
            } elseif (filter_input(INPUT_POST, "updatequantity")) {
                updateQuantity();
            } elseif (filter_input(INPUT_POST, "removefromcart")) {
                removeFromCart();
            }
            
            printCart();
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>