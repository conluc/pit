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
        <title>MaxCakes - Minha Conta</title>
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php
            function printAddresses() {
                echo "<p style='font-weight: bold; text-align: center'>Endereços:</p>";
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $user_cod = 0; $cod = 0; $name = ""; $cep = ""; $neighborhood = ""; $street = ""; $number = 0; $complement = "";
                $sql_query = "select user_cod, cod, name, cep, neighborhood, street, number, complement from addresses"
                            . " where user_cod = ?";
                
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
                    $sql_stmt->bind_result($user_cod, $cod, $name, $cep, $neighborhood, $street, $number, $complement);
                    $i = 0;
                    
                    while ($sql_stmt->fetch()) {
                        $i++;
                        echo "<form action='editarendereco.php' method='post'>";
                        
                        echo "<input type='hidden' name='addrcod' value='$cod'/>";
                        echo "<div>Endereço $i: Nome: $name - Rua: $street";
                        echo "<input type='submit' name='edit' value='Editar' title='Editar endereço'></div>";
                        
                        echo "</form>";
                    }
                }
                
                echo "<form action='editarendereco.php' method='post'>";
                echo "<input type='submit' name='add' value='Adicionar endereço' title='Adicionar endereço'></div>";
                echo "</form>";
                
                $sql_stmt->close();
                $sqlconn->close();
            }
            
            function printSales() {
                echo "<p style='font-weight: bold; text-align: center'>Vendas do Dia:</p>";
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $email = ""; $price = 0;
                $sql_query = "select users.email, purchase.price from users inner join purchase on users.cod = purchase.user_cod"
                            . " where purchase.purchase_date = ?";
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                $current_date = date("Y-m-d");
                if ($sql_stmt->bind_param("s", $current_date) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                if ($sql_stmt->num_rows !== 0) {
                    $sql_stmt->bind_result($email, $price);
                    
                    $total_price = 0;
                    while ($sql_stmt->fetch()) {
                        $total_price += $price;
                        
                        echo "<div>";
                        echo "<span style='float: left'>$email</span>";
                        echo "<span style='float: right'>R$" . number_format($price, 2) . "</span>";
                        echo "</div>";
                        echo "<br/>";
                    }
                    echo "<div style='float: right'>Total vendido: R$" . number_format($total_price, 2) . "</div>";
                } else {
                    echo "Nenhuma venda hoje.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
            
            printHeader();
            printMenu();
            
            echo "<div id='body-small'>";
            echo "<h1>Minha conta</h1>";
            
            echo "<div style='margin: auto; width: 300px'>";
            
            echo "Email: " . $_SESSION["user_email"] . "<br/>";
            echo "<a href='logout.php' style='float: right;'>Fazer logout</a><br/>";
            
            if ($_SESSION["user_type"] == "client") {
                printAddresses();
            } else if ($_SESSION["user_type"] == "employee") {
                printSales();
            }
            
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>