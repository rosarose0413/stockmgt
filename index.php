<?php
include 'connection.php';
mysqli_query($db, "set names 'utf8'");
$sql = "SELECT product.*,stock.* FROM product left join stock on product.product_id = stock.product_id where stock.no_of_stock > 0 group by product.product_id order by stock.stock_id";  
$result = mysqli_query($db, $sql);
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
	<th>Add to Cart</th>
  </tr>
   <?php
    $i = 0;
	while($row = mysqli_fetch_array($result))  
	{  
		
		echo '  
			<tr>  
				<td><input type="hidden" id="stock_id'.$i.'" value="'.$row["stock_id"].'"/><input type="text" id="product_name'.$i.'" value="'.$row["product_name"].'" readonly/></td>  
				<td><input type="text" id="product_stock'.$i.'" value="'.$row["no_of_stock"].'" readonly/></td>
				<td><input type="text" id="product_price'.$i.'" value="'.$row["price"].'" readonly/></td>
				<td>
					<button  id="add'.$i.'" type="button" style="background-color:green">ADD TO CART</button>
				</td>	
			</tr>  
			';  
		$i++;	
	}
	
	 ?>
	 <input type="hidden" id="total_product" value="<?= $i?>"/>

</table>
<h1>Order List</h1>
<form action="order.php" method="POST">
<table>
  <tr>
    <th>Product Name</th>
    <th>Price</th>
	<th>Quantity</th>
  </tr>
  <tbody id="newRow">
  </tbody>
</table>
<br>
<input type="submit" name="button" id="checkout" value="CHECKOUT" style="background-color: #008CBA;; padding:10px;" disabled>
</form>
<script>
	var i = document.getElementById("total_product").value;
	
	for(let a = 0; a < i; a++){
		
		$("#add"+a).click(function(){
			var stock_id = document.getElementById("stock_id"+a).value;
			var product_name = document.getElementById("product_name"+a).value;
			var product_stock = document.getElementById("product_stock"+a).value;
			var product_price = document.getElementById("product_price"+a).value;

				var html = '';
				html += '<tr>';
				html += '<td><input type="hidden" name="product['+ a +']" value="'+stock_id+'"/>'+product_name+'</td>';
				html += '<td>'+product_price+'</td>';
				html += '<td><input type="number" name="quantity['+ a+']" value="" min="1"  placeholder="Please enter quantity" required></td>';
				html += '<tr>';
				$('#newRow').append(html);
               
				$("#add"+a).prop('disabled', true);
				$("#checkout").prop('disabled', false);
			
		});
	}
		
</script>
</body>
</html>