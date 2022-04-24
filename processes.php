<?php 

//----------- reused data ----------------

include_once "./snipets/varriables.php";




// $db->query("DROP TABLE products");
	// echo $db->error."<br>";


$db->query("CREATE TABLE IF NOT EXISTS products
            (
            id int not null primary key auto_increment,
            category varchar(255) not null,
            top varchar(255) not null,
            ModelName varchar(255) not null,
            brand varchar(255) not null,
            title varchar(255) not null,
            price int not null,
            color varchar(25),
            description text(2000) not null,
            photos text(2000) not null,
            favorite int not null
            );");
echo $db->error; 
$count = $db->query("SELECT * FROM products")->num_rows;

if ($count == 0 ){
    $data = fopen("./storedata.csv", "r");

    // store data
    $keys = fgetcsv($data);
    $_SESSION['keys'] = $keys;
    $details = array();



    // ---------- organise data 
    while (!feof($data)) {
        $details[] = fgetcsv($data);
    }
    $_SESSION['details'] = $details;
    $products = array();

    // make an array of arrays that have $keys values as their key
    foreach ($details as $index => $single_product) {
        $detail_index = 0;
        foreach ($keys as $key) {
            if ($key == "photos"){
                $photos = json_decode($single_product[$detail_index],true);
                $products[$index][$key] = $photos;
                $detail_index++;
                continue;
            }
            $products[$index][$key] = $single_product[$detail_index];
            $detail_index++;
        }
    }
    $_SESSION['testcount'] = count($products);
		foreach ($products as $product){
			$id = $db->real_escape_string( $product['id'])+1;
			$category = $db->real_escape_string( $product['category']);
			$top = $db->real_escape_string( $product['top']);
			$ModelName = $db->real_escape_string( $product['ModelName']);
			$title = $db->real_escape_string( $product['title']);
			$brand = $db->real_escape_string( $product['brand']);
			$price = $db->real_escape_string( $product['price']);
			$color = $db->real_escape_string( $product['color']);
			$description = $db->real_escape_string( $product['description']);
			$photos = $db->real_escape_string( json_encode($product['photos']));
			$favorite = $db->real_escape_string( $product['favorite']);
			$insert = "INSERT INTO products(
			category,top,ModelName,
			title,brand,price,color,description,
			photos,favorite)
			VALUES('".$category."', '".$top."', '".$ModelName."', 
			'".$title."', '".$brand."',  ".$price.", '".$color."', 		
			'".$description."','".$photos."', ".$favorite.");
			";
			$db->query($insert);
			
			
		}
}

//------------ first timers ------------------

// -****--------DATA--------------**
// fetch data
if (!isset($_SESSION['products'])) {
    // ----------------database fetch -----------------
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
	// foreach($products as $product){
    //     $cash = $product['photos'];
    //     $product['photos'] = json_decode($cash,true);
    // }	
    

    // ****----SESSION VARRIABLES -----**

    // fill special arrays to keep track of different aspcets freely
    foreach ($products as $id => $product) {
        
        //------- make categories--------
        // get all categories names
        if (empty($categories)) {
            $categories[] = $product['category'];
        } else if (!in_array($product['category'], $categories)) {
            $categories[] = $product['category'];
        }

        // --------- make brands 

        if (empty($brands)) {
            $brands[] = $product['brand'];
        } else if (!in_array($product['brand'], $brands)) {
            $brands[] = $product['brand'];
        }
        asort($categories);
        asort($brands);
        // -------- make prices 
        $prices[$id] = $product['price'];
        sort($prices);
        
    }
    $min = htmlspecialchars($prices[0]);
    $max = htmlspecialchars(end($prices));
    $priceRanges = [[$min, 99],[100,499],[500,999],[1000, $max]];
}















// update the session array with the needed special arrays and vaariables 





// ------------- debugger -------------

// echo "<pre>";
// print_r($show);
// print_r($categories);
// print_r($brands);
// print_r($products);
// print_r($details);
// print_r($_SESSION['products']);
// print_r($_SESSION);


include_once "./snipets/updateSession.php";
// ::TODO : you might have to move this or change the "r" above to save changes
if (isset($data)) {
    fclose($data);
}
