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
        <title>MaxCakes - Enviar Endereço</title>
        <link rel="stylesheet" href="style.css?i=122"/>
    </head>
    <body>
        <?php
            function deleteAddress() {
                $user_cod = $_SESSION["user_cod"]; $cod = 0;
                
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
                
                $sql_query = "delete from addresses where user_cod = ? and cod = ?";                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if (!$sql_stmt) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if (!$sql_stmt->bind_param("ii", $user_cod, $cod)) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                echo "Endereço deletado com sucesso.";
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            function sendAddress() {
                $mode = filter_input(INPUT_POST, "mode");
                if ($mode === null) {
                    echo "Nenhum modo (Editar ou adicionar) válido.";
                    return;
                }
                
                $user_cod = $_SESSION["user_cod"]; $cod = 0; $name = ""; $cep = ""; $neighborhood = ""; $street = ""; $number = 0; $complement = "";
                if ($mode === "edit") {
                    $cod = filter_input(INPUT_POST, "addrcod");
                    if (!$cod) {
                        echo "Erro: Nenhum codigo de endereço recebido.";
                        return;
                    }
                }
                
                $name = filter_input(INPUT_POST, "name");
                if (!$name) {
                    echo "Erro: Nome inválido.";
                    return;
                }
                $cep = filter_input(INPUT_POST, "cep");
                if (!$cep || strlen($cep) > 8) {
                    echo "Erro: CEP inválido.";
                    return;
                }
                $neighborhood = filter_input(INPUT_POST, "neighborhood");
                if (!$neighborhood) {
                    echo "Erro: Bairro inválido.";
                    return;
                }
                $street = filter_input(INPUT_POST, "street");
                if (!$street) {
                    echo "Erro: Rua inválida.";
                    return;
                }
                $number = filter_input(INPUT_POST, "number", FILTER_VALIDATE_INT);
                if (!$number) {
                    echo "Erro: Número inválido.";
                    return;
                }
                $complement = filter_input(INPUT_POST, "complement");
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                if ($mode === "edit") {
                    $sql_pre_query = "";
                    $sql_pre_stmt = null;
                    
                    $sql_pre_query = "delete from addresses where user_cod = ? and cod = ?";
                    $sql_pre_stmt = $sqlconn->prepare($sql_pre_query);
                    if (!$sql_pre_stmt) {
                        echo "Erro ao preparar pré-query.";
                        return;
                    }
                    
                    if (!$sql_pre_stmt->bind_param("ii", $user_cod, $cod)) {
                        echo "Erro ao vincular parametros da pré-query.";
                        return;
                    }
                    
                    if ($sql_pre_stmt->execute() === false) {
                        echo "Erro ao executar pré-query.";
                        return;
                    }
                    
                    $sql_pre_stmt->close();
                }
                $sql_query = "";
                $sql_stmt = null;

                if ($complement) {
                    $sql_query = "insert into addresses (user_cod, name, cep, neighborhood, street, number, complement)"
                                . "values (?, ?, ?, ?, ?, ?, ?)";

                    $sql_stmt = $sqlconn->prepare($sql_query);
                    if (!$sql_stmt) {
                        echo "Erro ao preparar query.";
                        return;
                    }
                    if (!$sql_stmt->bind_param("issssis", $user_cod, $name, $cep, $neighborhood, $street, $number, $complement)) {
                        echo "Erro ao vincular parametros da query.";
                        return;
                    }
                } else {
                    $sql_query = "insert into addresses (user_cod, name, cep, neighborhood, street, number)"
                                . "values (?, ?, ?, ?, ?, ?)";

                    $sql_stmt = $sqlconn->prepare($sql_query);
                    if (!$sql_stmt) {
                        echo "Erro ao preparar query.";
                        return;
                    }
                    if (!$sql_stmt->bind_param("issssi", $user_cod, $name, $cep, $neighborhood, $street, $number)) {
                        echo "Erro ao vincular parametros da query.";
                        return;
                    }
                }

                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }

                if ($mode === "edit") {
                    echo "Endereço editado com sucesso.";
                } else {
                    echo "Endereço adicionado com sucesso.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
                
            echo "<div id='body-small'>";
            
            echo "<div style='margin: auto; width: 300px'>";
            
            if (filter_input(INPUT_POST, "delete") !== null){
                deleteAddress();
            } elseif (filter_input(INPUT_POST, "send") !== null) {
                sendAddress();
            } else {
                echo "Nenhum modo (Editar ou deletar) válido.";
                return;
            }
            
            echo "<br/>";
            echo "<a href='conta.php'><button>Voltar</button></a>";
            
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>