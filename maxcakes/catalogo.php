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
        <title>MaxCakes - Catálogo</title>
        <link rel="stylesheet" href="style.css?a=22"/>
    </head>
    <body>
        <?php
            $searchtext = null;
        
            function printCatalog() {
                global $searchtext;
                $page = 0;
                
                $searchtext = filter_input(INPUT_POST, "searchtext");
                
                $sqlconn = new mysqli("localhost", "root", "", "maxcakes");
                if ($sqlconn->connect_error) {
                        echo "Erro ao se conectar com o banco.";
                        return;
                }
                
                if (filter_input(INPUT_POST, "back")) {
                    $page = filter_input(INPUT_POST, "page");
                    if ($page != 0) {
                        $page--;
                    }
                } elseif (filter_input(INPUT_POST, "forward")) {
                    $page = filter_input(INPUT_POST, "page");
                    $page += 2;
                    
                    do {
                        $page--;
                        $sql_pre_query = "";
                        
                        if ($searchtext) {
                            $sql_pre_query = "select * from items where name like ? limit 3 offset " . ($page * 3);
                        } else {
                            $sql_pre_query = "select * from items limit 3 offset " . ($page * 3);
                        }
                        
                        $sql_pre_stmt = $sqlconn->prepare($sql_pre_query);
                        if ($sql_pre_stmt === false) {
                            echo "Erro ao preparar pré-query.";
                            return;
                        }
                        
                        if ($searchtext) {
                            $aux = "%" . $searchtext . "%";
                            $sql_pre_stmt->bind_param("s", $aux);
                        }
                        
                        if ($sql_pre_stmt->execute() === false) {
                            echo "Erro ao executar pré-query.";
                            return;
                        }
                        
                        $sql_pre_stmt->store_result();
                    } while ($sql_pre_stmt->num_rows === 0 && $page !== 0);
                }
                
                $item_cod = 0; $item_name = ""; $item_image = ""; $item_price = 0;
                $sql_query = "";
                
                if ($searchtext) {
                    $sql_query = "select cod, name, image, price from items where name like ? limit 3 offset " . ($page * 3);
                } else {
                    $sql_query = "select cod, name, image, price from items limit 3 offset " . ($page * 3);
                }
                
                $sql_stmt = $sqlconn->prepare($sql_query);
                if ($sql_stmt === false) {
                    echo "Erro ao preparar query.";
                    return;
                }
                
                if ($searchtext) {
                    $aux = "%" . $searchtext . "%";
                    $sql_stmt->bind_param("s", $aux);
                }
                
                if ($sql_stmt->execute() === false) {
                    echo "Erro ao executar query.";
                    return;
                }
                
                $sql_stmt->store_result();
                if ($sql_stmt->num_rows !== 0) {
                    $sql_stmt->bind_result($item_cod, $item_name, $item_image, $item_price);
                    
                    echo "<form action='catalogo.php' method='post'>";
                    echo "<input type='hidden' name='page' value='$page'/>";
                    echo "<input type='hidden' name='searchtext' value='$searchtext'/>";
                    echo "<input type='submit' name='back' value='<-' style='float: left;'/>";
                    echo "</form>";
                    
                    while ($sql_stmt->fetch()) {
                        echo "<table class='catalog-item'>";
                        
                        echo "<tr><td>Preço: R$" . number_format($item_price, 2) . "</td></tr>";
                        echo "<tr><td><img src='$item_image' width='120' height='120'></img></td></tr>";
                        echo "<tr><td>$item_name</td></tr>";
                        
                        echo "<tr>";
                        echo "<td>";
                        
                        if ($_SESSION["user_type"] == "client") {
                            echo "<form action='favoritos.php' method='post' style='float: left;'>";
                            echo "<input type='hidden' name='item_cod' value='$item_cod'/>";
                            echo "<input type='submit' name='addtofavorites' value=' ' title='Adicionar aos favoritos'"
                                . " style='background: url(add_to_favorites.png); background-size: contain; width: 32; height: 32;'/>";
                            echo "</form>";

                            echo "<form action='carrinho.php' method='post' style='float: right;'>";
                            echo "<input type='hidden' name='item_cod' value='$item_cod'/>";
                            echo "<input type='submit' name='addtocart' value=' ' title='Adicionar ao carrinho'"
                                . " style='background: url(add_to_cart.png); background-size: contain; width: 32; height: 32;'/>";
                            echo "</form>";
                        } else {
                            echo "<form action='enviarreceita.php' method='post'>";
                            echo "<input type='hidden' name='itemcod' value='$item_cod'/>";
                            echo "<input type='submit' name='edititem' value='Editar' title='Editar receita'></div>";
                            echo "</form>";
                        }
                        
                        echo "</td>";
                        echo "</tr>";
                    
                        echo "</table>";
                    }
                    
                    echo "<form action='catalogo.php' method='post'>";
                    echo "<input type='hidden' name='page' value='$page'/>";
                    echo "<input type='hidden' name='searchtext' value='$searchtext'/>";
                    echo "<input type='submit' name='forward' value='->' style='float: left;'/>";
                    echo "</form>";
                } else {
                    echo "Nenhum item a ser exibido.";
                }
                
                $sql_stmt->close();
                $sqlconn->close();
            }
        
            printHeader();
            printMenu();
            
            echo "<div id='body'>";
            echo "<h1>Catálogo de Receitas</h1>";
            echo "<br/>";
            
            echo "<div style='margin: auto; width: 900px; height: 300px'>";
            
            echo "<form action='catalogo.php' method='post'>";
            echo "<div style='text-align: center;'>";
            echo "<input type='text' name='searchtext' required autofocus/>";
            echo "<input type='submit' name='search' value='Pesquisar'/>";
            echo "</div>";
            echo "</form>";
            
            printCatalog();
            
            if ($_SESSION["user_type"] == "employee") {
                echo "<div style='text-align: center; clear: both;'>";
                
                echo "<form action='enviarreceita.php' method='post'>";
                echo "<input type='submit' name='additem' value='Adicionar Receita' title='Adicionar receita'/>";
                echo "</form>";
                
                echo "</div>";
            }
            
            echo "<br/><br/>";
            echo "</div>";
            echo "</div>";
            
            if ($_SESSION["user_type"] == "client") {
                printLowerMenu();
            }
        ?>
    </body>
</html>