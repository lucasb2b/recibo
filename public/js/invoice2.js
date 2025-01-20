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
  $(document).on("change", ".product-select", function () {
    let $row = $(this).closest(".form-group.row");
    let productId = $(this).val();

    if (productId) {
      $.ajax({
        url: "/api/productService",
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ product_id: productId }),
        success: function (response) {
          if (response) {
            $row.find(".price-input").val(Number(response).toFixed(2));
            calculateSubtotal($row);
          } else {
            alert("Preço não encontrado");
          }
        },
        error: function (xhr, status, error) {
          console.log(error, xhr, status);
        },
      });
    }
  });

  // Evento para recalcular o subtotal quando a quantidade mudar
  $(document).on("input", ".quantity-input", function () {
    let $row = $(this).closest(".form-group.row");
    calculateSubtotal($row);
    total();
  });

  // Função para calcular o subtotal
  function calculateSubtotal($row) {
    let price = parseFloat($row.find(".price-input").val()) || 0;
    let quantity = parseInt($row.find(".quantity-input").val()) || 0;
    let subtotal = price * quantity;
    $row.find(".subtotal-input").val(subtotal.toFixed(2));
  }

  function total() {
    let sub1 = parseFloat(document.getElementById("subtotal1").value) || 0;
    let sub2 = parseFloat(document.getElementById("subtotal2").value) || 0;
    let sub3 = parseFloat(document.getElementById("subtotal3").value) || 0;
    let sub4 = parseFloat(document.getElementById("subtotal4").value) || 0;
    let sub5 = parseFloat(document.getElementById("subtotal5").value) || 0;

    let discount = parseFloat(document.getElementById("discount").value) || 0;

    let sum = (sub1 + sub2 + sub3 + sub4 + sub5) - discount;
    document.getElementById("total").value = sum;
  }


  // Adicionando eventos para os campos de subtotal também
  ["subtotal1", "subtotal2", "subtotal3", "subtotal4", "subtotal5", "discount"].forEach(
    (id) => {
      document.getElementById(id).addEventListener("input", total);
    }
  );
});

$(document).ready(function(){
  // Função para coletar os dados do formulário
  function getFormData(){
    // Dados gerais
    const generalData = {
      customer: $('#customer').val(),
      payment: $('#payment').val(),
      obs: $('#obs').val(),
      discount: parseFloat($('#discount').val()) || 0,
      total: parseFloat($('#total').val()) || 0
    };

    //Função para verificar se o produto tem dados válidos
    function isValidProduct(productId, quantityId, priceId, subtotalId) {
      const product = $(`#${productId}`).val();
      const quantity = parseFloat($(`#${quantityId}`).val()) || 0;
      const price = parseFloat($(`#${priceId}`).val()) || 0;
      const subtotal = parseFloat($(`#${subtotalId}`).val()) || 0;
      return product && quantity > 0 && price > 0 && subtotal > 0;
    }

    // Dados dos produtos (só incluir se forem válidos)
    const productsData = [];

    if (isValidProduct('product1', 'qtd1', 'price1', 'subtotal1')) {
      productsData.push({
        product: $('#product1').val(),
        productName: $('#product1').find(':selected').data('name'),
        productUnits: $('#product1').find(':selected').data('units'),
        quantity: parseFloat($('#qtd1').val()) || 0,
        price: parseFloat($('#price1').val()) || 0,
        subtotal: parseFloat($('#subtotal1').val()) || 0
      });
    }

    if (isValidProduct('product2', 'qtd2', 'price2', 'subtotal2')) {
      productsData.push({
        product: $('#product2').val(),
        productName: $('#product2').find(':selected').data('name'),
        productUnits: $('#product2').find(':selected').data('units'),
        quantity: parseFloat($('#qtd2').val()) || 0,
        price: parseFloat($('#price2').val()) || 0,
        subtotal: parseFloat($('#subtotal2').val()) || 0
      });
    }

    if (isValidProduct('product3', 'qtd3', 'price3', 'subtotal3')) {
      productsData.push({
        product: $('#product3').val(),
        productName: $('#product3').find(':selected').data('name'),
        productUnits: $('#product3').find(':selected').data('units'),
        quantity: parseFloat($('#qtd3').val()) || 0,
        price: parseFloat($('#price3').val()) || 0,
        subtotal: parseFloat($('#subtotal3').val()) || 0
      });
    }

    if (isValidProduct('product4', 'qtd4', 'price4', 'subtotal4')) {
      productsData.push({
        product: $('#product4').val(),
        productName: $('#product4').find(':selected').data('name'),
        productUnits: $('#product4').find(':selected').data('units'),
        quantity: parseFloat($('#qtd4').val()) || 0,
        price: parseFloat($('#price4').val()) || 0,
        subtotal: parseFloat($('#subtotal4').val()) || 0
      });
    }

    if (isValidProduct('product5', 'qtd5', 'price5', 'subtotal5')) {
      productsData.push({
        product: $('#product5').val(),
        productName: $('#product5').find(':selected').data('name'),
        productUnits: $('#product5').find(':selected').data('units'),
        quantity: parseFloat($('#qtd5').val()) || 0,
        price: parseFloat($('#price5').val()) || 0,
        subtotal: parseFloat($('#subtotal5').val()) || 0
      });
    }

    return {
      generalData: generalData,
      productsData: productsData
    };
  }

  // Enviar os dados via AJAX quando o formulário for submetido
  $('#invoice-form').on('submit', function(event){
    event.preventDefault();

    const { generalData, productsData } = getFormData();

    // Enviar os dados via AJAX para a rota do servidor
    // console.log(generalData);
    // console.log(productsData);

    /*let data = JSON.stringify({
      generalData: generalData,
      productsData: productsData
    });

    console.log(data);*/
    

    $.ajax({
      url: '/invoice/create',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({
        generalData: generalData,
        productsData: productsData
      }),
      success: function(response) {
        alert("Recibo emitido com sucesso!");
        window.location.href = "/";
      },
      error: function(xhr, status, error){
        console.log('Erro ao enviar os dados: ', error);
      }
    });
  });
});
