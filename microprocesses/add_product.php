<?php session_start();
include_once "../snipets/varriables.php";


if (empty($_GET)) {
    echo "empty GET";
    include "./404.php";
    exit;
}
$newProduct = $_GET ?? array();
if (count($newProduct) < 9) {
    echo "missing items";
    include "./404.php";
    exit;
}

foreach ($newProduct as $property => $value) {
    if (empty($value)) {
        echo "$property is empty";
        include "./404.php";
        exit;
    }
}

$id = count($products);
$category = $db->real_escape_string($newProduct['category']);
$top = $db->real_escape_string($newProduct['top']);
$ModelName = $db->real_escape_string($newProduct['ModelName']);
$title = $db->real_escape_string($newProduct['title']);
$brand = $db->real_escape_string($newProduct['brand']);
$price = $db->real_escape_string($newProduct['price']);
$color = $db->real_escape_string($newProduct['color']);
$description = $db->real_escape_string($newProduct['description']);
$photos = $db->real_escape_string(json_encode($newProduct['photos']));
$favorite = 0;
$insert = "INSERT INTO products(
			category,top,ModelName,
			title,brand,price,color,description,
			photos,favorite)
			VALUES('" . $category . "', '" . $top . "', '" . $ModelName . "', 
			'" . $title . "', '" . $brand . "',  " . $price . ", '" . $color . "', 		
			'" . $description . "','" . $photos . "', " . $favorite . ");
			";
$db->query($insert);
$_SESSION['last_record'] = $db->insert_id;
unset($_SESSION['products']);

$products = array();
    $get = $db->query("SELECT * FROM products");
    $count = 0;
    while ($DATA = $get->fetch_assoc()){
        $cash = $DATA['photos'];
        $DATA['photos'] = json_decode($cash, true);
        $products[$DATA['id']] = $DATA;
        $count++;
    }
    $_SESSION['repeatcount'] = $count;






// -----------:: Deprecated ::------------ //
// $products[] = $newProduct;
// $file = fopen("../data/storedata.csv", "w");
// fputcsv($file, $_SESSION['keys']);
// $output = $products;
// foreach ($output as $key => $product) {
//     $output[$key]['photos'] = json_encode($product['photos']);
// }

// foreach ($output as $product) {
//     fputcsv($file, $product);
// }
// fclose($file);

include_once "../snipets/updateSession.php";

header("location:../index.php");
