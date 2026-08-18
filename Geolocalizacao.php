<?php

chdir(__DIR__);
require_once 'init.php';
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;

class Geolocalizacao
{

    public static function onSalvarLocalizacao($param = null)
    {    
        try {
            $user_id = TSession::getValue("userid");

            // if ($user_id != 18) {
            //     throw new Exception('Acesso não permitido');
            // }
            
            header('Content-Type: application/json');

            $input = file_get_contents('php://input');
            $dados = json_decode($input, true);

            if (empty($dados['latitude']) || empty($dados['longitude'])) {
                throw new Exception('Dados de localização inválidos');
            }

            echo json_encode([
                'status' => 'ok',
                'latitude' => $dados['latitude'],
                'longitude' => $dados['longitude']
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }  

    public static function store($dados)
    {
        try {
            TTransaction::open('minicrm');

            $localizacao = new InteracaoLocalizacao;
            $localizacao->interacao_id = $dados['id'];
            $localizacao->interacao_atividade = $dados['interacao_atividade'] ?? null;
            $localizacao->descricao = $dados['address'];
            $localizacao->latitude = $dados['latitude'];
            $localizacao->longitude = $dados['longitude'];
            $localizacao->dt_localizacao = date('Y-m-d H:i:s');
            $localizacao->store();

            TTransaction::close();

            echo json_encode(['error' => false, 'message' => 'Entrou com Sucesso!']);
            return;
        } catch (Exception $e) {
            TTransaction::rollback();
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            return;
        }
    }

   public static function deleteAtividade($dados)
    {
        try {

            TTransaction::open('minicrm');

            $interacaoId = $dados['interacao_id'] ?? null;
            $interacaoAtividade    = $dados['interacao_atividade'] ?? null;
            $historicoAtividadeId  = $dados['historico_atividade_id'] ?? null;
            $interacaoArquivoIds   = $dados['interacao_arquivo_ids'] ?? [];
            $historicoArquivoIds   = $dados['historico_arquivo_ids'] ?? [];
            $historicoEtapaId      = $dados['historico_etapa_id'] ?? null;

            if (!is_array($interacaoArquivoIds)) {
                $interacaoArquivoIds = [];
            }

            if (!is_array($historicoArquivoIds)) {
                $historicoArquivoIds = [];
            }

            if (empty($interacaoAtividade)) {
                throw new Exception('Interação atividade não informada.');
            }

            $atividade = InteracaoAtividade::find($interacaoAtividade);
            $intt = Interacao::find($interacaoId);

            if (!$atividade) {
                throw new Exception('Atividade não encontrada.');
            }

            if ((int) $atividade->tipo_atividade_id == 5) {
                TTransaction::close();

                echo json_encode([
                    'error' => false,
                    'message' => 'E-mail salvo com sucesso.'
                ]);
                return;
            }

            $localizacoes = InteracaoLocalizacao::where('interacao_atividade', '=', $interacaoAtividade)->load();
            if ($localizacoes) {
                foreach ($localizacoes as $localizacao) {
                    $localizacao->delete();
                }
            }

            if (!empty($historicoArquivoIds)) {
                foreach ($historicoArquivoIds as $historicoArquivoId) {
                    if (!empty($historicoArquivoId)) {
                        $historicoArquivo = InteracaoHistoricoArquivo::find($historicoArquivoId);
                        if ($historicoArquivo) {
                            $historicoArquivo->delete();
                        }
                    }
                }
            }

            if (!empty($interacaoArquivoIds)) {
                foreach ($interacaoArquivoIds as $interacaoArquivoId) {
                    if (!empty($interacaoArquivoId)) {
                        $interacaoArquivo = InteracaoArquivo::find($interacaoArquivoId);
                        if ($interacaoArquivo) {

                            $caminhoArquivo = trim((string) $interacaoArquivo->conteudo_arquivo);
                            if (!empty($caminhoArquivo) && is_file($caminhoArquivo)) {
                                @unlink($caminhoArquivo);
                            }

                            $interacaoArquivo->delete();
                        }
                    }
                }
            }

            if (!empty($historicoAtividadeId)) {
                $historicoAtividade = InteracaoHistoricoAtividade::find($historicoAtividadeId);
                if ($historicoAtividade) {
                    $historicoAtividade->delete();
                }
            }

           $atividade = InteracaoAtividade::find($interacaoAtividade);
           
            if ($atividade) {
                $atividade->delete();
            }

            $interacao = null;
            if (!empty($interacaoId)) {
                $interacao = Interacao::find($interacaoId);
            }

            if ($interacao && (int) $interacao->tipo_interacao_id === 2) {

                $historicosEtapa = InteracaoHistoricoEtapa::where('interacao_id', '=', $interacaoId)->load();
                if ($historicosEtapa) {
                    foreach ($historicosEtapa as $historicoEtapa) {
                        $historicoEtapa->delete();
                    }
                }

                $interacao->delete();
            } else {
                if (!empty($historicoEtapaId)) {
                    $historicoEtapa = InteracaoHistoricoEtapa::find($historicoEtapaId);
                    if ($historicoEtapa) {
                        $historicoEtapa->delete();
                    }
                }
            }
            TTransaction::close();

            echo json_encode([
                'error' => false,
                'message' => 'Atividade e vínculos removidos com sucesso'
            ]);
            return;
        } catch (Exception $e) {
            TTransaction::rollback();
            echo json_encode([
                'error' => true,
                'message' => $e->getMessage()
            ]);
            return;
        }
    }
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $dados = json_decode(file_get_contents('php://input'), true);
//     $acao = isset($dados['acao']) ? $dados['acao'] : '';

//     if ($acao === 'store') {
//         Geolocalizacao::store($dados); 
//     } elseif ($acao === 'delete_atividade') {
//         Geolocalizacao::deleteAtividade($dados);
//     } else {
//         echo json_encode(array('error' => true, 'message' => 'Ação desconhecida'));
//     }
// } else {
//     echo json_encode(array('error' => true, 'message' => 'Método Desconhecido'));
// }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $acao = isset($dados['acao']) ? $dados['acao'] : '';

    if ($acao === 'store') {
        Geolocalizacao::store($dados); 
    } elseif ($acao === 'delete_atividade') {
        Geolocalizacao::deleteAtividade($dados);
    } elseif ($acao === 'salvar_localizacao') {
        Geolocalizacao::onSalvarLocalizacao($dados);
    } else {
        echo json_encode(array('error' => true, 'message' => 'Ação desconhecida'));
    }
} else {
    echo json_encode(array('error' => true, 'message' => 'Método Desconhecido'));
}