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
        <title>MaxCakes - Cadastro</title>
        <link rel="stylesheet" href="style.css?i=122"/>
    </head>
    <body>
        <?php
            function insertData() {
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
                
                $confirm_password = filter_input(INPUT_POST, "confirm_password");
                if (!$confirm_password) {
                    echo "Erro: Senha inválida.";
                    return;
                }
                
                if ($password !== $confirm_password) {
                    echo "Erro: Campos \"Senha\" e \"Confirmar senha\" estão diferentes.";
                    return;
                }
                
                $password_length = strlen($password);
                if ($password_length < 8 || $password_length > 32) {
                    echo "Erro: Senha deve conter entre 8 e 32 caracteres.";
                    return;
                }
                
                $type = filter_input(INPUT_POST, "type",);
                if (!$type) {
                    echo "Erro: Tipo de conta inválido.";
                    return;
                }
                
                $password_hash = hash("sha256", $password);
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                    echo "Erro ao se conectar com o banco.";
                    return;
                }
                
                $sql_query = "insert into users(email, password, type) values(?, ?, ?)";
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                if ($sql_stmt->bind_param("sss", $email, $password_hash, $type) === false) {
                    echo "Erro ao vincular parametros da query.";
                    return;
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query: ";
                    if ($sql_stmt->errno === 1062) {
                        echo "Email existente.";
                    }
                    return;
                }
                
                echo "Conta criada com sucesso.";
                
                $sql_stmt->close();
                $sqlconn->close();
                
                //echo "<br/>email: $email<br/>Senha: $password<br/>Confirmar senha: $confirm_password<br/>Tipo de conta: $type<br/>";
            }
            
            echo "<div id='body-small'>";
            
            insertData();
            
            echo "<br/>";
            echo "<a href='cadastrar.php'><button>Voltar</button></a>";
            
            echo "</div>";
        ?>
    </body>
</html>