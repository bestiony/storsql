<?php 
    $servername = "127.0.0.1";
    $username = "test";
    $password = "passowrd";

    // Create connection
    $conn = new mysqli($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: {$conn->connect_error}");
    }
    echo "Connected successfully";
    ?> 

<html>
  <head>
    <title>PHP Test</title>
  </head>
  <body>
    
  </body>
</html>