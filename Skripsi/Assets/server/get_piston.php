<?php

include('connection.php');

if(
    isset($_GET['category_id'])){
        $category_id = $_GET['category_id'];

        $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->bind_param("i",$category_id);

        $stmt-> execute();

        $featured_products = $stmt->get_result();
    }
?>