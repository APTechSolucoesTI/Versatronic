<?php

class ViewInteracaoTimeline extends TRecord
{
    const TABLENAME  = 'view_interacao_timeline';
    const PRIMARYKEY = 'chave';
    const IDPOLICY   =  'max'; // {max, serial}

    private $interacao_historico_etapa;
    private $interacao_historico_atividade;
    private $interacao_historico_observacao;
    private $interacao_historico_arquivo;
    private $interacao_localizacao;
    private static $contador_documento = 1;
    private static $database = 'minicrm';
                                                                                                        

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('interacao_id');
        parent::addAttribute('dt_historico');
        parent::addAttribute('tipo');
    
    }

            public function get_interacao_historico_etapa()
    {
        if (!$this->interacao_historico_etapa)
        {
            $this->interacao_historico_etapa = InteracaoHistoricoEtapa::find($this->chave);
        }

        return $this->interacao_historico_etapa;
    }

    public function get_interacao_historico_atividade()
    {
        if (!$this->interacao_historico_atividade)
        {
            $this->interacao_historico_atividade = InteracaoHistoricoAtividade::find($this->chave);
        }

        return $this->interacao_historico_atividade;
    }

    public function get_interacao_historico_observacao()
    {
        if (!$this->interacao_historico_observacao)
        {
            $this->interacao_historico_observacao = InteracaoHistoricoObservacao::find($this->chave);
        }

        return $this->interacao_historico_observacao;
    }

    public function get_interacao_historico_arquivo()
    {
        if (!$this->interacao_historico_arquivo)
        {
            $this->interacao_historico_arquivo = InteracaoHistoricoArquivo::find($this->chave);
        }

        return $this->interacao_historico_arquivo;
    }

    public function get_interacao_localizacao()
    {
        if (!$this->interacao_localizacao)
        {
            $this->interacao_localizacao = InteracaoLocalizacao::find($this->chave);
        }

        return $this->interacao_localizacao;
    }

    private function esc($texto)
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }

    private function textoHtml($texto)
    {
        $texto = trim((string) $texto);

        if ($texto === '')
        {
            return '-';
        }

        // Decodifica entidades HTML
        $texto = html_entity_decode($texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove width e height fixos
        $texto = preg_replace('/\s(width|height)="[^"]*"/i', '', $texto);

        // Remove style existente das imagens
        $texto = preg_replace('/<img([^>]*?)style="[^"]*"([^>]*)>/i', '<img$1$2>', $texto);

        // Aplica estilo correto
        $texto = preg_replace(
            '/<img([^>]+)>/i',
            '<img$1 style="width:100%; max-width:100%; height:auto; display:block;">',
            $texto
        );

        return $texto;
    }
    private function formatarDataHora($data)
    {
        if (empty($data) || $data == '0000-00-00' || $data == '0000-00-00 00:00:00')
        {
            return '-';
        }

        $timestamp = strtotime($data);

        if (!$timestamp)
        {
            return '-';
        }

        return date('d/m/Y H:i', $timestamp);
    }

    private function normalizarTexto($texto)
    {
        $texto = mb_strtolower(trim((string) $texto), 'UTF-8');

        $mapa = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u',
            'ç' => 'c'
        ];

        return strtr($texto, $mapa);
    }

    public function deveOcultarNoDocumento()
    {
        if ($this->tipo == 'atividade')
        {
            $atividade = $this->get_interacao_historico_atividade();

            if ($atividade && $atividade->movimentacao)
            {
                $movimentacao = $this->normalizarTexto($atividade->movimentacao->nome);

                if (in_array($movimentacao, ['iniciar interacao', 'interacao finalizada']))
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function get_titulo()
    {
        if ($this->tipo == 'observacao')
        {
            $nota = $this->get_interacao_historico_observacao();
            $movimentacao = $nota && $nota->movimentacao ? $nota->movimentacao->nome : '';
            return "<strong>Observação {$this->esc($movimentacao)}</strong>";
        }
        else if ($this->tipo == 'arquivo')
        {
            $arquivo = $this->get_interacao_historico_arquivo();
            $movimentacao = $arquivo && $arquivo->movimentacao ? $arquivo->movimentacao->nome : '';
            return "<strong>Arquivo {$this->esc($movimentacao)}</strong>";
        }
        else if ($this->tipo == 'etapa')
        {
            $etapa = $this->get_interacao_historico_etapa();
            $nome = ($etapa && $etapa->etapa_interacao) ? $etapa->etapa_interacao->nome : 'Etapa';

            return "<span style='margin-right: 6px; width: 12px; height: 12px; display: inline-block; border-radius: 50%; background: #000000;'></span><strong>{$this->esc($nome)}</strong>";
        }
        else if ($this->tipo == 'atividade')
        {
            $atividade = $this->get_interacao_historico_atividade();
            $tipo = $atividade && $atividade->tipo_atividade ? $atividade->tipo_atividade->nome : 'Atividade';
            $movimentacao = $atividade && $atividade->movimentacao ? $atividade->movimentacao->nome : '';

            return "<strong>{$this->esc($tipo)} {$this->esc($movimentacao)}</strong>";
        }
        else if ($this->tipo == 'localizacao')
        {
            return "<strong>Localização</strong>";
        }

        return "<strong>Histórico</strong>";
    }

    public function get_descricao()
    {
        $div = "";

        if ($this->tipo == 'observacao')
        {
            $nota = $this->get_interacao_historico_observacao();

            if ($nota)
            {
                $div = $this->textoHtml($nota->descricao);
            }
            else
            {
                $div = "Observação não encontrada.";
            }
        }
        else if ($this->tipo == 'arquivo')
        {
            $arquivo = $this->get_interacao_historico_arquivo();

            if ($arquivo)
            {
                $descricao = $arquivo->descricao ?? '';
                $movimentacao = ($arquivo->movimentacao->nome ?? '');
                $div = $this->textoHtml(trim($descricao . ' ' . $movimentacao));
            }
            else
            {
                $div = "Arquivo não encontrado.";
            }
        }
        else if ($this->tipo == 'etapa')
        {
            $etapa = $this->get_interacao_historico_etapa();
            $nome = ($etapa && $etapa->etapa_interacao) ? $etapa->etapa_interacao->nome : 'Etapa não encontrada';
            $div = "Etapa alterada para <strong>{$this->esc($nome)}</strong>";
        }
        else if ($this->tipo == 'atividade')
        {
            $atividade = $this->get_interacao_historico_atividade();

            if ($atividade)
            {
               $observacao = $this->textoHtml($atividade->observacao);
                $ini = $this->formatarDataHora($atividade->horario_inicial);
                $fim = $this->formatarDataHora($atividade->horario_final);
                $estado = $atividade->estado_atividade ? $atividade->estado_atividade->nome : '-';

                $div = "<b>Estado:</b> {$this->esc($estado)}<br/>"
                    . "<b>Início:</b> {$ini}<br/>"
                    . "<b>Fim:</b> {$fim}<br/>"
                    . "<b>Observação:</b><br/>{$observacao}";
            }
            else
            {
                $div = "Atividade não encontrada.";
            }
        }
        else if ($this->tipo == 'localizacao')
        {
            $localizacao = $this->get_interacao_localizacao();

            if ($localizacao)
            {
                $latitude = $this->esc($localizacao->latitude);
                $longitude = $this->esc($localizacao->longitude);
                $descricao = $this->esc($localizacao->descricao);
                $div = "<a href='https://www.google.com/maps?q={$latitude},{$longitude}' target='_blank' style='color:#000000; text-decoration:underline;'>{$descricao}</a>";
            }
            else
            {
                $div = "Localização não encontrada.";
            }
        }

        return $div;
    }

    public function get_informacao_documento()
    {
        if ($this->deveOcultarNoDocumento())
        {
            return '';
        }

        $numero = self::$contador_documento;
        self::$contador_documento++;

        $dataHistorico = $this->formatarDataHora($this->dt_historico);
        $titulo = '';
        $conteudo = '';

        if ($this->tipo == 'observacao')
        {
            $nota = $this->get_interacao_historico_observacao();

            if ($nota)
            {
                $movimentacao = $nota->movimentacao ? $nota->movimentacao->nome : '';
                $titulo = 'Observação';
                if (!empty($movimentacao))
                {
                    $titulo .= ': ' . $this->esc($movimentacao);
                }

                $conteudo = "
                    <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Descrição</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->textoHtml($nota->descricao) . "</td>
                        </tr>
                    </table>
                ";
            }
            else
            {
                $titulo = 'Observação';
                $conteudo = "<div class='sem-registro' style='font-size:12px; color:#000000;'>-</div>";
            }
        }

        else if ($this->tipo == 'arquivo')
        {
            $arquivo = $this->get_interacao_historico_arquivo();

            if ($arquivo)
            {
                $movimentacao   = $arquivo->movimentacao ? $arquivo->movimentacao->nome : '';
                $caminhoArquivo = trim((string) ($arquivo->descricao ?? ''));
                $ehImagem       = $this->arquivoEhImagem($caminhoArquivo);

                $titulo = 'Arquivo';
                if (!empty($movimentacao))
                {
                    $titulo .= ': ' . $this->esc($movimentacao);
                }

                if ($ehImagem)
                {
                    $previewArquivo = $this->obterHtmlPreviewArquivoDocumento($caminhoArquivo, false);

                    return "
                        <div class='timeline-item timeline-item-imagem' style='position:relative; padding-left:44px; margin-bottom:16px; min-height:42px; font-size:12px; color:#000000; page-break-before:always;'>
                            <div class='timeline-marker' style='position:absolute; left:0; top:0; width:26px; height:26px; line-height:24px; text-align:center; border-radius:50%; background:#ffffff; color:#000000; font-weight:bold; font-size:11px; border:1px solid #000000;'>{$numero}</div>

                            <div class='timeline-card' style='border:1px solid #000000; border-radius:4px; padding:12px; background:#ffffff; color:#000000; font-size:12px;'>
                                <div class='timeline-meta' style='font-size:12px; color:#000000; margin-bottom:6px;'>{$dataHistorico}</div>
                                <div class='timeline-title' style='font-size:12px; font-weight:bold; color:#000000; margin-bottom:10px;'>{$titulo}</div>
                                {$previewArquivo}
                            </div>
                        </div>
                    ";
                }

                $conteudo = "
                    <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Descrição</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000; white-space:normal; word-break:break-all; overflow-wrap:anywhere;'>
                                " . $this->esc($caminhoArquivo) . "
                            </td>
                        </tr>
                    </table>
                ";
            }
            else
            {
                $titulo = 'Arquivo';
                $conteudo = "<div class='sem-registro' style='font-size:12px; color:#000000;'>-</div>";
            }
        }
        else if ($this->tipo == 'etapa')
        {
            $etapa = $this->get_interacao_historico_etapa();

            if ($etapa && $etapa->etapa_interacao)
            {
                $nomeEtapa = $this->esc($etapa->etapa_interacao->nome);

                $titulo = 'Mudança de etapa';
                $conteudo = "
                    <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Etapa atual:</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>
                                <span style='display:inline-block; padding:3px 9px; border-radius:12px; background:#ffffff; color:#000000; font-weight:bold; font-size:12px; border:1px solid #000000;'>{$nomeEtapa}</span>
                            </td>
                        </tr>
                    </table>
                ";
            }
            else
            {
                $titulo = 'Mudança de etapa';
                $conteudo = "<div class='sem-registro' style='font-size:12px; color:#000000;'>-</div>";
            }
        }
        else if ($this->tipo == 'atividade')
        {
            $atividade = $this->get_interacao_historico_atividade();

            if ($atividade)
            {
                $tipoAtividade = $atividade->tipo_atividade ? $atividade->tipo_atividade->nome : 'Atividade';
                $estado = $atividade->estado_atividade ? $atividade->estado_atividade->nome : '-';
                $inicio = $this->formatarDataHora($atividade->horario_inicial);
                $fim = $this->formatarDataHora($atividade->horario_final);

                $titulo = 'Atividade: ' . $this->esc($tipoAtividade);

                if ($atividade->tipo_atividade_id == 5) {
                    TTransaction::open(self::$database);
                    $atividade = InteracaoAtividade::where('id', '=', $atividade->interacao_atividade_id)->first();                
                    TTransaction::close();
                
                    if ($atividade->destinatario && $atividade->assunto) {   
                        $dest = $atividade->destinatario;
                        $ass = $atividade->assunto;

                        $conteudo = "
                            <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Estado:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->esc($estado) . "</td>
                                </tr>                
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Início:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$inicio}</td>
                                </tr>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Fim:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$fim}</td>
                                </tr>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Destinatário(s):</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$dest}</td>
                                </tr>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Assunto:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$ass}</td>
                                </tr>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Corpo do E-mail</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->textoHtml($atividade->observacao) . "</td>
                                </tr>
                            </table>
                        ";

                        if ($atividade->copia) {
                            $copy = $atividade->copia;
                                $conteudo = "
                                    <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Estado:</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->esc($estado) . "</td>
                                        </tr>                
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Início:</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$inicio}</td>
                                        </tr>
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Fim:</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$fim}</td>
                                        </tr>
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Cc(Com Cópia):</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$copy}</td>
                                        </tr>
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Destinatário(s):</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$dest}</td>
                                        </tr>
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Assunto:</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$ass}</td>
                                        </tr>
                                        <tr>
                                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Corpo do E-mail:</td>
                                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->textoHtml($atividade->observacao) . "</td>
                                        </tr>
                                    </table>
                                ";  
                        }
                    }
                    else {                
                        $conteudo = "
                            <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Estado:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->esc($estado) . "</td>
                                </tr>                
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Início:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$inicio}</td>
                                </tr>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Fim:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$fim}</td>
                                </tr>
                                <tr>
                                    <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Observação:</td>
                                    <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->textoHtml($atividade->observacao) . "</td>
                                </tr>
                            </table>
                        ";
                    }                    
                
                }
                else {                
                    $conteudo = "
                        <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                            <tr>
                                <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Estado:</td>
                                <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->esc($estado) . "</td>
                            </tr>                
                            <tr>
                                <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Início:</td>
                                <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$inicio}</td>
                            </tr>
                            <tr>
                                <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Fim:</td>
                                <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$fim}</td>
                            </tr>
                            <tr>
                                <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Observação:</td>
                                <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>" . $this->textoHtml($atividade->observacao) . "</td>
                            </tr>
                        </table>
                    ";
                }

            }
            else
            {
                $titulo = 'Atividade';
                $conteudo = "<div class='sem-registro' style='font-size:12px; color:#000000;'>Atividade não encontrada.</div>";
            }
        }
        else if ($this->tipo == 'localizacao')
        {
            $localizacao = $this->get_interacao_localizacao();

            if ($localizacao)
            {
                $latitude = $this->esc($localizacao->latitude);
                $longitude = $this->esc($localizacao->longitude);
                $descricao = $this->textoHtml($localizacao->descricao);
                $urlMapa = "https://www.google.com/maps?q={$latitude},{$longitude}";

                $titulo = 'Localização';

                $conteudo = "
                    <table class='timeline-table' style='width:100%; border-collapse:collapse; font-size:12px; color:#000000;'>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Descrição</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$descricao}</td>
                        </tr>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Latitude</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$latitude}</td>
                        </tr>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Longitude</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>{$longitude}</td>
                        </tr>
                        <tr>
                            <td class='timeline-label' style='width:110px; font-weight:bold; padding:6px; vertical-align:top; color:#000000;'>Mapa</td>
                            <td class='timeline-text' style='padding:6px; vertical-align:top; color:#000000;'>
                                <a href='{$urlMapa}' target='_blank' style='color:#000000; text-decoration:underline;'>Abrir no Google Maps</a>
                            </td>
                        </tr>
                    </table>
                ";
            }
            else
            {
                $titulo = 'Localização';
                $conteudo = "<div class='sem-registro' style='font-size:12px; color:#000000;'>Localização não encontrada.</div>";
            }
        }
        else
        {
            $titulo = 'Histórico';
            $conteudo = "<div class='sem-registro' style='font-size:12px; color:#000000;'>Tipo de histórico não identificado.</div>";
        }

        return "
    <div class='timeline-item' style='position:relative; padding-left:44px; margin-bottom:16px; min-height:42px; font-size:12px; color:#000000;'>
        <div class='timeline-marker' style='position:absolute; left:0; top:0; width:26px; height:26px; line-height:24px; text-align:center; border-radius:50%; background:#ffffff; color:#000000; font-weight:bold; font-size:11px; border:1px solid #000000;'>{$numero}</div>
        <div class='timeline-card' style='border:1px solid #000000; border-radius:4px; padding:12px; background:#ffffff; color:#000000; font-size:12px;'>
            <div class='timeline-meta' style='font-size:12px; color:#000000; margin-bottom:6px;'>{$dataHistorico}</div>
            <div class='timeline-title' style='font-size:12px; font-weight:bold; color:#000000; margin-bottom:8px;'>{$titulo}</div>
            {$conteudo}
        </div>
    </div>
";
    }

    public static function resetarContadorDocumento()
    {
        self::$contador_documento = 1;
    }

    public static function gerarHtmlDocumento($interacaoId)
    {
        self::resetarContadorDocumento();

        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $interacaoId));
        $criteria->setProperty('order', 'dt_historico asc, chave asc');

        $repo = new TRepository('ViewInteracaoTimeline');
        $objetos = $repo->load($criteria);

        $html = '';

        if ($objetos)
        {
            foreach ($objetos as $objeto)
            {
                $bloco = $objeto->get_informacao_documento();

                if (!empty(trim(strip_tags($bloco))))
                {
                    $html .= $bloco;
                }
            }
        }

        if (trim($html) == '')
        {
            $html = "
                <div style='border:1px solid #000000; padding:12px; font-size:12px; color:#000000;'>
                    Nenhum histórico encontrado para esta interação.
                </div>
            ";
        }

        return $html;
    }

    private function obterCaminhoFisicoArquivo($caminho)
    {
        $caminho = trim((string) $caminho);

        if ($caminho === '')
        {
            return null;
        }

        $caminho = str_replace('\\', '/', $caminho);
        $caminho = ltrim($caminho, '/');

        $tentativas = [
            $caminho,
            './' . $caminho,
            realpath('.') . DIRECTORY_SEPARATOR . $caminho,
        ];

        foreach ($tentativas as $tentativa)
        {
            if (!empty($tentativa) && is_file($tentativa))
            {
                return $tentativa;
            }
        }

        return null;
    }

    private function obterHtmlPreviewArquivoDocumento($caminho, $mostrarNome = true)
    {
        $caminhoOriginal = trim((string) $caminho);
        $caminhoFisico   = $this->obterCaminhoFisicoArquivo($caminhoOriginal);

        if (!$caminhoFisico)
        {
            return "<div style='color:#000000;'>Arquivo não encontrado no servidor.</div>";
        }

        $extensao = strtolower(pathinfo($caminhoFisico, PATHINFO_EXTENSION));
        $nome     = $this->esc(basename($caminhoFisico));

        $extensoesImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

        if (in_array($extensao, $extensoesImagem))
        {
            $mime = function_exists('mime_content_type') ? mime_content_type($caminhoFisico) : null;

            if (!$mime)
            {
                $mime = in_array($extensao, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/' . $extensao;
            }

            $conteudo = @file_get_contents($caminhoFisico);

            if ($conteudo === false)
            {
                return "<div style='color:#000000;'>Não foi possível ler o arquivo.</div>";
            }

            $base64 = base64_encode($conteudo);

            $nomeHtml = $mostrarNome
                ? "<div style='margin-bottom:8px; color:#000000; white-space:normal; word-break:break-all; overflow-wrap:anywhere;'>{$nome}</div>"
                : "";

            return "
                <div style='padding-top:4px; text-align:center;'>
                    {$nomeHtml}
                    <img
                        src='data:{$mime};base64,{$base64}'
                        style='display:block; margin:0 auto; max-width:100%; max-height:860px; width:auto; height:auto; border:1px solid #000000; border-radius:4px;'
                    >
                </div>
            ";
        }

        return "
            <div style='color:#000000;'>
                <div><strong>Arquivo:</strong> {$nome}</div>
            </div>
        ";
    }

    private function arquivoEhImagem($caminho)
    {
        $ext = strtolower(pathinfo((string) $caminho, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
    }

                                                                                                    //</userCustomFunctions

                                                                                                        
}

