<?php
	include 'includes/session.php';

	$conn = $pdo->open();

	$output = array('error'=>false);

	$id = $_POST['id'];
	$qty = $_POST['qty'];
	$days = isset($_POST['days']) ? $_POST['days'] : null;

	if(isset($_SESSION['user'])){
		try{
			if ($days !== null) {
				// Actualizamos cantidad y días
				$stmt = $conn->prepare("UPDATE cart SET quantity=:quantity, days=:days WHERE id=:id");
				$stmt->execute(['quantity'=>$qty, 'days'=>$days, 'id'=>$id]);
			} else {
				// Solo actualizamos cantidad
				$stmt = $conn->prepare("UPDATE cart SET quantity=:quantity WHERE id=:id");
				$stmt->execute(['quantity'=>$qty, 'id'=>$id]);
			}
			$output['message'] = 'Actualizado';
		}
		catch(PDOException $e){
			$output['error'] = true;
			$output['message'] = $e->getMessage();
		}
	}
	else{
		foreach($_SESSION['cart'] as $key => $row){
			if($row['productid'] == $id){
				$_SESSION['cart'][$key]['quantity'] = $qty;
				if ($days !== null) {
					$_SESSION['cart'][$key]['days'] = $days;
				}
				$output['message'] = 'Actualizado';
			}
		}
	}

	$pdo->close();
	echo json_encode($output);

?>
