<?php 
	$db=mysqli_connect('localhost','root','','stockmgt');
	
	if(mysqli_connect_errno($db))
	{
		echo'Failed to connect';
	}
?>