<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

	<?php include 'includes/navbar.php'; ?>
	 
	<div class="content-wrapper">
		<div class="container">

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-sm-9">
						<h1 class="page-header">TU CARRITO</h1>
						<div class="box box-solid">
							<div class="box-body">
								<table class="table table-bordered">
									<thead>
										<th></th>
										

										<th>Nombre</th>
										<th>Precio</th>
										<th width="18%">Personas</th>
										<th width="18%">Días</th>
										<th>Subtotal</th>
									</thead>
									<tbody id="tbody">
									</tbody>
								</table>
							</div>
						</div>
						<?php
						if(isset($_SESSION['user'])){
							echo "
								<div id='paypal-button'></div>
								<br>
								<form method='POST' action='reserve_cart.php'>
									<button type='submit' class='btn btn-warning btn-lg btn-block'>Reservar Paquetes</button>
								</form>
							";
						}
						else{
							echo "
								<h4>Necesitas <a href='login.php'>Iniciar Sesión</a> para comprar.</h4>
							";
						}
						?>
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
var total = 0;
$(function(){

	// Personas menos
	$(document).on('click', '.minus', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var qty = parseInt($('#qty_'+id).val());
		if(qty>1){
			qty--;
		}
		$('#qty_'+id).val(qty);
		var days = parseInt($('#days_'+id).val());
		updateCart(id, qty, days);
	});

	// Personas más
	$(document).on('click', '.add', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var qty = parseInt($('#qty_'+id).val());
		qty++;
		$('#qty_'+id).val(qty);
		var days = parseInt($('#days_'+id).val());
		updateCart(id, qty, days);
	});

	// Días menos
	$(document).on('click', '.minus-days', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var days = parseInt($('#days_'+id).val());
		if(days>1){
			days--;
		}
		$('#days_'+id).val(days);
		var qty = parseInt($('#qty_'+id).val());
		updateCart(id, qty, days);
	});

	// Días más
	$(document).on('click', '.add-days', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		var days = parseInt($('#days_'+id).val());
		days++;
		$('#days_'+id).val(days);
		var qty = parseInt($('#qty_'+id).val());
		updateCart(id, qty, days);
	});

	function updateCart(id, qty, days){
		$.ajax({
			type: 'POST',
			url: 'cart_update.php',
			data: {
				id: id,
				qty: qty,
				days: days
			},
			dataType: 'json',
			success: function(response){
				if(!response.error){
					getDetails();
					getCart();
					getTotal();
				}
			}
		});
	}

	$(document).on('click', '.cart_delete', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		$.ajax({
			type: 'POST',
			url: 'cart_delete.php',
			data: {id:id},
			dataType: 'json',
			success: function(response){
				if(!response.error){
					getDetails();
					getCart();
					getTotal();
				}
			}
		});
	});

	getDetails();
	getTotal();

});

function getDetails(){
	$.ajax({
		type: 'POST',
		url: 'cart_details.php',
		dataType: 'json',
		success: function(response){
			$('#tbody').html(response);
			getCart();
		}
	});
}

function getTotal(){
	$.ajax({
		type: 'POST',
		url: 'cart_total.php',
		dataType: 'json',
		success:function(response){
			total = response;
		}
	});
}
</script>
<!-- Paypal Express -->
<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: total.toString(),
                    currency_code: 'USD'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            window.location = 'sales.php?pay=' + data.orderID;
        });
    }
}).render('#paypal-button');
</script>

</body>
</html>
