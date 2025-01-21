<?php

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\ItemInvoice;
use Exception;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Respect\Validation\Validator as v;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

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

    //return $response->withJson($productsData);

    //die();

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
    // Obter invoice com base no hash
    $hash = $params['hash'];
    $invoice = Invoice::where('hash', $hash)
      ->with(['company', 'customer', 'items'])
      ->first();

    try {
      $printerName = "POS58 Printer";
      $connector = new FilePrintConnector("./ok.txt");
      $printer = new Printer($connector);

      // Cabeçalho do recibo
      $printer->initialize();
      $printer->setJustification(Printer::JUSTIFY_CENTER);
      $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
      $printer->text($this->removerAcentos($invoice->company->company_name));
      $printer->feed();
      $printer->pulse();
      $printer->selectPrintMode();
      $printer->text("CNPJ - " + $invoice->company->cnpj + "\n");
      $printer->text("CF/DF - " + $invoice->company->ie_cfdf + "\n");

    } finally {
      $printer->close();
    }
  }
}
