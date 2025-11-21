<?php

class SigissWebService
{
    private static $dbAp = 'minicrm';
    private static $dbRm = 'corporerm';
    private static $url = 'https://wssantabarbara.sigissweb.com/rest/';

    // --------------------- SERVIDOR DE HOMOLOGAÇÃO ---------------------
    //private static $url = 'https://wshml2.sigissweb.com/rest/';
    //private static $senha = '5CLZMwkb7';

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

            $sqlNota = "
                    SELECT
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
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO ELSE 0 END ) as base_csll,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO ELSE 0 END ) as base_cofins,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO ELSE 0 END ) as base_pis,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 1.00 ELSE 0 END ) as aliquota_csll,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 3.00 ELSE 0 END ) as aliquota_cofins,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN 0.65 ELSE 0 END ) as aliquota_pis,
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO * 0.01 ELSE 0 END ) as csll, 
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO * 0.03 ELSE 0 END ) as cofins, 
                        (CASE WHEN (SELECT tr.valor FROM ttrbmov tr (nolock) WHERE tr.codcoligada = m.CODCOLIGADA AND tr.idmov = m.idmov AND tr.NSEQITMMOV = 0 AND tr.codtrb = 'RET') > 0 THEN m.VALORLIQUIDO * 0.0065 ELSE 0 END ) as pis,
                        (m.VALORLIQUIDO * 0.02) as iss, 
                        (SELECT top 1 replace(replace(cast(i.historicolongo as varchar(2000)), CHAR(13), '|' ), char(10), '' ) FROM TITMMOVHISTORICO i (nolock) WHERE i.codcoligada = m.codcoligada AND i.idmov = m.idmov order by i.nseqitmmov desc ) + '|' + ( CASE WHEN ( SELECT string_agg( 'FATURA / DUPLICATA: ' + l.NUMERODOCUMENTO + ' - VALOR : ' + format( ( l.VALORORIGINAL - l.valorop1 - l.valorop2 - l.valorop3 - T.valor ), 'C', 'pt-br' ) + ' - DATA DE VENCIMENTO : ' + CONVERT(varchar(30), l.DATAVENCIMENTO, 103), '|' ) FROM flan l (nolock), FTRBLAN t (nolock) WHERE l.codcoligada = m.codcoligada AND l.idmov = m.idmov AND t.idlan = l.idlan AND t.CODCOLIGADA = l.codcoligada ) is not null THEN ( SELECT string_agg( 'FATURA / DUPLICATA: ' + l.NUMERODOCUMENTO + ' - VALOR : ' + format( ( l.VALORORIGINAL - l.valorop1 - l.valorop2 - l.valorop3 - T.valor ), 'C', 'pt-br' ) + ' - DATA DE VENCIMENTO : ' + CONVERT(varchar(30), l.DATAVENCIMENTO, 103), '|' ) FROM flan l (nolock), FTRBLAN t (nolock) WHERE l.codcoligada = m.codcoligada AND l.idmov = m.idmov AND t.idlan = l.idlan AND t.CODCOLIGADA = l.codcoligada ) ELSE ( SELECT string_agg( 'FATURA / DUPLICATA: ' + l.NUMERODOCUMENTO + ' - VALOR : ' + format( ( l.VALORORIGINAL - l.valorop1 - l.valorop2 - l.valorop3 ), 'C', 'pt-br' ) + ' - DATA DE VENCIMENTO : ' + CONVERT(varchar(30), l.DATAVENCIMENTO, 103), '|' ) FROM flan l (nolock) WHERE l.codcoligada = m.codcoligada AND l.idmov = m.idmov ) END ) as descricao
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

            // Criação do DOM
            $xml = new DOMDocument("1.0", "ISO-8859-1");
            $xml->formatOutput = true;

            // Elemento raiz
            $root = $xml->createElement("notafiscal_lote");
            $nf = $xml->createElement("notafiscal");

            // Dados convertidos com segurança
            $campos = [
                "cnpj_cpf_prestador" => $coligada->cnpj,
                "exterior_dest" => "0",
                "cnpj_cpf_destinatario" => preg_replace('/\D/', '', $dados->cnpj_cpf),
                "pessoa_destinatario" => $dados->pessoa,
                "ie_destinatario" => $dados->inscricao_estadual,
                "im_destinatario" => $dados->inscricao_municipal,
                "razao_social_destinatario" => $dados->razao_social,
                "endereco_destinatario" => $dados->endereco,
                "numero_ende_destinatario" => $dados->numero_end,
                "complemento_ende_destinatario" => $dados->complementopgto,
                "bairro_destinatario" => $dados->bairro,
                "cep_destinatario" => preg_replace('/\D/', '', $dados->ceppgto),
                "cidade_destinatario" => $dados->cidadepgto,
                "uf_destinatario" => $dados->codetdpgto,
                "pais_destinatario" => $dados->paispagto,
                "fone_destinatario" => $dados->telefonepgto,
                "email_destinatario" => $dados->emailpgto,
                "valor_nf" => number_format((float) $dados->valor_total, 2, ',', ''),
                "deducao" => "0",
                "valor_servico" => number_format((float) $dados->valor_total, 2, ',', ''),
                "data_emissao" => date("d/m/Y", strtotime($dados->data_emissao)),
                "forma_de_pagamento" => $dados->forma_pagamento,
                "descricao" => $dados->descricao,
                "id_codigo_servico" => "14.01.01",
                "cancelada" => "N",
                "iss_retido" => "N",
                "aliq_iss" => "2",
                "valor_iss" => number_format((float) ($dados->iss ?? 0), 2, ',', ''),
                "bc_pis" => number_format((float) ($dados->base_pis ?? 0), 2, ',', ''),
                "aliq_pis" => number_format((float) ($dados->aliquota_pis ?? 0), 2, ',', ''),
                "valor_pis" => number_format((float) ($dados->pis ?? 0), 2, ',', ''),
                "bc_cofins" => number_format((float) ($dados->base_cofins ?? 0), 2, ',', ''),
                "aliq_cofins" => number_format((float) ($dados->aliquota_cofins ?? 0), 2, ',', ''),
                "valor_cofins" => number_format((float) ($dados->cofins ?? 0), 2, ',', ''),
                "bc_csll" => number_format((float) ($dados->base_csll ?? 0), 2, ',', ''),
                "aliq_csll" => number_format((float) ($dados->aliquota_csll ?? 0), 2, ',', ''),
                "valor_csll" => number_format((float) ($dados->csll ?? 0), 2, ',', ''),
                "bc_irrf" => "0",
                "aliq_irrf" => "0",
                "valor_irrf" => "0",
                "bc_inss" => "0",
                "aliq_inss" => "0",
                "valor_inss" => "0",
                "sistema_gerador" => "TOTVS RM",
                "serie_rps" => "NFS",
                "rps" => (int) ltrim($dados->numero, '0'),
                "codigo_nbs" => "1.2001.50.00",
                "exterior_prestacao_servico" => "0",
                "pais_local_prest" => "Brasil",
                "cidade_local_prest" => "Santa Barbara D'Oeste",
                "uf_local_prest" => "SP"
            ];

            // Adiciona os elementos ao XML
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
            ];

            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            // Inicializa o cURL
            $ch = curl_init($url);

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

            $endpoint = self::$url . "nfes/pegaxml/{$numero}/serierps/NFS";
            $headers = ["Authorization: {$token}"];

            // Executa requisição cURL
            $ch = curl_init($endpoint);
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
            $nota->store();
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
}