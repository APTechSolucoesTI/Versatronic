<?php

chdir(__DIR__);
require_once 'init.php';
use Adianti\Database\TTransaction;

class Geolocalizacao
{
    public static function store($dados)
    {
        
        try {
            TTransaction::open('minicrm');
        
            $localizacao = new InteracaoLocalizacao;
            $localizacao->interacao_id = $dados['id'];
            $localizacao->descricao = $dados['address'];
            $localizacao->latitude = $dados['latitude'];
            $localizacao->longitude = $dados['longitude'];
            $localizacao->dt_localizacao = date('Y-m-d H:i:s');
            
            $localizacao->store();
            
            TTransaction::close();
            
            echo json_encode(array('error' => false, 'message' => 'Entrou com Sucesso!'));
            return;
        } catch (Exception $e) {
            TTransaction::rollback();
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            return;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $acao = isset($dados['acao']) ? $dados['acao'] : '';

    if ($acao === 'store') {
        Geolocalizacao::store($dados); 
    } else {
        echo json_encode(array('error' => true, 'message' => 'Ação desconhecida'));
    }
} else {
    echo json_encode(array('error' => true, 'message' => 'Método Desconhecido'));
}