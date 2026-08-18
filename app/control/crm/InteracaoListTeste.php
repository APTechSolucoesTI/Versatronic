<?php

class InteracaoListTeste extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'Interacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

    use BuilderDatagridTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Listagem de Interações");
        $this->limit = 20;

        $criteria_cliente_id = new TCriteria();
        $criteria_etapa_interacao_id = new TCriteria();
        $criteria_id = new TCriteria();
        $criteria_tipo_interacao_nome = new TCriteria();
        $criteria_cliente_razao_social = new TCriteria();
        $criteria_cidade = new TCriteria();
        $criteria_estado = new TCriteria();
        $criteria_vendedor_id = new TCriteria();
        $criteria_origem_contato_nome = new TCriteria();
        $criteria_attnainteracao = new TCriteria();
        $criteria_etapa_interacao_nome = new TCriteria();

        $filterVar = Grupo::CLIENTE;
        $criteria_cliente_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_id = '{$filterVar}')")); 

        $filter = new TFilter('id', 'in', "(SELECT cliente_id FROM interacao)");
         TTransaction::open(self::$database);
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
             $filter = new TFilter('id', 'in', "(SELECT cliente_id FROM interacao WHERE vendedor_id in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");

        }
        TTransaction::close();

        $criteria_cliente_razao_social->add($filter);

        $filter = new TFilter('id', 'in', "(SELECT vendedor_id FROM interacao)");
        $criteria_vendedor_id->add($filter);

        $cliente_id = new TDBUniqueSearch('cliente_id', 'minicrm', 'Pessoa', 'id', 'razao_social','razao_social asc' , $criteria_cliente_id );
        $etapa_interacao_id = new TDBCombo('etapa_interacao_id', 'minicrm', 'EtapaInteracao', 'id', '{nome}','nome asc' , $criteria_etapa_interacao_id );
        $data_inicio = new TDate('data_inicio');
        $data_inicio_final = new TDate('data_inicio_final');
        $id = new TDBCombo('id', 'minicrm', 'Interacao', 'id', '{id}','id asc' , $criteria_id );
        $tipo_interacao_nome = new TDBCombo('tipo_interacao_nome', 'minicrm', 'TipoInteracao', 'id', '{nome}','nome asc' , $criteria_tipo_interacao_nome );
        $cliente_razao_social = new TDBUniqueSearch('cliente_razao_social', 'minicrm', 'Pessoa', 'id', 'razao_social','razao_social asc' , $criteria_cliente_razao_social );
        $cliente_cpf_cnpj = new TEntry('cliente_cpf_cnpj');
        $cidade = new TDBCombo('cidade', 'minicrm', 'Interacao', 'cidade', '{cidade}','id asc' , $criteria_cidade );
        $estado = new TDBCombo('estado', 'minicrm', 'Interacao', 'estado', '{estado}','id asc' , $criteria_estado );
        $vendedor_id = new TDBUniqueSearch('vendedor_id', 'minicrm', 'Representante', 'id', 'razao_social','razao_social asc' , $criteria_vendedor_id );
        $data_inicio_col = new TDate('data_inicio_col');
        $data_fechamento_esperada = new TDate('data_fechamento_esperada');
        $data_fechamento = new TDate('data_fechamento');
        $origem_contato_nome = new TDBCombo('origem_contato_nome', 'minicrm', 'OrigemContato', 'id', '{nome}','nome asc' , $criteria_origem_contato_nome );
        $attnainteracao = new TDBCombo('attnainteracao', 'minicrm', 'TipoAtividade', 'id', '{nome}','id asc' , $criteria_attnainteracao );
        $etapa_interacao_nome = new TDBCombo('etapa_interacao_nome', 'minicrm', 'EtapaInteracao', 'id', '{nome}','id asc' , $criteria_etapa_interacao_nome );

        $cliente_cpf_cnpj->exitOnEnter();

        $cliente_cpf_cnpj->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $data_inicio_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $data_fechamento_esperada->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $data_fechamento->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $tipo_interacao_nome->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cliente_razao_social->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cidade->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $estado->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $vendedor_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $origem_contato_nome->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $attnainteracao->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $etapa_interacao_nome->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $cliente_razao_social->setFilterColumns(["razao_social"]);
        $cliente_id->setMinLength(0);
        $vendedor_id->setMinLength(0);
        $cliente_razao_social->setMinLength(3);

        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_inicio_col->setDatabaseMask('yyyy-mm-dd');
        $data_fechamento->setDatabaseMask('yyyy-mm-dd');
        $data_inicio_final->setDatabaseMask('yyyy-mm-dd');
        $data_fechamento_esperada->setDatabaseMask('yyyy-mm-dd');

        $data_inicio->setMask('dd/mm/yyyy');
        $cliente_id->setMask('{razao_social}');
        $vendedor_id->setMask('{razao_social}');
        $data_inicio_col->setMask('dd/mm/yyyy');
        $data_fechamento->setMask('dd/mm/yyyy');
        $data_inicio_final->setMask('dd/mm/yyyy');
        $data_fechamento_esperada->setMask('dd/mm/yyyy');
        $cliente_razao_social->setMask('{razao_social}  - {cpf_cnpj}');

        $id->enableSearch();
        $cidade->enableSearch();
        $estado->enableSearch();
        $attnainteracao->enableSearch();
        $etapa_interacao_id->enableSearch();
        $tipo_interacao_nome->enableSearch();
        $origem_contato_nome->enableSearch();
        $etapa_interacao_nome->enableSearch();

        $id->setSize('100%');
        $cidade->setSize('100%');
        $estado->setSize('100%');
        $data_inicio->setSize(110);
        $cliente_id->setSize('100%');
        $vendedor_id->setSize('100%');
        $data_inicio_col->setSize(110);
        $data_fechamento->setSize(110);
        $data_inicio_final->setSize(110);
        $attnainteracao->setSize('100%');
        $cliente_cpf_cnpj->setSize('100%');
        $etapa_interacao_id->setSize('100%');
        $tipo_interacao_nome->setSize('100%');
        $origem_contato_nome->setSize('100%');
        $cliente_razao_social->setSize('100%');
        $etapa_interacao_nome->setSize('100%');
        $data_fechamento_esperada->setSize(110);

        $row1 = $this->form->addFields([new TLabel("Cliente:", null, '14px', null, '100%'),$cliente_id],[new TLabel("Etapa:", null, '14px', null, '100%'),$etapa_interacao_id],[new TLabel("Data de início:", null, '14px', null, '100%'),$data_inicio,new TLabel("até", null, '14px', null),$data_inicio_final]);
        $row1->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_id = new TDataGridColumn('id', "Número", 'left');
        $column_tipo_interacao_nome = new TDataGridColumn('tipo_interacao->nome', "Tipo de Interação", 'left');
        $column_id_transformed = new TDataGridColumn('id', "Cliente", 'left');
        $column_cliente_cpf_cnpj_transformed = new TDataGridColumn('cliente->cpf_cnpj', "CPF/CNPJ", 'left');
        $column_cidade = new TDataGridColumn('cidade', "Cidade", 'left');
        $column_estado = new TDataGridColumn('estado', "Estado", 'left');
        $column_vendedor_razao_social = new TDataGridColumn('vendedor->razao_social', "Representante", 'left');
        $column_data_inicio_transformed = new TDataGridColumn('data_inicio', "Data de início", 'left');
        $column_data_fechamento_esperada_transformed = new TDataGridColumn('data_fechamento_esperada', "Data esperada de fechamento", 'left');
        $column_data_fechamento_transformed = new TDataGridColumn('data_fechamento', "Data de fechamento", 'left');
        $column_origem_contato_nome = new TDataGridColumn('origem_contato->nome', "Origem do contato", 'left');
        $column__transformed = new TDataGridColumn('', "Atividades", 'left');
        $column_etapa_interacao_nome_transformed = new TDataGridColumn('etapa_interacao->nome', "Etapa", 'left');

        $column_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if($object->cliente_id !== null){
                return $object->cliente->razao_social;
            }else{
                return $object->cliente_nome;
            }

        });

        $column_cliente_cpf_cnpj_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $numero = preg_replace('/\D/', '', $value);

            if (strlen($numero) === 11) {
                // Formatar CPF: 000.000.000-00
                return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $numero);
            } elseif (strlen($numero) === 14) {
                // Formatar CNPJ: 00.000.000/0000-00
                return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $numero);
            } 

        });

        $column_data_inicio_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_data_fechamento_esperada_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_data_fechamento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column__transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            TTransaction::open(self::$database);
            $conn = TTransaction::get();

            $sql = "
                SELECT ia.tipo_atividade_id,
                          ta.nome
                FROM interacao_atividade ia
                JOIN tipo_atividade ta ON ia.tipo_atividade_id = ta.id
                    WHERE ia.interacao_id = {$object->id}
                    GROUP BY ia.tipo_atividade_id,
        			         ta.nome
            ";

            $result = $conn->query($sql);

            $list = [];

            while ($item = $result->fetch(PDO::FETCH_OBJ)) {
                $list[] = $item->nome;
            }    

            $string = implode("<br>", $list);

            TTransaction::close(); 

            if(empty($list)){
                return 'Nenhuma';
            }   

            return $string;
        });

        $column_etapa_interacao_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if(!empty($object->etapa_interacao_id))
            {
                return "<span class='label ' style='width: 100%; max-width: 200px; background-color:{$object->etapa_interacao->cor}'>{$object->etapa_interacao->nome}</span>";
            }

        });        

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_tipo_interacao_nome);
        $this->datagrid->addColumn($column_id_transformed);
        $this->datagrid->addColumn($column_cliente_cpf_cnpj_transformed);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_estado);
        $this->datagrid->addColumn($column_vendedor_razao_social);
        $this->datagrid->addColumn($column_data_inicio_transformed);
        $this->datagrid->addColumn($column_data_fechamento_esperada_transformed);
        $this->datagrid->addColumn($column_data_fechamento_transformed);
        $this->datagrid->addColumn($column_origem_contato_nome);
        $this->datagrid->addColumn($column__transformed);
        $this->datagrid->addColumn($column_etapa_interacao_nome_transformed);

        $action_onShow = new TDataGridAction(array('InteracaoFormView', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Abrir");
        $action_onShow->setImage('far:folder-open #000000');
        $action_onShow->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onShow);

        $action_onOpenOnEdit = new TDataGridAction(array('InteracaoListTeste', 'onOpenOnEdit'));
        $action_onOpenOnEdit->setUseButton(false);
        $action_onOpenOnEdit->setButtonClass('btn btn-default btn-sm');
        $action_onOpenOnEdit->setLabel("Editar");
        $action_onOpenOnEdit->setImage('far:edit #478fca');
        $action_onOpenOnEdit->setField(self::$primaryKey);

        $action_onOpenOnEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onOpenOnEdit);

        $action_onDelete = new TDataGridAction(array('InteracaoListTeste', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        $this->applyDatagridProperties();
        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        if(!$action_onShow->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_onOpenOnEdit->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_onDelete->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_id = TElement::tag('td', $id);
        $tr->add($td_id);
        $td_tipo_interacao_nome = TElement::tag('td', $tipo_interacao_nome);
        $tr->add($td_tipo_interacao_nome);
        $td_cliente_razao_social = TElement::tag('td', $cliente_razao_social);
        $tr->add($td_cliente_razao_social);
        $td_cliente_cpf_cnpj = TElement::tag('td', $cliente_cpf_cnpj);
        $tr->add($td_cliente_cpf_cnpj);
        $td_cidade = TElement::tag('td', $cidade);
        $tr->add($td_cidade);
        $td_estado = TElement::tag('td', $estado);
        $tr->add($td_estado);
        $td_vendedor_id = TElement::tag('td', $vendedor_id);
        $tr->add($td_vendedor_id);
        $td_data_inicio_col = TElement::tag('td', $data_inicio_col);
        $tr->add($td_data_inicio_col);
        $td_data_fechamento_esperada = TElement::tag('td', $data_fechamento_esperada);
        $tr->add($td_data_fechamento_esperada);
        $td_data_fechamento = TElement::tag('td', $data_fechamento);
        $tr->add($td_data_fechamento);
        $td_origem_contato_nome = TElement::tag('td', $origem_contato_nome);
        $tr->add($td_origem_contato_nome);
        $td_attnainteracao = TElement::tag('td', $attnainteracao);
        $tr->add($td_attnainteracao);
        $td_etapa_interacao_nome = TElement::tag('td', $etapa_interacao_nome);
        $tr->add($td_etapa_interacao_nome);
        $tr->add(TElement::tag('td', ''));

        $this->datagrid_form->addField($id);
        $this->datagrid_form->addField($tipo_interacao_nome);
        $this->datagrid_form->addField($cliente_razao_social);
        $this->datagrid_form->addField($cliente_cpf_cnpj);
        $this->datagrid_form->addField($cidade);
        $this->datagrid_form->addField($estado);
        $this->datagrid_form->addField($vendedor_id);
        $this->datagrid_form->addField($data_inicio_col);
        $this->datagrid_form->addField($data_fechamento_esperada);
        $this->datagrid_form->addField($data_fechamento);
        $this->datagrid_form->addField($origem_contato_nome);
        $this->datagrid_form->addField($attnainteracao);
        $this->datagrid_form->addField($etapa_interacao_nome);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Listagem de Interações");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;

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

        $this->datagrid_form->add($headerActions);

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['InteracaoForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $button_simples = new TButton('button_button_simples');
        $button_simples->setAction(new TAction(['InteracaoListTeste', 'onLocSimples']), "Simples");
        $button_simples->addStyleClass('btn-default');
        $button_simples->setImage('fas:plus #69AA46');

        $this->datagrid_form->addField($button_simples);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['InteracaoListTeste', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['InteracaoListTeste', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['InteracaoListTeste', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['InteracaoListTeste', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['InteracaoListTeste', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['InteracaoListTeste', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['InteracaoListTeste', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);
        $head_left_actions->add($button_simples);
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Interações TESTE"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onOpenOnEdit($param = null) 
    {
        try 
        {

            $key = $param['key'];

            TTransaction::open('minicrm');

            $interacao = Interacao::where('id', '=', $key)->first();
            $tipo_interacao = $interacao->tipo_interacao_id;

            $page_param = ['key'=>$key];
            TTransaction::close();

            if ($tipo_interacao == 1) {
                TApplication::loadPage('InteracaoForm', 'onEdit', $page_param);
            }
            else if ($tipo_interacao == 2) {
                TApplication::loadPage('InteracaoSimplesForm', 'onEdit', $page_param);
            }            
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                $object = new Interacao($key, FALSE); 
                $atividades = (InteracaoAtividade::where('interacao_id','=',$object->id))->count();

                if($atividades > 0)
                {
                    TTransaction::close();
                    new TMessage('info', "Não é possivel deletar interação que contém atividades!");
                    return;
                }

                $interacaoArquivos = InteracaoArquivo::where('interacao_id', '=', $object->id)->load();
                $interacaoArquivosIds = [];
                if (!empty($interacaoArquivos)) {
                    foreach ($interacaoArquivos as $interacaoArquivo) {
                        if (!empty($interacaoArquivo)) {
                            $interacaoArquivosIds[] = $interacaoArquivo->id;                            
                        }
                    }
                    if (!empty($interacaoArquivosIds)) {                        
                        foreach ($interacaoArquivosIds as $interacaoArquivosId) {
                            $interacaoArq = InteracaoArquivo::find($interacaoArquivosId);
                            $caminhoArquivo = trim((string) $interacaoArq->conteudo_arquivo);
                            if (!empty($caminhoArquivo) && is_file($caminhoArquivo)) {
                                @unlink($caminhoArquivo);
                            }

                            $interacaoArq->delete();                        
                        }                    
                    }
                }

                $criteria = new TCriteria;
                $criteria->add(new TFilter('interacao_id', '=', $object->id));

                $tabelas = [
                    'InteracaoHistoricoObservacao',
                    'InteracaoHistoricoAtividade',
                    'InteracaoHistoricoArquivo',
                    'InteracaoHistoricoEtapa',
                    'InteracaoLocalizacao',
                    'InteracaoObservacao',
                    'InteracaoArquivo',                    
                    'InteracaoItem'

                ];

                foreach ($tabelas as $tabela)
                {
                    (new TRepository($tabela))->delete($criteria);
                }
                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));        
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
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
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
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
                    else if (strpos($column->getWidth(), '%') !== false)
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
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags(call_user_func($transformer, $value, $object, null));
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
                $object = new TElement('object');
                $object->data  = $output;
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
                                $column_name = (strpos($column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
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
    public function onLocSimples($param = null) 
    {
        try 
        {            
            TApplication::loadPage('InteracaoSimplesForm');
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
            //code here

                        $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'InteracaoListTesteSearch');
            $page->setProperty('page_name', 'InteracaoListTesteSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onClearFilters($param = null) 
    {
        for ($i = 0; $i < 7; $i++) {

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
        }              
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        if ((isset($param['static']) && ($param['static'] == '1')) || !empty($param['globalSearch']))
        {
            $data = $this->datagrid_form->getData();
        }
        else
        {
            $data = $this->form->getData();
        }
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->cliente_id) AND ( (is_scalar($data->cliente_id) AND $data->cliente_id !== '') OR (is_array($data->cliente_id) AND (!empty($data->cliente_id)) )) )
        {

            $filters[] = new TFilter('cliente_id', '=', $data->cliente_id);// create the filter 
        }

        if (isset($data->etapa_interacao_id) AND ( (is_scalar($data->etapa_interacao_id) AND $data->etapa_interacao_id !== '') OR (is_array($data->etapa_interacao_id) AND (!empty($data->etapa_interacao_id)) )) )
        {

            $filters[] = new TFilter('etapa_interacao_id', '=', $data->etapa_interacao_id);// create the filter 
        }

        if (isset($data->data_inicio) AND ( (is_scalar($data->data_inicio) AND $data->data_inicio !== '') OR (is_array($data->data_inicio) AND (!empty($data->data_inicio)) )) )
        {

            $filters[] = new TFilter('data_inicio', '>=', $data->data_inicio);// create the filter 
        }

        if (isset($data->data_inicio_final) AND ( (is_scalar($data->data_inicio_final) AND $data->data_inicio_final !== '') OR (is_array($data->data_inicio_final) AND (!empty($data->data_inicio_final)) )) )
        {

            $filters[] = new TFilter('data_inicio', '<=', $data->data_inicio_final);// create the filter 
        }

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->tipo_interacao_nome) AND ( (is_scalar($data->tipo_interacao_nome) AND $data->tipo_interacao_nome !== '') OR (is_array($data->tipo_interacao_nome) AND (!empty($data->tipo_interacao_nome)) )) )
        {

            $filters[] = new TFilter('tipo_interacao_id', '=', $data->tipo_interacao_nome);// create the filter 
        }

        if (isset($data->cliente_razao_social) AND ( (is_scalar($data->cliente_razao_social) AND $data->cliente_razao_social !== '') OR (is_array($data->cliente_razao_social) AND (!empty($data->cliente_razao_social)) )) )
        {

            $filters[] = new TFilter('cliente_id', '=', $data->cliente_razao_social);// create the filter 
        }

        if (isset($data->cliente_cpf_cnpj) AND ( (is_scalar($data->cliente_cpf_cnpj) AND $data->cliente_cpf_cnpj !== '') OR (is_array($data->cliente_cpf_cnpj) AND (!empty($data->cliente_cpf_cnpj)) )) )
        {

            $filters[] = new TFilter('cliente_id', 'in', "(SELECT id FROM pessoa WHERE  deleted_at is null AND cpf_cnpj like '%{$data->cliente_cpf_cnpj}%')");// create the filter 
        }

        if (isset($data->cidade) AND ( (is_scalar($data->cidade) AND $data->cidade !== '') OR (is_array($data->cidade) AND (!empty($data->cidade)) )) )
        {

            $filters[] = new TFilter('cidade', 'ilike', "%{$data->cidade}%");// create the filter 
        }

        if (isset($data->estado) AND ( (is_scalar($data->estado) AND $data->estado !== '') OR (is_array($data->estado) AND (!empty($data->estado)) )) )
        {

            $filters[] = new TFilter('estado', 'ilike', "%{$data->estado}%");// create the filter 
        }

        if (isset($data->vendedor_id) AND ( (is_scalar($data->vendedor_id) AND $data->vendedor_id !== '') OR (is_array($data->vendedor_id) AND (!empty($data->vendedor_id)) )) )
        {

            $filters[] = new TFilter('vendedor_id', 'in', "(SELECT id FROM representante WHERE id = '{$data->vendedor_id}')");// create the filter 
        }

        if (isset($data->data_inicio_col) AND ( (is_scalar($data->data_inicio_col) AND $data->data_inicio_col !== '') OR (is_array($data->data_inicio_col) AND (!empty($data->data_inicio_col)) )) )
        {

            $filters[] = new TFilter('data_inicio', '=', $data->data_inicio_col);// create the filter 
        }

        if (isset($data->data_fechamento_esperada) AND ( (is_scalar($data->data_fechamento_esperada) AND $data->data_fechamento_esperada !== '') OR (is_array($data->data_fechamento_esperada) AND (!empty($data->data_fechamento_esperada)) )) )
        {

            $filters[] = new TFilter('data_fechamento_esperada', '=', $data->data_fechamento_esperada);// create the filter 
        }

        if (isset($data->data_fechamento) AND ( (is_scalar($data->data_fechamento) AND $data->data_fechamento !== '') OR (is_array($data->data_fechamento) AND (!empty($data->data_fechamento)) )) )
        {

            $filters[] = new TFilter('data_fechamento', '=', $data->data_fechamento);// create the filter 
        }

        if (isset($data->origem_contato_nome) AND ( (is_scalar($data->origem_contato_nome) AND $data->origem_contato_nome !== '') OR (is_array($data->origem_contato_nome) AND (!empty($data->origem_contato_nome)) )) )
        {

            $filters[] = new TFilter('origem_contato_id', '=', $data->origem_contato_nome);// create the filter 
        }

        if (isset($data->etapa_interacao_nome) AND ( (is_scalar($data->etapa_interacao_nome) AND $data->etapa_interacao_nome !== '') OR (is_array($data->etapa_interacao_nome) AND (!empty($data->etapa_interacao_nome)) )) )
        {

            $filters[] = new TFilter('etapa_interacao_id', '=', $data->etapa_interacao_nome);// create the filter 
        }

         if (isset($data->attnainteracao) AND ( (is_scalar($data->attnainteracao) AND $data->attnainteracao !== '') OR (is_array($data->attnainteracao) AND (!empty($data->attnainteracao)) )) )
        {
            $filters[] = new TFilter('id', 'in', "(SELECT interacao_id FROM interacao_atividade WHERE tipo_atividade_id = '{$data->attnainteracao}')");// create the filter 
        }

        // fill the form with data again
        if ((isset($param['static']) && ($param['static'] == '1')) || !empty($param['globalSearch']))
        {
            $this->datagrid_form->setData($data);
        }
        else
        {
            $this->form->setData($data);
        }

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            $onReloadParam = ['offset' => 0, 'first_page' => 1, 'target_container' => $param['target_container'] ?? null];
            AdiantiCoreApplication::loadPage($class, 'onReload', $onReloadParam);
            TScript::create('$(".select2").prev().select2("close");');
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for Interacao
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            /*
            //</blockLine><btnShowCurtainFiltersAutoCode>
            // if($param['cliente'])
            // {
            //     $filter = new TFilter('id','in','(SELECT id FROM interacao WHERE deleted_at is null AND cliente_id = '. $param['cliente'] . ')');
            //     $criteria->add($filter);
            //             TDBUniqueSearch::disableField(self::$formName, 'cliente_id');

            // }

             TSession::setValue('origem', $param['class']);
             TSession::setValue('method', $param['method']);

            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }

            TTransaction::open(self::$database);
            $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
            if($representante){

                $filter = new TFilter('id', 'in', "(SELECT id FROM interacao WHERE deleted_at is null AND vendedor_id in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");
                $criteria->add($filter);  

                 $object = new stdClass();
                $object->vendedor_id = $representante->id;
                TForm::sendData(self::$formName, $object);
            }
            TTransaction::close();

            //</blockLine></btnShowCurtainFiltersAutoCode>
            */
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }

            TTransaction::open(self::$database);
            $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
            if($representante){

                $filter = new TFilter('id', 'in', "(SELECT id FROM interacao WHERE deleted_at is null AND vendedor_id in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");
                $criteria->add($filter);  

                 $object = new stdClass();
                $object->vendedor_id = $representante->id;
                TForm::sendData(self::$formName, $object);
            }
            TTransaction::close();                

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

            $this->datagrid->initPopoverHeaderFilters();

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
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
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

        $object = new Interacao($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

     public function onShowReload($param = null)
    {
        //<onShow>

        //</onShow>
    }

}

