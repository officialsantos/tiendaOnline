<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Historial de Ventas</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Ventas</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <div class="pull-right">
                <form method="POST" class="form-inline" action="">
                  <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    <input type="text" class="form-control pull-right col-sm-8" id="reservation" name="date_range">
                  </div>
                </form>
              </div>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th class="hidden"></th>
                  <th>Fecha de Venta</th>
                  <th>Nombre del Cliente</th>
                  <th>Transacción#</th>
                  <th>Fecha de Reservación</th>
                  <th>Total a Pagar</th>
                  <th>Detalles Completos</th>
                </thead>
                <tbody>
                  <?php
                    $conn = $pdo->open();

                    try {
                      $stmt = $conn->prepare("
                        SELECT sales.*, users.firstname, users.lastname, reservations.reserved_at, sales.id AS salesid
                        FROM sales
                        LEFT JOIN users ON users.id = sales.user_id
                        LEFT JOIN reservations ON reservations.sales_id = sales.id
                        ORDER BY sales.sales_date DESC
                      ");
                      $stmt->execute();

                      foreach ($stmt as $row) {
                        // Calcular total
                        $stmtDetails = $conn->prepare("SELECT * FROM details WHERE sales_id = :id");
                        $stmtDetails->execute(['id' => $row['salesid']]);
                        $total = 0;
                        foreach ($stmtDetails as $detail) {
                          $total = $detail['price'];
                        }

                        echo "
                          <tr>
                            <td class='hidden'></td>
                            <td>".date('M d, Y', strtotime($row['sales_date']))."</td>
                            <td>".htmlspecialchars($row['firstname'])." ".htmlspecialchars($row['lastname'])."</td>
                            <td>".htmlspecialchars($row['pay_id'])."</td>
                            <td>".($row['reserved_at'] ? date('M d, Y', strtotime($row['reserved_at'])) : 'Sin reservación')."</td>
                            <td>&#36; ".number_format($total, 2)."</td>
                            <td><button type='button' class='btn btn-info btn-sm btn-flat transact' data-id='".$row['salesid']."'><i class='fa fa-search'></i> Ver</button></td>
                          </tr>
                        ";
                      }
                    } catch (PDOException $e) {
                      echo $e->getMessage();
                    }

                    $pdo->close();
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
  <?php include 'includes/footer.php'; ?>
  <?php include '../includes/profile_modal.php'; ?>

</div>
<?php include 'includes/scripts.php'; ?>
<!-- Date Picker -->
<script>
$(function(){
  $('#datepicker_add').datepicker({ autoclose: true, format: 'yyyy-mm-dd' });
  $('#datepicker_edit').datepicker({ autoclose: true, format: 'yyyy-mm-dd' });

  $('.timepicker').timepicker({ showInputs: false });

  $('#reservation').daterangepicker();
  $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' });
  $('#daterange-btn').daterangepicker({
    ranges   : {
      'Today'       : [moment(), moment()],
      'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month'  : [moment().startOf('month'), moment().endOf('month')],
      'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29, 'days'),
    endDate  : moment()
  });
});
</script>
<script>
$(function(){
  $(document).on('click', '.transact', function(e){
    e.preventDefault();
    $('#transaction').modal('show');
    var id = $(this).data('id');
    $.ajax({
      type: 'POST',
      url: 'transact.php',
      data: {id:id},
      dataType: 'json',
      success:function(response){
        $('#date').html(response.date);
        $('#transid').html(response.transaction);
        $('#detail').prepend(response.list);
        $('#total').html(response.total);
      }
    });
  });

  $("#transaction").on("hidden.bs.modal", function () {
      $('.prepend_items').remove();
  });
});
</script>
</body>
</html>
