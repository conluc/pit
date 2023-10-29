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
        <title>MaxCakes - Finalizar Compra</title>
        <link rel="stylesheet" href="style.css?a=43"/>
    </head>
    <body>
        <?php
            $can_buy = true;
            $total_price = 0.0;
            
            function printPurchase() {
                global $can_buy;
                global $total_price;
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $item_name = ""; $item_quantity = 0; $item_price = 0;
                $sql_query = "select items.name, cart.quantity, items.price from cart inner join items on cart.item_cod = items.cod"
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
                    $sql_stmt->bind_result($item_name, $item_quantity, $item_price);
                    
                    echo "<table class = 'item-list'>";
                    while ($sql_stmt->fetch()) {
                        $item_price_times_quantity = $item_price * $item_quantity;
                        $total_price += $item_price_times_quantity;
                        
                        echo "<tr>";
                        
                        echo "<td style='width: 700px;'>$item_name</td>";
                        echo "<td>Qtd.$item_quantity</td>";
                        echo "<td>R$" . number_format($item_price_times_quantity, 2) . "</td>";
                        
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<div style='text-align: right;'>Custo total: R$" . number_format($total_price, 2) . "</div>";
                } else {
                    echo "Nenhum item salvo no carrinho.";
                    $can_buy = false;
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
            
            function printAddresses() {
                global $can_buy;
                
                echo "<p style='font-weight: bold; text-align: center'>Endereço de entrega:</p>";
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $cod = 0; $name = ""; $street = "";
                $sql_query = "select cod, name, street from addresses where user_cod = ?";
                
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
                    $sql_stmt->bind_result($cod, $name, $street);
                    $i = 0;
                    
                    echo "<form action='comprafinalizada.php' method='post' id='form1'>";
                    while ($sql_stmt->fetch()) {
                        $i++;
                        
                        echo "<input type='radio' name='address' value='$cod' required checked/>";
                        echo "Endereço $i: Nome: $name - Rua: $street";
                        echo "<br/>";
                    }
                    echo "</form>";
                } else {
                    echo "Você precisa registrar um endereço para fazer a compra.";
                    $can_buy = false;
                }
            }

            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Finalizar Compra</h1>";
            
            echo "<div style='margin: auto; width: 900px'>";
            
            printPurchase();
            printAddresses();
            
            if ($can_buy) {
                echo "<input type='hidden' name='price' value='$total_price' form='form1'/>";
                echo "<div style='text-align: center;'>";
                echo "<h1>Adicionar detalhes:</h1>";
                echo "<textarea name='details' cols='60' rows='5' maxlength='255' form='form1'></textarea><br/>";
                echo "<input type='submit' name='sendpurchase' value='Finalizar' form='form1'/>";
                echo "</div>";
            }
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>