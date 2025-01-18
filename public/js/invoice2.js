/*$(document).ready(function(){
  $('#product1').change(function(){
    let productId = $(this).val();

    if(productId){
      $.ajax({
        url: '/api/productService',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ product_id: productId }),
        success: function(response){
          if(response){
            $('#price1').val(Number(response).toFixed(2));
            subtotalCalc();
          } else {
            alert('Preço não encontrado');
          }
        },
        error: function(xhr, status, error) {
          console.log(error, xhr, status);
        }
      })
    } else {
      
    }
  })
});

$('#qtd1').on('input', function () {
  subtotalCalc();
});

function subtotalCalc(){
  let price = parseFloat($('#price1').val()) || 0;
  let quantity = parseInt($('#qtd1').val()) || 0;
  let subtotal = price * quantity;
  $('#subtotal1').val(subtotal.toFixed(2));
}*/

$(document).ready(function () {
  // Evento para quando o produto for alterado
  $(document).on('change', '.product-select', function () {
    let $row = $(this).closest('.form-group.row');
    let productId = $(this).val();

    if (productId) {
      $.ajax({
        url: '/api/productService',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ product_id: productId }),
        success: function (response) {
          if (response) {
            $row.find('.price-input').val(Number(response).toFixed(2));
            calculateSubtotal($row);
          } else {
            alert('Preço não encontrado');
          }
        },
        error: function (xhr, status, error) {
          console.log(error, xhr, status);
        },
      });
    }
  });

  // Evento para recalcular o subtotal quando a quantidade mudar
  $(document).on('input', '.quantity-input', function () {
    let $row = $(this).closest('.form-group.row');
    calculateSubtotal($row);
  });

  // Função para calcular o subtotal
  function calculateSubtotal($row) {
    let price = parseFloat($row.find('.price-input').val()) || 0;
    let quantity = parseInt($row.find('.quantity-input').val()) || 0;
    let subtotal = price * quantity;
    $row.find('.subtotal-input').val(subtotal.toFixed(2));
  }
});
