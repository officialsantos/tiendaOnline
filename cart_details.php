<?php
	include 'includes/session.php';
	$conn = $pdo->open();

	$output = '';

	if(isset($_SESSION['user'])){
		if(isset($_SESSION['cart'])){
			foreach($_SESSION['cart'] as $row){
				// Al insertar o actualizar, ahora también guardamos days
				$stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM cart WHERE user_id=:user_id AND product_id=:product_id");
				$stmt->execute(['user_id'=>$user['id'], 'product_id'=>$row['productid']]);
				$crow = $stmt->fetch();
				$days = isset($row['days']) ? $row['days'] : 1; // por defecto 1 día
				if($crow['numrows'] < 1){
					$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, days) VALUES (:user_id, :product_id, :quantity, :days)");
					$stmt->execute([
						'user_id'=>$user['id'],
						'product_id'=>$row['productid'],
						'quantity'=>$row['quantity'],
						'days'=>$days
					]);
				}
				else{
					$stmt = $conn->prepare("UPDATE cart SET quantity=:quantity, days=:days WHERE user_id=:user_id AND product_id=:product_id");
					$stmt->execute([
						'quantity'=>$row['quantity'],
						'days'=>$days,
						'user_id'=>$user['id'],
						'product_id'=>$row['productid']
					]);
				}
			}
			unset($_SESSION['cart']);
		}

		try{
			$total = 0;
			$stmt = $conn->prepare("SELECT *, cart.id AS cartid FROM cart LEFT JOIN products ON products.id=cart.product_id WHERE user_id=:user");
			$stmt->execute(['user'=>$user['id']]);
			foreach($stmt as $row){
				$image = (!empty($row['photo'])) ? 'images/'.$row['photo'] : 'images/noimage.jpg';
				$quantity = $row['quantity'];
				$days = isset($row['days']) ? $row['days'] : 1;
				$subtotal = $row['price'] * $quantity * $days;
				$total += $subtotal;
				$output .= "
					<tr>
						<td><button type='button' data-id='".$row['cartid']."' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
						<td><img src='".$image."' width='30px' height='30px'></td>
						<td>".$row['name']."</td>
						<td>&#36; ".number_format($row['price'], 2)."</td>

						<td class='input-group'>
							<span class='input-group-btn'>
								<button type='button' id='minus' class='btn btn-default btn-flat minus' data-id='".$row['cartid']."'><i class='fa fa-minus'></i></button>
							</span>
							<input type='text' class='form-control' value='".$quantity."' id='qty_".$row['cartid']."'>
							<span class='input-group-btn'>
								<button type='button' id='add' class='btn btn-default btn-flat add' data-id='".$row['cartid']."'><i class='fa fa-plus'></i></button>
							</span>
						</td>

						<td class='input-group'>
							<span class='input-group-btn'>
								<button type='button' id='minus-days' class='btn btn-default btn-flat minus-days' data-id='".$row['cartid']."'><i class='fa fa-minus'></i></button>
							</span>
							<input type='text' class='form-control' value='".$days."' id='days_".$row['cartid']."'>
							<span class='input-group-btn'>
								<button type='button' id='add-days' class='btn btn-default btn-flat add-days' data-id='".$row['cartid']."'><i class='fa fa-plus'></i></button>
							</span>
						</td>

						<td>&#36; ".number_format($subtotal, 2)."</td>
					</tr>
				";
			}
			$output .= "
				<tr>
					<td colspan='6' align='right'><b>Total</b></td>
					<td><b>&#36; ".number_format($total, 2)."</b></td>
				<tr>
			";

		}
		catch(PDOException $e){
			$output .= $e->getMessage();
		}

	}
	else{
		if(count($_SESSION['cart']) != 0){
			$total = 0;
			foreach($_SESSION['cart'] as $row){
				$stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname FROM products LEFT JOIN category ON category.id=products.category_id WHERE products.id=:id");
				$stmt->execute(['id'=>$row['productid']]);
				$product = $stmt->fetch();
				$image = (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg';
				$quantity = $row['quantity'];
				$days = isset($row['days']) ? $row['days'] : 1;
				$subtotal = $product['price'] * $quantity * $days;
				$total += $subtotal;
				$output .= "
					<tr>
						<td><button type='button' data-id='".$row['productid']."' class='btn btn-danger btn-flat cart_delete'><i class='fa fa-remove'></i></button></td>
						<td><img src='".$image."' width='30px' height='30px'></td>
						<td>".$product['prodname']."</td>
						<td>&#36; ".number_format($product['price'], 2)."</td>

						<td class='input-group'>
							<span class='input-group-btn'>
								<button type='button' id='minus' class='btn btn-default btn-flat minus' data-id='".$row['productid']."'><i class='fa fa-minus'></i></button>
							</span>
							<input type='text' class='form-control' value='".$quantity."' id='qty_".$row['productid']."'>
							<span class='input-group-btn'>
								<button type='button' id='add' class='btn btn-default btn-flat add' data-id='".$row['productid']."'><i class='fa fa-plus'></i></button>
							</span>
						</td>

						<td class='input-group'>
							<span class='input-group-btn'>
								<button type='button' id='minus-days' class='btn btn-default btn-flat minus-days' data-id='".$row['productid']."'><i class='fa fa-minus'></i></button>
							</span>
							<input type='text' class='form-control' value='".$days."' id='days_".$row['productid']."'>
							<span class='input-group-btn'>
								<button type='button' id='add-days' class='btn btn-default btn-flat add-days' data-id='".$row['productid']."'><i class='fa fa-plus'></i></button>
							</span>
						</td>

						<td>&#36; ".number_format($subtotal, 2)."</td>
					</tr>
				";
				
			}

			$output .= "
				<tr>
					<td colspan='6' align='right'><b>Total</b></td>
					<td><b>&#36; ".number_format($total, 2)."</b></td>
				<tr>
			";
		}

		else{
			$output .= "
				<tr>
					<td colspan='7' align='center'>Carrito vacío</td>
				<tr>
			";
		}
		
	}

	$pdo->close();
	echo json_encode($output);
?>
