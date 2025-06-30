<!-- Transaction History -->
<div class="modal fade" id="transaction">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Detalles de Transacción</b></h4>
            </div>
            <div class="modal-body">
              <p>
                Fecha: <span id="date"></span>
                <span class="pull-right">Transacción#: <span id="transid"></span></span> 
              </p>
              <table class="table table-bordered">
                <thead>
                  <th>Producto</th>
                  <th>Precio</th>
                  <th>Cantidad</th>
                  <th>Duración</th>
                  <th>Subtotal</th>
                </thead>
                <tbody id="detail">
                  <!-- Se llena dinámicamente -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4" align="right"><b>Total</b></td>
                    <td><span id="total"></span></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">
                <i class="fa fa-close"></i> Cerrar
              </button>
            </div>
        </div>
    </div>
</div>
