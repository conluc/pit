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
        <title>MaxCakes - Compra Finalizada</title>
        <link rel="stylesheet" href="style.css?a=43"/>
    </head>
    <body>
        <?php
            $purchase_cod = 0;
        
            function registerPurchase() {
                global $purchase_cod;
                $address = 0; $price = 0.0; $details = ""; $purchase_date = null;
                
                $address = filter_input(INPUT_POST, "address");
                if (!$address) {
                    echo "Erro: Endereço inválido.";
                    return false;
                }
                $price = filter_input(INPUT_POST, "price", FILTER_VALIDATE_FLOAT);
                if (!$price) {
                    echo "Erro: Preço total inválido.";
                    return false;
                }
                $details = filter_input(INPUT_POST, "details");
                
                $purchase_date = date("Y-m-d");
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return false;
                }
                
                $sql_query = "insert into purchase(user_cod, address_cod, details, price, purchase_date) values(?, ?, ?, ?, ?)";
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return false;
                }
                if ($sql_stmt->bind_param("iisds", $_SESSION["user_cod"], $address, $details, $price, $purchase_date) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return false;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return false;
                }
                
                $purchase_cod = $sql_stmt->insert_id;
                
                $sql_stmt->close();
                $sqlconn->close();
                
                return true;
            }
            
            function registerPurchaseItems() {
                global $purchase_cod;
                $item_cod = 0; $quantity = 0;
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return false;
                }
                
                $sql_query_1 = "select item_cod, quantity from cart where user_cod = ?";
                $sql_stmt_1 = $sqlconn->prepare($sql_query_1);
                if ($sql_stmt_1 === false) {
                    echo "Erro ao preparar query 1.";
                    return false;
                }
                if ($sql_stmt_1->bind_param("i", $_SESSION["user_cod"]) === false) {
                    echo "Erro ao vincular parametros da query 1.";
                    return false;
                }
                
                if ($sql_stmt_1->execute() === false) {
                    echo "Erro ao executar query 1.";
                    return false;
                }
                
                $sql_stmt_1->store_result();
                if ($sql_stmt_1->num_rows !== 0) {
                    $sql_stmt_1->bind_result($item_cod, $quantity);
                    
                    while ($sql_stmt_1->fetch()) {
                        $sql_query_2 = "insert into purchase_items(purchase_cod, item_cod, quantity) values(?, ?, ?)";
                        $sql_stmt_2 = $sqlconn->prepare($sql_query_2);
                        if ($sql_stmt_2 === false) {
                            echo "Erro ao preparar query 2.";
                            return false;
                        }

                        if ($sql_stmt_2->bind_param("iii", $purchase_cod, $item_cod, $quantity) === false) {
                            echo "Erro ao vincular parametros da query 2.";
                            return false;
                        }

                        if ($sql_stmt_2->execute() === false) {
                            echo "Erro ao executar query 2.";
                            return false;
                        }
                        
                        $sql_stmt_2->close();
                    }
                } else {
                    echo "Erro: Nenhum item no carrinho.";
                    return false;
                }
                
                $sql_stmt_1->close();
                $sqlconn->close();
                
                return true;
            }
            
            function emptyCart() {
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return false;
                }
                
                $sql_query = "delete from cart where user_cod = ?";
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return false;
                }
                if ($sql_stmt->bind_param("i", $_SESSION["user_cod"]) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return false;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return false;
                }
                
                return true;
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body-small'>";
            
            if (filter_input(INPUT_POST, "sendpurchase")) {
                if (registerPurchase()) {
                    
                    if (registerPurchaseItems()) {
                        
                        if (emptyCart()) {
                            echo "Compra realizada com sucesso.";
                        } else {
                            echo "Erro ao esvaziar carrinho.";
                        }
                    } else {
                        echo "Erro ao registrar items da compra.";
                    }
                } else {
                    echo "Erro ao registrar compra.";
                }
            } else {
                echo "Erro: Nenhum dado enviado.";
            }
            
            echo "<br/>";
            echo "<a href='carrinho.php'><button>Voltar</button></a>";
            
            echo "</div>";
        ?>
    </body>
</html>