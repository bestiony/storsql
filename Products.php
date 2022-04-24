<?php session_start();
include_once "./snipets/functions.php";
include_once "./snipets/varriables.php";




// without products array there musy be sth wrong
if (empty($products)) {
    include_once "./snipets/404.php";
    exit;
}
// echo count($_GET);
// if there is no query we must show all the products
if (count($_GET)==0) {
    $thereIsSearch = false; }

if (!$thereIsSearch){
    $_SESSION['show'] = $products;
    $show = $_SESSION['show'];
    $filter = array();
    $_SESSION['currentSqlQuery'] = "";
}






// user filter
// if ther is a get 
// take the first filter 
// compare it to the show array
// add matches to cash
// repeat till end
// make show = cash

// ***** :: FILTER :: *****

// build filter
if (isset($_GET['brand'])) {
    $filter['brand'] = $db->real_escape_string($_GET['brand']);
}
if (isset($_GET['category'])) {
    $filter['category'] = $db->real_escape_string($_GET['category']);
}
$_SESSION['filter'] = $filter;

// use the filter
if (isset($_GET['category']) && !empty($_GET['category'])|| isset($_GET['brand'])&& !empty($_GET['brand']) ) {
    $cash = array();
	
		$optionCategory = isset($_GET['category'])? " category='".$db->real_escape_string($_GET['category'])."'" : "";
		$optionBrand = isset($_GET['brand']) ? " brand='".$db->real_escape_string($_GET['brand'])."'" : "";
		$seperator = count($filter) == 2 ? " AND " : "";
	
		$filterQuery = "SELECT id FROM products WHERE".$optionCategory.$seperator.$optionBrand;
        $_SESSION['currentSqlQuery'] = $filterQuery;
        $get = $db->query($filterQuery);
        echo $db->error;
		$result = array();
		while ($output = $get->fetch_assoc()){
            $cash[$output['id']] = $products[$output['id']];
		}
    
    
    $show = $cash;
}




// search 
if (isset($_GET['searchwords'])) {
    $searchResults = array();
    $thereIsSearch = true ;
    $searchword = $db->real_escape_string($_GET['searchwords']);
    $searchQuery = "SELECT id FROM products WHERE LOCATE('$searchword',description) OR LOCATE('$searchword',title) OR LOCATE('$searchword',ModelName) OR LOCATE('$searchword',brand)";
    $_SESSION['currentSqlQuery'] = $searchQuery ;
    $get = $db->query($searchQuery);
    echo $db->error;
    while ($DATA = $get->fetch_assoc()){
        $searchResults[$DATA['id']]=$products[$DATA['id']];
    }
    $show = $searchResults;
    $search_history[] = $searchword;
    $_SESSION['search_history'] = $search_history;
}
// sort by Normal

if (isset($_GET['sortby'])){
    $sortOption =$db->real_escape_string($_GET['sortby']);
    $previous = strlen($_SESSION['currentSqlQuery']) > 0 ? $_SESSION['currentSqlQuery'] : "SELECT * FROM products";
    $sortQuery = $previous." ORDER BY ".$sortOption;
    $get = $db->query($sortQuery);
    
    while ($DATA = $get->fetch_assoc()){
        $sortedCash[$DATA['id']] = $products[$DATA['id']];
    }
    $show = $sortedCash;
    $_SESSION['show'] = $show;
}
// items per page
if (isset($_GET['items_per_page'])){
    $items_per_page = $db->real_escape_string($_GET['items_per_page']);
    $_SESSION['items_per_page']= $items_per_page;
}

// sort by Reverse

// not needed in this build. replaced with DESC using sql query




// make pages
If (!empty($show)){
    $pages = devide_into_pages($show,$items_per_page);
}

include "./snipets/html_head.php";
?>

<body >
    <!-- ---------------- top button ------------ -->
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

    <?php
    include "./snipets/header_and_cover.php";
    ?>

    <div class="small-container">
        <div class="row row-2">
            <h2>All Products</h2>
            <div class="drop-down-container"> 
            <div class="drop-down">
                <span>items per page</span>
                <select name="sortby" id="sortby" onchange="location = options[this.selectedIndex].value;">
                    <option style="background: #6f40cf;color: #fff;"><?php echo $items_per_page; ?></option>
                    <option value="./Products.php?items_per_page=9&<?php echo http_build_query($_SESSION['filter']) ?>" >9</option>
                    <option value="./Products.php?items_per_page=12&<?php echo http_build_query($_SESSION['filter']) ?>" >12</option>
                    <option value="./Products.php?items_per_page=15&<?php echo http_build_query($_SESSION['filter']) ?>">15</option>
                    <option value="./Products.php?items_per_page=21&<?php echo http_build_query($_SESSION['filter']) ?>">21</option>
                    <option value="./Products.php?items_per_page=30&<?php echo http_build_query($_SESSION['filter']) ?>">30</option>
                </select>
            </div>
            <div class="drop-down">
                <span>Sort by</span>
                <select name="sortby" id="sortby" onchange="location = options[this.selectedIndex].value;">
                    <option style="background: #6f40cf;color: #fff;" ><?php echo $sortOption??"Default"; ?></option>
                    <option value="./Products.php?sortby=category&<?php echo http_build_query($_SESSION['filter']) ?>">category</option>
                    <option value="./Products.php?sortby=title&<?php echo http_build_query($_SESSION['filter']) ?>">title</option>
                    <option value="./Products.php?sortby=price&<?php echo http_build_query($_SESSION['filter']) ?>">price(low to high)</option>
                    <option value="./Products.php?sortby=price DESC&<?php echo http_build_query($_SESSION['filter']) ?>">price(high to low)</option>
                    <option value="./Products.php?sortby=brand&<?php echo http_build_query($_SESSION['filter']) ?>">brand</option>
                    
                </select>
            </div>
            </div>
            <?php 
                if (!empty($show)){
                    make_pages_buttons($pages);
                }
            ?>
            
        </div>
        <div class="row">
            <div id="filterBtn" onclick="showFilter()" class="showfilter">
                <span class="filter">Filter</span>
            </div>
            <div id="filterBar" class="tall-row" onblur="hideFilter()">
                <!-- <h3>CPU Brands</h3>
                <div class="row">
                    <a href=""><i class="fa fa-square-o"></i> intel</a>
                    <a href=""><i class="fa fa-square-o"></i> AMD</a>
                </div>
                <hr> -->
                <h3>Categories</h3>
                <div class="row">
                    <?php

                    foreach ($categories as $name) {
                        $cashFilter = $_SESSION['filter'];
						$compare = isset($_GET['category'])? $db->real_escape_string($_GET['category']) : "";
                        $cashFilter['category'] = $name != $compare ? $name : "";
                        $query = http_build_query($cashFilter);
                        $check = $name == $compare ? "fa-check-square-o" : "fa-square-o";
                        $link =  "Products.php?$query";
                        echo "<a href='$link'><i class='fa $check'></i> " . str_replace("_", " ", $name) . "</a>";
                    }
                    ?>
                </div>
                <hr>
                <h3>Brands</h3>
                <div class="row">
                    <?php
                    foreach ($brands as $name) {
                        $cashFilter = $_SESSION['filter'];
						$compare = isset($_GET['brand'])? $db->real_escape_string($_GET['brand']) : "";
                        $cashFilter['brand'] = $name != $compare ? $name : "";
                        $query = http_build_query($cashFilter);
                        $check = $name == $compare ? "fa-check-square-o" : "fa-square-o";
                        $link =  "Products.php?$query";
                        echo "<a href='$link'><i class='fa $check'></i> $name</a>";
                    }
                    ?>
                </div>
            </div>

            <!-- --------------------  Products -------------------- -->
            <div class="row All-products-row" id="popular_products">

                <?php
                $continue = true;
                if(empty($show)){
                    echo "
                    <dic class='error_message'>
                    <h1>The item you're looking for was not found</h1><br>
                    <a href='./contact.php'><span style='color:#D21F3C;'>Contact us</span> to add it to the list </a>
                    <br>
                    <img src='./images/puzzled.gif'>
                    </div>
                    ";
                    $continue = false;
                    // refresh_page();
                }
                if (isset($_GET['page']) && $continue) {
                    $page = $db->real_escape_string($_GET['page']);
                    
                    foreach ($pages[$page] as $product) {
                        printProduct($product);
                    }
                } else {
                    if($continue){
                        foreach ($pages[0] as $product) {
                            printProduct($product);
                        }

                    }
                }    
                ?>


            </div>

        </div>
        <?php 
            if($continue){
                make_pages_buttons($pages);
            }
        ?>
    </div>





    <!-- ------------------footer------------------  -->
    <?php
    include "./snipets/footer.php";
    ?>

    <!-- ---------------- scripts ---------------- -->
    <script src="./js/toggleMenu.js"></script>
    <script src="./js/blackCover.js"></script>
    <script src="./js/scroll_top_button.js"></script>

    <!-- ----------------- scroll to the same position when reloaded ----------- -->
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var scrollpos = localStorage.getItem('scrollposProducts');
            if (scrollpos) window.scrollTo(0, scrollpos);
            
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollposProducts', window.scrollY);
        };
    </script>
    <!-- ------------ hide and show the filter sidebar in small screen sizes -------------- -->
    <script>
        let filterBar = document.getElementById('filterBar');
        let filterBtn = document.getElementById('filterBtn');
        function showFilter (){
            filterBar.style.transform = "translateX(-25%)";
            setVisible ('visible');
        }

        function hideFilter(){
            filterBar.style.transform = "translateX(-150%)";
            setVisible ('hidden');
            
        }
    </script>

</body>

</html>
<?php include_once "./snipets/updateSession.php"; ?>