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
        <title>MaxCakes - Envio de Receita</title>
        <link rel="stylesheet" href="style.css?i=122"/>
    </head>
    <body>
        <?php
            
            function deleteItem() {
                $cod = 0;
                $cod = filter_input(INPUT_POST, "itemcod");
                if (!$cod) {
                    echo "Erro: Nenhum codigo de item recebido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "delete from items where cod = ?";                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if (!$sql_stmt) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if (!$sql_stmt->bind_param("i", $cod)) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                echo "Item deletado com sucesso.";
                
                $sql_stmt->close();
                $sqlconn->close();
            }
            
            function sendItem() {
                $mode = filter_input(INPUT_POST, "mode");
                if ($mode === null) {
                    echo "Nenhum modo (Editar ou adicionar) válido.";
                    return;
                }
                
                $cod = 0; $name = ""; $image = ""; $price = 0.0;
                
                if ($mode === "edit") {
                    $cod = filter_input(INPUT_POST, "itemcod");
                    if (!$cod) {
                        echo "Erro: Nenhum codigo de item recebido.";
                        return;
                    }
                }
                
                $name = filter_input(INPUT_POST, "name");
                if (!$name) {
                    echo "Erro: Nome inválido.";
                    return;
                }
                
                $image = filter_input(INPUT_POST, "image");
                if (!$image) {
                    echo "Erro: imagem inválida.";
                    return;
                }
                
                $price = filter_input(INPUT_POST, "price");
                if (!$price) {
                    echo "Erro: Preço inválido.";
                    return;
                }
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "";
                $sql_stmt = null;
                
                if ($mode === "edit") {
                    $sql_query = "update items set name = ?, image = ?, price = ? where cod = ?";
                
                    $sql_stmt = $sqlconn->prepare($sql_query);
                    if (!$sql_stmt) {
                        echo "Erro ao preparar query.";
                        return;
                    }
                    if (!$sql_stmt->bind_param("ssdi", $name, $image, $price, $cod)) {
                        echo "Erro ao vincular parametros da query.";
                        return;
                    }
                } else {
                    $sql_query = "insert into items(name, image, price) values(?, ?, ?)";
                    
                    $sql_stmt = $sqlconn->prepare($sql_query);
                    if (!$sql_stmt) {
                        echo "Erro ao preparar query.";
                        return;
                    }
                    if (!$sql_stmt->bind_param("ssd", $name, $image, $price)) {
                        echo "Erro ao vincular parametros da query.";
                        return;
                    }
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }

                if ($mode === "edit") {
                    echo "Item editado com sucesso.";
                } else {
                    echo "Item adicionado com sucesso.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            echo "<div id='body-small'>";
            
            echo "<div style='margin: auto; width: 300px'>";
            
            if (filter_input(INPUT_POST, "delete") !== null){
                deleteItem();
            } elseif (filter_input(INPUT_POST, "send") !== null) {
                sendItem();
            } else {
                echo "Nenhum modo (Enviar ou deletar) válido.";
                return;
            }
            
            echo "<br/>";
            echo "<a href='catalogo.php'><button>Voltar</button></a>";
            
            echo "</div>";
            echo "</div>";
        ?>
    </body>
</html>