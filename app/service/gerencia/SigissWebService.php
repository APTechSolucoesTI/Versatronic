<?php

class SigissWebService
{
    private static $dbAp = 'minicrm';
    private static $dbRm = 'corporerm';
    private static $url = 'https://wssantabarbara.sigissweb.com/rest/';

    private const CENTROS_CUSTO_COMISSAO = [
        '002.03.001',
        '002.03.002',
        '002.04.001',
        '002.04.002',
    ];

    // --------------------- SERVIDOR DE HOMOLOGAÇÃO ---------------------
    //private static $url = 'https://wshml.sigissweb.com/rest/';
    //private static $senhaReforma = 'FTKf97Xsd';
    //private static $senhaVersatronic ='d4wQkK@KG';

    // Método principal que executa todo o fluxo
    public static function buscarNota($numero, $coligada_id){
        try {
            // Executa todos os passos do processo
            $resultGetNota = self::getNota($numero, $coligada_id);
            if ($resultGetNota['status'] === 'error') {
                return $resultGetNota;
            }

            $resultInserir = self::inserirMovimentacaoXml($numero, $coligada_id);
            if ($resultInserir['status'] === 'error') {
                return $resultInserir;
            }

            $resultRps = self::obtemXmlRps($numero, $coligada_id);
            if ($resultRps['status'] === 'error') {
                return $resultRps;
            }

            $resultNf = self::obtemXmlNf($numero, $coligada_id);
            if ($resultNf['status'] === 'error') {
                return $resultNf;
            }

            $resultPdf = self::obterPdf($numero, $coligada_id);
            if ($resultPdf['status'] === 'error') {
                return $resultPdf;
            }

            // Atualiza a data de emissão da OS das notas pendentes
            $resultDataEmissaoOs = self::atualizarDataEmissaoOsNotaBaixada();

            if ($resultDataEmissaoOs['status'] === 'error') {
                LogCrontab::registrarLog(
                    __CLASS__,
                    __METHOD__,
                    1,
                    'Nota emitida, mas houve erro ao atualizar a data de emissão da OS: ' .
                    $resultDataEmissaoOs['mensagem'],
                    "Arquivo: SigissWeb.<br/>Linha: " . __LINE__ . "."
                );
            }

            // Busca a data de emissão da nota recém-gerada
            $numeroNormalizado = self::normalizarNumeroNota($numero);

            TTransaction::open(self::$dbAp);

            $notaGerada = NotaBaixada::where('numero', '=', $numeroNormalizado)
                ->where('coligada_id', '=', $coligada_id)
                ->first();

            TTransaction::close();

            if ($notaGerada && !empty($notaGerada->data_emissao)) {
                $dataEmissao = date('Y-m-d', strtotime($notaGerada->data_emissao));

                $resultComissao = self::atualizarComissaoNotasBaixadasPorPeriodo(
                    $dataEmissao,
                    $dataEmissao
                );

                if ($resultComissao['status'] === 'error') {
                    LogCrontab::registrarLog(
                        __CLASS__,
                        __METHOD__,
                        1,
                        'Nota emitida, mas houve erro ao calcular a comissão: ' .
                        $resultComissao['mensagem'],
                        "Arquivo: SigissWeb.<br/>Linha: " . __LINE__ . "."
                    );
                }
            }

            //Registro de log de execução
            LogCrontab::registrarLog(__CLASS__, __METHOD__, 0, "Nota $numero processada com sucesso", "Arquivo: SigissWeb.<br/>Linha: " . __LINE__ . ".");

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'Nota processada com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,$e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine());

            return [
                'status' => 'error',
                'numero' => $numero,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    private static function configurarSslCurl($ch)
    {
        curl_setopt(
            $ch,
            CURLOPT_CAINFO,
            '/etc/ssl/certs/sigiss-ca-bundle.pem'
        );

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }

    public static function getNota($numero, $coligada_id){
        try {
            
            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

            TTransaction::open(self::$dbAp);
            $coligada = Coligada::find($coligada_id);
            if (NotaBaixada::where('numero', '=', $numero)->where('coligada_id', '=', $coligada->id)->count() > 0) {
                TTransaction::close();
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "NFSe {$numero} da coligada {$coligada->nome} já gerada."
                ];
            }
            TTransaction::close();

            $sqlNota ="SELECT
                        m.numeromov as numero,
                        m.dataemissao as data_emissao,
                        f.CGCCFO as cnpj_cpf,
                        f.INSCRMUNICIPAL as inscricao_municipal,
                        f.INSCRESTADUAL as inscricao_estadual,
                        f.PESSOAFISOUJUR as pessoa,
                        f.NOME as razao_social,
                        dr.descricao as tiporua,
                        db.DESCRICAO as tipobairro,
                        dr.DESCRICAO + ' - ' + f.RUAPGTO as endereco,
                        db.DESCRICAO + ' - ' + f.BAIRROPGTO as bairro,
                        f.NUMEROPGTO as numero_end,
                        f.COMPLEMENTOPGTO as complementopgto,
                        G.NOMEMUNICIPIO AS cidadepgto,
                        f.CODETDPGTO as codetdpgto,
                        f.CEPPGTO as ceppgto,
                        f.PAISPAGTO as paispagto,
                        f.TELEFONEPGTO as telefonepgto,
                        f.EMAILPGTO as emailpgto,
                        m.VALORLIQUIDO as valor_total,
                        p.nome as forma_pagamento, 
						(CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 0 ELSE 1 END ) as retido, 
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO ELSE 0 END ) as base_csll,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO ELSE m.VALORLIQUIDO END ) as base_cofins,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO ELSE m.VALORLIQUIDO END ) as base_pis,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 4.65 ELSE 0 END ) as aliquota_csll,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 3.00 ELSE 3.00 END ) as aliquota_cofins,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 0.65 ELSE 0.65 END ) as aliquota_pis,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO * 0.0465 ELSE 0 END ) as csll, 
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO * 0.03 ELSE m.VALORLIQUIDO * 0.03 END ) as cofins, 
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO * 0.0065 ELSE m.VALORLIQUIDO * 0.0065 END ) as pis,
                        (m.VALORLIQUIDO * 0.02) as iss, 
                        (SELECT TOP 1
                            REPLACE(REPLACE(CAST(i.historicolongo AS varchar(2000)), CHAR(13), '|'), CHAR(10), '')
                    FROM TITMMOVHISTORICO i (NOLOCK)
                    WHERE i.codcoligada = m.codcoligada
                    AND i.idmov      = m.idmov
                    ORDER BY i.nseqitmmov DESC
                    )
                    + '|'
                    + ISNULL(
                        (SELECT STRING_AGG(
                                    'FATURA / DUPLICATA: ' + l.NUMERODOCUMENTO
                                    + ' - VALOR : ' + FORMAT(
                                            (l.VALORORIGINAL - l.valorop1 - l.valorop2 - l.valorop3 - ISNULL(tt.valor_retido, 0)),
                                            'C', 'pt-br'
                                    )
                                    + ' - DATA DE VENCIMENTO : ' + CONVERT(varchar(30), l.DATAVENCIMENTO, 103),
                                    '|'
                            )
                        FROM flan l (NOLOCK)
                        LEFT JOIN (
                                SELECT
                                    t.codcoligada,
                                    t.idlan,
                                    SUM(CASE WHEN t.CODTRB = 'RET' THEN t.valor ELSE 0 END) AS valor_retido
                                FROM FTRBLAN t (NOLOCK)
                                GROUP BY
                                    t.codcoligada,
                                    t.idlan
                            ) tt
                                ON tt.codcoligada = l.codcoligada
                            AND tt.idlan       = l.idlan
                        WHERE l.codcoligada = m.codcoligada
                        AND l.idmov      = m.idmov
                        ),
                        ''
                    ) AS descricao
                    FROM
                        tmov m (nolock)
                        INNER JOIN fcfo f (nolock) ON f.codcfo = m.codcfo
                        LEFT JOIN DTIPORUA dr (nolock) ON dr.codigo = f.TIPORUAPGTO
                        LEFT JOIN DTIPOBAIRRO db (nolock) ON db.codigo = f.TIPOBAIRROPGTO
                        LEFT JOIN TCPG P (NOLOCK) ON m.codcpg = p.CODCPG AND p.CODCOLIGADA = m.CODCOLIGADA
                        LEFT JOIN GMUNICIPIO G (NOLOCK) ON G.CODETDMUNICIPIO = F.CODETDPGTO AND G.CODMUNICIPIO = F.CODMUNICIPIOPGTO
                    WHERE
                        m.codtmv = '2.2.15'
                        AND m.codcoligada = $coligada_id
                        AND m.numeromov = '$numero'
                    ORDER BY
                        m.numeromov";

            TTransaction::open(self::$dbRm);
            $conn = TTransaction::get();
            $result = $conn->query($sqlNota);
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();

            if (empty($objects)) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Nenhum registro encontrado para a nota {$numero}"
                ];
            }

            $dados = $objects[0];
            
            if(empty($dados->tiporua)){
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Campo tipo rua não informado para nota $numero"
                ];
            }
            if(empty($dados->tipobairro)){
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Campo tipo bairro não informado para nota $numero"
                ];
            }
            if(empty($dados->forma_pagamento)){
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Campo forma de pagamento não informado para nota $numero"
                ];
            }
            if(empty($dados->cidadepgto)){
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Campo cidade de pagamento não informado para nota $numero"
                ];
            }
            
            $xml = new DOMDocument("1.0", "ISO-8859-1");
            $xml->formatOutput = true;

            $root = $xml->createElement("notafiscal_lote");
            $nf   = $xml->createElement("notafiscal");

            $docDestinatario = preg_replace('/\D/', '', $dados->cnpj_cpf);
            $pessoaDestinatario = $dados->pessoa; // J ou F

            

            $campos = [

                "cnpj_cpf_prestador"        => $coligada->cnpj,
                "exterior_dest"             => "0",
                "cnpj_cpf_destinatario"     => $docDestinatario,
                "pessoa_destinatario"       => $dados->pessoa,
                "ie_destinatario"           => $dados->inscricao_estadual,
                "im_destinatario"           => $dados->inscricao_municipal,
                "razao_social_destinatario" => $dados->razao_social,
                "endereco_destinatario"     => $dados->endereco,
                "numero_ende_destinatario"  => $dados->numero_end,
                "complemento_ende_destinatario" => $dados->complementopgto,
                "bairro_destinatario"       => $dados->bairro,
                "cep_destinatario"          => preg_replace('/\D/', '', $dados->ceppgto),
                "cidade_destinatario"       => $dados->cidadepgto,
                "uf_destinatario"           => $dados->codetdpgto,
                "pais_destinatario"         => $dados->paispagto,
                "fone_destinatario"         => $dados->telefonepgto,
                "email_destinatario"        => $dados->emailpgto,
                "valor_nf"                  => number_format((float) $dados->valor_total, 2, ',', ''),
                "deducao"                   => "0",
                "valor_servico"             => number_format((float) $dados->valor_total, 2, ',', ''),
                "data_emissao"              => date("d/m/Y", strtotime($dados->data_emissao)),
                "forma_de_pagamento"        => $dados->forma_pagamento,
                "descricao"                 => $dados->descricao,
                "id_codigo_servico"         => "14.01.01",
                "cancelada"                 => "N",
                "iss_retido"                => "N",
                "aliq_iss"                  => "2",
                "valor_iss"                 => number_format((float) ($dados->iss ?? 0), 2, ',', ''),
                "bc_pis"                    => number_format((float) ($dados->base_pis ?? 0), 2, ',', ''),
                "aliq_pis"                  => number_format((float) ($dados->aliquota_pis ?? 0), 2, ',', ''),
                "valor_pis"                 => number_format((float) ($dados->pis ?? 0), 2, ',', ''),
                "bc_cofins"                 => number_format((float) ($dados->base_cofins ?? 0), 2, ',', ''),
                "aliq_cofins"               => number_format((float) ($dados->aliquota_cofins ?? 0), 2, ',', ''),
                "valor_cofins"              => number_format((float) ($dados->cofins ?? 0), 2, ',', ''),
                "bc_csll"                   => number_format((float) ($dados->base_csll ?? 0), 2, ',', ''),
                "aliq_csll"                 => number_format((float) ($dados->aliquota_csll ?? 0), 2, ',', ''),
                "valor_csll"                => number_format((float) ($dados->csll ?? 0), 2, ',', ''),
                "bc_irrf"                   => "0",
                "aliq_irrf"                 => "0",
                "valor_irrf"                => "0",
                "bc_inss"                   => "0",
                "aliq_inss"                 => "0",
                "valor_inss"                => "0",
                "sistema_gerador"           => "TOTVS RM",
                "serie_rps"                 => 1,
                "rps"                       => (int) ltrim($dados->numero, '0'),
                "codigo_nbs"                => "1.2001.50.00",
                "exterior_prestacao_servico"=> "0",
                "pais_local_prest"          => "Brasil",
                "cidade_local_prest"        => "Santa Barbara D'Oeste",
                "uf_local_prest"            => "SP",

                // ----------------------
                // REFORMA TRIBUTÁRIA CBS/IBS
                // ----------------------
                "c_classtrib"               => "000001",
                "ind_op"                    => "050101",
                "exterior_op"               => "0",
                "uf_local_op"               => "SP",
                "cidade_local_op"           => "Santa Barbara D'Oeste",
                "consumo_pessoal"           => "0",

                // bloco destinatário CBS/IBS
                "pessoa_destinatario_cbsibs"       => $pessoaDestinatario,
                "cnpj_cpf_destinatario_cbsibs"     => $docDestinatario,
                "ie_destinatario_cbsibs"           => $dados->inscricao_estadual,
                "im_destinatario_cbsibs"           => $dados->inscricao_municipal,
                "razao_social_destinatario_cbsibs" => $dados->razao_social,
                "endereco_destinatario_cbsibs"     => $dados->endereco,
                "numero_ende_destinatario_cbsibs"  => $dados->numero_end,
                "complemento_ende_destinatario_cbsibs" => $dados->complementopgto,
                "bairro_destinatario_cbsibs"       => $dados->bairro,
                "cep_destinatario_cbsibs"          => preg_replace('/\D/', '', $dados->ceppgto),
                "cidade_destinatario_cbsibs"       => $dados->cidadepgto,
                "uf_destinatario_cbsibs"           => $dados->codetdpgto,
                "pais_destinatario_cbsibs"         => $dados->paispagto,
                "email_destinatario_cbsibs"        => $dados->emailpgto,
                "n_retencao_piscofins"             => $dados->retido,

            ];
            
            foreach ($campos as $tag => $valor) {
                $el = $xml->createElement($tag, htmlspecialchars($valor));
                $nf->appendChild($el);
            }

            $root->appendChild($nf);
            $xml->appendChild($root);

            $numero = preg_replace('/\D/', '', $dados->numero);
            $coligada_pasta = str_replace(' ', '_', $coligada->nome);
            $arquivoPath = realpath('.') . "/files/notas/{$coligada_pasta}/totvs/{$numero}.xml";
            $xml->save($arquivoPath);

            if (!file_exists($arquivoPath)) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Erro ao salvar XML {$numero} da coligada {$coligada->nome}."
                ];
            } else {
                TTransaction::open(self::$dbAp);
                $nota = new NotaBaixada();
                $nota->coligada_id    = $coligada_id;
                $nota->nota_status_id = NotaStatus::ERRO;
                $nota->numero         = $numero;
                $nota->totvs_xml      = $arquivoPath;
                $nota->data_emissao   = $dados->data_emissao;
                $nota->razao_social   = $dados->razao_social;
                $nota->documento      = preg_replace('/\D/', '', $dados->cnpj_cpf);
                $nota->valor_total    = $dados->valor_total;
                $nota->enviado_email  = 0;
                $nota->store();
                TTransaction::close();

                return [
                    'status' => 'success',
                    'numero' => $numero,
                    'mensagem' => 'XML gerado com sucesso'
                ];
            }
        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,$e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine());

            return [
                'status' => 'error',
                'numero' => $numero,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function login($coligada_id){
        try {
            TTransaction::open(self::$dbAp);
            $coligada = Coligada::find($coligada_id);
            TTransaction::close();
            $url = self::$url . 'login';

            $data = [
                "login" => $coligada->cnpj,
                "senha" => $coligada->senha
                //"senha" => self::$senhaVersatronic
                
            ];

            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            // Inicializa o cURL
            $ch = curl_init($url);
            self::configurarSslCurl($ch);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json; charset=utf-8"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode == 200) {
                return $resposta;
            } else {
                throw new Exception("Erro no login. Resposta: {$resposta}.{$httpCode}{$curlError}");
            }
        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,$e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");
            throw $e; // Re-lança a exceção para ser tratada pelo método chamador
        }
    }

    public static function inserirMovimentacaoXml($numero, $coligada_id){
        try {
            $token = self::login($coligada_id);
            if (!$token) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Erro no login. Token não obtido.'
                ];
            }

            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);
            TTransaction::open(self::$dbAp);
            $nota = NotaBaixada::where('numero', '=', $numero)->where('coligada_id', '=', $coligada_id)->first();
            TTransaction::close();

            if (!$nota) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Nota não encontrada na base de dados.'
                ];
            }

            $caminhoArquivo = $nota->totvs_xml;

            if (!file_exists($caminhoArquivo)) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Arquivo não encontrado: {$caminhoArquivo}"
                ];
            }

            $conteudo = file_get_contents($caminhoArquivo);
            $conteudo = str_replace(['<notafiscal_lote>', '</notafiscal_lote>'], '', $conteudo);

            $endpoint = self::$url . 'nfes';

            $headers = [
                "Authorization: {$token}",
                "Content-Type: application/xml;"
            ];

            $ch = curl_init($endpoint);
            self::configurarSslCurl($ch);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $conteudo);

            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200 && strpos($resposta, 'ja foi importado anteriormente') === false) {
                $nota->nota_status_id = NotaStatus::ERRO;
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Erro ao emitir NFSe. Resposta: {$resposta}.{$httpCode}{$curlError}"
                ];
            }
            TTransaction::open(self::$dbAp);
            $nota->nota_status_id = NotaStatus::AUTORIZADA;
            $nota->store();
            TTransaction::close();

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'NFS emitida com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,"Exception: " . $e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");

            return [
                'status' => 'error',
                'numero' => $numero,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function obtemXmlRps($numero, $coligada_id){
        try {
            $token = self::login($coligada_id);
            if (!$token) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Erro no login. Token não obtido.'
                ];
            }

            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

            TTransaction::open(self::$dbAp);
            $nota = NotaBaixada::where('numero', '=', $numero)
                ->where('coligada_id', '=', $coligada_id)
                ->first();

            if (!$nota) {
                TTransaction::close();
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Nota não encontrada.'
                ];
            }

            $coligada = $nota->get_coligada();
            TTransaction::close();

            $endpoint = self::$url . "nfes/pegaxml/{$numero}/serierps/1";
            $headers = ["Authorization: {$token}"];

            // Executa requisição cURL
            $ch = curl_init($endpoint);
            self::configurarSslCurl($ch);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Trata encoding da resposta para UTF-8 se necessário
            $resposta = mb_convert_encoding($resposta, 'UTF-8', 'auto');

            if ($httpCode !== 200) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Erro ao obter XML RPS. Código: {$httpCode}. cURL: {$curlError}. Resposta: {$resposta}"
                ];
            }

            $coligada_pasta = str_replace(' ', '_', $coligada->nome);
            $nota->rps_xml = realpath('.') . "/files/notas/{$coligada_pasta}/rps/{$numero}.xml";
            file_put_contents($nota->rps_xml, $resposta);

            $xmlString = file_get_contents($nota->rps_xml);
            if ($xmlString === false) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Falha ao ler o arquivo XML em {$nota->rps_xml}"
                ];
            }

            $xml = simplexml_load_string($xmlString);
            if ($xml === false) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'XML inválido ou mal formatado'
                ];
            }

            if (
                preg_match('/<numero_nf>(.*?)<\/numero_nf>/', $xmlString, $mNum) &&
                preg_match('/<serie>(.*?)<\/serie>/', $xmlString, $mSer)
            ) {
                $nota->numero_nf = $mNum[1];
                $nota->serie_nf = $mSer[1];
            } else {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Tags <numero_nf> ou <serie> não encontradas no XML'
                ];
            }

            TTransaction::open(self::$dbAp);
            $nota->store();
            TTransaction::close();

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'XML RPS obtido com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,"Exception: " . $e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");

            return [
                'status' => 'error',
                'numero' => $numero,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function obtemXmlNf($numero, $coligada_id){
        try {
            $token = self::login($coligada_id);
            if (!$token) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Erro no login. Token não obtido.'
                ];
            }

            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

            TTransaction::open(self::$dbAp);
            $nota = NotaBaixada::where('numero', '=', $numero)
                ->where('coligada_id', '=', $coligada_id)
                ->first();

            if (!$nota) {
                TTransaction::close();
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Nota não encontrada.'
                ];
            }

            $coligada = $nota->get_coligada();
            TTransaction::close();

            $endpoint = self::$url . "nfes/pegaxmlpelonumeronf/{$nota->numero_nf}/serienf/{$nota->serie_nf}";
            $headers = ["Authorization: {$token}"];

            // Executa requisição cURL
            $ch = curl_init($endpoint);
            self::configurarSslCurl($ch);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Trata encoding da resposta para UTF-8 se necessário
            $resposta = mb_convert_encoding($resposta, 'UTF-8', 'auto');

            if ($httpCode !== 200) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Erro ao obter XML NF. Endpoint: {$endpoint}. Código: {$httpCode}. cURL: {$curlError}. Resposta: {$resposta}"
                ];
            }

            $coligada_pasta = str_replace(' ', '_', $coligada->nome);
            $nota->nfs_xml = realpath('.') . "/files/notas/{$coligada_pasta}/nfs/{$numero}.xml";
            file_put_contents($nota->nfs_xml, $resposta);

            $xmlString = file_get_contents($nota->nfs_xml);
            if ($xmlString !== false) {
                $xml = simplexml_load_string($xmlString);
                if ($xml !== false) {
                    if (preg_match('/<cancelada>(.*?)<\/cancelada>/', $xmlString, $canc)) {
                        $nota->nota_status_id = ($canc[1] === 'S') ? NotaStatus::CANCELADA : NotaStatus::AUTORIZADA;
                    }
                }
            }

            TTransaction::open(self::$dbAp);
            $nota->store();
            TTransaction::close();

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'XML NFS obtido com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__, __METHOD__, 1, "Exception: " . $e->getMessage(), "Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");
        }
    }

    public static function obterPdf($numero, $coligada_id){
        try {
            $token = self::login($coligada_id);
            if (!$token) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Erro no login. Token não obtido.'
                ];
            }

            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

            TTransaction::open(self::$dbAp);
            $nota = NotaBaixada::where('numero', '=', $numero)
                ->where('coligada_id', '=', $coligada_id)
                ->first();
            if (!$nota) {
                TTransaction::close();
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Nota não encontrada.'
                ];
            }
            $coligada = $nota->get_coligada();
            TTransaction::close();

            $endpoint = self::$url . "nfes/nfimpressa/{$nota->numero_nf}/serie/{$nota->serie_nf}";
            $headers = ["Authorization: {$token}"];

            // Executa requisição cURL
            $ch = curl_init($endpoint);
            self::configurarSslCurl($ch);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Erro ao obter PDF. Código: {$httpCode}. cURL: {$curlError}. Resposta: {$resposta}"
                ];
            }

            $coligada_pasta = str_replace(' ', '_', $coligada->nome);
            $nota->nfs_pdf = realpath('.') . "/files/notas/{$coligada_pasta}/nfs/{$numero}.pdf";
            file_put_contents($nota->nfs_pdf, $resposta);

            TTransaction::open(self::$dbAp);
            $nota->store();
            TTransaction::close();

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'PDF obtido com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,"Exception: " . $e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");

            return [
                'status' => 'error',
                'numero' => $numero,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function enviarEmail($numero, $coligada_id, $copia = 'S'){
        try {
            $token = self::login($coligada_id);
            if (!$token) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Erro no login. Token não obtido.'
                ];
            }

            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

            TTransaction::open(self::$dbAp);
            $nota = NotaBaixada::where('numero', '=', $numero)
                ->where('coligada_id', '=', $coligada_id)
                ->first();
            if (!$nota) {
                TTransaction::close();
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Nota não encontrada.'
                ];
            }
            $coligada = $nota->get_coligada();
            TTransaction::close();

            $endpoint = self::$url . "nfes/envianf/{$nota->numero_nf}/serie/{$nota->serie_nf}/comcopiaprestador/$copia";
            $headers = ["Authorization: {$token}"];

            // Executa requisição cURL
            $ch = curl_init($endpoint);
            self::configurarSslCurl($ch);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => "Erro ao enviar email. Resposta: {$resposta}.{$httpCode}{$curlError}"
                ];
            }
            TTransaction::open(self::$dbAp);
            $nota->enviado_email = 1;
            $nota->store();
            TTransaction::close();

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'Email enviado com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__,__METHOD__,1,$e->getMessage(),"Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");

            return [
                'status' => 'error',
                'numero' => $numero,
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function cancelarNf($numero, $coligada_id, $motivo){
        try {
            
            // Validar se ainda tem conteúdo após sanitização
            if (empty($motivo)) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Motivo inválido ou vazio.'
                ];
            }
            
            $token = self::login($coligada_id);
            if (!$token) {
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Erro no login. Token não obtido.'
                ];
            }

            $numero = str_pad($numero, 6, '0', STR_PAD_LEFT);

            TTransaction::open(self::$dbAp);
            $nota = NotaBaixada::where('numero', '=', $numero)->where('coligada_id', '=', $coligada_id)->first();
            if (!$nota) {
                TTransaction::close();
                return [
                    'status' => 'error',
                    'numero' => $numero,
                    'mensagem' => 'Nota não encontrada.'
                ];
            }
            $coligada = $nota->get_coligada();
            TTransaction::close();

            // URL encode do motivo para evitar problemas na URL
            //$endpoint = self::$url . "nfes/cancela/{$nota->numero_nf}/serie/{$nota->serie_nf}/motivo/{$motivo}";
            $endpoint = self::$url . "/nfes/cancela/{$nota->numero_nf}/serie/{$nota->serie_nf}/motivo/" . urlencode($motivo);

            $headers = ["Authorization: {$token}"];

            // Executa requisição cURL
            $ch = curl_init($endpoint);
            self::configurarSslCurl($ch);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $resposta = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Trata encoding da resposta para UTF-8 se necessário
            $resposta = mb_convert_encoding($resposta, 'UTF-8', 'auto');

            if ($httpCode !== 200) {
                throw new Exception("Erro ao cancelar NF. Resposta: {$resposta}.{$httpCode}{$curlError}");
            }
            
           TTransaction::open(self::$dbAp);

            $nota->nota_status_id = NotaStatus::CANCELADA;
            $nota->tem_comissao   = 'N';
            $nota->comissao       = 0;
            $nota->store();

            /*
            * Se já existir rateio de comissão por centro de custo,
            * também zera para não deixar comissão residual.
            */
            $conn = TTransaction::get();

            $stmt = $conn->prepare("
                UPDATE centro_custo_nota
                SET comissao_centro_custo = 0
                WHERE nota_baixada_id = :nota_id
            ");

            $stmt->execute([
                ':nota_id' => $nota->id
            ]);

            TTransaction::close();

            return [
                'status' => 'success',
                'numero' => $numero,
                'mensagem' => 'NF cancelada com sucesso'
            ];

        } catch (Exception $e) {
            LogCrontab::registrarLog(__CLASS__, __METHOD__, 1, $e->getMessage(), "Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine() . "<br/>");

            return [
                'status' => 'error',
                'numero' => $numero ?? 'N/A',
                'mensagem' => $e->getMessage()
            ];
        }
    }

    // ATUALIZAÇÃO DE DATA EMISSAO OS PARA NOTAS FISCAIS
    
    private static function normalizarNumeroNota($numero)
    {
        $numero = preg_replace('/\D/', '', (string) $numero);
        return str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    private static function normalizarDataBanco($valor)
    {
        if (empty($valor)) {
            return null;
        }

        if ($valor instanceof DateTimeInterface) {
            return $valor->format('Y-m-d H:i:s');
        }

        $time = strtotime((string) $valor);

        if ($time === false) {
            return $valor;
        }

        return date('Y-m-d H:i:s', $time);
    }

    private static function sqlDataEmissaoOs()
    {
        return "
            ;WITH nf AS (
                SELECT TOP 1
                    m.CODCOLIGADA,
                    m.IDMOV
                FROM [dbo].[TMOV] m WITH (NOLOCK)
                WHERE m.CODTMV = '2.2.15'
                AND m.CODCOLIGADA = :codcoligada
                AND m.NUMEROMOV = :numero
            ),

            origem_nf AS (
                SELECT TOP 1
                    n.CODCOLIGADA,
                    r.IDMOVORIGEM AS IDMOV
                FROM nf n
                INNER JOIN [dbo].[TITMMOVRELAC] r WITH (NOLOCK)
                    ON r.CODCOLDESTINO = n.CODCOLIGADA
                AND r.IDMOVDESTINO = n.IDMOV
            ),

            inicio_fluxo AS (
                SELECT
                    o.CODCOLIGADA,
                    CASE
                        WHEN t.CODTMV = '2.1.65' THEN o.IDMOV
                        ELSE rel_anterior.IDMOVORIGEM
                    END AS IDMOV
                FROM origem_nf o
                LEFT JOIN [dbo].[TMOV] t WITH (NOLOCK)
                    ON t.CODCOLIGADA = o.CODCOLIGADA
                AND t.IDMOV = o.IDMOV
                OUTER APPLY (
                    SELECT TOP 1
                        r2.IDMOVORIGEM
                    FROM [dbo].[TITMMOVRELAC] r2 WITH (NOLOCK)
                    WHERE r2.CODCOLDESTINO = o.CODCOLIGADA
                    AND r2.IDMOVDESTINO = o.IDMOV
                ) rel_anterior
            ),

            passo_1 AS (
                SELECT TOP 1
                    i.CODCOLIGADA,
                    r.IDMOVORIGEM AS IDMOV
                FROM inicio_fluxo i
                INNER JOIN [dbo].[TITMMOVRELAC] r WITH (NOLOCK)
                    ON r.CODCOLDESTINO = i.CODCOLIGADA
                AND r.IDMOVDESTINO = i.IDMOV
                WHERE i.IDMOV IS NOT NULL
            ),

            passo_2 AS (
                SELECT TOP 1
                    p.CODCOLIGADA,
                    r.IDMOVORIGEM AS IDMOV
                FROM passo_1 p
                INNER JOIN [dbo].[TITMMOVRELAC] r WITH (NOLOCK)
                    ON r.CODCOLDESTINO = p.CODCOLIGADA
                AND r.IDMOVDESTINO = p.IDMOV
            ),

            passo_3 AS (
                SELECT TOP 1
                    p.CODCOLIGADA,
                    r.IDMOVORIGEM AS IDMOV
                FROM passo_2 p
                INNER JOIN [dbo].[TITMMOVRELAC] r WITH (NOLOCK)
                    ON r.CODCOLDESTINO = p.CODCOLIGADA
                AND r.IDMOVDESTINO = p.IDMOV
            )

            SELECT TOP 1
                mov_os.DATAEMISSAO AS data_emissao_os
            FROM passo_3 p
            INNER JOIN [dbo].[OFMOV] ofm WITH (NOLOCK)
                ON ofm.CODCOLIGADA = p.CODCOLIGADA
            AND ofm.IDMOV = p.IDMOV
            INNER JOIN [dbo].[TMOV] mov_os WITH (NOLOCK)
                ON mov_os.CODCOLIGADA = p.CODCOLIGADA
            AND mov_os.IDMOV = ofm.IDMOVOS
        ";
    }

    private static function executarBuscaDataEmissaoOs($stmt, $numero, $coligada_id)
    {
        $numero = self::normalizarNumeroNota($numero);

        $stmt->bindValue(':codcoligada', (int) $coligada_id, PDO::PARAM_INT);
        $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $stmt->closeCursor();

        if (!$row || empty($row->data_emissao_os)) {
            return null;
        }

        return self::normalizarDataBanco($row->data_emissao_os);
    }

    public static function buscarDataEmissaoOsTotvs($numero, $coligada_id)
    {
        try {
            TTransaction::open(self::$dbRm);

            $conn = TTransaction::get();
            $stmt = $conn->prepare(self::sqlDataEmissaoOs());

            $data = self::executarBuscaDataEmissaoOs($stmt, $numero, $coligada_id);

            TTransaction::close();

            return $data;

        } catch (Exception $e) {
            try {
                TTransaction::rollback();
            } catch (Exception $ignore) {}

            LogCrontab::registrarLog(
                __CLASS__,
                __METHOD__,
                1,
                $e->getMessage(),
                "Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine()
            );

            return null;
        }
    }

    public static function atualizarDataEmissaoOsNotaBaixada()
    {
        $atualizar = [];
        $comDataTotvs = [];
        $comExcecao = [];
        $semData = [];
        $erros = [];

        try {
            TTransaction::open(self::$dbAp);

            $connAp = TTransaction::get();

            $sqlLocal = "
                SELECT
                    id,
                    numero,
                    coligada_id,
                    data_emissao,
                    nota_status_id
                FROM nota_baixada
                WHERE data_emissao_os IS NULL
                AND nota_status_id = 1
                ORDER BY id
            ";

            $notas = $connAp->query($sqlLocal)->fetchAll(PDO::FETCH_OBJ);

            TTransaction::close();

            if (empty($notas)) {
                return [
                    'status' => 'success',
                    'mensagem' => 'Nenhuma nota pendente para atualizar.'
                ];
            }

            TTransaction::open(self::$dbRm);

            $connRm = TTransaction::get();
            $stmtRm = $connRm->prepare(self::sqlDataEmissaoOs());

            foreach ($notas as $nota) {
                try {
                    $dataOs = self::executarBuscaDataEmissaoOs(
                        $stmtRm,
                        $nota->numero,
                        $nota->coligada_id
                    );

                    if ($dataOs) {
                        $comDataTotvs[] = "{$nota->coligada_id}/{$nota->numero}";
                    } else {
                        $dataOs = self::normalizarDataBanco($nota->data_emissao);

                        if ($dataOs) {
                            $comExcecao[] = "{$nota->coligada_id}/{$nota->numero}";
                        } else {
                            $semData[] = "{$nota->coligada_id}/{$nota->numero}";
                            continue;
                        }
                    }

                    $atualizar[] = [
                        'id' => (int) $nota->id,
                        'numero' => $nota->numero,
                        'coligada_id' => $nota->coligada_id,
                        'data_emissao_os' => $dataOs
                    ];

                } catch (Exception $e) {
                    $erros[] = "{$nota->coligada_id}/{$nota->numero}: " . $e->getMessage();
                }
            }

            TTransaction::close();

            if (!empty($atualizar)) {
                TTransaction::open(self::$dbAp);

                $connAp = TTransaction::get();

                $stmtUpdate = $connAp->prepare("
                    UPDATE nota_baixada
                    SET data_emissao_os = :data_emissao_os
                    WHERE id = :id
                ");

                foreach ($atualizar as $item) {
                    $stmtUpdate->bindValue(':data_emissao_os', $item['data_emissao_os']);
                    $stmtUpdate->bindValue(':id', $item['id'], PDO::PARAM_INT);
                    $stmtUpdate->execute();
                }

                TTransaction::close();
            }

            return [
                'status' => 'success',
                'mensagem' =>
                    'Processadas: ' . count($notas) .
                    '<br>Atualizadas: ' . count($atualizar) .
                    '<br>Com data encontrada na TOTVS: ' . count($comDataTotvs) .
                    '<br>Com exceção usando data_emissao da nota: ' . count($comExcecao) .
                    '<br>Sem nenhuma data disponível: ' . count($semData) .
                    '<br>Erros: ' . count($erros)
            ];

        } catch (Exception $e) {
            try {
                TTransaction::rollback();
            } catch (Exception $ignore) {}

            LogCrontab::registrarLog(
                __CLASS__,
                __METHOD__,
                1,
                $e->getMessage(),
                "Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine()
            );

            return [
                'status' => 'error',
                'mensagem' => $e->getMessage()
            ];
        }
    }
    
    // Filtra pelo período de emissão da nota,
    // mas só sincroniza notas que possuem data de emissão da OS preenchida
    
    public static function atualizarComissaoNotasBaixadasPorPeriodo($dataInicial, $dataFinal, $zerarCanceladasErro = true)
    {
        try {
            $dataInicial = self::normalizarDataFiltroComissao($dataInicial);
            $dataFinal   = self::normalizarDataFiltroComissao($dataFinal);

            if ($dataInicial > $dataFinal) {
                throw new Exception('A data inicial não pode ser maior que a data final.');
            }

            TTransaction::open(self::$dbAp);

            $conn = TTransaction::get();

            $canceladasZeradas = 0;

            if ($zerarCanceladasErro) {
                $stmtZerar = $conn->prepare("
                    UPDATE nota_baixada
                    SET tem_comissao = 'N',
                        comissao = 0
                    WHERE nota_status_id IN (2, 3)
                    AND data_emissao_os IS NOT NULL
                    AND tem_comissao IS DISTINCT FROM 'C'
                    AND tem_comissao IS DISTINCT FROM 'N'
                    AND tem_comissao IS DISTINCT FROM 'S'
                    AND tem_comissao IS DISTINCT FROM 'P'
                    AND data_emissao::date BETWEEN :data_inicial AND :data_final
                    AND (
                            tem_comissao IS DISTINCT FROM 'N'
                            OR comissao IS DISTINCT FROM 0
                    )
                ");

                $stmtZerar->bindValue(':data_inicial', $dataInicial);
                $stmtZerar->bindValue(':data_final', $dataFinal);
                $stmtZerar->execute();

                $canceladasZeradas = $stmtZerar->rowCount();
            }

            $sql = "
            WITH notas AS (
                SELECT
                    nb.id AS nota_id,
                    nb.numero,
                    nb.documento,
                    nb.valor_total,
                    nb.data_emissao_os::date + 2 AS data_base
                FROM nota_baixada nb
                WHERE nb.nota_status_id = 1
                AND nb.data_emissao_os IS NOT NULL
                AND nb.tem_comissao IS DISTINCT FROM 'C'
                AND nb.tem_comissao IS DISTINCT FROM 'P'
                AND nb.tem_comissao IS DISTINCT FROM 'N'
                AND nb.tem_comissao IS DISTINCT FROM 'S'
                AND nb.data_emissao::date BETWEEN :data_inicial AND :data_final
            ),

            base AS (
                SELECT
                    n.nota_id,
                    n.numero,
                    n.documento,
                    n.valor_total,
                    n.data_base,

                    p.id AS pessoa_id,
                    p.categoria_cliente_id,
                    cc.nome AS categoria,

                    comp.representante_id

                FROM notas n

                LEFT JOIN LATERAL (
                    SELECT
                        p.*
                    FROM pessoa p
                    WHERE regexp_replace(
                            COALESCE(p.cpf_cnpj, ''),
                            '[^0-9]',
                            '',
                            'g'
                        ) = regexp_replace(
                            COALESCE(n.documento, ''),
                            '[^0-9]',
                            '',
                            'g'
                        )
                    AND p.deleted_at IS NULL
                    ORDER BY p.id DESC
                    LIMIT 1
                ) p ON true

                LEFT JOIN categoria_cliente cc
                    ON cc.id = p.categoria_cliente_id

                LEFT JOIN LATERAL (
                    SELECT
                        c.representante_id
                    FROM complemento c
                    WHERE c.pessoa_id = p.id
                    AND c.deleted_at IS NULL
                    AND c.representante_id IS NOT NULL
                    ORDER BY
                        c.created_at DESC NULLS LAST,
                        c.id DESC
                    LIMIT 1
                ) comp ON true
            ),

            regras AS (
                SELECT
                    b.*,

                    pr1.regras_tipo_atividade_id AS regra_tipo1_id,
                    pr1.dias AS dias_tipo1,

                    pr2.regras_tipo_atividade_id AS regra_tipo2_id,
                    pr2.dias AS dias_tipo2,
                    COALESCE(pr2.ambos, 'N') AS ambos_tipo2

                FROM base b

                LEFT JOIN LATERAL (
                    SELECT
                        pa.regras_tipo_atividade_id,
                        pa.dias
                    FROM prazo_atividade pa
                    INNER JOIN regras_tipo_atividade rta
                        ON rta.id = pa.regras_tipo_atividade_id
                    WHERE pa.categoria_cliente_id = b.categoria_cliente_id
                    AND rta.tipo = 1
                    ORDER BY pa.id DESC
                    LIMIT 1
                ) pr1 ON true

                LEFT JOIN LATERAL (
                    SELECT
                        pa.regras_tipo_atividade_id,
                        pa.dias,
                        pa.ambos
                    FROM prazo_atividade pa
                    INNER JOIN regras_tipo_atividade rta
                        ON rta.id = pa.regras_tipo_atividade_id
                    WHERE pa.categoria_cliente_id = b.categoria_cliente_id
                    AND rta.tipo = 2
                    ORDER BY pa.id DESC
                    LIMIT 1
                ) pr2 ON true
            ),

            atividades AS (
                SELECT
                    r.*,

                    ult1.ultima_atividade AS ultima_tipo1,
                    ult2.ultima_atividade AS ultima_tipo2,
                    ult_tipo1_real.ultima_atividade AS ultima_tipo1_real

                FROM regras r

                LEFT JOIN LATERAL (
                    SELECT
                        MAX(ia.horario_final) AS ultima_atividade
                    FROM interacao i
                    INNER JOIN interacao_atividade ia
                        ON ia.interacao_id = i.id
                    INNER JOIN tipo_atividade ta
                        ON ta.id = ia.tipo_atividade_id
                    WHERE i.cliente_id = r.pessoa_id
                    AND ia.estado_atividade_id = 2
                    AND ia.horario_final IS NOT NULL
                    AND ia.horario_final::date <= r.data_base
                    AND ta.regras_tipo_atividade_id = r.regra_tipo1_id
                ) ult1 ON true

                LEFT JOIN LATERAL (
                    SELECT
                        MAX(ia.horario_final) AS ultima_atividade
                    FROM interacao i
                    INNER JOIN interacao_atividade ia
                        ON ia.interacao_id = i.id
                    INNER JOIN tipo_atividade ta
                        ON ta.id = ia.tipo_atividade_id
                    WHERE i.cliente_id = r.pessoa_id
                    AND ia.estado_atividade_id = 2
                    AND ia.horario_final IS NOT NULL
                    AND ia.horario_final::date <= r.data_base
                    AND ta.regras_tipo_atividade_id = r.regra_tipo2_id
                ) ult2 ON true

                LEFT JOIN LATERAL (
                    SELECT
                        MAX(ia.horario_final) AS ultima_atividade
                    FROM interacao i
                    INNER JOIN interacao_atividade ia
                        ON ia.interacao_id = i.id
                    INNER JOIN tipo_atividade ta
                        ON ta.id = ia.tipo_atividade_id
                    INNER JOIN regras_tipo_atividade rta
                        ON rta.id = ta.regras_tipo_atividade_id
                    WHERE i.cliente_id = r.pessoa_id
                    AND ia.estado_atividade_id = 2
                    AND ia.horario_final IS NOT NULL
                    AND ia.horario_final::date <= r.data_base
                    AND rta.tipo = 1
                ) ult_tipo1_real ON true
            ),

            prazos AS (
                SELECT
                    a.*,

                    CASE
                        WHEN a.pessoa_id IS NULL THEN 0
                        WHEN a.categoria_cliente_id IS NULL THEN 9999

                        WHEN a.categoria IN ('D', 'E')
                        AND a.dias_tipo2 IS NULL
                        THEN 9999

                        WHEN a.categoria IN ('D', 'E')
                        AND a.ultima_tipo1_real IS NULL
                        THEN 0

                        WHEN a.categoria IN ('D', 'E') THEN
                            GREATEST(
                                a.dias_tipo2
                                - (
                                    a.data_base
                                    - a.ultima_tipo1_real::date
                                ),
                                0
                            )

                        WHEN a.regra_tipo1_id IS NULL THEN 9999
                        WHEN a.ultima_tipo1 IS NULL THEN 0

                        ELSE
                            GREATEST(
                                a.dias_tipo1
                                - (
                                    a.data_base
                                    - a.ultima_tipo1::date
                                ),
                                0
                            )
                    END AS tipo1,

                    CASE
                        WHEN a.pessoa_id IS NULL THEN 0
                        WHEN a.categoria_cliente_id IS NULL THEN 9999
                        WHEN a.regra_tipo2_id IS NULL THEN 9999

                        /*
                        * Se o cadastro do Contato estiver com ambos = S,
                        * usa a atividade mais recente entre Física e Contato.
                        *
                        * A atividade Física não precisa possuir um prazo próprio
                        * cadastrado. Os dias utilizados serão os dias do Contato.
                        */
                        WHEN COALESCE(a.ambos_tipo2, 'N') = 'S' THEN
                            CASE
                                WHEN a.ultima_tipo1_real IS NULL
                                AND a.ultima_tipo2 IS NULL
                                THEN 0

                                ELSE
                                    GREATEST(
                                        a.dias_tipo2
                                        - (
                                            a.data_base
                                            - GREATEST(
                                                a.ultima_tipo1_real,
                                                a.ultima_tipo2
                                            )::date
                                        ),
                                        0
                                    )
                            END

                        /*
                        * Se ambos = N, somente uma atividade de Contato
                        * renova o prazo do Contato.
                        */
                        WHEN a.ultima_tipo2 IS NULL THEN 0

                        ELSE
                            GREATEST(
                                a.dias_tipo2
                                - (
                                    a.data_base
                                    - a.ultima_tipo2::date
                                ),
                                0
                            )
                    END AS tipo2

                FROM atividades a
            ),

            status_cliente AS (
                SELECT
                    p.*,

                    CASE
                        WHEN p.pessoa_id IS NULL THEN false

                        WHEN p.categoria IN ('D', 'E')
                        AND (
                                p.tipo1 > 0
                                OR p.tipo2 > 0
                            )
                        THEN true

                        WHEN p.tipo1 = 9999
                        AND p.tipo2 = 9999
                        THEN false

                        WHEN (
                                p.tipo1 > 0
                                OR p.tipo1 = 9999
                            )
                        AND (
                                p.tipo2 > 0
                                OR p.tipo2 = 9999
                            )
                        THEN true

                        ELSE false
                    END AS cliente_ativo

                FROM prazos p
            ),

            regras_comissao AS (
                SELECT
                    s.*,

                    COALESCE(
                        exc.tipo_comissao,
                        cr.tipo_comissao
                    ) AS tipo_comissao_final,

                    COALESCE(
                        exc.valor,
                        cr.valor
                    ) AS valor_comissao_regra

                FROM status_cliente s

                LEFT JOIN LATERAL (
                    SELECT
                        e.id,
                        e.tipo_comissao,
                        e.valor
                    FROM comissao_repres_excecao e
                    WHERE e.pessoa_id = s.pessoa_id
                    AND e.representante_id = s.representante_id
                    AND e.deleted_at IS NULL
                    AND e.ativo = 'S'
                    ORDER BY e.id DESC
                    LIMIT 1
                ) exc ON true

                LEFT JOIN LATERAL (
                    SELECT
                        c.id,
                        c.tipo_comissao,
                        c.valor
                    FROM comissao_repres c
                    WHERE c.representante_id = s.representante_id
                    AND c.deleted_at IS NULL
                    ORDER BY c.id DESC
                    LIMIT 1
                ) cr ON true
            ),

            calculo AS (
                SELECT
                    rc.*,

                    CASE
                        WHEN rc.cliente_ativo = true
                        AND rc.tipo_comissao_final = 'P'
                        THEN ROUND(
                            COALESCE(rc.valor_total, 0)::numeric
                            * COALESCE(rc.valor_comissao_regra, 0)::numeric
                            / 100,
                            2
                        )

                        WHEN rc.cliente_ativo = true
                        AND rc.tipo_comissao_final = 'V'
                        THEN ROUND(
                            COALESCE(
                                rc.valor_comissao_regra,
                                0
                            )::numeric,
                            2
                        )

                        ELSE 0::numeric
                    END AS comissao_calculada

                FROM regras_comissao rc
            ),

            atualizado AS (
                UPDATE nota_baixada nb

                SET tem_comissao =
                    CASE
                        WHEN c.cliente_ativo = true
                        AND c.tipo_comissao_final IS NOT NULL
                        THEN 'S'

                        ELSE 'N'
                    END,

                    comissao = c.comissao_calculada

                FROM calculo c

                WHERE nb.id = c.nota_id

                RETURNING
                    nb.id,
                    nb.tem_comissao,
                    nb.comissao,
                    c.cliente_ativo,
                    c.pessoa_id,
                    c.representante_id,
                    c.tipo_comissao_final
            )

            SELECT
                COUNT(*) AS processadas,

                COUNT(*) FILTER (
                    WHERE tem_comissao = 'S'
                ) AS com_comissao,

                COUNT(*) FILTER (
                    WHERE tem_comissao = 'N'
                ) AS sem_comissao,

                COUNT(*) FILTER (
                    WHERE pessoa_id IS NULL
                ) AS cliente_nao_encontrado,

                COUNT(*) FILTER (
                    WHERE representante_id IS NULL
                ) AS sem_representante,

                COUNT(*) FILTER (
                    WHERE cliente_ativo = true
                    AND tipo_comissao_final IS NULL
                ) AS ativas_sem_regra_comissao,

                COALESCE(
                    SUM(comissao),
                    0
                ) AS total_comissao

            FROM atualizado
        ";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':data_inicial', $dataInicial);
            $stmt->bindValue(':data_final', $dataFinal);
            $stmt->execute();

            $resumo = $stmt->fetch(PDO::FETCH_OBJ);

            TTransaction::close();

            $centroCusto = self::atualizarCentroCustoNotasBaixadasPorPeriodo($dataInicial, $dataFinal);

            $mensagemCentroCusto = '';

            if ($centroCusto['status'] === 'success') {
                $mensagemCentroCusto =
                    '<br><br><b>Centros de custo:</b>' .
                    $centroCusto['mensagem'];
            } else {
                $mensagemCentroCusto =
                    '<br><br><b>Erro ao atualizar centros de custo:</b> ' .
                    $centroCusto['mensagem'];
            }

            return [
                'status' => 'success',
                'mensagem' =>
                    'Comissões atualizadas com sucesso.' .
                    '<br>Período: ' . date('d/m/Y', strtotime($dataInicial)) . ' até ' . date('d/m/Y', strtotime($dataFinal)) .
                    '<br>Processadas: ' . (int) $resumo->processadas .
                    '<br>Com comissão: ' . (int) $resumo->com_comissao .
                    '<br>Sem comissão: ' . (int) $resumo->sem_comissao .
                    '<br>Cliente não encontrado: ' . (int) $resumo->cliente_nao_encontrado .
                    '<br>Sem representante: ' . (int) $resumo->sem_representante .
                    '<br>Ativas sem regra de comissão: ' . (int) $resumo->ativas_sem_regra_comissao .
                    '<br>Erro/canceladas zeradas no período: ' . (int) $canceladasZeradas .
                    '<br>Total comissão: R$ ' . number_format((float) $resumo->total_comissao, 2, ',', '.') .
                    $mensagemCentroCusto
            ];

        } catch (Exception $e) {
            try {
                TTransaction::rollback();
            } catch (Exception $ignore) {}

            LogCrontab::registrarLog(
                __CLASS__,
                __METHOD__,
                1,
                $e->getMessage(),
                "Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine()
            );

            return [
                'status' => 'error',
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function atualizarCentroCustoNotasBaixadasPorPeriodo($dataInicial, $dataFinal)
    {
        try {
            $dataInicial = self::normalizarDataFiltroComissao($dataInicial);
            $dataFinal   = self::normalizarDataFiltroComissao($dataFinal);

            if ($dataInicial > $dataFinal) {
                throw new Exception('A data inicial não pode ser maior que a data final.');
            }

            /*
            * 1) Busca o rateio na TOTVS
            */
           $sqlTotvs = "
                SELECT
                    m.CODCOLIGADA AS codcoligada,
                    CONVERT(VARCHAR(255), m.NUMEROMOV) AS numeromov,
                    c.CODCCUSTO AS codcusto,
                    SUM(mr.VALOR) AS valor
                FROM TMOV m (NOLOCK)

                INNER JOIN TMOVRATCCU mr (NOLOCK)
                    ON mr.CODCOLIGADA = m.CODCOLIGADA
                AND mr.IDMOV = m.IDMOV

                INNER JOIN GCCUSTO c (NOLOCK)
                    ON c.CODCOLIGADA = mr.CODCOLIGADA
                AND c.CODCCUSTO = mr.CODCCUSTO

                WHERE m.CODTMV = '2.2.15'

                AND m.CODCOLIGADA IN (1, 2)

                AND c.CODCCUSTO IN (
                    '002.03.001',
                    '002.03.002',
                    '002.04.001',
                    '002.04.002'
                )

                AND m.DATAEMISSAO >= CAST(:data_inicial AS DATE)
                AND m.DATAEMISSAO < DATEADD(DAY, 1, CAST(:data_final AS DATE))

                GROUP BY
                    m.CODCOLIGADA,
                    m.NUMEROMOV,
                    c.CODCCUSTO

                ORDER BY
                    m.CODCOLIGADA,
                    m.NUMEROMOV
            ";

            TTransaction::open(self::$dbRm);
            $connRm = TTransaction::get();

            $stmtRm = $connRm->prepare($sqlTotvs);
            $stmtRm->bindValue(':data_inicial', $dataInicial);
            $stmtRm->bindValue(':data_final', $dataFinal);
            $stmtRm->execute();

            $rateios = $stmtRm->fetchAll(PDO::FETCH_OBJ);

            TTransaction::close();

            /*
            * 2) Joga os dados em uma tabela temporária no MiniCRM
            */
            TTransaction::open(self::$dbAp);
            $connAp = TTransaction::get();

            $connAp->exec("
                CREATE TEMP TABLE tmp_rateio_cc_totvs (
                    coligada_id INTEGER,
                    numero VARCHAR(255),
                    codcusto VARCHAR(25),
                    valor NUMERIC(15, 2)
                ) ON COMMIT DROP
            ");

            $stmtTmp = $connAp->prepare("
                INSERT INTO tmp_rateio_cc_totvs
                    (coligada_id, numero, codcusto, valor)
                VALUES
                    (:coligada_id, :numero, :codcusto, :valor)
            ");

            $totalRateiosTotvs = 0;

           foreach ($rateios as $rateio) {
                $numeroLimpo = preg_replace('/\D/', '', (string) $rateio->numeromov);
                $codcusto    = trim((string) $rateio->codcusto);

                if ($numeroLimpo === '' || $codcusto === '') {
                    continue;
                }

                if (!in_array($codcusto, self::CENTROS_CUSTO_COMISSAO, true)) {
                    continue;
                }

                // Nota no MiniCRM está no padrão 000000
                $numero = str_pad($numeroLimpo, 6, '0', STR_PAD_LEFT);

                $stmtTmp->bindValue(':coligada_id', (int) $rateio->codcoligada);
                $stmtTmp->bindValue(':numero', $numero);
                $stmtTmp->bindValue(':codcusto', $codcusto);
                $stmtTmp->bindValue(':valor', (float) $rateio->valor);
                $stmtTmp->execute();

                $totalRateiosTotvs++;
            }

            /*
            * 3) Conta inconsistências antes de inserir
            */
            $notasNaoEncontradas = $connAp->query("
                SELECT COUNT(*) AS total
                FROM (
                    SELECT DISTINCT
                        t.coligada_id,
                        t.numero
                    FROM tmp_rateio_cc_totvs t
                    LEFT JOIN nota_baixada nb
                        ON nb.coligada_id = t.coligada_id
                    AND nb.numero = t.numero
                    WHERE nb.id IS NULL
                ) x
            ")->fetch(PDO::FETCH_OBJ)->total ?? 0;

            $centrosNaoEncontrados = $connAp->query("
                SELECT COUNT(*) AS total
                FROM (
                    SELECT DISTINCT
                        t.codcusto
                    FROM tmp_rateio_cc_totvs t
                    LEFT JOIN centro_custo cc
                        ON TRIM(cc.codcusto) = TRIM(t.codcusto)
                    AND cc.deleted_at IS NULL
                    WHERE cc.id IS NULL
                ) x
            ")->fetch(PDO::FETCH_OBJ)->total ?? 0;

            /*
            * 4) Limpa rateios antigos do período
            * Assim, se mudar o rateio na TOTVS, o MiniCRM fica igual.
            */
            $stmtDelete = $connAp->prepare("
                DELETE FROM centro_custo_nota ccn
                USING nota_baixada nb
                WHERE nb.id = ccn.nota_baixada_id
                AND nb.data_emissao::date BETWEEN :data_inicial AND :data_final
                AND nb.tem_comissao IS DISTINCT FROM 'C'
                AND nb.tem_comissao IS DISTINCT FROM 'P'

                AND nb.coligada_id IN (1, 2)
            ");

            $stmtDelete->bindValue(':data_inicial', $dataInicial);
            $stmtDelete->bindValue(':data_final', $dataFinal);
            $stmtDelete->execute();

            $rateiosApagados = $stmtDelete->rowCount();

            /*
            * 5) Insere novamente calculando:
            *
            * comissao_centro_custo =
            * valor_centro_custo / valor_total_nf * comissao_nf
            */
            $stmtInsert = $connAp->prepare("
                WITH rateio AS (
                    SELECT
                        coligada_id,
                        numero,
                        codcusto,
                        SUM(valor) AS valor
                    FROM tmp_rateio_cc_totvs
                    GROUP BY
                        coligada_id,
                        numero,
                        codcusto
                )

                INSERT INTO centro_custo_nota (
                    nota_baixada_id,
                    centro_custo_id,
                    valor_centro_custo,
                    comissao_centro_custo
                )
                SELECT
                    nb.id AS nota_baixada_id,
                    cc.id AS centro_custo_id,

                    ROUND(r.valor::numeric, 2) AS valor_centro_custo,

                    CASE
                        WHEN COALESCE(nb.valor_total, 0) > 0 THEN
                            ROUND(
                                (
                                    r.valor::numeric
                                    / nb.valor_total::numeric
                                )
                                * COALESCE(nb.comissao, 0)::numeric,
                                2
                            )
                        ELSE 0
                    END AS comissao_centro_custo

                FROM rateio r

                INNER JOIN nota_baixada nb
                    ON nb.coligada_id = r.coligada_id
                AND nb.numero = r.numero
                AND nb.data_emissao::date BETWEEN :data_inicial AND :data_final
                AND nb.tem_comissao IS DISTINCT FROM 'C'
                AND nb.tem_comissao IS DISTINCT FROM 'P'
                

                INNER JOIN centro_custo cc
                    ON TRIM(cc.codcusto) = TRIM(r.codcusto)
                AND cc.deleted_at IS NULL
            ");

            $stmtInsert->bindValue(':data_inicial', $dataInicial);
            $stmtInsert->bindValue(':data_final', $dataFinal);
            $stmtInsert->execute();

            $rateiosInseridos = $stmtInsert->rowCount();

            /*
            * 6) Notas que não ficaram com nenhum dos CCUs válidos
            * não recebem comissão.
            *
            * Mantém C/P protegidos.
            */
            $stmtSemCentroCusto = $connAp->prepare("
                UPDATE nota_baixada nb
                SET
                    tem_comissao = NULL,
                    comissao = 0
                WHERE nb.data_emissao::date BETWEEN :data_inicial AND :data_final
                AND nb.coligada_id IN (1, 2)
                AND nb.nota_status_id = 1

                AND nb.tem_comissao IS DISTINCT FROM 'C'
                AND nb.tem_comissao IS DISTINCT FROM 'P'

                AND NOT EXISTS (
                    SELECT 1
                    FROM centro_custo_nota ccn

                    INNER JOIN centro_custo cc
                        ON cc.id = ccn.centro_custo_id

                    WHERE ccn.nota_baixada_id = nb.id

                        AND TRIM(cc.codcusto) IN (
                            '002.03.001',
                            '002.03.002',
                            '002.04.001',
                            '002.04.002'
                        )
                )
            ");

            $stmtSemCentroCusto->bindValue(':data_inicial', $dataInicial);
            $stmtSemCentroCusto->bindValue(':data_final', $dataFinal);
            $stmtSemCentroCusto->execute();

            $notasSemCentroCusto = $stmtSemCentroCusto->rowCount();

            TTransaction::close();

            return [
                'status' => 'success',
                'mensagem' =>
                    '<br>Rateios encontrados na TOTVS: ' . (int) $totalRateiosTotvs .
                    '<br>Rateios antigos apagados: ' . (int) $rateiosApagados .
                    '<br>Rateios inseridos: ' . (int) $rateiosInseridos .
                    '<br>Notas não encontradas no MiniCRM: ' . (int) $notasNaoEncontradas .
                    '<br>Centros de custo não encontrados no MiniCRM: ' . (int) $centrosNaoEncontrados
            ];

        } catch (Exception $e) {
            try {
                TTransaction::rollback();
            } catch (Exception $ignore) {}

            LogCrontab::registrarLog(
                __CLASS__,
                __METHOD__,
                1,
                $e->getMessage(),
                "Arquivo: " . $e->getFile() . "<br/>Linha: " . $e->getLine()
            );

            return [
                'status' => 'error',
                'mensagem' => $e->getMessage()
            ];
        }
    }

    public static function confirmarComissaoManualNota($notaId)
    {
        try {
            /*
            * 1) Busca nota + pessoa + representante + regra de comissão
            *
            * IMPORTANTE:
            * aqui NÃO verificamos cliente_ativo.
            * A confirmação manual é justamente um override.
            */
            TTransaction::open(self::$dbAp);

            $conn = TTransaction::get();

            $stmt = $conn->prepare("
                SELECT
                    nb.id,
                    nb.numero,
                    nb.coligada_id,
                    nb.documento,
                    nb.valor_total,

                    p.id AS pessoa_id,

                    comp.representante_id,

                    COALESCE(
                        exc.tipo_comissao,
                        cr.tipo_comissao
                    ) AS tipo_comissao,

                    COALESCE(
                        exc.valor,
                        cr.valor
                    ) AS valor_regra

                FROM nota_baixada nb

                LEFT JOIN LATERAL (
                    SELECT
                        p.id
                    FROM pessoa p
                    WHERE regexp_replace(
                            COALESCE(p.cpf_cnpj, ''),
                            '[^0-9]',
                            '',
                            'g'
                        ) = regexp_replace(
                            COALESCE(nb.documento, ''),
                            '[^0-9]',
                            '',
                            'g'
                        )
                    AND p.deleted_at IS NULL
                    ORDER BY p.id DESC
                    LIMIT 1
                ) p ON true

                LEFT JOIN LATERAL (
                    SELECT
                        c.representante_id
                    FROM complemento c
                    WHERE c.pessoa_id = p.id
                    AND c.deleted_at IS NULL
                    AND c.representante_id IS NOT NULL
                    ORDER BY
                        c.created_at DESC NULLS LAST,
                        c.id DESC
                    LIMIT 1
                ) comp ON true

                LEFT JOIN LATERAL (
                    SELECT
                        e.tipo_comissao,
                        e.valor
                    FROM comissao_repres_excecao e
                    WHERE e.pessoa_id = p.id
                    AND e.representante_id = comp.representante_id
                    AND e.deleted_at IS NULL
                    AND e.ativo = 'S'
                    ORDER BY e.id DESC
                    LIMIT 1
                ) exc ON true

                LEFT JOIN LATERAL (
                    SELECT
                        c.tipo_comissao,
                        c.valor
                    FROM comissao_repres c
                    WHERE c.representante_id = comp.representante_id
                    AND c.deleted_at IS NULL
                    ORDER BY c.id DESC
                    LIMIT 1
                ) cr ON true

                WHERE nb.id = :nota_id
            ");

            $stmt->execute([
                ':nota_id' => $notaId
            ]);

            $nota = $stmt->fetch(PDO::FETCH_OBJ);

            TTransaction::close();

            if (!$nota) {
                throw new Exception('Nota não encontrada.');
            }

            if (!$nota->pessoa_id) {
                throw new Exception('Cliente da nota não encontrado.');
            }

            if (!$nota->representante_id) {
                throw new Exception('Cliente não possui representante.');
            }

            if (!$nota->tipo_comissao || $nota->valor_regra === null) {
                throw new Exception('Representante não possui regra de comissão.');
            }

            /*
            * 2) Calcula comissão SEM validar atividade/prazo.
            * A confirmação manual é a autorização.
            */
            if ($nota->tipo_comissao === 'P') {
                $comissao = round(
                    ((float) $nota->valor_total * (float) $nota->valor_regra) / 100,
                    2
                );
            }
            elseif ($nota->tipo_comissao === 'V') {
                $comissao = round(
                    (float) $nota->valor_regra,
                    2
                );
            }
            else {
                throw new Exception(
                    'Tipo de comissão inválido: ' . $nota->tipo_comissao
                );
            }

            /*
            * 3) Busca rateios da SOMENTE ESTA NOTA na TOTVS
            */
            TTransaction::open(self::$dbRm);

            $connRm = TTransaction::get();

            $stmtRateio = $connRm->prepare("
                SELECT
                    c.CODCCUSTO AS codcusto,
                    SUM(mr.VALOR) AS valor

                FROM TMOV m WITH (NOLOCK)

                INNER JOIN TMOVRATCCU mr WITH (NOLOCK)
                    ON mr.CODCOLIGADA = m.CODCOLIGADA
                AND mr.IDMOV = m.IDMOV

                INNER JOIN GCCUSTO c WITH (NOLOCK)
                    ON c.CODCOLIGADA = mr.CODCOLIGADA
                AND c.CODCCUSTO = mr.CODCCUSTO

                WHERE m.CODTMV = '2.2.15'

                AND m.CODCOLIGADA = :coligada_id

                AND m.NUMEROMOV = :numero

                AND c.CODCCUSTO IN (
                    '002.03.001',
                    '002.03.002',
                    '002.04.001',
                    '002.04.002'
                )

                GROUP BY
                    c.CODCCUSTO
            ");

            $stmtRateio->bindValue(
                ':coligada_id',
                (int) $nota->coligada_id,
                PDO::PARAM_INT
            );

            $stmtRateio->bindValue(
                ':numero',
                $nota->numero
            );

            $stmtRateio->execute();

            $rateios = $stmtRateio->fetchAll(PDO::FETCH_OBJ);

            TTransaction::close();

            /*
            * 4) Atualiza comissão e CC dessa nota.
            */
            TTransaction::open(self::$dbAp);

            $conn = TTransaction::get();

            $stmtUpdate = $conn->prepare("
                UPDATE nota_baixada
                SET tem_comissao = 'S',
                    comissao = :comissao
                WHERE id = :nota_id
            ");

            $stmtUpdate->execute([
                ':comissao' => $comissao,
                ':nota_id'  => $notaId
            ]);

            /*
            * Limpa somente o rateio desta nota.
            */
            $stmtDelete = $conn->prepare("
                DELETE FROM centro_custo_nota
                WHERE nota_baixada_id = :nota_id
            ");

            $stmtDelete->execute([
                ':nota_id' => $notaId
            ]);

            /*
            * Recria rateios.
            */
            $stmtCentro = $conn->prepare("
                SELECT id
                FROM centro_custo
                WHERE TRIM(codcusto) = TRIM(:codcusto)
                AND deleted_at IS NULL
                LIMIT 1
            ");

            $stmtInsert = $conn->prepare("
                INSERT INTO centro_custo_nota (
                    nota_baixada_id,
                    centro_custo_id,
                    valor_centro_custo,
                    comissao_centro_custo
                )
                VALUES (
                    :nota_id,
                    :centro_custo_id,
                    :valor_centro_custo,
                    :comissao_centro_custo
                )
            ");

           foreach ($rateios as $rateio) {

                $codcusto = trim((string) $rateio->codcusto);

                if (!in_array($codcusto, self::CENTROS_CUSTO_COMISSAO, true)) {
                    continue;
                }

                $stmtCentro->execute([
                    ':codcusto' => $codcusto
                ]);

                $centro = $stmtCentro->fetch(PDO::FETCH_OBJ);

                if (!$centro) {
                    continue;
                }

                $valorCentro = round(
                    (float) $rateio->valor,
                    2
                );

                if ((float) $nota->valor_total > 0) {

                    $comissaoCentro = round(
                        (
                            $valorCentro
                            / (float) $nota->valor_total
                        ) * $comissao,
                        2
                    );

                } else {
                    $comissaoCentro = 0;
                }

                $stmtInsert->execute([
                    ':nota_id'               => $notaId,
                    ':centro_custo_id'       => $centro->id,
                    ':valor_centro_custo'    => $valorCentro,
                    ':comissao_centro_custo' => $comissaoCentro
                ]);
            }

            TTransaction::close();

            return [
                'status'   => 'success',
                'comissao' => $comissao
            ];

        } catch (Exception $e) {

            try {
                TTransaction::rollback();
            } catch (Exception $ignore) {}

            return [
                'status'   => 'error',
                'mensagem' => $e->getMessage()
            ];
        }
    }

    private static function normalizarDataFiltroComissao($data)
    {
        if (empty($data)) {
            throw new Exception('Informe a data inicial e a data final.');
        }

        if ($data instanceof DateTimeInterface) {
            return $data->format('Y-m-d');
        }

        $data = trim((string) $data);

        $dt = DateTime::createFromFormat('d/m/Y', $data);
        if ($dt && $dt->format('d/m/Y') === $data) {
            return $dt->format('Y-m-d');
        }

        $dt = DateTime::createFromFormat('Y-m-d', $data);
        if ($dt && $dt->format('Y-m-d') === $data) {
            return $dt->format('Y-m-d');
        }

        throw new Exception("Data inválida: {$data}");
    }
}