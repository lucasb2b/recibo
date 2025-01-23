<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\ItemInvoice;
use FPDF;

class InvoiceController extends Controller
{

  public function invoice($request, $response)
  {
    if ($request->isGet())
      return $this->container->view->render($response, 'invoice.twig');
  }

  public function create($request, $response)
  {
    $data = $request->getParsedBody();

    // Dividir o JSON em duas partes, uma para os dados do invoice, outra para produtos
    $generalData = $data['generalData'];
    $productsData = $data['productsData'];

    try {
      $invoice = Invoice::create([
        'company_id_company' => 1,
        'customer_id_client' => $generalData['customer'],
        'total' => $generalData['total'],
        'discount' => $generalData['discount'],
        'payment_type' => $generalData['payment'],
        'observation' => $generalData['obs'],
        'hash' => 'g5s4g5s4g5s4g6sh4er654h',
        'qr_code_hash' => 'g5s4g5s4g5s4g6sh4er654h',
        'datetime' => date('Y-m-d H:i:s')
      ]);

      foreach ($productsData as $product) {
        ItemInvoice::create([
          'item_name' => $product['productName'],
          'quantity' => $product['quantity'],
          'unit_price' => $product['price'],
          'unit_type' => $product['productUnits'],
          'subtotal' => $product['subtotal'],
          'invoice_id_invoice' => Invoice::latest('id_invoice')->value('id_invoice')
        ]);
      }

      $this->container->flash->addMessage('success', 'Recibo criado com sucesso!');
    } catch (\Exception $e) {
      $this->container->flash->addMessage('error', 'Erro ao emitir recibo!');
    }

    return $response->withRedirect($this->container->router->pathFor('home'));
  }

  function removerAcentos($string)
  {
    $acentos = [
      'á' => 'a',
      'à' => 'a',
      'ã' => 'a',
      'â' => 'a',
      'ä' => 'a',
      'é' => 'e',
      'è' => 'e',
      'ê' => 'e',
      'ë' => 'e',
      'í' => 'i',
      'ì' => 'i',
      'î' => 'i',
      'ï' => 'i',
      'ó' => 'o',
      'ò' => 'o',
      'õ' => 'o',
      'ô' => 'o',
      'ö' => 'o',
      'ú' => 'u',
      'ù' => 'u',
      'û' => 'u',
      'ü' => 'u',
      'ç' => 'c',
      'ñ' => 'n',
    ];
    return strtr($string, $acentos);
  }

  public function print($request, $response, $params)
  {
    $pdf = new FPDF('P', 'mm', [48, 297]); // Configurar página para 58mm de largura
    $pdf->SetMargins(2, 2, 2); // Margens
    $pdf->AddPage();

    // Cabeçalho
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 5, 'Minha Loja', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Rua Exemplo, 123', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Telefone: (99) 9999-9999', 0, 1, 'C');
    $pdf->Ln(5);

    // Itens
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(40, 5, 'Produto', 1, 0, 'L');
    $pdf->Cell(10, 5, 'Qtd', 1, 0, 'C');
    $pdf->Cell(15, 5, 'Total', 1, 1, 'R');

    $itens = [
      ['Produto A', 2, '10.00'],
      ['Produto B', 1, '5.00']
    ];

    foreach ($itens as $item) {
      $pdf->Cell(40, 5, $item[0], 1, 0, 'L');
      $pdf->Cell(10, 5, $item[1], 1, 0, 'C');
      $pdf->Cell(15, 5, $item[2], 1, 1, 'R');
    }

    // Total
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 5, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(15, 5, '15.00', 0, 1, 'R');

    // Mensagem final
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5, 'Obrigado pela preferencia!', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Volte sempre!', 0, 1, 'C');

    // Saída
    $pdf->Output();
    exit;
  }
}
