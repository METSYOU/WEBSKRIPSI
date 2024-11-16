<?php

include('connection.php');

if(
    isset($_GET['brand_id'])){
        $brand_id = $_GET['brand_id'];

        $stmt = $conn->prepare("SELECT * FROM brand WHERE brand_id = ?");
        $stmt->bind_param("i",$brand_id);

        $stmt-> execute();

        $featured_brand = $stmt->get_result();
    }
?>