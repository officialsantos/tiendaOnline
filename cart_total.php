<?php
	include 'includes/session.php';

	if(isset($_SESSION['user'])){
		$conn = $pdo->open();

		$stmt = $conn->prepare("SELECT * FROM cart LEFT JOIN products ON products.id = cart.product_id WHERE user_id = :user_id");
		$stmt->execute(['user_id' => $user['id']]);

		$total = 0;
		foreach($stmt as $row){
			// Si 'days' no está definido o es cero, consideramos 1 día mínimo
			$days = isset($row['days']) && $row['days'] > 0 ? $row['days'] : 1;
			$subtotal = $row['price'] * $row['quantity'] * $days;
			$total += $subtotal;
		}

		$pdo->close();

		echo json_encode($total);
	}
?>
