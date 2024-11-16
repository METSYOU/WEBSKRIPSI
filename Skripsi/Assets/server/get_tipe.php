<?php

include('connection.php');

if(
    isset($_GET['machine_id'])){
        $machine_id = $_GET['machine_id'];

        $stmt = $conn->prepare("SELECT * FROM diesel_machine WHERE brand_id = ?");
        $stmt->bind_param("i",$machine_id);

        $stmt-> execute();

        $featured_brand = $stmt->get_result();
    }
?>