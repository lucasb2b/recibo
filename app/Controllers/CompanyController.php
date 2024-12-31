<?php

namespace App\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Exception;
use Slim\Http\UploadedFile;
use Respect\Validation\Validator as v;

class CompanyController extends Controller {
  
  public function company($request, $response){

    if ($request->isGet())
      return $this->container->view->render($response, 'company.twig');

    $directory = $this->container->upload_directory;
    $companyLogo = $request->getUploadedFiles()['logo'];
    $files = $request->getUploadedFiles();

    if(!$companyLogo->getError()){
      $filename = $this->moveUploadedFile($directory, $companyLogo);

      $validation = $this->container->validator->validate($request, [
        'company-name' => v::stringType()->notEmpty(),
        'cnpj' => v::stringType()->notEmpty(),
        'ie-cfdf' => v::stringType()->notEmpty(),
        'logradouro' => v::stringType()->notEmpty(),
        'postal-code' => v::stringType()->notEmpty(),
        'numero' => v::stringType()->notEmpty(),
        'district' => v::stringType()->notEmpty(),
        'city' => v::stringType()->notEmpty(),
        'uf' => v::stringType()->notEmpty(),
      ]);

      if(empty($companyLogo)){
        throw new Exception('Invalid Image');
      }

      $uploadedFile = $files['logo'];

      if ($uploadedFile->getClientMediaType() == 'image/jpeg' || $uploadedFile->getClientMediaType() == 'image/png'){
        if($validation->failed()){
          return $response->withRedirect($this->container->router->pathFor('company.index'));
        }

        Company::create([
          'company_name' => $request->getParam('company-name'),
          'cnpj' => $request->getParam('cnpj'),
          'ie_cfdf' => $request->getParam('ie-cfdf'),
          'address' => $request->getParam('logradouro'),
          'postal_code' => $request->getParam('postal-code'),
          'number' => $request->getParam('numero'),
          'district' => $request->getParam('district'),
          'city' => $request->getParam('city'),
          'state' => $request->getParam('uf'),
          'phone' => $request->getParam('phone'),
          'image' => $filename
        ]);
      } else {
        $this->container->flash->addMessage('error', 'Escolha um formato de arquivo válido');
      }

      $this->container->flash->addMessage('success', 'Empresa cadastrada com sucesso!');
    } else {
      $this->container->flash->addMessage('error', 'Houve um erro ao processar a requisição');
    }
    return $response->withRedirect($this->container->router->pathFor('company.index'));
  }

  private function moveUploadedFile($directory, UploadedFile $uploadedFile){
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8));
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    return $filename;
  }
}