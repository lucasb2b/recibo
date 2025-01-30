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

  private function generateRandomHash($length = 23) {
    return substr(bin2hex(random_bytes($length)), 0, $length);
  }

  public function create($request, $response)
  {
    $data = $request->getParsedBody();

    // Dividir o JSON em duas partes, uma para os dados do invoice, outra para produtos
    $generalData = $data['generalData'];
    $productsData = $data['productsData'];

    $hash = $this->generateRandomHash(23);

    try {
      $invoice = Invoice::create([
        'company_id_company' => 1,
        'customer_id_client' => $generalData['customer'],
        'total' => $generalData['total'],
        'discount' => $generalData['discount'],
        'payment_type' => $generalData['payment'],
        'observation' => $generalData['obs'],
        'hash' => $hash,
        'qr_code_hash' => $hash,
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

    # Buscar os dados do recibo
    $invoice = Invoice::where('hash', $params['hash'])
      ->with(['company', 'customer', 'items'])
      ->first();


    $pdf = new FPDF('P', 'mm', [48, 297]); // Configurar página para 58mm de largura
    $pdf->SetMargins(0, 2, 0); // Margens
    $pdf->AddPage();

    // Imagem do Cabeçalho
    $imagePath = $this->container->get('upload_directory') . '/' . $invoice->company->image;
    $pdf->Image($imagePath, 14);

    // Cabeçalho
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->Cell(0, 5, utf8_decode($invoice->company->company_name), 0, 1, 'C');
    $pdf->SetFont('Helvetica', '', 8);
    $pdf->Cell(0, 7, "CNPJ: " . $invoice->company->cnpj, 0, 1, 'L');
    $pdf->Cell(0, 0, "CF/DF:" . $invoice->company->ie_cfdf, 0, 1, 'L');
    $pdf->Cell(0, 7, utf8_decode($invoice->company->address) . ", " . utf8_decode($invoice->company->number), 0, 1, 'L');
    $pdf->Cell(0, 0, "CEP: " . $invoice->company->postal_code, 0, 1, 'L');
    $pdf->Cell(0, 7, utf8_decode($invoice->company->district), 0, 1, 'L');
    $pdf->Cell(0, 0, utf8_decode($invoice->company->city) . " - " . $invoice->company->state, 0, 1, 'L');
    $pdf->Cell(0, 7, $invoice->company->phone, 0, 1, 'L');
    $pdf->Ln(3);

    $pdf->SetFont('Helvetica', '', 8);
    $pdf->Cell(0, 0, "Cliente: " . utf8_decode($invoice->customer->customer_name), 0, 1, 'L');
    $pdf->Cell(0, 7, "CPF/CNPJ: " . utf8_decode($invoice->customer->cpf_cnpj), 0, 1, 'L');
    $pdf->Cell(0, 0, "Telefone: " . utf8_decode($invoice->customer->telephone), 0, 1, 'L');
    $pdf->Ln(3);

    $pdf->SetFont('Helvetica', '', 8);
    $pdf->Cell(0, 7, "Data: " . date("d/m/Y H:i:s", strtotime($invoice->datetime)), 0, 1, 'L');
    $pdf->Cell(0, 0, "Tipo de pagamento: " . utf8_decode($invoice->payment_type), 0, 1, 'L');
    $pdf->Ln(2);

    $pdf->SetFont('Courier', 'B', 8);
    $pdf->Cell(0, 7, utf8_decode("-- PRODUTOS E/OU SERVIÇOS --"), 0, 1, 'C');
    $pdf->Ln(1);

    # Produtos e ou serviços

    foreach ($invoice->items as $item) {
      // Descrição dos produtos
      #$pdf->setFont('Courier', 'B', 7);
      #$pdf->Cell(48, 3, utf8_decode("Produto ou Serviço"), 1, 1, 'C');
      $pdf->setFont('Helvetica', '', 7);
      $pdf->Cell(48, 3, utf8_decode($item->item_name), 0, 1, 'C');

      // quantidade, valor unitário, subtotal     
      $pdf->setFont('Helvetica', 'B', 7);
      $pdf->Cell(16, 3, 'QTDE', 0, 0, 'C');
      $pdf->Cell(16, 3, 'V. UNIT', 0, 0, 'C');
      $pdf->Cell(16, 3, 'TOTAL', 0, 1, 'C');

      $pdf->setFont('Helvetica', '', 7);
      $pdf->Cell(16, 3, $item->quantity, 'B', 0, 'C');
      $pdf->Cell(16, 3, number_format($item->unit_price, 2, ',', '.'), 'B', 0, 'C');
      $pdf->Cell(16, 3, number_format($item->subtotal, 2, ',', '.'), 'B', 1, 'C');
      $pdf->Ln(1);
    }

    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->Cell(0, 7, utf8_decode("Observações:"), 0, 1, 'L');
    $pdf->SetFont('Helvetica', '', 8);
    //$pdf->Cell(0, 0, utf8_decode($invoice->observation), 0, 1, 'L');
    $pdf->MultiCell(49.5, 2.5, utf8_decode($invoice->observation), 0, 'L');
    $pdf->Ln(2);

    $pdf->SetFont('Helvetica', 'B', 8);
    $pdf->Cell(0, 7, 'SUBTOTAL: R$ ' . number_format(($invoice->total + $invoice->discount), 2, ',', '.'), 0, 1, 'R');
    $pdf->Cell(0, 0, 'DESCONTOS: R$ ' . number_format(($invoice->discount), 2, ',', '.'), 0, 1, 'R');
    $pdf->Cell(0, 7, 'TOTAL: R$ ' . number_format(($invoice->total), 2, ',', '.'), 0, 1, 'R');
    $pdf->Ln(4);

    // Chave de acesso HASH
    $pdf->SetFont('Courier', '', 7);
    $pdf->Cell(0, 7, 'CHAVE DE ACESSO', 0, 1, 'C');
    $pdf->SetFont('Courier', 'B', 7);
    $pdf->Cell(0, 0, $invoice->hash, 0, 1, 'C');


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
