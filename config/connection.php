<?php
$hostname = 'localhost';
$uname = 'root';
$password = '2002';
$db_name = 'crawler';
$conn = mysqli_connect($hostname,$uname,$password,$db_name);
if(!$conn){
    echo "connection failed";
}

?>