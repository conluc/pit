<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function printHeader() {
    echo "<div id='header'>";
    echo "Cafeteria MaxCakes";
    echo "</div>";
    
}
function printMenu() {
    if (!isset($_SESSION["user_cod"])) {
        echo "<div id='menu-no-account'>";
        echo "<ul>";
        
        echo "<li><a href='cadastrar.php'>Cadastrar</a></li>";
        echo "<li><a href='login.php'>Login</a></li>";
        
        echo "</ul>";
        echo "</div>";
    } elseif ($_SESSION["user_type"] == "client") {
        echo "<div id='menu-client'>";
        echo "<ul>";
        
        echo "<li><a href='catalogo.php'>Catálogo de Receitas</a></li>";
        echo "<li><a href='carrinho.php'>Carrinho de Compras</a></li>";
        echo "<li><a href='favoritos.php'>Favoritos</a></li>";
        echo "<li><a href='conta.php'>Minha Conta</a></li>";
        
        echo "</ul>";
        echo "</div>";
    } elseif ($_SESSION["user_type"] == "employee") {
        echo "<div id='menu-employee'>";
        echo "<ul>";
        
        echo "<li><a href='catalogo.php'>Catálogo de Receitas</a></li>";
        echo "<li><a href='pedidos.php'>Lista de Pedidos</a></li>";
        echo "<li><a href='conta.php'>Minha Conta</a></li>";
        
        echo "</ul>";
        echo "</div>";
    }
}

function printLowerMenu() {
    echo "<div id='lower-menu'>";
    echo "<ul>";

    echo "<li><a href='reclamacoes.php'>Fazer Reclamações</a></li>";
    echo "<li><a href='depoimentos.php'>Depoimentos de Satisfação</a></li>";

    echo "</ul>";
    echo "</div>";
}

?>