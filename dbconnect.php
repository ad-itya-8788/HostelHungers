<?php
$user = 'postgres';
$host = 'localhost';
$password = '1234';
$dbname = 'hotel';
$port = '5432';

try {
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    if (!$conn) 
      {
        throw new Exception("Failed to connect to the database.");
      }

 }
 catch (Exception $e)
 {
    echo "Error: " . $e->getMessage();
  }

?>
