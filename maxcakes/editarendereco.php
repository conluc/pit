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
        <title>MaxCakes - Editar Endereço</title>
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <?php
            function printAddressForm() {
                $mode = "";
                if (filter_input(INPUT_POST, "edit") !== null){
                    $mode = "edit";
                } elseif (filter_input(INPUT_POST, "add") !== null) {
                    $mode = "add";
                } else {
                    echo "Nenhum modo (Editar ou adicionar) válido.";
                    return;
                }
                
                echo "<p style='font-weight: bold; text-align: center'>Endereço:</p>";
                
                $name = ""; $cep = ""; $neighborhood = ""; $street = ""; $number = 0; $complement = "";
                
                if ($mode === "edit") {
                    $cod = filter_input(INPUT_POST, "addrcod");
                    if (!$cod) {
                        echo "Erro: Nenhum codigo de endereço recebido.";
                        return;
                    }
                    
                    $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                    if ($sqlconn->connect_error) {
                            echo "Erro ao se conectar com o banco.";
                            return;
                    }
                    
                    $sql_query = "select name, cep, neighborhood, street, number, complement from addresses"
                                . " where user_cod = ? and cod = ?";

                    $sql_stmt = $sqlconn->prepare($sql_query);
                    if ($sql_stmt === false) {
                        echo "Erro ao preparar query.";
                        return;
                    }

                    if ($sql_stmt->bind_param("ii", $_SESSION["user_cod"], $cod) === false) {
                        echo "Erro ao vincular parametros da query.";
                        return;
                    }

                    if ($sql_stmt->execute() === false) {
                        echo "Erro ao executar query.";
                        return;
                    }

                    $sql_stmt->store_result();
                    if ($sql_stmt->num_rows === 0) {
                        echo "Erro: Nenhum endereço encontrado.";
                        return;
                    }

                    $sql_stmt->bind_result($name, $cep, $neighborhood, $street, $number, $complement);
                    $sql_stmt->fetch();
                    
                    $sql_stmt->close();
                    $sqlconn->close();
                }
                
                echo "<form action='enviarendereco.php' method='post'>";
                
                if ($mode === "edit") {
                    echo "<input type='hidden' name='addrcod' value='$cod'/>";
                    echo "<input type='hidden' name='mode' value='edit'/>";
                } elseif ($mode === "add") { 
                    echo "<input type='hidden' name='mode' value='add'/>";
                }
                
                echo "<span style='float: left;'>Nome: </span>";
                echo "<input type='text' name='name' style='float: right; width: 150px' value='$name' required autofocus/><br/>";
                
                echo "<br/><span style='float: left;'>CEP: </span>";
                echo "<input type='text' name='cep' style='float: right; width: 150px' value='$cep' pattern='^[0-9]+$' required/><br/>";
                
                echo "<br/><span style='float: left;'>Bairro: </span>";
                echo "<input type='text' name='neighborhood' style='float: right; width: 150px' value='$neighborhood' required/><br/>";
                
                echo "<br/><span style='float: left;'>Rua: </span>";
                echo "<input type='text' name='street' style='float: right; width: 150px' value='$street' required/><br/>";
                
                echo "<br/><span style='float: left;'>Número: </span>";
                echo "<input type='text' name='number' style='float: right; width: 150px' value='$number' required/><br/>";
                
                echo "<br/><span style='float: left;'>Complemento: </span>";
                echo "<input type='text' name='complement' style='float: right; width: 150px' value='$complement'/><br/>";
                
                echo "<br/><input type='submit' name='send' value='Enviar'/>";
                echo "</form>";
                
                if ($mode === "edit") {
                    echo "<form action='enviarendereco.php' method='post'>";
                    echo "<input type='hidden' name='addrcod' value='$cod'/>";
                    echo "<input type='submit' name='delete' value='Deletar'/>";
                    echo "</form>";
                }
            }
            
            printHeader();
            printMenu();
            
            echo "<div id='body-small'>";
            
            echo "<div style='margin: auto; width: 300px'>";
            printAddressForm();
            
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>

?>