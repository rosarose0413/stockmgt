<?php
include 'connection.php';
mysqli_query($db, "set names 'utf8'");

date_default_timezone_set('Asia/Manila');
$otp = "";
$length = 6;
$chars = 'ABCDEFGHJKLMNOPRQSTUVWXYZ0123456789';
for ($i = 0; $i < $length; $i++) {
     $otp .= $chars[rand(0, strlen($chars) - 1)];
}
$ref = date("Ymd")."".$otp;

$reference=$ref;
$product= $_POST['product'];
$quantity = $_POST['quantity'];
$number_of_order = count($product);
$sub_total = 0;
$total = 0;
	for($i = 0; $i<$number_of_order; $i++)
	{  
		
		$query_product = "SELECT product.*,stock.* FROM product left join stock on product.product_id = stock.product_id where stock.stock_id='$product[$i]' order by stock.stock_id ASC limit 1";  	
		$result_product = mysqli_query($db, $query_product);
		while($row = mysqli_fetch_array($result_product))  
		{ 
			if($row["no_of_stock"] >= $quantity[$i]){
				$product_id = $row["product_id"];
				
				$new_stock = $row["no_of_stock"] - $quantity[$i];
				$sub_total = $row["price"] * $quantity[$i];
				$insert_order = "INSERT INTO order_product (reference_no, product_id,stock_id, quantity,sub_total, remarks)
				VALUES ('$ref','$product_id','$product[$i]','$quantity[$i]','$sub_total','')";

				if ($db->query($insert_order) === TRUE) {
				  //echo "New record created successfully";
					
					$sql = "UPDATE stock SET no_of_stock='$new_stock' WHERE  stock_id ='$product[$i]'";
					
					if(mysqli_query($db, $sql)){
						
					} else {
						echo "ERROR: Could not able to execute $sql " . mysqli_error($db);
					}
				} else {
				  echo "Error: " . $insert_order . "<br>" . $db->error;
				}
				
			}else{
				$sub_total1 = '0';
				$sub_total2 = '0';
				$new_stock = $row["no_of_stock"];
				$product_id = $row["product_id"];
				$sub_total1 = $row["price"] * $new_stock;
				
				$insert_order = "INSERT INTO order_product (reference_no, product_id,stock_id, quantity,sub_total, remarks)
							VALUES ('$ref ', '$product_id','$product[$i]' ,'$new_stock','$sub_total1','')";
						
				if ($db->query($insert_order) === TRUE) {
					$sql = "UPDATE stock SET no_of_stock='0' WHERE stock_id ='$product[$i]'";
					if(mysqli_query($db, $sql)){
						//start
						$query_pullout = "SELECT product.*,stock.* FROM product left join stock on product.product_id = stock.product_id where product.product_id='$product[$i]'and  stock.no_of_stock > 0 order by stock.stock_id ASC limit 1";  	
						$result_pullout= mysqli_query($db, $query_pullout);
						if ($result_pullout)
						{
						  // Return the number of rows in result set
							if (mysqli_num_rows($result_pullout) > 0){
								
								while($row_pullout = mysqli_fetch_array($result_pullout))  
								{  
									
									
									$id_pullout = $row_pullout['stock_id'];
									
									$to_pullout = $quantity[$i] - $new_stock;
									$new_stock_pullout = $row_pullout["no_of_stock"] - $to_pullout;
									
								
											$sub_total2= $row_pullout["price"] * $to_pullout;
											$insert_order = "INSERT INTO order_product (reference_no, product_id,stock_id, quantity,sub_total, remarks)
											VALUES ('$ref ', '$product_id','$id_pullout' ,'$to_pullout','$sub_total2','')";

											if ($db->query($insert_order) === TRUE) {
												
												$sql = "UPDATE stock SET no_of_stock='$new_stock_pullout' WHERE stock_id ='$id_pullout'";
											;
												if(mysqli_query($db, $sql)){
													
												}
												
											}else{
											}
										
									
									
								}
							}else{
								/*$remarks = "Not enough stock.";
								$product_id = $row["product_id"];
								$new_stock = $row["no_of_stock"];
								$sub_total = $row["price"] * $new_stock;
								$insert_order = "INSERT INTO order_product (reference_no, product_id,stock_id, quantity,sub_total, remarks)
								VALUES ('$ref ','$product_id', '$product[$i]' ,'$new_stock ','$sub_total','$remarks')";

								if ($db->query($insert_order) === TRUE) {
								  //echo "New record created successfully";
									
									$sql = "UPDATE stock SET no_of_stock='0' WHERE  stock_id ='$product[$i]'";
									
									if(mysqli_query($db, $sql)){
										
									} else {
										echo "ERROR: Could not able to execute  " . mysqli_error($db);
									}
								} else {
								  echo "Error: " . $sql . "<br>" . $db->error;
								}
								*/
								
							}
						}
						//end									
					} else {
									echo "ERROR: Could not able to execute $sql " . mysqli_error($db);
					}
				} else {
							  echo "Error: " . $sql . "<br>" . $db->error;
				}
				$sub_total = $sub_total1 + $sub_total2;
			}	
		$total += $sub_total;
		}
		
	}

		$insert_total = "INSERT INTO total_order (reference_no, total)
		VALUES ('$ref ', '$total')";

			if ($db->query($insert_total) === TRUE) {
			  //echo "New record created successfully";
				
			} else {
					  echo "Error: " . $sql . "<br>" . $db->error;
			}
$sql = "SELECT product.*,stock.* FROM product left join stock on product.product_id = stock.product_id where stock.no_of_stock > 0 group by product.product_id order by stock.stock_id";  
$result = mysqli_query($db, $sql);

$sql_order = "SELECT product.* , order_product.*,stock.* FROM order_product left join product on order_product.product_id = product.product_id left join stock on order_product.stock_id = stock.stock_id where order_product.reference_no='$reference' group by stock.stock_id, order_product.order_id";  
$result_order = mysqli_query($db, $sql_order);

$sql_total = "select * from total_order where reference_no='$reference'";
$result_total = mysqli_query($db, $sql_total);
$row_rsmyQuery = mysqli_fetch_assoc($result_total);
$total = $row_rsmyQuery['total'];

?>
<!DOCTYPE html>
<html>
<head>
<title>Stock Management System</title>
<style>
table,th, td{
  border: 1px solid black;
}
</style>
<!-- Script -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
 
<!-- jQuery UI -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css" />
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
 
</head>
<body>

<h1>Product and Price List</h1>
<table>
  <tr>
    <th>Product Name</th>
    <th>Stock</th>
    <th>Price</th>
  </tr>
   <?php
    $i = 0;
	while($row = mysqli_fetch_array($result))  
	{  
		
		echo '  
			<tr>  
				<td><input type="hidden" id="product_id'.$i.'" value="'.$row["product_id"].'"/><input type="text" id="product_name'.$i.'" value="'.$row["product_name"].'" readonly/></td>  
				<td><input type="text" id="product_stock'.$i.'" value="'.$row["no_of_stock"].'" readonly/></td>
				<td><input type="text" id="product_price'.$i.'" value="'.$row["price"].'" readonly/></td>	
			</tr>  
			';  
		$i++;	
	}
	
	 ?>
	 <input type="hidden" id="total_product" value="<?= $i?>"/>

</table>
<h1>Order List</h1>
<p>Reference #: <?= $reference?></p>
<table>
  <tr>
    <th>Product Name</th>
    <th>Price</th>
	<th>Quantity</th>
	<th>Sub total</th>
	<th>Remarks</th>
  </tr>
	<?php
	while($row = mysqli_fetch_array($result_order))  
	{  
		echo '  
			<tr>  
				<td>'.$row["product_name"].'</td>  
				<td>'.$row["price"].'</td>
				<td>'.$row["quantity"].'</td>
				<td>'.$row["sub_total"].'</td>	
				<td>'.$row["remarks"].'</td>				
			</tr>  
			';  
		$i++;	
	}
	?>
</table>
<h3>Total Amount: <?= $total?></h3>
</body>
</html>