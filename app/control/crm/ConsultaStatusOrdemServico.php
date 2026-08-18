<?php

class ConsultaStatusOrdemServico extends TPage
{
    protected $form;
    protected $resultado;

    private static $database = "corporerm";

    public function __construct()
    {
        parent::__construct();

        $this->montarFormulario();
        $this->montarPagina();
    }

    private function montarFormulario()
    {
        $this->form = new BootstrapFormBuilder("form_ConsultaStatusOrdemServico");
        $this->form->setFormTitle("Acompanhamento da Ordem de Serviço");

        $coligada = new TCombo("coligada_id");
        $coligada->addItems(array(
            1 => "VERSATRONIC",
            2 => "VERSATRONIC CNC"
        ));
        $coligada->setDefaultOption(false);
        $coligada->setValue(1);
        $coligada->setSize("100%");
        $coligada->addValidation("Coligada", new TRequiredValidator);

        $numeroOs = new TEntry("numero_os");
        $numeroOs->setSize("100%");
        $numeroOs->setProperty("placeholder", "Digite o número da OS");
        $numeroOs->setProperty("autocomplete", "off");
        $numeroOs->addValidation("Número da OS", new TRequiredValidator);

        $this->form->addFields(
            array(new TLabel("Coligada")),
            array($coligada),
            array(new TLabel("Número da OS")),
            array($numeroOs)
        );

        $this->form->addAction(
            "Consultar andamento",
            new TAction(array($this, "onConsultar")),
            "fa:search blue"
        );

        $this->form->addAction(
            "Limpar",
            new TAction(array($this, "onLimpar")),
            "fa:eraser red"
        );
    }

    private function montarPagina()
    {
        $this->resultado = new TElement("div");
        $this->resultado->id = "resultado_timeline_os";
        $this->resultado->style = "width: 100%; margin-top: 16px;";

        $container = new TVBox;
        $container->style = "width: 100%;";
        $container->add($this->form);
        $container->add($this->resultado);

        parent::add($container);
    }

    public function onConsultar($param = null)
    {
        try {
            $this->form->validate();

            $dados = $this->form->getData();
            $this->form->setData($dados);

            $registro = $this->buscarFluxoOrdemServico(
                $dados->coligada_id,
                $dados->numero_os
            );

            if (!$registro) {
                $html = $this->montarMensagemNaoEncontradoHtml(
                    $dados->numero_os,
                    (int) $dados->coligada_id
                );

                $this->exibirResultadoHtml($html);

                new TMessage(
                    "warning",
                    "A Ordem de Serviço informada não foi encontrada."
    
                );

                return;
            }

            $resultado = $this->montarResultadoTimeline(
                $registro,
                (int) $dados->coligada_id
            );

            $html = $this->montarTimelineHtml($resultado);
            $this->exibirResultadoHtml($html);
            TScript::create($this->montarScriptExportacaoPdf());
        } catch (Exception $e) {
            new TMessage("error", $e->getMessage());
        }
    }

    public function onLimpar($param = null)
    {
        $dados = new stdClass;
        $dados->coligada_id = 1;
        $dados->numero_os = "";

        $this->form->setData($dados);

        TScript::create("var resultado = document.getElementById('resultado_timeline_os'); if (resultado) { resultado.innerHTML = ''; }");
    }

    private function buscarFluxoOrdemServico($coligadaId, $numeroOs)
    {
        $coligadaId = (int) $coligadaId;
        $numeroOs = trim((string) $numeroOs);

        $this->validarFiltros($coligadaId, $numeroOs);

        $numeroOsComZeros = ctype_digit($numeroOs)
            ? str_pad($numeroOs, 6, "0", STR_PAD_LEFT)
            : $numeroOs;

        $transacaoAberta = false;

        try {
            TTransaction::open(self::$database);
            $transacaoAberta = true;

            $conexao = TTransaction::get();
            $comando = $conexao->prepare($this->montarSqlFluxo());

            $comando->execute(array(
                ":coligada" => $coligadaId,
                ":numero_os" => $numeroOs,
                ":numero_os_zeros" => $numeroOsComZeros
            ));

            $registro = $comando->fetch(PDO::FETCH_ASSOC);

            TTransaction::close();
            $transacaoAberta = false;

            if (!$registro) {
                return null;
            }

            return array_change_key_case($registro, CASE_UPPER);
        } catch (Exception $e) {
            if ($transacaoAberta) {
                TTransaction::rollback();
            }

            throw $e;
        }
    }

    private function montarSqlFluxo()
    {
        $sql = "
        /*
         * Entrada da consulta:
         * - número da OS digitado pelo usuário;
         * - movimento 2.1.60 da coligada escolhida.
         *
         * 
         * A OFMOV é usada para inverter o relacionamento:
         * OFMOV.IDMOVOS = IDMOV da OS 2.1.60
         * OFMOV.IDMOV   = IDMOV do movimento 2.1.06
         */
        WITH os AS (
            SELECT TOP 1
                m.CODCOLIGADA,
                m.IDMOV,
                LTRIM(RTRIM(m.NUMEROMOV)) AS NUMEROMOV,
                m.DATAEMISSAO,
                m.STATUS
            FROM dbo.TMOV m WITH (NOLOCK)
            WHERE m.CODCOLIGADA = :coligada
            AND LTRIM(RTRIM(m.CODTMV)) = '2.1.60'
            AND (
                    LTRIM(RTRIM(m.NUMEROMOV)) = :numero_os
                    OR LTRIM(RTRIM(m.NUMEROMOV)) = :numero_os_zeros
                )
            ORDER BY m.IDMOV DESC
        )
        SELECT
            os.IDMOV                AS OS_IDMOV,
            os.NUMEROMOV            AS OS_NUMEROMOV,
            os.DATAEMISSAO          AS OS_DATAEMISSAO,
            os.STATUS               AS OS_STATUS,

            rm.IDMOV                AS RM_IDMOV,
            rm.NUMEROMOV            AS RM_NUMEROMOV,
            rm.DATAEMISSAO          AS RM_DATAEMISSAO,
            rm.STATUS               AS RM_STATUS,

            realizado.IDMOV         AS REALIZADO_IDMOV,
            realizado.NUMEROMOV     AS REALIZADO_NUMEROMOV,
            realizado.DATAEMISSAO   AS REALIZADO_DATAEMISSAO,
            realizado.STATUS        AS REALIZADO_STATUS,

            aguardando.IDMOV        AS AGUARDANDO_IDMOV,
            aguardando.NUMEROMOV    AS AGUARDANDO_NUMEROMOV,
            aguardando.DATAEMISSAO  AS AGUARDANDO_DATAEMISSAO,
            aguardando.STATUS       AS AGUARDANDO_STATUS,

            aprovado.IDMOV          AS APROVADO_IDMOV,
            aprovado.NUMEROMOV      AS APROVADO_NUMEROMOV,
            aprovado.DATAEMISSAO    AS APROVADO_DATAEMISSAO,
            aprovado.STATUS         AS APROVADO_STATUS,

            liberacao.IDMOV         AS LIBERACAO_IDMOV,
            liberacao.NUMEROMOV     AS LIBERACAO_NUMEROMOV,
            liberacao.DATAEMISSAO   AS LIBERACAO_DATAEMISSAO,
            liberacao.STATUS        AS LIBERACAO_STATUS,

            nota.IDMOV              AS NOTA_IDMOV,
            nota.NUMEROMOV          AS NOTA_NUMEROMOV,
            nota.DATAEMISSAO        AS NOTA_DATAEMISSAO,
            nota.STATUS             AS NOTA_STATUS
        FROM os

        OUTER APPLY (
            SELECT TOP 1
                movimento.IDMOV,
                LTRIM(RTRIM(movimento.NUMEROMOV)) AS NUMEROMOV,
                movimento.DATAEMISSAO,
                movimento.STATUS
            FROM dbo.OFMOV relacao WITH (NOLOCK)
            INNER JOIN dbo.TMOV movimento WITH (NOLOCK)
                ON movimento.CODCOLIGADA = relacao.CODCOLIGADA
            AND movimento.IDMOV = relacao.IDMOV
            WHERE relacao.CODCOLIGADA = os.CODCOLIGADA
            AND relacao.IDMOVOS = os.IDMOV
            AND LTRIM(RTRIM(movimento.CODTMV)) = '2.1.06'
            ORDER BY movimento.IDMOV DESC
        ) rm

        OUTER APPLY (
            SELECT TOP 1
                movimento.IDMOV,
                LTRIM(RTRIM(movimento.NUMEROMOV)) AS NUMEROMOV,
                movimento.DATAEMISSAO,
                movimento.STATUS
            FROM dbo.TITMMOVRELAC relacao WITH (NOLOCK)
            INNER JOIN dbo.TMOV movimento WITH (NOLOCK)
                ON movimento.CODCOLIGADA = relacao.CODCOLDESTINO
            AND movimento.IDMOV = relacao.IDMOVDESTINO
            WHERE relacao.CODCOLORIGEM = os.CODCOLIGADA
            AND relacao.CODCOLDESTINO = os.CODCOLIGADA
            AND relacao.IDMOVORIGEM = rm.IDMOV
            AND LTRIM(RTRIM(movimento.CODTMV)) = '2.1.57'
            ORDER BY movimento.IDMOV DESC
        ) realizado

        OUTER APPLY (
            SELECT TOP 1
                movimento.IDMOV,
                LTRIM(RTRIM(movimento.NUMEROMOV)) AS NUMEROMOV,
                movimento.DATAEMISSAO,
                movimento.STATUS
            FROM dbo.TITMMOVRELAC relacao WITH (NOLOCK)
            INNER JOIN dbo.TMOV movimento WITH (NOLOCK)
                ON movimento.CODCOLIGADA = relacao.CODCOLDESTINO
            AND movimento.IDMOV = relacao.IDMOVDESTINO
            WHERE relacao.CODCOLORIGEM = os.CODCOLIGADA
            AND relacao.CODCOLDESTINO = os.CODCOLIGADA
            AND relacao.IDMOVORIGEM = realizado.IDMOV
            AND LTRIM(RTRIM(movimento.CODTMV)) = '2.1.64'
            ORDER BY movimento.IDMOV DESC
        ) aguardando

        OUTER APPLY (
            SELECT TOP 1
                movimento.IDMOV,
                LTRIM(RTRIM(movimento.NUMEROMOV)) AS NUMEROMOV,
                movimento.DATAEMISSAO,
                movimento.STATUS
            FROM dbo.TITMMOVRELAC relacao WITH (NOLOCK)
            INNER JOIN dbo.TMOV movimento WITH (NOLOCK)
                ON movimento.CODCOLIGADA = relacao.CODCOLDESTINO
            AND movimento.IDMOV = relacao.IDMOVDESTINO
            WHERE relacao.CODCOLORIGEM = os.CODCOLIGADA
            AND relacao.CODCOLDESTINO = os.CODCOLIGADA
            AND relacao.IDMOVORIGEM = aguardando.IDMOV
            AND LTRIM(RTRIM(movimento.CODTMV)) = '2.1.65'
            ORDER BY movimento.IDMOV DESC
        ) aprovado

        OUTER APPLY (
            SELECT TOP 1
                movimento.IDMOV,
                LTRIM(RTRIM(movimento.NUMEROMOV)) AS NUMEROMOV,
                movimento.DATAEMISSAO,
                movimento.STATUS
            FROM dbo.TITMMOVRELAC relacao WITH (NOLOCK)
            INNER JOIN dbo.TMOV movimento WITH (NOLOCK)
                ON movimento.CODCOLIGADA = relacao.CODCOLDESTINO
            AND movimento.IDMOV = relacao.IDMOVDESTINO
            WHERE relacao.CODCOLORIGEM = os.CODCOLIGADA
            AND relacao.CODCOLDESTINO = os.CODCOLIGADA
            AND relacao.IDMOVORIGEM = aprovado.IDMOV
            AND LTRIM(RTRIM(movimento.CODTMV)) = '2.1.63'
            ORDER BY movimento.IDMOV DESC
        ) liberacao

        OUTER APPLY (
            SELECT TOP 1
                movimento.IDMOV,
                LTRIM(RTRIM(movimento.NUMEROMOV)) AS NUMEROMOV,
                movimento.DATAEMISSAO,
                movimento.STATUS
            FROM dbo.TITMMOVRELAC relacao WITH (NOLOCK)
            INNER JOIN dbo.TMOV movimento WITH (NOLOCK)
                ON movimento.CODCOLIGADA = relacao.CODCOLDESTINO
            AND movimento.IDMOV = relacao.IDMOVDESTINO
            WHERE relacao.CODCOLORIGEM = os.CODCOLIGADA
            AND relacao.CODCOLDESTINO = os.CODCOLIGADA
            AND LTRIM(RTRIM(movimento.CODTMV)) = '2.2.15'
            AND (
                    relacao.IDMOVORIGEM = liberacao.IDMOV
                    OR relacao.IDMOVORIGEM = aprovado.IDMOV
                )
            ORDER BY
                CASE
                    WHEN liberacao.IDMOV IS NOT NULL
                    AND relacao.IDMOVORIGEM = liberacao.IDMOV THEN 0
                    ELSE 1
                END,
                movimento.IDMOV DESC
        ) nota
        ";

        return $sql;
    }

    private function validarFiltros($coligadaId, $numeroOs)
    {
        if (!in_array($coligadaId, array(1, 2), true)) {
            throw new Exception(
                "Coligada inválida. Selecione VERSATRONIC ou VERSATRONIC CNC."
            );
        }

        if ($numeroOs === "") {
            throw new Exception("Informe o número da Ordem de Serviço.");
        }
    }

    private function montarResultadoTimeline($registro, $coligadaId)
    {
        $configuracoes = $this->getConfiguracoesEtapas();
        $etapas = array();
        $ultimaEtapa = null;
        $notaEncontrada = !empty($registro["NOTA_IDMOV"]);

        foreach ($configuracoes as $configuracao) {
            $prefixo = $configuracao["prefixo"];
            $idMov = $this->valorRegistro($registro, $prefixo . "_IDMOV");
            $encontrada = !empty($idMov);
            $situacao = "pendente";

            if ($encontrada) {
                $situacao = "concluida";
                $ultimaEtapa = $configuracao;
            } elseif ($configuracao["opcional"] && $notaEncontrada) {
                $situacao = "nao_utilizada";
            }

            $etapas[] = array(
                "codigo" => $configuracao["codigo"],
                "titulo" => $configuracao["titulo"],
                "descricao" => $configuracao["descricao"],
                "opcional" => $configuracao["opcional"],
                "encontrada" => $encontrada,
                "situacao" => $situacao,
                "numero_mov" => $this->valorRegistro($registro, $prefixo . "_NUMEROMOV"),
                "data_emissao" => $this->valorRegistro($registro, $prefixo . "_DATAEMISSAO"),
                "status_totvs" => $this->valorRegistro($registro, $prefixo . "_STATUS")
            );
        }

        return array(
            "coligada_nome" => $coligadaId === 1 ? "VERSATRONIC" : "VERSATRONIC CNC",
            "numero_os" => $this->valorRegistro($registro, "OS_NUMEROMOV"),
            "codigo_atual" => $ultimaEtapa ? $ultimaEtapa["codigo"] : "2.1.60",
            "etapa_atual" => $ultimaEtapa ? $ultimaEtapa["titulo"] : "Ordem de Serviço",
            "etapas" => $etapas
        );
    }

    private function getConfiguracoesEtapas()
    {
        return array(
            array(
                "prefixo" => "OS",
                "codigo" => "2.1.60",
                "titulo" => "Ordem de Serviço",
                "descricao" => "2.1.60 - Ordem de Serviço",
                "opcional" => false
            ),
            array(
                "prefixo" => "RM",
                "codigo" => "2.1.06",
                "titulo" => "Orçamento de Venda - RM Officina",
                "descricao" => "2.1.06 - Movimento Orçamento de Venda - RM Officina para 2.1.60 - Ordem de Serviço",
                "opcional" => false
            ),
            array(
                "prefixo" => "REALIZADO",
                "codigo" => "2.1.57",
                "titulo" => "Orçamento de Venda - Realizado",
                "descricao" => "2.1.57 - Movimento Orçamento de Venda - Realizado para 2.1.06 - Orçamento de Venda - RM Officina",
                "opcional" => false
            ),
            array(
                "prefixo" => "AGUARDANDO",
                "codigo" => "2.1.64",
                "titulo" => "Aguardando Aprovação",
                "descricao" => "2.1.64 - Movimento Orçamento de Venda - Aguardando Aprovação para 2.1.57 - Orçamento de Venda - Realizado",
                "opcional" => false
            ),
            array(
                "prefixo" => "APROVADO",
                "codigo" => "2.1.65",
                "titulo" => "Orçamento Aprovado",
                "descricao" => "2.1.65 - Movimento do Orçamento de Venda - Aprovado para 2.1.64 - Movimento de Venda - Aguardando Aprovação",
                "opcional" => false
            ),
            array(
                "prefixo" => "LIBERACAO",
                "codigo" => "2.1.63",
                "titulo" => "Aprovado Aguardando Liberação",
                "descricao" => "2.1.63 - Orçamento de Venda - Aprovado Aguardando Liberação para 2.1.65 - Movimento do Orçamento de Venda - Aprovado",
                "opcional" => true
            ),
            array(
                "prefixo" => "NOTA",
                "codigo" => "2.2.15",
                "titulo" => "Nota Fiscal",
                "descricao" => "2.2.15 - Movimento da Nota Fiscal gerado a partir do orçamento aprovado",
                "opcional" => false
            )
        );
    }

    private function montarTimelineHtml($resultado)
    {
        $etapasHtml = array();
        $totalEtapas = count($resultado["etapas"]);

        foreach ($resultado["etapas"] as $indice => $etapa) {
            $etapasHtml[] = $this->montarEtapaHtml(
                $etapa,
                $indice < ($totalEtapas - 1)
            );
        }

        $css = $this->montarCssTimeline();
        $numeroOs = $this->escapar($resultado["numero_os"]);
        $coligada = $this->escapar($resultado["coligada_nome"]);
        $codigoAtual = $this->escapar($resultado["codigo_atual"]);
        $etapaAtual = $this->escapar($resultado["etapa_atual"]);
        $conteudoEtapas = implode("", $etapasHtml);
        $logoHtml = self::obterLogoHtml();

        $html = "
            {$css}
            <div class='os-card'>
                <div class='os-header'>
                    <div class='os-brand-area'>
                        <div class='os-logo-wrap'>
                            {$logoHtml}
                        </div>

                        <div class='os-header-info'>
                            <div class='os-eyebrow'>ACOMPANHAMENTO DE OS</div>
                            <h3>Ordem de Serviço {$numeroOs}</h3>
                            <div class='os-company'>{$coligada}</div>
                        </div>
                    </div>

                    <div class='os-header-actions'>
                        <div class='os-current'>
                            <span>Movimento mais avançado</span>
                            <strong>{$codigoAtual} - {$etapaAtual}</strong>
                        </div>

                        <button
                            type='button'
                            class='os-export-pdf'
                            onclick='exportarTimelineOsPdf(this)'
                        >
                            <i class='fas fa-file-pdf'></i>
                            <span>Exportar PDF</span>
                        </button>
                    </div>
                </div>

               <div class='os-legend'>
                    <span><i class='dot complete'></i>Movimento gerado</span>
                    <span><i class='dot pending'></i>Movimento ainda não gerado</span>
                    <span><i class='dot skipped'></i>Movimento não utilizado neste fluxo</span>
                </div>

                <div class='os-timeline'>
                    {$conteudoEtapas}
                </div>
            </div>
            ";

        return $html;
    }

    private function montarEtapaHtml($etapa, $temProxima)
    {
        $situacao = $etapa["situacao"];
        $simbolo = "&#8226;";
        $textoSituacao = "Movimento ainda não gerado";

        if ($situacao === "concluida") {
            $simbolo = "&#10003;";
            $textoSituacao = "Movimento gerado";
        } elseif ($situacao === "nao_utilizada") {
            $simbolo = "&#8722;";
            $textoSituacao = "Movimento não utilizado neste fluxo";
        }

        $classeLinha = $temProxima ? " has-next" : "";
        $codigo = $this->escapar($etapa["codigo"]);
        $titulo = $this->escapar($etapa["titulo"]);
        $descricao = $this->escapar($etapa["descricao"]);
        $estado = $this->escapar($textoSituacao);
        $opcionalHtml = "";
        $detalhesHtml = $etapa["encontrada"]
            ? $this->montarDetalhesHtml($etapa)
            : "";

        $html = "
        <div class='os-step {$situacao}{$classeLinha}'>
            <div class='os-marker'>
                <span class='os-marker-symbol'>{$simbolo}</span>
            </div>

            <div class='os-content'>
                <div class='os-topline'>
                    <span class='os-code'>{$codigo}</span>
                    {$opcionalHtml}
                    <span class='os-state'>{$estado}</span>
                </div>

                <h4>{$titulo}</h4>
                <p>{$descricao}</p>
                {$detalhesHtml}
            </div>
        </div>
        ";

        return $html;
    }

    private function montarDetalhesHtml($etapa)
    {
        $numero = !empty($etapa["numero_mov"])
            ? $this->escapar($etapa["numero_mov"])
            : "-";

        $dataEmissao = $this->escapar(
            $this->formatarData($etapa["data_emissao"])
        );

        $status = !empty($etapa["status_totvs"])
            ? $this->escapar($etapa["status_totvs"])
            : "-";

        $html = "
        <div class='os-details'>
            <span><b>Número:</b> {$numero}</span>
            <span><b>Data de emissão:</b> {$dataEmissao}</span>
        </div>
        ";

        return $html;
    }

    private function montarCssTimeline()
    {
        $css = "
        <style>
            .os-card {
                background: #ffffff;
                border: 1px solid #bfdbfe;
                border-radius: 16px;
                box-shadow: 0 12px 32px rgba(30, 64, 175, 0.12);
                overflow: hidden;
            }

            .os-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 24px;
                padding: 26px;
                color: #ffffff;
                background:
                    radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 34%),
                    linear-gradient(135deg, #123b75 0%, #075985 50%, #0891b2 100%);
                border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            }

            .os-brand-area {
                display: flex;
                align-items: center;
                flex: 1;
                min-width: 320px;
                gap: 18px;
            }

            .os-logo-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 76px;
                height: 76px;
                flex: 0 0 76px;
                padding: 8px;
                background: #ffffff;
                border: 1px solid rgba(255, 255, 255, 0.65);
                border-radius: 16px;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.16);
            }

            .os-logo-wrap img {
                display: block;
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }

            .logo-box {
                color: #075985;
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-align: center;
            }

            .os-header-info {
                min-width: 220px;
                flex: 1;
            }

            .os-eyebrow {
                color: #dbeafe;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.12em;
            }

            .os-header h3 {
                margin: 5px 0;
                color: #ffffff;
                font-size: 25px;
                font-weight: 700;
            }

            .os-company {
                color: #bae6fd;
                font-size: 14px;
                font-weight: 500;
            }

            .os-header-actions {
                display: flex;
                align-items: stretch;
                justify-content: flex-end;
                flex-wrap: wrap;
                gap: 12px;
            }

            .os-export-pdf {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 48px;
                padding: 0 18px;
                color: #0f4c81;
                background: #ffffff;
                border: 1px solid rgba(255, 255, 255, 0.7);
                border-radius: 12px;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.12);
                cursor: pointer;
                font-size: 13px;
                font-weight: 700;
                transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
            }

            .os-export-pdf:hover {
                color: #0f4c81;
                background: #eff6ff;
                box-shadow: 0 10px 26px rgba(15, 23, 42, 0.18);
                transform: translateY(-1px);
            }

            .os-export-pdf:focus {
                outline: 2px solid #bfdbfe;
                outline-offset: 2px;
            }

            .os-export-pdf i {
                color: #dc2626;
                font-size: 16px;
            }

            .os-current {
                min-width: 290px;
                padding: 15px 17px;
                background: rgba(255, 255, 255, 0.13);
                border: 1px solid rgba(255, 255, 255, 0.28);
                border-radius: 12px;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.12);
                backdrop-filter: blur(4px);
            }

            .os-current span {
                display: block;
                margin-bottom: 5px;
                color: #dbeafe;
                font-size: 12px;
            }

            .os-current strong {
                display: block;
                color: #ffffff;
                font-size: 14px;
            }

            .os-legend {
                display: flex;
                flex-wrap: wrap;
                gap: 18px;
                padding: 14px 24px;
                color: #334155;
                background: #eff6ff;
                border-bottom: 1px solid #dbeafe;
                font-size: 12px;
            }

            .os-legend span {
                display: flex;
                align-items: center;
            }

            .dot {
                width: 9px;
                height: 9px;
                margin-right: 6px;
                border-radius: 50%;
            }

            .dot.complete {
                background: #1d4ed8;
            }

            .dot.pending {
                background: #93c5fd;
            }

            .dot.skipped {
                background: #94a3b8;
            }

            .os-timeline {
                padding: 24px;
                background: #f8fbff;
            }

            .os-step {
                position: relative;
                display: grid;
                grid-template-columns: 44px minmax(0, 1fr);
                gap: 14px;
                min-height: 122px;
            }

            .os-step.has-next::before {
                content: '';
                position: absolute;
                top: 42px;
                bottom: -4px;
                left: 20px;
                width: 2px;
                background: #bfdbfe;
            }

            .os-step.concluida.has-next::before {
                background: #60a5fa;
            }

            .os-marker {
                position: relative;
                z-index: 2;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border: 2px solid #bfdbfe;
                border-radius: 50%;
                background: #f8fbff;
                color: #60a5fa;
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
            }

            .os-marker-symbol {
                display: block;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 20px;
                font-weight: 900;
                line-height: 1;
            }

            .os-step.concluida .os-marker {
                background: #dbeafe;
                border-color: #2563eb;
                color: #1d4ed8;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.22);
            }

            .os-step.concluida .os-marker-symbol {
                font-size: 22px;
            }

            .os-step.nao_utilizada .os-marker {
                background: #f8fafc;
                border-color: #94a3b8;
                border-style: dashed;
                color: #64748b;
                box-shadow: none;
            }

            .os-content {
                margin-bottom: 18px;
                padding: 16px 18px;
                background: #ffffff;
                border: 1px solid #dbeafe;
                border-radius: 12px;
                box-shadow: 0 5px 16px rgba(30, 64, 175, 0.05);
            }

            .os-step.concluida .os-content {
                background: #eff6ff;
                border-color: #93c5fd;
                box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
            }

            .os-step.nao_utilizada .os-content {
                background: #f8fafc;
                border-color: #e2e8f0;
                box-shadow: none;
            }

            .os-topline {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
            }

            .os-code {
                padding: 3px 8px;
                background: #dbeafe;
                border-radius: 999px;
                color: #1d4ed8;
                font-size: 12px;
                font-weight: 700;
            }

            .os-step.concluida .os-code {
                background: #bfdbfe;
                color: #1e40af;
            }

            .os-step.nao_utilizada .os-code {
                background: #e2e8f0;
                color: #475569;
            }

            .os-state {
                margin-left: auto;
                color: #2563eb;
                font-size: 12px;
                font-weight: 600;
            }

            .os-step.concluida .os-state {
                color: #1d4ed8;
            }

            .os-step.nao_utilizada .os-state {
                color: #64748b;
            }

            .os-content h4 {
                margin: 9px 0 5px;
                color: #0f172a;
                font-size: 16px;
                font-weight: 700;
            }

            .os-content p {
                margin: 0;
                color: #64748b;
                font-size: 13px;
                line-height: 1.5;
            }

            .os-details {
                display: flex;
                flex-wrap: wrap;
                gap: 8px 20px;
                margin-top: 12px;
                padding-top: 11px;
                border-top: 1px solid #dbeafe;
                color: #475569;
                font-size: 12px;
            }

            .os-step.concluida .os-details {
                border-top-color: #bfdbfe;
            }

            @media (max-width: 768px) {
                .os-header {
                    padding: 22px 18px;
                }

                .os-brand-area {
                    width: 100%;
                    min-width: 0;
                }

                .os-logo-wrap {
                    width: 62px;
                    height: 62px;
                    flex-basis: 62px;
                }

                .os-header-actions {
                    width: 100%;
                }

                .os-current {
                    width: 100%;
                    min-width: 100%;
                }

                .os-export-pdf {
                    width: 100%;
                }

                .os-state {
                    width: 100%;
                    margin-left: 0;
                }

                .os-timeline {
                    padding: 16px;
                }
            }
        </style>

        ";

        return $css;
    }

    private function montarScriptExportacaoPdf()
    {
        $script = "
        window.exportarTimelineOsPdf = function (botao) {
            var card = botao.closest('.os-card');

            if (!card) {
                alert('Não foi possível localizar a timeline para exportação.');
                return;
            }

            var copia = card.cloneNode(true);
            var botaoCopia = copia.querySelector('.os-export-pdf');

            if (botaoCopia) {
                botaoCopia.remove();
            }

            var iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.left = '-10000px';
            iframe.style.top = '0';
            iframe.style.width = '1px';
            iframe.style.height = '1px';
            iframe.style.border = '0';

            document.body.appendChild(iframe);

            var documento = iframe.contentWindow.document;
            var estilos = '';
            var folhasEstilo = document.querySelectorAll('style');

            for (var i = 0; i < folhasEstilo.length; i++) {
                if (folhasEstilo[i].textContent.indexOf('.os-card') !== -1) {
                    estilos += folhasEstilo[i].outerHTML;
                }
            }

            documento.open();
            documento.write(
                '<!DOCTYPE html>' +
                '<html>' +
                '<head>' +
                    '<meta charset=UTF-8>' +
                    '<title> </title>' +
                    estilos +
                    '<style>' +
                        '@page { size: A4 portrait; margin: 10mm; }' +
                        'html, body { margin: 0; padding: 0; background: #ffffff; }' +
                        'body { font-family: Arial, Helvetica, sans-serif; -webkit-print-color-adjust: exact; print-color-adjust: exact; }' +
                        '.os-card { box-shadow: none !important; }' +
                        '.os-step { break-inside: avoid; page-break-inside: avoid; }' +
                        '.os-header-actions { display: block !important; }' +
                        '.os-current { width: auto !important; min-width: 0 !important; }' +
                        '.os-logo-wrap { background: #ffffff !important; }' +
                        '.os-logo-wrap img { display: block !important; }' +
                        '.os-marker-symbol { font-family: Arial, Helvetica, sans-serif !important; }' +
                        '.os-export-pdf { display: none !important; }' +
                    '</style>' +
                '</head>' +
                '<body>' + copia.outerHTML + '</body>' +
                '</html>'
            );
            documento.close();

            var removerIframe = function () {
                if (iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }
            };

            iframe.contentWindow.onafterprint = removerIframe;

            var imprimir = function () {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                setTimeout(removerIframe, 1500);
            };

            var imagens = documento.images;
            var imagensPendentes = 0;

            for (var j = 0; j < imagens.length; j++) {
                if (!imagens[j].complete) {
                    imagensPendentes++;

                    imagens[j].onload = imagens[j].onerror = function () {
                        imagensPendentes--;

                        if (imagensPendentes === 0) {
                            setTimeout(imprimir, 150);
                        }
                    };
                }
            }

            if (imagensPendentes === 0) {
                setTimeout(imprimir, 300);
            }
        };
        ";

        return $script;
    }

    private function montarMensagemNaoEncontradoHtml($numeroOs, $coligadaId)
    {
        $numeroOs = $this->escapar($numeroOs);
        $coligada = $coligadaId === 1 ? "VERSATRONIC" : "VERSATRONIC CNC";

        $html = "
        <style>
            .os-not-found {
                display: flex;
                align-items: flex-start;
                gap: 14px;
                padding: 20px;
                background: #fff7ed;
                border: 1px solid #fdba74;
                border-radius: 12px;
                color: #9a3412;
                box-shadow: 0 6px 18px rgba(154, 52, 18, 0.08);
            }

            .os-not-found-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                flex: 0 0 42px;
                border-radius: 50%;
                background: #ffedd5;
                color: #ea580c;
                font-size: 18px;
            }

            .os-not-found h4 {
                margin: 0 0 6px;
                color: #9a3412;
                font-size: 16px;
                font-weight: 700;
            }

            .os-not-found p {
                margin: 0;
                color: #c2410c;
                font-size: 13px;
                line-height: 1.55;
            }
        </style>

        <div class='os-not-found'>
            <div class='os-not-found-icon'>
                <i class='fas fa-search'></i>
            </div>

            <div>
                <h4>Ordem de Serviço não encontrada</h4>
                <p>
                    Não foi localizada uma OS
                    <strong>{$numeroOs}</strong> na coligada
                    <strong>{$coligada}</strong>.
                    Informe o número da Ordem de Serviço.
                </p>
            </div>
        </div>
        ";

        return $html;
    }

    private function exibirResultadoHtml($html)
    {
        $htmlJson = json_encode(
            $html,
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_APOS
            | JSON_HEX_AMP
            | JSON_HEX_QUOT
        );

        TScript::create(
            "var resultado = document.getElementById('resultado_timeline_os');"
            . "if (resultado) { resultado.innerHTML = " . $htmlJson . "; }"
        );
    }

    private function criarElementoHtml($html)
    {
        $elemento = new TElement("div");
        $elemento->add($html);

        return $elemento;
    }

    private static function obterLogoHtml()
    {
        $caminhos = array(
            "app/images/logo_versatronic.png",
            "app/images/versatronic.png",
            "app/images/logo.png"
        );

        foreach ($caminhos as $caminho) {
            if (is_file($caminho)) {
                $extensao = strtolower(pathinfo($caminho, PATHINFO_EXTENSION));
                $mime = "image/png";

                if ($extensao === "jpg" || $extensao === "jpeg") {
                    $mime = "image/jpeg";
                } elseif ($extensao === "gif") {
                    $mime = "image/gif";
                } elseif ($extensao === "svg") {
                    $mime = "image/svg+xml";
                } elseif ($extensao === "webp") {
                    $mime = "image/webp";
                }

                $conteudo = base64_encode(file_get_contents($caminho));

                return "<img src='data:{$mime};base64,{$conteudo}' alt='Versatronic'>";
            }
        }

        return "<div class='logo-box'>VERSATRONIC</div>";
    }

    private function formatarData($data)
    {
        if (empty($data)) {
            return "-";
        }

        if ($data instanceof DateTimeInterface) {
            return $data->format("d/m/Y H:i");
        }

        try {
            return (new DateTime((string) $data))->format("d/m/Y H:i");
        } catch (Exception $e) {
            return (string) $data;
        }
    }

    private function valorRegistro($registro, $campo)
    {
        if (is_array($registro) && array_key_exists($campo, $registro)) {
            return $registro[$campo];
        }

        return null;
    }

    private function escapar($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
    }
}
