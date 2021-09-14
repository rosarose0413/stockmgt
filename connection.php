<?php 
	$db=mysqli_connect('localhost','root','','stockmgt');
	
	if(!$db){
            die('Connect Error: ' . mysqli_connect_errno());
        }
?>
