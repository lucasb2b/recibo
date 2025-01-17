$(document).ready(function(){
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
}