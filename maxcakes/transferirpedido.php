<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_cod"])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_OFF);

?>

<html>
    <head>
        <title>MaxCakes - Transferir Pedido</title>
        <link rel="stylesheet" href="style.css?i=122"/>
    </head>
    <body>
        <?php
            $purchaseType = 'CONFIRMED';
        
            function transferPurchase() {
                global $purchaseType;
                
                $purchaseCod = filter_input(INPUT_POST, "purchasecod");
                if ($purchaseCod == null) {
                    echo "Erro: Código de compra inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "update purchase set status = ? where cod = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($sql_stmt->bind_param("si", $purchaseType, $purchaseCod) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                if ($purchaseType == "CANCELED") {
                    echo "Pedido cancelado com sucesso.";
                } elseif ($purchaseType == "CONFIRMED") {
                    echo "Pedido confirmado com sucesso.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
            
            if (filter_input(INPUT_POST, "cancel") !== null) {
                $purchaseType = "CANCELED";
            } elseif (filter_input(INPUT_POST, "confirm") !== null) {
                $purchaseType = "CONFIRMED";
            }
        
            echo "<div id='body-small'>";
            
            transferPurchase();
            
            echo "<br/>";
            echo "<a href='pedidos.php'><button>Voltar</button></a>";
            
            echo "</div>";
        ?>
    </body>
</html>