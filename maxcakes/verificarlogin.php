<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_cod"])) {
    header("Location: conta.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_OFF);

?>

<html>
    <head>
        <title>MaxCakes - Verificar Login</title>
        <link rel="stylesheet" href="style.css?i=122"/>
    </head>
    <body>
        <?php
            function verifyData() {
                $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    echo "Erro: Email inválido.";
                    return;
                }
                
                $password = filter_input(INPUT_POST, "password");
                if (!$password) {
                    echo "Erro: Senha inválida.";
                    return;
                }
                
                $password_hash = hash("sha256", $password);
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "select cod, email, type from users where email=? and password=?";
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                if ($sql_stmt->bind_param("ss", $email, $password_hash) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                if ($sql_stmt->num_rows === 0) {
                    echo 'Erro: Usuário ou senha inválidos.';
                    //echo "<br/>Email: " . $email . "<br/>Senha: " . $password_hash;
                    return;
                }
                
                $cod = 0; $email = ""; $type = "";
                $sql_stmt->bind_result($cod, $email, $type);
                
                $sql_stmt->fetch();
                $_SESSION["user_cod"] = $cod;
                $_SESSION["user_email"] = $email;
                $_SESSION["user_type"] = $type;
                
                echo "Login realizado com sucesso.";
                
                $sql_stmt->close();
                $sqlconn->close();
                
                //echo "<br/>Cod: " . $_SESSION["user_cod"]
                //    . "<br/>Email: " . $_SESSION["user_email"]
                //    . "<br/>Type: " . $_SESSION["user_type"];
            }
            
            echo "<div id='body-small'>";
            
            verifyData();
            
            echo "<br/>";
            echo "<a href='login.php'><button>Voltar</button></a>";
            
            echo "</div>";
        ?>
    </body>
</html>