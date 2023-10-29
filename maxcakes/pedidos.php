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
        <title>MaxCakes - Lista de Pedidos</title>
        <link rel="stylesheet" href="style.css?a=6"/>
    </head>
    <body>
        <?php
            $purchaseType = "PENDING";
            
            function printPurchase() {
                global $purchaseType;
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $purchaseCod = ""; $email = ""; $price = 0; $purchaseDate = "";
                $sql_query = "select purchase.cod, users.email, purchase.price, purchase.purchase_date from users inner join purchase"
                            . " on users.cod = purchase.user_cod where purchase.status = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("s", $purchaseType) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                
                if ($sql_stmt->num_rows !== 0) {
                    $sql_stmt->bind_result($purchaseCod, $email, $price, $purchaseDate);
                    
                    while ($sql_stmt->fetch()) {
                        echo "<form action='detalhesdopedido.php' method='POST'>";
                        
                        echo "<div>";
                        echo "<input type='hidden' name='purchasecod' value='$purchaseCod'/>";
                        echo "<input type='hidden' name='purchasetype' value='$purchaseType'/>";
                        echo "$email - R$" . number_format($price, 2) . " - $purchaseDate";
                        echo "<input type='submit' value='Detalhes'/>";
                        echo "</div>";
                        
                        echo "</form>";
                    }
                } else {
                    echo "Nenhuma venda a ser exibida.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
            
            printHeader();
            printMenu();
            
            if (filter_input(INPUT_POST, "pendingbutton") !== null) {
                $purchaseType = "PENDING";
            } elseif (filter_input(INPUT_POST, "canceledbutton") !== null) {
                $purchaseType = "CANCELED";
            } elseif (filter_input(INPUT_POST, "confirmedbutton") !== null) {
                $purchaseType = "CONFIRMED";
            }
            
            echo "<div id='body'>";
            
            if ($purchaseType === "PENDING") {
                echo "<h1>Lista de Pedidos Pendentes:</h1>";
            } elseif ($purchaseType === "CANCELED") {
                echo "<h1>Lista de Pedidos Cancelados:</h1>";
            } elseif ($purchaseType === "CONFIRMED") {
                echo "<h1>Lista de Pedidos Confirmados:</h1>";
            }
            
            echo "<div style='margin: auto; width: 900px'>";
            
            echo "<span style='text-align: center;'>";
            echo "<form action='pedidos.php' method='post'>";
            
            echo "<input type='submit' name='pendingbutton' value='Pendentes'/>";
            echo "<input type='submit' name='canceledbutton' value='Cancelados'/>";
            echo "<input type='submit' name='confirmedbutton' value='Confirmados'/>";
            
            echo "<br/><br/>";
            
            echo "</form>";
            echo "</span>";
            
            printPurchase();
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>