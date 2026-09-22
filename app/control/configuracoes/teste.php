<?php

class teste extends TPage
{

    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'minicrm';
    private static $activeRecord = 'Produto';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Produto';
    private $limit = 20;

    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 20;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_tipo_produto_nome = new TDataGridColumn('tipo_produto->nome', "Tipo de produto", 'left');
        $column_familia_produto_nome = new TDataGridColumn('familia_produto->nome', "Família de produto", 'left');
        $column_fornecedor_razao_social = new TDataGridColumn('fornecedor->razao_social', "Fornecedor", 'left');
        $column_fabricante_nome = new TDataGridColumn('fabricante->nome', "Fabricante", 'left');
        $column_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_preco_venda = new TDataGridColumn('preco_venda', "Preco venda", 'left');
        $column_qtde_estoque = new TDataGridColumn('qtde_estoque', "Qtde estoque", 'left');
        $column_ativo = new TDataGridColumn('ativo', "Ativo", 'left');

        $this->datagrid->addColumn($column_tipo_produto_nome);
        $this->datagrid->addColumn($column_familia_produto_nome);
        $this->datagrid->addColumn($column_fornecedor_razao_social);
        $this->datagrid->addColumn($column_fabricante_nome);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_preco_venda);
        $this->datagrid->addColumn($column_qtde_estoque);
        $this->datagrid->addColumn($column_ativo);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $panel->getBody()->insert(0, $headerActions);

        $button_atualizar_cliente_especifico = new TButton('button_button_atualizar_cliente_especifico');
        $button_atualizar_cliente_especifico->setAction(new TAction(['teste', 'onsync']), "ATUALIZAR CLIENTE ESPECIFICO");
        $button_atualizar_cliente_especifico->addStyleClass('btn-default');
        $button_atualizar_cliente_especifico->setImage('far:circle #000000');

        $this->datagrid_form->addField($button_atualizar_cliente_especifico);

        $button_depois_d_alterar = new TButton('button_button_depois_d_alterar');
        $button_depois_d_alterar->setAction(new TAction(['teste', 'onAltSync']), "depois d alterar");
        $button_depois_d_alterar->addStyleClass('btn-default');
        $button_depois_d_alterar->setImage('far:circle #000000');

        $this->datagrid_form->addField($button_depois_d_alterar);

        $button_teste_sql = new TButton('button_button_teste_sql');
        $button_teste_sql->setAction(new TAction(['teste', 'testesqQQl']), "teste sql");
        $button_teste_sql->addStyleClass('btn-default');
        $button_teste_sql->setImage('far:circle #000000');

        $this->datagrid_form->addField($button_teste_sql);

        $button_atualizar_cpfs = new TButton('button_button_atualizar_cpfs');
        $button_atualizar_cpfs->setAction(new TAction(['teste', 'onAttCpfs']), "atualizar cpfs");
        $button_atualizar_cpfs->addStyleClass('btn-default');
        $button_atualizar_cpfs->setImage('far:circle #000000');

        $this->datagrid_form->addField($button_atualizar_cpfs);

        $button_atualizar_notas = new TButton('button_button_atualizar_notas');
        $button_atualizar_notas->setAction(new TAction(['teste', 'atualizarNotas']), "atualizar notas");
        $button_atualizar_notas->addStyleClass('btn-default');
        $button_atualizar_notas->setImage('far:circle #000000');

        $this->datagrid_form->addField($button_atualizar_notas);

        $button_atualizar_comissao = new TButton('button_button_atualizar_comissao');
        $button_atualizar_comissao->setAction(new TAction(['teste', 'atualizarComissao']), "atualizar comissao");
        $button_atualizar_comissao->addStyleClass('btn-default');
        $button_atualizar_comissao->setImage('far:circle #000000');

        $this->datagrid_form->addField($button_atualizar_comissao);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['teste', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['teste', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['teste', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['teste', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_atualizar_cliente_especifico);
        $head_left_actions->add($button_depois_d_alterar);
        $head_left_actions->add($button_teste_sql);
        $head_left_actions->add($button_atualizar_cpfs);
        $head_left_actions->add($button_atualizar_notas);
        $head_left_actions->add($button_atualizar_comissao);

        $head_right_actions->add($dropdown_button_exportar);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Configurações","teste"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();

                            if (isset($object->$column_name))
                            {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos((string)$column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';
                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags((string)call_user_func($transformer, $value, $object, null));
                            }

                            $table->addCell($value, 'center', 'data');
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('iframe');
                $object->src  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value)); 
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onsync($param = null) 
    {
 $codigo = 'C029672';

    try {

        /*
         * =========================================================
         * BUSCA CLIENTE + ENDEREÇOS NO TOTVS
         * =========================================================
         */
        TTransaction::open('corporerm');
        $conn = TTransaction::get();

        $stmt = $conn->prepare("
            SELECT
                CODCFO,
                NOMEFANTASIA AS fantasia,
                UPPER(NOME) AS razao_social,
                CGCCFO AS cpf_cnpj,
                INSCRESTADUAL AS ie,

                CASE
                    WHEN PESSOAFISOUJUR = 'F' THEN 1
                    WHEN PESSOAFISOUJUR = 'J' THEN 2
                END AS tipo_pessoa_id,

                CODTCF,
                TELEFONE,
                EMAIL,
                DATAULTALTERACAO,

                CASE
                    WHEN ATIVO = 1 THEN 'S'
                    ELSE 'N'
                END AS ativo,

                CASE
                    WHEN CFOIMOB = 1 THEN 'S'
                    ELSE 'N'
                END AS bloqueado,

                /* PRINCIPAL */
                RUA,
                NUMERO,
                COMPLEMENTO,
                BAIRRO,
                CIDADE,
                CODMUNICIPIO,
                CODETD,
                CEP,

                /* PAGAMENTO */
                RUAPGTO,
                NUMEROPGTO,
                COMPLEMENTOPGTO,
                BAIRROPGTO,
                CIDADEPGTO,
                CODMUNICIPIOPGTO,
                CODETDPGTO,
                CEPPGTO,

                /* ENTREGA */
                RUAENTREGA,
                NUMEROENTREGA,
                COMPLEMENTREGA,
                BAIRROENTREGA,
                CIDADEENTREGA,
                CODMUNICIPIOENTREGA,
                CODETDENTREGA,
                CEPENTREGA

            FROM FCFO
            WHERE CODCFO = :codigo
        ");

        $stmt->execute([
            ':codigo' => $codigo
        ]);

        $totvs = $stmt->fetch(PDO::FETCH_OBJ);

        TTransaction::close();

        if (!$totvs) {
            throw new Exception("CODCFO {$codigo} não encontrado no TOTVS");
        }

        $cpfCnpj = preg_replace('/[^0-9]/', '', (string) $totvs->cpf_cnpj);

        if (!$cpfCnpj) {
            throw new Exception("Cliente {$codigo} está sem CPF/CNPJ no TOTVS");
        }

        /*
         * =========================================================
         * ATUALIZA CLIENTE NO MINICRM
         * =========================================================
         */
        TTransaction::open('minicrm');

        /*
         * Procura primeiro pelo CODCFO.
         * Se não existir, procura CPF/CNPJ.
         */
        $cliente = Pessoa::where('codigo', '=', $codigo)->first();

        if (!$cliente) {
            $cliente = Pessoa::where('cpf_cnpj', '=', $cpfCnpj)->first();
        }

        if (!$cliente) {
            $cliente = new Pessoa();
        }

        $categoria = CategoriaCliente::where(
            'codigo',
            '=',
            $totvs->CODTCF
        )->first();

        $cliente->tipo_pessoa_id = $totvs->tipo_pessoa_id;
        $cliente->codigo = $totvs->CODCFO;
        $cliente->nome_fantasia = $totvs->fantasia;
        $cliente->razao_social = $totvs->razao_social;
        $cliente->cpf_cnpj = $cpfCnpj;
        $cliente->rg_id = $totvs->ie;
        $cliente->email = $totvs->EMAIL;
        $cliente->fone = preg_replace('/[^0-9]/', '', (string) $totvs->TELEFONE);
        $cliente->ativo = $totvs->ativo;
        $cliente->bloqueado = $totvs->bloqueado;
        $cliente->categoria_cliente_id = $categoria->id ?? null;
        $cliente->data_alteracao_totvs = $totvs->DATAULTALTERACAO;
        $cliente->updated_at = date('Y-m-d H:i:s');
        $cliente->origem = null;

        $cliente->store();

        $clienteId = $cliente->id;

        /*
         * Garante que é CLIENTE
         */
        if (!PessoaGrupo::where('pessoa_id', '=', $clienteId)->first()) {

            $grupo = new PessoaGrupo();
            $grupo->pessoa_id = $clienteId;
            $grupo->grupo_id = Grupo::CLIENTE;
            $grupo->store();
        }

        /*
         * =========================================================
         * MONTA OS 3 ENDEREÇOS
         * =========================================================
         */
        $enderecos = [
            [
                'tipo'          => 'Principal',
                'rua'           => $totvs->RUA,
                'numero'        => $totvs->NUMERO,
                'complemento'   => $totvs->COMPLEMENTO,
                'bairro'        => $totvs->BAIRRO,
                'cidade'        => $totvs->CIDADE,
                'cod_municipio' => $totvs->CODMUNICIPIO,
                'uf'            => $totvs->CODETD,
                'cep'           => $totvs->CEP,
                'principal'     => 'S',
            ],

            [
                'tipo'          => 'Pagamento',
                'rua'           => $totvs->RUAPGTO,
                'numero'        => $totvs->NUMEROPGTO,
                'complemento'   => $totvs->COMPLEMENTOPGTO,
                'bairro'        => $totvs->BAIRROPGTO,
                'cidade'        => $totvs->CIDADEPGTO,
                'cod_municipio' => $totvs->CODMUNICIPIOPGTO,
                'uf'            => $totvs->CODETDPGTO,
                'cep'           => $totvs->CEPPGTO,
                'principal'     => 'N',
            ],

            [
                'tipo'          => 'Entrega',
                'rua'           => $totvs->RUAENTREGA,
                'numero'        => $totvs->NUMEROENTREGA,
                'complemento'   => $totvs->COMPLEMENTREGA,
                'bairro'        => $totvs->BAIRROENTREGA,
                'cidade'        => $totvs->CIDADEENTREGA,
                'cod_municipio' => $totvs->CODMUNICIPIOENTREGA,
                'uf'            => $totvs->CODETDENTREGA,
                'cep'           => $totvs->CEPENTREGA,
                'principal'     => 'N',
            ],
        ];

        foreach ($enderecos as $dados) {

            $cidadeNome = trim((string) $dados['cidade']);
            $uf = strtoupper(trim((string) $dados['uf']));

            /*
             * Igual seu sync:
             * pagamento/entrega vazio não cria lixo.
             */
            if ($cidadeNome === '') {
                continue;
            }

            $codMunicipio = preg_replace(
                '/[^0-9]/',
                '',
                (string) $dados['cod_municipio']
            );

            if ($codMunicipio !== '') {
                $codMunicipio = str_pad(
                    $codMunicipio,
                    5,
                    '0',
                    STR_PAD_LEFT
                );
            }

            /*
             * Procura estado
             */
            $estado = Estado::where('sigla', '=', $uf)->first();

            if (!$estado) {
                throw new Exception(
                    "Estado {$uf} não encontrado no MiniCRM"
                );
            }

            /*
             * Procura cidade pelo código do município primeiro
             */
            $cidade = null;

            if ($codMunicipio !== '') {
                $cidade = Cidade::where(
                    'cod_municipio',
                    '=',
                    $codMunicipio
                )
                ->where(
                    'estado_id',
                    '=',
                    $estado->id
                )
                ->first();
            }

            /*
             * Fallback pelo nome
             */
            if (!$cidade) {
                $cidade = Cidade::where(
                    'nome',
                    '=',
                    strtoupper($cidadeNome)
                )
                ->where(
                    'estado_id',
                    '=',
                    $estado->id
                )
                ->first();
            }

            if (!$cidade) {
                throw new Exception(
                    "Cidade não encontrada: {$cidadeNome}/{$uf} " .
                    "(CODMUNICIPIO {$codMunicipio})"
                );
            }

            /*
             * Atualiza/cria endereço daquele tipo
             */
            $endereco = PessoaEndereco::where(
                'pessoa_id',
                '=',
                $clienteId
            )
            ->where(
                'nome',
                '=',
                $dados['tipo']
            )
            ->first();

            if (!$endereco) {
                $endereco = new PessoaEndereco();
            }

            $endereco->pessoa_id = $clienteId;
            $endereco->nome = $dados['tipo'];
            $endereco->cep = preg_replace(
                '/[^0-9]/',
                '',
                (string) $dados['cep']
            ) ?: null;

            $endereco->numero = trim(
                (string) $dados['numero']
            ) ?: null;

            $endereco->cidade_id = $cidade->id;

            $endereco->complemento = trim(
                (string) $dados['complemento']
            ) ?: null;

            $endereco->rua = trim(
                (string) $dados['rua']
            ) ?: null;

            $endereco->bairro = trim(
                (string) $dados['bairro']
            ) ?: null;

            $endereco->principal = $dados['principal'];

            $endereco->data_alteracao_totvs =
                $totvs->DATAULTALTERACAO;

            $endereco->store();
        }

        TTransaction::close();

        new TMessage(
            'info',
            "C029672 atualizado.<br>
             Pessoa ID: {$clienteId}<br>
             CPF/CNPJ: {$cpfCnpj}<br>
             {$totvs->razao_social}"
        );

    } catch (Exception $e) {

        if (TTransaction::get()) {
            TTransaction::rollback();
        }

        new TMessage(
            'error',
            $e->getMessage()
        );
    }

            //</autoCode>

    }

    public function onAltSync($param = null) 
    {
        try 
        {
             $sqlNota =" SELECT
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
                        AND m.codcoligada = 1
                        AND m.numeromov = '021488'
                    ORDER BY
                        m.numeromov";

            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query($sqlNota);
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();

            echo "<pre>";
            var_dump($objects);
            echo "</pre>";

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function testesqQQl($param = null) 
    {
        try 
        {
             $sqlNota ="SELECT l.IDLAN, l.NUMERODOCUMENTO, t.CODTRB, t.VALOR
            FROM FLAN l
            LEFT JOIN FTRBLAN t ON t.CODCOLIGADA = l.CODCOLIGADA AND t.IDLAN = l.IDLAN
            WHERE l.CODCOLIGADA = 1
            AND l.IDMOV = (SELECT IDMOV FROM TMOV WHERE CODCOLIGADA=1 AND NUMEROMOV='021488' AND CODTMV='2.2.15')";

            TTransaction::open('corporerm');
            $conn = TTransaction::get();
            $result = $conn->query($sqlNota);
            $objects = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
            TTransaction::close();

            echo "<pre>";
            var_dump($objects);
            echo "</pre>";
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onAttCpfs($param = null) 
    {
        try 
        {

        TTransaction::open('minicrm');

        $conn = TTransaction::get();

        $sql = "
            SELECT 
                p.id,
                p.codigo,
                p.razao_social,
                p.cpf_cnpj
            FROM 
                pessoa p
            WHERE 
                NOT EXISTS (
                    SELECT 1
                    FROM pessoa_endereco pe
                    WHERE pe.pessoa_id = p.id
                )
                AND p.codigo IS NULL 
                AND p.cpf_cnpj IS NOT NULL
                AND LENGTH(regexp_replace(p.cpf_cnpj, '[^0-9]', '', 'g')) = 14
        ";

        $result = $conn->query($sql);
        $pessoas = $result->fetchAll(PDO::FETCH_OBJ);

        $total = 0;
        $sucesso = 0;
        $ignoradas = 0;
        $erros = [];

        foreach ($pessoas as $pessoaRow)
        {
            $total++;

            try
            {
                $cnpj = preg_replace('/[^0-9]/', '', $pessoaRow->cpf_cnpj);

                if (empty($cnpj) || strlen($cnpj) != 14)
                {
                    $ignoradas++;
                    continue;
                }

                $dados = CNPJService::get($cnpj);
                $dadosFull = CNPJService::getFull($cnpj);

                if (!$dados || !$dadosFull)
                {
                    $ignoradas++;
                    $erros[] = "Pessoa ID {$pessoaRow->id}: CNPJ não encontrado na API.";
                    continue;
                }

                $cidadeId = $dados->cidade_id ?? null;

                if (empty($cidadeId))
                {
                    $ignoradas++;
                    $erros[] = "Pessoa ID {$pessoaRow->id}: cidade_id não encontrado para o CNPJ {$cnpj}.";
                    continue;
                }

                $jaTemEndereco = PessoaEndereco::where('pessoa_id', '=', $pessoaRow->id)->first();

                if ($jaTemEndereco)
                {
                    $ignoradas++;
                    continue;
                }

                $endereco = new PessoaEndereco();
                $endereco->pessoa_id     = $pessoaRow->id;
                $endereco->cep           = $dados->cep ?? null;
                $endereco->rua           = $dados->logradouro ?? null;
                $endereco->bairro        = $dados->bairro ?? null;
                $endereco->numero        = $dados->numero ?? null;
                $endereco->complemento   = $dados->complemento ?? null;
                $endereco->cidade_id     = $cidadeId;
                $endereco->principal     = 'S';

                $endereco->store();

                $sucesso++;
            }
            catch (Exception $eItem)
            {
                $erros[] = "Pessoa ID {$pessoaRow->id}: " . $eItem->getMessage();
            }
        }

        TTransaction::close();

        $mensagem = "Processamento concluído.<br>"
                  . "Total encontrados: {$total}<br>"
                  . "Endereços criados: {$sucesso}<br>"
                  . "Ignorados: {$ignoradas}";

        if (!empty($erros))
        {
            $mensagem .= "<br><br><b>Erros:</b><br>" . implode('<br>', $erros);
        }

        new TMessage('info', $mensagem);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function atualizarNotas($param = null) 
    {
        try 
        {
             $retorno = SigissWebService::atualizarDataEmissaoOsNotaBaixada(true);

                if ($retorno['status'] === 'success') {
                    new TMessage('info', $retorno['mensagem']);
                } else {
                    new TMessage('error', $retorno['mensagem']);
                }

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function atualizarComissao($param = null) 
    {
        try 
        {
            $form = new BootstrapFormBuilder('form_periodo_comissao');
            $form->setFormTitle('Período da comissão');

            $data_inicial = new TDate('data_inicial_comissao');
            $data_final   = new TDate('data_final_comissao');

            $data_inicial->setMask('dd/mm/yyyy');
            $data_final->setMask('dd/mm/yyyy');

            $data_inicial->setDatabaseMask('yyyy-mm-dd');
            $data_final->setDatabaseMask('yyyy-mm-dd');

            $data_inicial->setSize('100%');
            $data_final->setSize('100%');

            // Já abre preenchido com o mês atual
            $data_inicial->setValue(date('Y-m-01'));
            $data_final->setValue(date('Y-m-t'));

            $data_inicial->addValidation('Data inicial', new TRequiredValidator);
            $data_final->addValidation('Data final', new TRequiredValidator);

            $form->addFields(
                [new TLabel('Data inicial'), $data_inicial],
                [new TLabel('Data final'), $data_final]
            );

            $form->addAction(
                'Atualizar comissão',
                new TAction([$this, 'confirmarAtualizacaoComissao']),
                'fa:money-bill'
            );

            $window = TWindow::create('Atualizar comissão por período', 0.45, null);
            $window->add($form);
            $window->show();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minicrm'
            TTransaction::open(self::$database);

            // creates a repository for Produto
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = new TCriteria;

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Produto($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public function confirmarAtualizacaoComissao($param = null)
    {
        try 
        {
            $dataInicial = $param['data_inicial_comissao'] ?? null;
            $dataFinal   = $param['data_final_comissao'] ?? null;

            if (empty($dataInicial) || empty($dataFinal)) {
                new TMessage('error', 'Informe a data inicial e a data final.');
                return;
            }

            $retorno = SigissWebService::atualizarComissaoNotasBaixadasPorPeriodo(
                $dataInicial,
                $dataFinal,
                true
            );

            if ($retorno['status'] === 'success') {
                new TMessage('info', $retorno['mensagem']);
            } else {
                new TMessage('error', $retorno['mensagem']);
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

}

