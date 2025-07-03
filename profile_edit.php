<?php
	include 'includes/session.php';

	$conn = $pdo->open();

	if(isset($_POST['edit'])){
		// Recoger datos del formulario
		$curr_password = $_POST['curr_password'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$contact = $_POST['contact'];
		$address = $_POST['address'];
		$photo = $_FILES['photo']['name'];

		// Verificar que la contraseña actual sea correcta
		if(password_verify($curr_password, $user['password'])){
			// Si hay una foto, moverla al directorio de imágenes
			if(!empty($photo)){
				move_uploaded_file($_FILES['photo']['tmp_name'], 'images/'.$photo);
				$filename = $photo;	
			}
			else{
				// Si no hay foto nueva, se usa la foto actual del usuario
				$filename = $user['photo'];
			}

			// Si no se ingresó una nueva contraseña, mantenemos la actual
			if(empty($password)){
				$password = $user['password'];
			}
			else{
				// Si hay una nueva contraseña, la encriptamos
				$password = password_hash($password, PASSWORD_DEFAULT);
			}

			// Preparar la consulta para actualizar los datos del usuario
			try{
				$stmt = $conn->prepare("UPDATE users SET email=:email, password=:password, firstname=:firstname, lastname=:lastname, contact_info=:contact, address=:address, photo=:photo WHERE id=:id");
				$stmt->execute([
					'email' => $email, 
					'password' => $password, 
					'firstname' => $firstname, 
					'lastname' => $lastname, 
					'contact' => $contact, 
					'address' => $address, 
					'photo' => $filename, 
					'id' => $user['id']
				]);

				$_SESSION['success'] = 'Cuenta actualizada correctamente';
			}
			catch(PDOException $e){
				$_SESSION['error'] = 'Error al actualizar la cuenta: ' . $e->getMessage();
			}
		}
		else{
			// Si la contraseña actual no es correcta
			$_SESSION['error'] = 'Contraseña incorrecta';
		}
	}
	else{
		// Si no se envió el formulario
		$_SESSION['error'] = 'Rellena el formulario antes de continuar';
	}

	$pdo->close();

	// Redirigir a la página de perfil
	header('location: profile.php');
	exit();
?>
