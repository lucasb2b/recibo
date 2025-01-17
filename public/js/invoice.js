$.ajax({
  url: '/api/customers',
  type: 'GET',
  dataType: 'json',
  success: function(data){

    const customers = data.customers;

    $('#customer').empty().append('<option value="">Selecione uma opção</option>');
    $.each(customers, function(index, customer){
      $('#customer').append(`<option value="${customer.id_client}">${customer.customer_name} - ${customer.cpf_cnpj}</option>`);
    });
  },
  error: function(xhr, status, error){
    console.error('Erro ao consumir JSON:', error);
  }
});

$.ajax({
  url: '/api/productsServices',
  type: 'GET',
  dataType: 'json',
  success: function(data){
    const productsServices = data.productsServices;

    $('#product1').empty().append('<option value="">Produto ou serviço</option>');
    $('#product2').empty().append('<option value="">Produto ou serviço</option>');
    $('#product3').empty().append('<option value="">Produto ou serviço</option>');
    $('#product4').empty().append('<option value="">Produto ou serviço</option>');
    $('#product5').empty().append('<option value="">Produto ou serviço</option>');

    $.each(productsServices, function(index, productService){
      $('#product1').append(`<option value="${productService.id_product_service}">${productService.product_service}</option>`);
      $('#product2').append(`<option value="${productService.id_product_service}">${productService.product_service}</option>`);
      $('#product3').append(`<option value="${productService.id_product_service}">${productService.product_service}</option>`);
      $('#product4').append(`<option value="${productService.id_product_service}">${productService.product_service}</option>`);
      $('#product5').append(`<option value="${productService.id_product_service}">${productService.product_service}</option>`);
    });    
  },
  error: function(xhr, status, error){
    console.error('Erro ao consumir JSON:', error);
  }
});