<?php

include('connection.php');

$stmt = $conn->prepare("SELECT * FROM category LIMIT 6 ");

$stmt-> execute();

$featured_category = $stmt->get_result();

?>