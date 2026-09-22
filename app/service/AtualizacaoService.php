<?php

class AtualizacaoService
{
    public static function atualizarTudo()
    {
        AtualizacaoService::atualizarCategoriaCliente(); 
        AtualizacaoService::atualizarCentroCusto();
        AtualizacaoService::atualizarCondicaoPagamento();

        // Estrutura geográfica primeiro
        AtualizacaoService::atualizarPais();
        AtualizacaoService::atualizarEstado(); 
        AtualizacaoService::atualizarCidade();

        // Cadastros que dependem desses dados
        AtualizacaoService::atualizarTransportadora();
        //AtualizacaoService::atualizarVendedor();
        AtualizacaoService::atualizarRepresentante(); 
        AtualizacaoService::atualizarCliente();

        // Dados dependentes do cliente
        AtualizacaoService::atualizarEndereco();
        AtualizacaoService::atualizarComplemento(); 
        AtualizacaoService::atualizarContato(); 
        
        TTransaction::open('log');
        SystemSqlLog::where('id','>',0)->delete();
        TTransaction::close();
    }
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarRepresentante() {
        try{
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT 
                    R.CODCOLIGADA AS codcoligada,
                    R.CODRPR AS codigo,
                    UPPER(R.NOME) AS razao_social,
                    R.NOMEFANTASIA AS fantasia,
                    R.CGC AS cpf_cnpj,
                    R.INSCRESTADUAL AS ie,
                    R.RUA AS rua,
                    R.NUMERO AS numero,
                    R.COMPLEMENTO AS complemento,
                    R.BAIRRO AS bairro,
                    R.CIDADE AS cidade,
                    R.CODETD AS estado,
                    R.CEP AS cep,
                    R.CONTATO AS contato,
                    R.TELEFONE AS telefone,
                    R.FAX AS fax,
                    R.PERCENTCOMISSAO AS comissao,
                    R.FATCLIENTEDIRETO AS faturamento_direto,
                    CASE 
                        WHEN R.INATIVO = 0 THEN 'S' 
                        ELSE 'N' 
                    END AS ativo,
                    R.EMAIL AS email,
                    R.CELULAR AS celular,
                    R.PAIS AS pais
                FROM 
                    TRPR R
                WHERE
                    CODCOLIGADA in (1,2)
                ");
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            TTransaction::open('minicrm');
            $cidadeArray = Cidade::getIndexedArray('id', 'cod_municipio');
            foreach($objects as $object){
                /*
                $representante = RepresentanteTotvs::where('codigo','=',$object->codigo)->where('codcoligada','=',$object->codcoligada)->first() ?? new RepresentanteTotvs();
                $representante->codigo              = $object->codigo;
                $representante->razao_social        = $object->razao_social;
                $representante->fantasia            = $object->fantasia;
                $representante->cpf_cnpj            = $object->cpf_cnpj;
                $representante->inscrestadual       = $object->ie;
                $representante->cep                 = $object->cep;
                $representante->rua                 = $object->rua;
                $representante->numero              = $object->numero;
                $representante->complemento         = $object->complemento;
                $representante->bairro              = $object->bairro;
                $representante->cidade_id           = ($cidade_id = array_search($object->cidade, $cidadeArray)) !== false ? $cidade_id : null;
                $representante->contato             = $object->contato;
                $representante->telefone            = preg_replace("/[^0-9]/", "", $object->telefone);
                $representante->fax                 = $object->fax;
                $representante->pais_id             = (Pais::where('nome','like',$object->pais)->first())->id ?? null;
                $representante->percentual_comissao = $object->comissao;
                $representante->fatclientedireto    = $object->faturamento_direto;
                $representante->ativo               = $object->ativo;
                $representante->email               = $object->email;
                $representante->celular             = preg_replace("/[^0-9]/", "", $object->celular);
                $representante->codcoligada         = $object->codcoligada;
                $representante->store();
                */
                $rep_ap = Representante::where('codigo','=',$object->codigo)->first() ?? new Representante();
                $rep_ap->codigo         = $object->codigo;
                $rep_ap->razao_social   = $object->razao_social;
                $rep_ap->cpf_cnpj       = $object->cpf_cnpj;
                $rep_ap->inscrestadual  = $object->ie;
                $rep_ap->telefone       = preg_replace("/[^0-9]/", "", $object->telefone);
                $rep_ap->ativo          = $object->ativo;
                $rep_ap->email          = $object->email;
                $rep_ap->store();
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Representantes atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }

    /****************************************************************************************************************************************************************************/ 
    public static function atualizarCategoriaCliente(){
        try{
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query('
                SELECT 
                    CODTCF,
                    DESCRICAO
                FROM
                    FTCF
                ');
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            TTransaction::open('minicrm');
            foreach($objects as $object){
                $tipo_cliente = CategoriaCliente::where('codigo','=',$object->CODTCF)->first() ?? new CategoriaCliente();
                
                $tipo_cliente->codigo = $object->CODTCF;
                $tipo_cliente->nome = $object->DESCRICAO;
                $tipo_cliente->store();
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Categorias de Cliente atualizadas.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarCliente(){
        try{
            TTransaction::open('minicrm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT 
                (data_alteracao_totvs - INTERVAL '3 day') AS data_alteracao_totvs
                FROM 
                    public.pessoa 
                WHERE 
                    data_alteracao_totvs IS NOT NULL 
                ORDER BY data_alteracao_totvs DESC 
                LIMIT(1)
            ");
            $hora = $result->fetch(PDO::FETCH_ASSOC);
            $hora = ($hora !== false && isset($hora['data_alteracao_totvs'])) ? $hora['data_alteracao_totvs'] : null;
            TTransaction::close();
            
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
            SELECT
                CODCOLIGADA as coligada,
                CODCFO,
                NOMEFANTASIA as fantasia,
                UPPER(NOME) as razao_social,
                CGCCFO as cpf_cnpj,
                INSCRESTADUAL as ie,
                CASE 
                    WHEN PESSOAFISOUJUR = 'F' THEN 1
                    WHEN PESSOAFISOUJUR = 'J' THEN 2
                END as tipo_pessoa_id,
                CODTCF,
                TELEFONE,
                EMAIL,
                CONTATO,
            	DATAULTALTERACAO,
                CASE WHEN ATIVO = 1 THEN 'S' ELSE 'N' END AS ATIVO,
                CASE WHEN CFOIMOB = 1 THEN 'S' ELSE 'N' END AS BLOQUEADO
            FROM 
                FCFO
            WHERE
                PAGREC <> 2
                AND CGCCFO IS NOT NULL
                AND DATAULTALTERACAO >= '{$hora}'
                ;
            ");
            
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            $categoriaArray = CategoriaCliente::getIndexedArray('id','codigo');
            $clienteArray = Pessoa::getIndexedArray('id','cpf_cnpj');
            TTransaction::close();
                 
            foreach ($objects as $object) {
             
                $cpfCnpjSemFormatacao = preg_replace("/[^0-9]/", "", $object->cpf_cnpj);
                
                if (empty($cpfCnpjSemFormatacao)) {
                    continue;
                }
                
                if (array_search($cpfCnpjSemFormatacao, $clienteArray) !== false) {
                    $clienteId = array_search($cpfCnpjSemFormatacao, $clienteArray);
                    TTransaction::open('minicrm');
                    $cliente = Pessoa::find($clienteId);
                    TTransaction::close();
                    
                } else {
                    $cliente = new Pessoa();
                }
                if($cliente) {
                    $cliente->tipo_pessoa_id = $object->tipo_pessoa_id;
                    $cliente->codigo = $object->CODCFO;
                    $cliente->nome_fantasia = $object->fantasia;
                    $cliente->razao_social = $object->razao_social;
                    $cliente->cpf_cnpj = preg_replace("/[^0-9]/", "", $object->cpf_cnpj);
                    $cliente->rg_id = $object->ie;
                    $cliente->email = $object->EMAIL;
                    $cliente->fone = preg_replace("/[^0-9]/", "", $object->TELEFONE);
                    $cliente->ativo = $object->ATIVO ?? 'N';
                    $cliente->bloqueado = $object->BLOQUEADO;
                    $cliente->categoria_cliente_id = ($categoria_id = array_search($object->CODTCF, $categoriaArray)) !== false ? $categoria_id : null;
                    $cliente->updated_at = date('Y-m-d H:i:s');
                    $cliente->data_alteracao_totvs = $object->DATAULTALTERACAO;
                    $cliente->origem = null;
                    
                    TTransaction::open('minicrm');
                    $cliente->store();
                    
                    if(!PessoaGrupo::where('pessoa_id','=',$cliente->id)->first()){
                        $grupo = new PessoaGrupo();
                        $grupo->grupo_id = Grupo::CLIENTE;
                        $grupo->pessoa_id = $cliente->id;
                        $grupo->store();
                    }

                    TTransaction::close();
                }
            }
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização de Clientes", __METHOD__, 0, "Clientes atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização de Clientes", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }

    /****************************************************************************************************************************************************************************/ 
    public static function atualizarEndereco()
    {
        try {
            TTransaction::open('minicrm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT 
                (data_alteracao_totvs - INTERVAL '3 day') AS data_alteracao_totvs
                FROM public.pessoa_endereco 
                WHERE data_alteracao_totvs IS NOT NULL 
                ORDER BY data_alteracao_totvs DESC 
                LIMIT 1
            ");
            $hora = $result->fetch(PDO::FETCH_ASSOC);
            TTransaction::close();

            $hora = ($hora !== false && isset($hora['data_alteracao_totvs'])) ? $hora['data_alteracao_totvs'] : null;
            $filtroData = '';

            if (!empty($hora)) {
                $hora = date('Y-m-d H:i:s', strtotime($hora));
                $filtroData = " AND DATAULTALTERACAO >= '{$hora}' ";
            }

            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT
                    'Principal' as tipo,
                    CODCFO as codigo,
                    RUA as rua,
                    NUMERO as numero,
                    COMPLEMENTO as complemento,
                    BAIRRO as bairro,
                    UPPER(CIDADE) AS cidade,
                    CODMUNICIPIO as cod_municipio,
                    CODETD as uf,
                    CEP as cep,
                    DATAULTALTERACAO as dt_alteracao
                FROM FCFO
                WHERE
                    CODCFO IS NOT NULL
                    AND PAGREC <> 2
                    AND CGCCFO IS NOT NULL
                    {$filtroData}

                UNION ALL

                SELECT
                    'Pagamento' as tipo,
                    CODCFO as codigo,
                    RUAPGTO as rua,
                    NUMEROPGTO as numero,
                    COMPLEMENTOPGTO as complemento,
                    BAIRROPGTO as bairro,
                    UPPER(CIDADEPGTO) as cidade,
                    CODMUNICIPIOPGTO as cod_municipio,
                    CODETDPGTO as uf,
                    CEPPGTO as cep,
                    DATAULTALTERACAO as dt_alteracao
                FROM FCFO
                WHERE
                    CODCFO IS NOT NULL
                    AND PAGREC <> 2
                    AND CGCCFO IS NOT NULL
                    {$filtroData}

                UNION ALL

                SELECT
                    'Entrega' as tipo,
                    CODCFO as codigo,
                    RUAENTREGA as rua,
                    NUMEROENTREGA as numero,
                    COMPLEMENTREGA as complemento,
                    BAIRROENTREGA as bairro,
                    UPPER(CIDADEENTREGA) as cidade,
                    CODMUNICIPIOENTREGA as cod_municipio,
                    CODETDENTREGA as uf,
                    CEPENTREGA as cep,
                    DATAULTALTERACAO as dt_alteracao
                FROM FCFO
                WHERE
                    CODCFO IS NOT NULL
                    AND PAGREC <> 2
                    AND CGCCFO IS NOT NULL
                    {$filtroData}

                    
            ");

            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();

            TTransaction::open('minicrm');
            $conn = TTransaction::get();

            $clienteArray = Pessoa::where('id', '>', 0)->getIndexedArray('codigo', 'id');

            $sqlCidades = "
                SELECT
                    c.id,
                    c.nome,
                    c.cod_municipio,
                    e.sigla
                FROM cidade c
                INNER JOIN estado e ON e.id = c.estado_id
                WHERE e.sigla IS NOT NULL
            ";

            $resultCidades = $conn->query($sqlCidades);
            $rowsCidades = $resultCidades->fetchAll(PDO::FETCH_OBJ);

            $cidadePorCodigo = [];
            $cidadePorNome = [];

            foreach ($rowsCidades as $rowCidade) {
                $sigla = strtoupper(trim((string) $rowCidade->sigla));
                $cod   = preg_replace('/\D/', '', (string) $rowCidade->cod_municipio);
                $nome  = self::normalizarTexto($rowCidade->nome);

                if ($cod !== '') {
                    $cod = str_pad($cod, 5, '0', STR_PAD_LEFT);
                    $cidadePorCodigo[$sigla . '|' . $cod] = $rowCidade->id;
                }

                if ($sigla !== '' && $nome !== '') {
                    $cidadePorNome[$sigla . '|' . $nome] = $rowCidade->id;
                }
            }

            foreach ($objects as $object) {
                $codigo = trim((string) $object->codigo);

                if (empty($codigo) || !isset($clienteArray[$codigo])) {
                    continue;
                }

                $tipo          = trim((string) $object->tipo);
                $rua           = trim((string) $object->rua);
                $numero        = trim((string) $object->numero);
                $bairro        = trim((string) $object->bairro);
                $complemento   = trim((string) $object->complemento);
                $cep           = preg_replace('/\D/', '', (string) $object->cep);
                $uf            = strtoupper(trim((string) $object->uf));
                $cidade_nome   = trim((string) $object->cidade);
                $cod_municipio = preg_replace('/\D/', '', (string) $object->cod_municipio);

                if ($cod_municipio !== '') {
                    $cod_municipio = str_pad($cod_municipio, 5, '0', STR_PAD_LEFT);
                }

                /*
                * Se não veio cidade, ignora esse endereço.
                * Isso mata Pagamento/Entrega lixo.
                */
                if ($cidade_nome === '') {
                    continue;
                }

                $cliente_id = $clienteArray[$codigo];

                $cliente_endereco = PessoaEndereco::where('pessoa_id', '=', $cliente_id)
                    ->where('nome', '=', $tipo)
                    ->first();

                if (!$cliente_endereco) {
                    $cliente_endereco = new PessoaEndereco();
                }

                $cidade_id = null;

                $chaveCodigo = $uf . '|' . $cod_municipio;
                $chaveNome   = $uf . '|' . self::normalizarTexto($cidade_nome);

                if ($cod_municipio !== '' && isset($cidadePorCodigo[$chaveCodigo])) {
                    $cidade_id = $cidadePorCodigo[$chaveCodigo];
                }

                if (!$cidade_id && isset($cidadePorNome[$chaveNome])) {
                    $cidade_id = $cidadePorNome[$chaveNome];
                }

                if (!$cidade_id && $uf !== '') {
                    $cidade_id = self::cadastrarCidade($cidade_nome, $cod_municipio, $uf);
                }

                $principal = ($tipo == 'Principal') ? 'S' : 'N';

                $cliente_endereco->pessoa_id = $cliente_id;
                $cliente_endereco->nome = $tipo;
                $cliente_endereco->cep = $cep ?: null;
                $cliente_endereco->numero = $numero ?: null;
                $cliente_endereco->cidade_id = $cidade_id;
                $cliente_endereco->complemento = $complemento ?: null;
                $cliente_endereco->rua = $rua ?: null;
                $cliente_endereco->bairro = $bairro ?: null;
                $cliente_endereco->data_alteracao_totvs = $object->dt_alteracao;
                $cliente_endereco->principal = $principal;

                if (!$cidade_id) {
                    LogCrontab::registrarLog(
                        "Atualização de Clientes",
                        __METHOD__,
                        1,
                        "Cidade não encontrada",
                        "Código: {$codigo}\nTipo: {$tipo}\nCidade: {$cidade_nome}\nUF: {$uf}\nCod município: {$cod_municipio}"
                    );

                    continue;
                }

                $cliente_endereco->store();
            }

            TTransaction::close();

        }catch (Exception $e) {

            if (TTransaction::get()) {
                    TTransaction::rollback();
            }

                LogCrontab::registrarLog(
                    "Atualização de Clientes",
                    __METHOD__,
                    1,
                    "Exception: " . $e->getMessage(),
                    "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n"
            );
        }
    }

    public static function cadastrarCidade($nome, $cod_municipio = null, $codEstado = null)
    {
        $nome = trim((string) $nome);
        $cod_municipio = preg_replace('/\D/', '', (string) $cod_municipio);
        $codEstado = strtoupper(trim((string) $codEstado));

        if ($cod_municipio !== '') {
            $cod_municipio = str_pad($cod_municipio, 5, '0', STR_PAD_LEFT);
        }

        if (empty($nome) || empty($codEstado)) {
            return null;
        }

        $estado = Estado::where('sigla', '=', $codEstado)
                        ->orderBy('id')
                        ->first();

        if (!$estado) {
            return null;
        }

        $estado_id = $estado->id;
        $cidade = null;

        if (!empty($cod_municipio)) {
            $conn = TTransaction::get();
            $sql = "
                SELECT c.id
                FROM cidade c
                INNER JOIN estado e ON e.id = c.estado_id
                WHERE e.sigla = ?
                AND c.cod_municipio = ?
                ORDER BY c.id
                LIMIT 1
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$codEstado, $cod_municipio]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $row['id'];
            }
        }

        $nomeNormalizado = self::normalizarTexto($nome);

        $conn = TTransaction::get();
        $sql = "
            SELECT c.id, c.nome
            FROM cidade c
            INNER JOIN estado e ON e.id = c.estado_id
            WHERE e.sigla = ?
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$codEstado]);
        $cidades = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach ($cidades as $cidadeItem) {
            if (self::normalizarTexto($cidadeItem->nome) === $nomeNormalizado) {
                return $cidadeItem->id;
            }
        }

        $cidade = new Cidade();
        $cidade->nome = $nome;
        $cidade->cod_municipio = !empty($cod_municipio) ? $cod_municipio : null;
        $cidade->estado_id = $estado_id;
        $cidade->store();

        return $cidade->id;
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarPais() {
        try {
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query('
                SELECT
                    P.CODPAIS as codigo,
                    P.DESCRICAO as pais
                FROM
                    GPAIS P
            ');
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
          
            TTransaction::open('minicrm');
            foreach ($objects as $object) {
                $pais = Pais::where('codigo', '=', trim($object->codigo))
                        ->first() ?? new Pais();

                    $pais->codigo = trim($object->codigo);
                    $pais->nome   = trim($object->pais);
                    $pais->store();
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Paises atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }

    /****************************************************************************************************************************************************************************/ 
    public static function atualizarEstado() {
        try {
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query('
                SELECT
                    E.CODETD AS sigla,
                    E.NOME AS estado,
                    E.CODIGOSINIEF AS codigo,
                    P.CODPAIS AS codigoPais
                FROM
                    GETD E
                        INNER JOIN GPAIS P ON E.IDPAIS = P.IDPAIS
            ');
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            foreach($objects as $object)
            {
              $estado = Estado::where('sigla', '=', strtoupper(trim($object->sigla)))
                ->first() ?? new Estado();

            $estado->pais_id = Pais::where('codigo', '=', trim($object->codigoPais))
                ->first()?->id;

            $estado->codigo_ibge = $object->codigo;
            $estado->nome = trim($object->estado);
            $estado->sigla = strtoupper(trim($object->sigla));
            $estado->store();
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Estados atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarCidade() {
        try {
        TTransaction::open('corporerm');

        $conn = TTransaction::get();
        $result = $conn->query("
            SELECT 
                UPPER(TRIM(C.NOMEMUNICIPIO)) AS cidade,
                TRIM(C.CODMUNICIPIO) AS codigo,
                TRIM(D.CODIGO) AS codigo_ibge,
                TRIM(C.CODETDMUNICIPIO) AS estado
            FROM 
                GMUNICIPIO C
            LEFT JOIN DCODIFICACAOMUNICIPIO D
                ON C.CODMUNICIPIO = D.CODMUNICIPIO 
                AND C.CODETDMUNICIPIO = D.CODETDMUNICIPIO
            WHERE 
                D.IDCLASSIFMUNICIPIO = 1
        ");

        $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
        TTransaction::close();

        TTransaction::open('minicrm');
        $estadoArray = Estado::getIndexedArray('id', 'sigla');

        foreach ($objects as $object)
        {
            $estado_id = array_search($object->estado, $estadoArray);
            $estado_id = ($estado_id !== false) ? $estado_id : null;

            $cidade = Cidade::where('cod_municipio', '=', $object->codigo)
                            ->where('estado_id', '=', $estado_id)
                            ->first();

            if (!$cidade && !empty($object->codigo_ibge)) {
                $cidade = Cidade::where('codigo_ibge', '=', $object->codigo_ibge)
                                ->where('estado_id', '=', $estado_id)
                                ->first();
            }

            if (!$cidade) {
                $cidade = Cidade::where('nome', '=', $object->cidade)
                                ->where('estado_id', '=', $estado_id)
                                ->first();
            }

            if (!$cidade) {
                $cidade = new Cidade();
            }

            $cidade->estado_id = $estado_id;
            $cidade->nome = $object->cidade;
            $cidade->cod_municipio = $object->codigo ?: null;
            $cidade->codigo_ibge = $object->codigo_ibge ?: null;
            $cidade->store();
        }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Cidades atualizadas.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarContato() {
        try {
            
            TTransaction::open('minicrm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT 
                (data_alteracao_totvs - INTERVAL '3 day') AS data_alteracao_totvs
                FROM 
                    public.pessoa_contato 
                WHERE 
                    data_alteracao_totvs IS NOT NULL 
                ORDER BY data_alteracao_totvs DESC 
                LIMIT(1)
            ");
            
            $hora = $result->fetch(PDO::FETCH_ASSOC);
            TTransaction::close();
            
            $hora = ($hora !== false && isset($hora['data_alteracao_totvs'])) ? $hora['data_alteracao_totvs'] : null;
            
             TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT
                    C.CODCOLIGADA AS codcoligada,
                    C.CODCFO AS codigo,
                    C.NOME AS nome,
                    C.EMAIL AS email,
                    C.TELEFONE AS telefone,
                    C.FUNCAO AS funcao,
                    C.IDCONTATO AS idcontato,
                	C.DATAALTERACAO AS DATAALTERACAO
                FROM
                    FCFOCONTATO C
                INNER JOIN FCFO f
                    ON C.CODCFO = f.CODCFO
                    AND f.PAGREC <> 2
                WHERE C.DATAALTERACAO >= '{$hora}'
             ");
            
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            $clienteArray = Pessoa::getIndexedArray('id', 'codigo'); 
            TTransaction::close();
            
            foreach($objects as $object)
            {
                $pessoaId = array_search($object->codigo, $clienteArray) !== false ? array_search($object->codigo, $clienteArray) : null;
                
                if($pessoaId != null)
                {
                    TTransaction::open('minicrm');
                    $contato = (PessoaContato::where('pessoa_id','=',$pessoaId)->where('codcoligada','=',$object->codcoligada)->first()) ?? new PessoaContato();
                    TTransaction::close();
                    $contato->codcoligada = $object->codcoligada;
                    $contato->pessoa_id = $pessoaId;
                    $contato->nome = $object->nome;
                    $contato->email = $object->email;
                    $contato->idcontato = $object->idcontato;
                    $contato->telefone = preg_replace('/[^0-9]/', '', $object->telefone);
                    $contato->obs = "{$object->nome} - {$object->funcao}";
                    $contato->data_alteracao_totvs = $object->DATAALTERACAO;

                    TTransaction::open('minicrm');
                    $contato->store();
                    TTransaction::close();
                }
            }
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização de Clientes", __METHOD__, 0, "Contatos atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização de Clientes", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarTransportadora() {
        try {
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT
                    CODTRA,
                    UPPER(NOME) AS NOME,
                    RUA,
                    NUMERO,
                    COMPLEMENTO,
                    BAIRRO,
                    CODMUNICIPIO AS CIDADE,
                    CEP,
                    TRIM(CGC) AS CGC,
                    INSCRESTADUAL,
                    CONTATO,
                    TELEFONE,
                    TELEX,
                    FAX,
                    LIVRE,
                    NOMEFANTASIA,
                    CEI,
                    INSCRMUNICIPAL,
                    CASE WHEN INATIVO = 0 THEN 'S' ELSE 'N' END AS ATIVO,
                    EMAIL
                FROM
                    TTRA
                WHERE 
                    CGC IS NOT NULL
                    AND CODCOLIGADA in (1,2)
             ");
            
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            
            $cidadeArray = Cidade::getIndexedArray('id', 'cod_municipio');
            $transportadoraArray = Transportadora::getIndexedArray('id','cgc');
            
            foreach($objects as $object)
            {
                $cpfCnpjSemFormatacao = preg_replace("/[^0-9]/", "", $object->CGC);
                
                if (!empty($cpfCnpjSemFormatacao)) {
                    $transportadoraId = array_search($cpfCnpjSemFormatacao, $transportadoraArray);
                    
                    $transportadora = $transportadoraId !== false ? Transportadora::find($transportadoraId) : new Transportadora();
                    
                    $transportadora->codtra = $object->CODTRA;
                    $transportadora->nome = $object->NOME;
                    $transportadora->rua = $object->RUA;
                    $transportadora->numero = $object->NUMERO;
                    $transportadora->complemento = $object->COMPLEMENTO;
                    $transportadora->bairro = $object->BAIRRO;
                    $transportadora->cidade_id = ($cidade_id = array_search($object->CIDADE, $cidadeArray)) !== false ? $cidade_id : null;
                    $transportadora->cep = preg_replace('/[^0-9]/', '',$object->CEP);
                    $transportadora->cgc = $cpfCnpjSemFormatacao;
                    $transportadora->inscrestadual = $object->INSCRESTADUAL;
                    $transportadora->contato = $object->CONTATO;
                    $transportadora->telefone = preg_replace('/[^0-9]/', '',$object->TELEFONE) ?? null;
                    $transportadora->telex = preg_replace('/[^0-9]/', '',$object->TELEX) ?? null;
                    $transportadora->fax = preg_replace('/[^0-9]/', '',$object->FAX) ?? null;
                    $transportadora->livre = $object->LIVRE;
                    $transportadora->nomefantasia = $object->NOMEFANTASIA;
                    $transportadora->cei = $object->CEI;
                    $transportadora->inscrmunicipal = $object->INSCRMUNICIPAL;
                    $transportadora->ativo = $object->ATIVO;
                    $transportadora->email = $object->EMAIL;
                    
                    $transportadora->store();
                }
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Transportadoras atualizadas.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarCentroCusto() {
          try {
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT
                    CODCCUSTO AS codigo,
                    NOME AS nome,
                    CASE WHEN ATIVO = 'T' THEN 'S' ELSE 'N' END AS ativo
                FROM 
                    GCCUSTO
                WHERE
                    CODCOLIGADA in (1,2)
             ");
            
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            foreach($objects as $object)
            {
                $custo = (CentroCusto::where('codcusto','=', $object->codigo)->first()) ?? new CentroCusto();
                $custo->codcusto = $object->codigo;
                $custo->nome = $object->nome;
                $custo->ativo = $object->ativo;
                
                $custo->store();
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Centros de Custo atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarCondicaoPagamento() {
          try {
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT
                    CODCPG AS codigo,
                    NOME AS nome,
                    CASE WHEN INATIVO = 0 THEN 'S' ELSE 'N' END AS ativo
                FROM 
                    TCPG
                WHERE
                    CODCOLIGADA in (1,2)
             ");
            
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            foreach($objects as $objects)
            {
                $tcpg = (CondicaoPagamento::where('codcpg','=', $objects->codigo)->first()) ?? new CondicaoPagamento();
                $tcpg->codcpg = $objects->codigo;
                $tcpg->nome = $objects->nome;
                $tcpg->ativo = $objects->ativo;
                
                $tcpg->store();
            }
            TTransaction::close();
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 0, "Condições de Pagamento atualizadas.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização Diária", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    
    /****************************************************************************************************************************************************************************/ 
    public static function atualizarComplemento() {
         try {
            TTransaction::open('minicrm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT 
                (data_alteracao_totvs - INTERVAL '3 day') AS data_alteracao_totvs
            FROM 
                public.complemento 
            WHERE 
                data_alteracao_totvs IS NOT NULL 
            ORDER BY 
                data_alteracao_totvs DESC 
            LIMIT 1;
            ");
            
            $hora = $result->fetch(PDO::FETCH_ASSOC);
            TTransaction::close();
            
            $hora = ($hora !== false && isset($hora['data_alteracao_totvs'])) ? $hora['data_alteracao_totvs'] : null;
            
            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query("
                SELECT 
                    CODCOLIGADA AS codcoligada,
                	CODRPR  AS representante,
                	CODTRA AS transportadora,
                	CODTRA2 AS transportadora2,
                	CODCFO AS cliente,
                	CODVEN AS vendedor,
                	CIFFOB AS frete,
                	RECMODIFIEDON AS modificacao
                FROM 
                	FCFODEF 
                WHERE 
                	CODCFO IS NOT NULL 
                	AND CODCOLIGADA in (1,2)
                	AND RECMODIFIEDON >= '{$hora}'
             ");
            
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();
            
            TTransaction::open('minicrm');
            
            $clienteArray = Pessoa::getIndexedArray('id', 'codigo');
            $representanteArray = Representante::getIndexedArray('id', 'codigo');
            $transportadoraArray = Transportadora::getIndexedArray('id', 'codtra');
            
            TTransaction::close();
               
            foreach ($objects as $object) {
                $pessoaId = array_search($object->cliente, $clienteArray);
                if ($pessoaId !== false) {
                    
                    TTransaction::open('minicrm');
                    $complemento = Complemento::where('pessoa_id', '=', $pessoaId)
                        ->where('codcoligada', '=', $object->codcoligada)
                        ->first() ?? new Complemento();
                    TTransaction::close();
                    
                        $complemento->codcoligada = $object->codcoligada;
                        $complemento->pessoa_id = $pessoaId;
                        
                        $representante_totvs = array_search($object->representante, $representanteArray, true);

                        if ($object->representante === null || $object->representante === '' || $representante_totvs === false) {
                            $representante_totvs = 0;
                        }                        

                        TTransaction::open('minicrm');
                        $qtdeInteracoes = Interacao::where('cliente_id','=',$complemento->pessoa_id)->getIndexedArray('vendedor_id');
                        TTransaction::close();
                        
                        if($complemento->representante_id != $representante_totvs && $qtdeInteracoes > 0){
                            
                            $repres_interacoes = array_unique($qtdeInteracoes);
                            
                            
                            
                            if(count($repres_interacoes) > 1){
                                TTransaction::open('minicrm');
                                $divergencia = RepresentanteDivergente::where('pessoa_id','=',$complemento->pessoa_id)->where('status','=',0)->first() ?? new RepresentanteDivergente();
                                TTransaction::close();
                                $divergencia->pessoa_id    = $complemento->pessoa_id;
                                $divergencia->rep_ap_id    = $complemento->representante_id ?? 0;
                                $divergencia->rep_totvs_id = $representante_totvs ?? 0;
                                $divergencia->status       = 0;
                                TTransaction::open('minicrm');
                                $divergencia->store();
                                TTransaction::close();
                            }else{
                                $complemento->representante_id = $representante_totvs;
                            }
                            
                            
                        }else{
                            $complemento->representante_id = $representante_totvs;
                        }
                        
                        $complemento->transportadora_id = array_search($object->transportadora, $transportadoraArray) ?: null;
                        $complemento->transportadora1_id = array_search($object->transportadora2, $transportadoraArray) ?: null;
                        $complemento->ciffob = $object->frete;
                        $complemento->data_alteracao_totvs = $object->modificacao;
                        
                        TTransaction::open('minicrm');
                        $complemento->store();
                        TTransaction::close();
                    
                }
            }
            
            //Registro de log de execução
            LogCrontab::registrarLog("Atualização de Clientes", __METHOD__, 0, "Complementos atualizados.", "Arquivo: AtualizacaoDiaria.\nLinha: ".__LINE__.".");
        } catch (Exception $e) {
            LogCrontab::registrarLog("Atualização de Clientes", __METHOD__, 1, "Exception: ".$e->getMessage(), "Arquivo: " . $e->getFile() . "\nLinha: " . $e->getLine() . "\n");
        }
    }
    public static function normalizarTexto($texto)
    {
        $texto = trim((string) $texto);
        $texto = mb_strtoupper($texto, 'UTF-8');
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        $texto = preg_replace('/[^A-Z0-9 ]/', ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        return trim($texto);
    }
}