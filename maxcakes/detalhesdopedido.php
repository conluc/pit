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
        <title>MaxCakes - Detalhes do Pedido</title>
        <link rel="stylesheet" href="style.css?a=6"/>
    </head>
    <body>
        <?php
            $total_price = 0.0;
            
            function printItems() {
                global $total_price;
                
                $purchaseCod = filter_input(INPUT_POST, "purchasecod");
                if ($purchaseCod == null) {
                    echo "Erro: Código de compra inválido.";
                    return;
                }
                
                $purchaseType = filter_input(INPUT_POST, "purchasetype");
                if ($purchaseCod == null) {
                    echo "Erro: Tipo de compra inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $item_name = ""; $item_price = 0; $item_quantity = 0; 
                $sql_query = "select items.name, items.price, purchase_items.quantity from items inner join purchase_items on" 
                            . " purchase_items.item_cod = items.cod where purchase_items.purchase_cod = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("i", $purchaseCod) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                if ($sql_stmt->num_rows !== 0) {
                    $sql_stmt->bind_result($item_name, $item_price, $item_quantity);
                    
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
                    echo "Erro: Nenhum item de compra encontrado.";
                }
                
                if ($purchaseType === "PENDING") {
                    echo "<form action='transferirpedido.php' method='POST'>";
                    echo "<input type='hidden' name='purchasecod' value='$purchaseCod'/>";
                    echo "<input type='submit' name='confirm' value='Confirmar para entrega'/>";
                    echo "</form>";

                    echo "<br/><br/>";

                    echo "<form action='transferirpedido.php' method='POST'>";
                    echo "Cancelar pedido: <br/>";
                    echo "<input type='hidden' name='purchasecod' value='$purchaseCod'/>";
                    echo "<textarea name='details' cols='60' rows='5' maxlength='255' required placeholder='Motivo do cancelamento...'></textarea><br/>";
                    echo "<input type='submit' name='cancel' value='Cancelar pedido'/>";
                    echo "</form>";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Detalhes do Pedido:</h1>";
            
            echo "<div style='margin: auto; width: 900px'>";
            
            printItems();
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>