<?php include 'includes/session.php'; ?>
<?php
	$conn = $pdo->open();

	$slug = $_GET['product'];

	try{
	    $stmt = $conn->prepare("SELECT *, products.name AS prodname, category.name AS catname, category.cat_slug, products.id AS prodid, products.category_id FROM products LEFT JOIN category ON category.id=products.category_id WHERE slug = :slug");
	    $stmt->execute(['slug' => $slug]);
	    $product = $stmt->fetch();
	    $category_id = $product['category_id']; // Guardamos la categoría para JS
	}
	catch(PDOException $e){
		echo "Hubo un problema en la conexión: " . $e->getMessage();
	}

	//page view
	$now = date('Y-m-d');
	if($product['date_view'] == $now){
		$stmt = $conn->prepare("UPDATE products SET counter=counter+1 WHERE id=:id");
		$stmt->execute(['id'=>$product['prodid']]);
	}
	else{
		$stmt = $conn->prepare("UPDATE products SET counter=1, date_view=:now WHERE id=:id");
		$stmt->execute(['id'=>$product['prodid'], 'now'=>$now]);
	}

?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<script>
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.12';
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
<div class="wrapper">

	<?php include 'includes/navbar.php'; ?>
	 
	<div class="content-wrapper">
	    <div class="container">

	      <!-- Main content -->
	      <section class="content">
	        <div class="row">
	        	<div class="col-sm-9">
	        		<div class="callout" id="callout" style="display:none">
	        			<button type="button" class="close"><span aria-hidden="true">&times;</span></button>
	        			<span class="message"></span>
	        		</div>
		            <div class="row">
		            	<div class="col-sm-6">
		            		<!--<img src="<?php echo (!empty($product['photo'])) ? 'images/'.$product['photo'] : 'images/noimage.jpg'; ?>" width="100%" class="zoom" data-magnify-src="images/large-<?php echo $product['photo']; ?>"> -->
		            		<br><br>
							<form class="form-inline" id="productForm">
								<div class="form-group">
									<!-- Personas -->
									<p style="font-weight:bold; font-size:20px;">Cantidad de personas</p>
									<div class="input-group col-sm-6">
										<span class="input-group-btn">
											<button type="button" id="minus-person" class="btn btn-default btn-flat btn-lg"><i class="fa fa-minus"></i></button>
										</span>
										<input type="text" name="quantity" id="quantity" class="form-control input-lg" value="1" readonly>
										<span class="input-group-btn">
											<button type="button" id="add-person" class="btn btn-default btn-flat btn-lg"><i class="fa fa-plus"></i></button>
										</span>
									</div>
									<p id="person-limit-info" style="color: red; font-weight: bold; margin-top:10px;">
										<?php
											if($category_id == 1) echo "Este paquete es individual, solo 1 persona permitida.";
											elseif($category_id == 2) echo "Máximo 7 personas para paquetes familiares.";
											elseif($category_id == 3) echo "Máximo 20 personas para paquetes grupales.";
											else echo "Cantidad máxima permitida: 99 personas.";
										?>
									</p>

									<br><br>

									<!-- Días -->
									<p style="font-weight:bold; font-size:20px;">Cantidad de días</p>
									<div class="input-group col-sm-6">
										<span class="input-group-btn">
											<button type="button" id="minus-day" class="btn btn-default btn-flat btn-lg"><i class="fa fa-minus"></i></button>
										</span>
										<input type="text" name="duration_days" id="duration_days" class="form-control input-lg" value="1" readonly>
										<span class="input-group-btn">
											<button type="button" id="add-day" class="btn btn-default btn-flat btn-lg"><i class="fa fa-plus"></i></button>
										</span>
									</div>

									<input type="hidden" value="<?php echo $product['prodid']; ?>" name="id">

									<br>

									<button type="submit" class="btn btn-primary btn-lg btn-flat"><i class="fa fa-shopping-cart"></i> Añadir al Carrito</button>
								</div>
							</form>
		            	</div>
		            	<div class="col-sm-6">
		            		<h1 class="page-header"><?php echo $product['prodname']; ?></h1>
		            		<h3><b>&#36; <?php echo number_format($product['price'], 2); ?></b></h3>
		            		<p><b>Categoría:</b> <a href="category.php?category=<?php echo $product['cat_slug']; ?>"><?php echo $product['catname']; ?></a></p>
		            		<p><b>Descripción:</b></p>
		            		<p><?php echo $product['description']; ?></p>
		            	</div>
		            </div>
		            <br>
				    <div class="fb-comments" data-href="http://localhost/ecommerce/product.php?product=<?php echo $slug; ?>" data-numposts="10" width="100%"></div> 
	        	</div>
	        	<div class="col-sm-3">
	        		<?php include 'includes/sidebar.php'; ?>
	        	</div>
	        </div>
	      </section>
	     
	    </div>
	  </div>
  	<?php $pdo->close(); ?>
  	<?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
	// Definir máximo según categoría (PHP embebido para pasar el valor)
	let maxPersons = 99; // default

	<?php if($category_id == 1): ?>  // Individual
		maxPersons = 1;
	<?php elseif($category_id == 2): ?> // Familiar
		maxPersons = 7;
	<?php elseif($category_id == 3): ?> // Grupal
		maxPersons = 20;
	<?php endif; ?>

	// Personas - añadir
	$('#add-person').click(function(e){
		e.preventDefault();
		let quantity = parseInt($('#quantity').val());
		if(quantity < maxPersons){
			$('#quantity').val(quantity + 1);
		} else {
			alert('El máximo permitido para este paquete es ' + maxPersons + ' persona(s).');
		}
	});
	// Personas - restar
	$('#minus-person').click(function(e){
		e.preventDefault();
		let quantity = parseInt($('#quantity').val());
		if(quantity > 1){
			$('#quantity').val(quantity - 1);
		}
	});

	// Días - añadir
	$('#add-day').click(function(e){
		e.preventDefault();
		let days = parseInt($('#duration_days').val());
		if(days < 99){
			$('#duration_days').val(days + 1);
		}
	});
	// Días - restar
	$('#minus-day').click(function(e){
		e.preventDefault();
		let days = parseInt($('#duration_days').val());
		if(days > 1){
			$('#duration_days').val(days - 1);
		}
	});

	// Manejar el submit del formulario
	$('#productForm').submit(function(e){
		e.preventDefault();

		const id = $('input[name=id]').val();
		const quantity = $('#quantity').val();
		const duration_days = $('#duration_days').val();

		$.ajax({
			url: 'add_to_cart.php',
			method: 'POST',
			data: { id, quantity, duration_days },
			dataType: 'json',
			success: function(response){
				if(response.error){
					alert('Error: ' + response.message);
				}else{
					alert(response.message);
				}
			},
		});
	});
});
</script>
</body>
</html>
