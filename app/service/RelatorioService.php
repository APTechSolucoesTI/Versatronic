<?php
use Dompdf\Dompdf;
use Dompdf\Options;
use Adianti\Database\TTransaction;

class RelatorioService
{
    private static $database = 'minicrm';

    public static function gerarRelatorioInteracao($interacaoId)
    {
        try
        {
            if (empty($interacaoId))
            {
                throw new Exception('ID da interação não informado.');
            }

            TTransaction::open(self::$database);

            $interacao = new Interacao($interacaoId);

            if (empty($interacao->id))
            {
                throw new Exception('Interação não encontrada.');
            }

            $cliente  = !empty($interacao->cliente_id) ? new Pessoa($interacao->cliente_id) : null;
            $vendedor = !empty($interacao->vendedor_id) ? new Representante($interacao->vendedor_id) : null;

            $timeline = ViewInteracaoTimeline::gerarHtmlDocumento($interacao->id);

            $html = self::montarHtml($interacao, $cliente, $vendedor, $timeline);

            TTransaction::close();

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $filePath = 'app/output/interacao_' . $interacao->id . '.pdf';

            file_put_contents($filePath, $output);

            return $filePath;
        }
        catch (Exception $e)
        {
            if (TTransaction::get())
            {
                TTransaction::rollback();
            }

            throw $e;
        }
    }

    private static function montarHtml($interacao, $cliente, $vendedor, $timeline)
    {
        $logo = self::obterLogoHtml();

        $numeroInteracao   = self::e($interacao->id ?? '');
        $dataGeracao       = date('d/m/Y H:i');
        $dataInicio        = self::formatarData($interacao->data_inicio ?? null);
        $dataFechamento    = self::formatarData($interacao->data_fechamento ?? null);
        $dataEsperada      = self::formatarData($interacao->data_fechamento_esperada ?? null);
        $valorTotal        = self::formatarMoeda($interacao->valor_total ?? null);

        $clienteNome       = self::e($cliente->razao_social ?? '');
        $clienteEmail      = self::e($cliente->email ?? '');
        $clienteFone       = self::e($cliente->fone ?? '');
        $clienteDocumento  = self::e($cliente->cpf_cnpj ?? '');
        $clienteCategoria  = self::e(self::obterCategoriaCliente($cliente));
        $clienteCidade     = self::e(self::obterCidadeCliente($cliente));
        $clienteUf         = self::e($cliente->cidade_uf ?? '');

        $clienteEndereco   = self::e($cliente->endereco ?? '');
        $clienteNumero     = self::e($cliente->numero ?? '');
        $clienteBairro     = self::e($cliente->bairro ?? '');
        $clienteCep        = self::e($cliente->cep ?? '');
        $clienteComp       = self::e($cliente->complemento ?? '');

        $vendedorNome      = self::e($vendedor->razao_social ?? '');
        $mes               = self::e($interacao->mes ?? '');
        $ano               = self::e($interacao->ano ?? '');

        return "
        <!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <style>
                @page {
                    margin: 14px 16px 14px 16px;
                }

                body {
                    margin: 0;
                    padding: 0;
                    font-family: Calibri, Arial, sans-serif;
                    font-size: 10px;
                    color: #000000;
                    background: #ffffff;
                }

                * {
                    box-sizing: border-box;
                }

                .pagina {
                    width: 100%;
                }

                .topo {
                    width: 100%;
                    border-collapse: collapse;
                    border: 1px solid #000000;
                    margin-bottom: 8px;
                    table-layout: fixed;
                }

                .topo td {
                    vertical-align: middle;
                }

                .topo-esquerda {
                    width: 79%;
                    padding: 6px 10px;
                    border-right: 1px solid #000000;
                }

                .topo-direita {
                    width: 21%;
                }

                .numero-titulo {
                    padding: 8px 6px;
                    text-align: center;
                    font-weight: bold;
                    font-size: 10px;
                    background: #ffffff;
                }

                .numero-valor {
                    text-align: center;
                    font-size: 22px;
                    font-weight: bold;
                    padding: 24px 6px;
                    background: #f3f3f3;
                    border-top: 1px solid #000000;
                }

                .cabecalho-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .cabecalho-table td {
                    vertical-align: middle;
                }

                .logo-col {
                    width: 84px;
                    padding-right: 8px;
                }

                .empresa-col {
                    padding-top: 0;
                }

                .empresa-nome {
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0 0 5px 0;
                    line-height: 1.05;
                    white-space: nowrap;
                }

                .empresa-linha {
                    font-size: 9.5px;
                    line-height: 1.2;
                    margin: 0 0 3px 0;
                    white-space: nowrap;
                }

                .empresa-linha .bloco {
                    display: inline-block;
                    margin-right: 16px;
                }

                .logo-box {
                    width: 68px;
                    height: 68px;
                    border: 1px solid #000000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 9px;
                    font-weight: bold;
                    text-align: center;
                }

                .box {
                    width: 100%;
                    border: 1px solid #000000;
                    margin-bottom: 8px;
                    background: #ffffff;
                }

                .box-titulo {
                    border-bottom: 1px solid #000000;
                    padding: 5px 8px;
                    font-size: 10px;
                    font-weight: bold;
                    background: #f3f3f3;
                    text-transform: uppercase;
                }

                .box-conteudo {
                    padding: 7px 8px;
                }

                .dados-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .dados-table td {
                    padding: 3px 6px;
                    vertical-align: middle;
                    white-space: nowrap;
                }

                .dados-table .campo {
                    width: 17%;
                    font-weight: bold;
                }

                .dados-table .valor {
                    width: 33%;
                }

                .historico-wrap {
                    padding-top: 4px;
                }

                .historico-wrap .timeline-item {
                    page-break-inside: avoid;
                }

                .rodape {
                    margin-top: 4px;
                    font-size: 9px;
                    text-align: right;
                }

                .historico-wrap .timeline-item {
                    page-break-inside: avoid;
                }

                .historico-wrap .timeline-anexo-pagina {
                    page-break-before: always;
                    page-break-inside: avoid;
                    min-height: 930px;
                    border: 1px solid #000000;
                    padding: 12px;
                    margin: 0 0 16px 0;
                    background: #ffffff;
                }

                .historico-wrap .timeline-anexo-titulo {
                    font-size: 12px;
                    font-weight: bold;
                    color: #000000;
                    margin-bottom: 10px;
                }

                .historico-wrap .timeline-anexo-pagina img {
                    display: block;
                    margin: 0 auto;
                    max-width: 100% !important;
                    max-height: 860px !important;
                    width: auto !important;
                    height: auto !important;
                    border: 1px solid #000000;
                    border-radius: 4px;
                }
            </style>
        </head>
        <body>
            <div class='pagina'>

                <table class='topo'>
                    <tr>
                        <td class='topo-esquerda'>
                            <table class='cabecalho-table'>
                                <tr>
                                    <td class='logo-col'>
                                        {$logo}
                                    </td>
                                    <td class='empresa-col'>
                                        <p class='empresa-nome'>VERSATRONIC COM E MANUT. ELETR IND LTDA</p>

                                        <p class='empresa-linha'>RUA HENRIQUE WIEZEL 961 - DISTRITO INDUSTRIAL</p>

                                        <p class='empresa-linha'>
                                            <span class='bloco'>SANTA BÁRBARA D'OESTE</span>
                                            <span class='bloco'>SP</span>
                                            <span class='bloco'><strong>CEP:</strong> 13456-165</span>
                                        </p>

                                        <p class='empresa-linha'>
                                            <span class='bloco'><strong>CNPJ:</strong> 68.245.877/0001-86</span>
                                            <span class='bloco'><strong>Inscrição Estadual:</strong> 606.058.761.112</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class='topo-direita'>
                            <div class='numero-titulo'>Número da Interação</div>
                            <div class='numero-valor'>#{$numeroInteracao}</div>
                        </td>
                    </tr>
                </table>

                <div class='box'>
                    <div class='box-titulo'>Dados da Interação</div>
                    <div class='box-conteudo'>
                        <table class='dados-table'>
                            <tr>
                                <td class='campo'>Representante:</td>
                                <td class='valor'>{$vendedorNome}</td>
                                <td class='campo'>Data de Início:</td>
                                <td class='valor'>{$dataInicio}</td>
                            </tr>
                            <tr>
                                <td class='campo'>Data de Fechamento:</td>
                                <td class='valor'>{$dataFechamento}</td>
                                <td class='campo'>Fechamento Esperado:</td>
                                <td class='valor'>{$dataEsperada}</td>
                            </tr>
                            <tr>
                                <td class='campo'>Referência:</td>
                                <td class='valor'>{$mes}/{$ano}</td>
                                <td class='campo'></td>
                                <td class='valor'></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class='box'>
                    <div class='box-titulo'>Cliente</div>
                    <div class='box-conteudo'>
                        <table class='dados-table'>
                            <tr>
                                <td class='campo'>Nome/Razão Social:</td>
                                <td class='valor'>{$clienteNome}</td>
                                <td class='campo'>Email:</td>
                                <td class='valor'>{$clienteEmail}</td>
                            </tr>
                            <tr>
                                <td class='campo'>Telefone:</td>
                                <td class='valor'>{$clienteFone}</td>
                                <td class='campo'>CNPJ/CPF:</td>
                                <td class='valor'>{$clienteDocumento}</td>
                            </tr>
                            <tr>
                                <td class='campo'>Categoria:</td>
                                <td class='valor'>{$clienteCategoria}</td>
                                <td class='campo'></td>
                                <td class='valor'></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class='box'>
                    <div class='box-titulo'>Histórico da Interação</div>
                    <div class='box-conteudo historico-wrap'>
                        {$timeline}
                    </div>
                </div>

                <div class='rodape'>
                    Documento gerado em {$dataGeracao}
                </div>

            </div>
        </body>
        </html>
        ";
    }

    private static function obterCategoriaCliente($cliente)
    {
        if (!$cliente)
        {
            return '';
        }

        if (!empty($cliente->categoria_cliente) && !empty($cliente->categoria_cliente->nome))
        {
            return $cliente->categoria_cliente->nome;
        }

        if (!empty($cliente->categoria_cliente_id))
        {
            try
            {
                $categoria = new CategoriaCliente($cliente->categoria_cliente_id);
                return $categoria->nome ?? '';
            }
            catch (Exception $e)
            {
            }
        }

        return '';
    }

    private static function obterCidadeCliente($cliente)
    {
        if (!$cliente)
        {
            return '';
        }

        if (!empty($cliente->cidade))
        {
            return $cliente->cidade;
        }

        if (!empty($cliente->cidade_nome))
        {
            return $cliente->cidade_nome;
        }

        if (!empty($cliente->cidade_id))
        {
            try
            {
                $cidade = new Cidade($cliente->cidade_id);
                return $cidade->nome ?? '';
            }
            catch (Exception $e)
            {
            }
        }

        return '';
    }

    private static function formatarData($data)
    {
        if (empty($data) || $data == '0000-00-00' || $data == '0000-00-00 00:00:00')
        {
            return '';
        }

        $timestamp = strtotime($data);

        if (!$timestamp)
        {
            return '';
        }

        if (strlen($data) > 10)
        {
            return date('d/m/Y H:i', $timestamp);
        }

        return date('d/m/Y', $timestamp);
    }

    private static function formatarMoeda($valor)
    {
        if ($valor === null || $valor === '')
        {
            return '';
        }

        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    private static function e($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    private static function obterLogoHtml()
    {
        $caminhos = [
            'app/images/logo_versatronic.png',
            'app/images/versatronic.png',
            'app/images/logo.png',
        ];

        foreach ($caminhos as $caminho)
        {
            if (file_exists($caminho))
            {
                $conteudo = base64_encode(file_get_contents($caminho));
                return "<img src='data:image/png;base64,{$conteudo}' style='max-width:88px; max-height:88px;'>";
            }
        }

        return "<div class='logo-box'>VERSATRONIC</div>";
    }

}
