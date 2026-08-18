<?php

class ViewClassificacaoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'ViewClassificacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_ViewClassificacaoList';
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
        $this->form->setFormTitle("Filtros Personalizados:");
        $this->limit = 20;

        $criteria_nome_cliente = new TCriteria();
        $criteria_categoria = new TCriteria();
        $criteria_codigo_cliente = new TCriteria();
        $criteria_cidade = new TCriteria();
        $criteria_uf = new TCriteria();
        $criteria_representante = new TCriteria();
        $criteria_categoria1 = new TCriteria();
        $criteria_codigo_cliente1 = new TCriteria();
        $criteria_nome_cliente1 = new TCriteria();
        $criteria_cidade1 = new TCriteria();
        $criteria_uf1 = new TCriteria();

        $filterVar = TSession::getValue("userid");
        $usuariosQueVeemTudo = [1, 16, 13, 20, 4, 11, 12, 17, 10];

        if (!in_array($filterVar, $usuariosQueVeemTudo))
        {
            $criteria_representante->add(new TFilter('system_user_id', '=', $filterVar));
        }

        $nome_cliente = new TDBCombo('nome_cliente', 'minicrm', 'Pessoa', 'razao_social', '{razao_social}','razao_social asc' , $criteria_nome_cliente );
        $categoria = new TDBCombo('categoria', 'minicrm', 'CategoriaCliente', 'nome', '{nome}','nome asc' , $criteria_categoria );
        $codigo_cliente = new TDBCombo('codigo_cliente', 'minicrm', 'Pessoa', 'codigo', '{codigo}','codigo asc' , $criteria_codigo_cliente );
        $cidade = new TDBCombo('cidade', 'minicrm', 'Cidade', 'nome', '{nome}','nome asc' , $criteria_cidade );
        $uf = new TDBCombo('uf', 'minicrm', 'Estado', 'sigla', '{sigla}','sigla asc' , $criteria_uf );
        $tipo1_ini = new TEntry('tipo1_ini');
        $tipo1_fim = new TEntry('tipo1_fim');
        $tipo2_ini = new TEntry('tipo2_ini');
        $tipo2_fim = new TEntry('tipo2_fim');
        $representante = new TDBCombo('representante', 'minicrm', 'Representante', 'razao_social', '{razao_social}','razao_social asc' , $criteria_representante );
        $categoria1 = new TDBCombo('categoria1', 'minicrm', 'CategoriaCliente', 'nome', '{nome}','nome asc' , $criteria_categoria1 );
        $codigo_cliente1 = new TDBCombo('codigo_cliente1', 'minicrm', 'Pessoa', 'codigo', '{codigo}','codigo asc' , $criteria_codigo_cliente1 );
        $nome_cliente1 = new TDBCombo('nome_cliente1', 'minicrm', 'Pessoa', 'razao_social', '{razao_social}','razao_social asc' , $criteria_nome_cliente1 );
        $cidade1 = new TDBCombo('cidade1', 'minicrm', 'Cidade', 'nome', '{nome}','nome asc' , $criteria_cidade1 );
        $uf1 = new TDBCombo('uf1', 'minicrm', 'Estado', 'sigla', '{sigla}','sigla asc' , $criteria_uf1 );
        $dentro_prazo_tipo1 = new TCombo('dentro_prazo_tipo1');
        $tipo11 = new TEntry('tipo11');
        $dentro_prazo_tipo2 = new TCombo('dentro_prazo_tipo2');
        $tipo22 = new TEntry('tipo22');
        $dentro_prazo_ambos = new TCombo('dentro_prazo_ambos');

        $tipo11->exitOnEnter();
        $tipo22->exitOnEnter();

        $tipo11->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $tipo22->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $representante->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $categoria1->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $codigo_cliente1->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $nome_cliente1->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cidade1->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $uf1->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $dentro_prazo_tipo1->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $dentro_prazo_tipo2->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $dentro_prazo_ambos->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $dentro_prazo_ambos->addItems(["Sim"=>"Ativo","Não"=>"Inativo"]);
        $dentro_prazo_tipo1->addItems(["Sim"=>"Ativo","Não"=>"Inativo","N/A"=>"Não Aplicável"]);
        $dentro_prazo_tipo2->addItems(["Sim"=>"Ativo","Não"=>"Inativo","N/A"=>"Não Aplicável"]);

        $tipo1_fim->setTip("Dias Finais");
        $tipo2_fim->setTip("Dias Finais");
        $tipo1_ini->setTip("Dias Iniciais");
        $tipo2_ini->setTip("Dias Iniciais");

        $uf->enableSearch();
        $uf1->enableSearch();
        $cidade->enableSearch();
        $cidade1->enableSearch();
        $categoria->enableSearch();
        $categoria1->enableSearch();
        $nome_cliente->enableSearch();
        $representante->enableSearch();
        $nome_cliente1->enableSearch();
        $codigo_cliente->enableSearch();
        $codigo_cliente1->enableSearch();
        $dentro_prazo_tipo1->enableSearch();
        $dentro_prazo_tipo2->enableSearch();
        $dentro_prazo_ambos->enableSearch();

        $uf->setSize('100%');
        $uf1->setSize('100%');
        $cidade->setSize('100%');
        $tipo11->setSize('100%');
        $tipo22->setSize('100%');
        $cidade1->setSize('100%');
        $tipo1_ini->setSize('70%');
        $tipo1_fim->setSize('63%');
        $tipo2_ini->setSize('59%');
        $tipo2_fim->setSize('67%');
        $categoria->setSize('100%');
        $categoria1->setSize('100%');
        $nome_cliente->setSize('100%');
        $representante->setSize('100%');
        $nome_cliente1->setSize('100%');
        $codigo_cliente->setSize('100%');
        $codigo_cliente1->setSize('100%');
        $dentro_prazo_tipo1->setSize('100%');
        $dentro_prazo_tipo2->setSize('100%');
        $dentro_prazo_ambos->setSize('100%');

        $tipo1_ini->placeholder = "Ex: 1";
        $tipo2_ini->placeholder = "Ex: 1";
        $tipo1_fim->placeholder = "Ex: 30";
        $tipo2_fim->placeholder = "Ex: 30";

         $limitesIntervalos = self::getLimitesIntervalos();

        foreach ([
            [$tipo1_ini, $limitesIntervalos['tipo1'], '0'],
            [$tipo1_fim, $limitesIntervalos['tipo1'], 'Máx. '.$limitesIntervalos['tipo1']],
            [$tipo2_ini, $limitesIntervalos['tipo2'], '0'],
            [$tipo2_fim, $limitesIntervalos['tipo2'], 'Máx. '.$limitesIntervalos['tipo2']]
        ] as $configuracao)
        {
            [$campo, $maximo, $placeholder] = $configuracao;

            $campo->setProperty('type', 'number');
            $campo->setProperty('min', '0');
            $campo->setProperty('step', '1');
            $campo->setProperty('inputmode', 'numeric');

            if ($maximo !== null)
            {
                $campo->setProperty('max', (string) $maximo);
            }

            $campo->placeholder = $placeholder;
        }
        $row1 = $this->form->addFields([new TLabel("Nome do Cliente:", null, '14px', null, '100%'),$nome_cliente]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Categoria:", null, '14px', null, '100%'),$categoria],[new TLabel("Código:", null, '14px', null, '100%'),$codigo_cliente]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Cidade:", null, '14px', null, '100%'),$cidade],[new TLabel("UF:", null, '14px', null, '100%'),$uf]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Intervalo Física:", null, '14px', null)]);
        $row4->layout = [' col-sm-6'];

        $row5 = $this->form->addFields([$tipo1_ini,new TLabel("dias.", null, '14px', null)],[new TLabel(" ", null, '14px', null),new TLabel("- até -", null, '14px', null)],[$tipo1_fim,new TLabel("dias.", null, '14px', null)]);
        $row5->layout = [' col-sm-2',' col-sm-1',' col-sm-2'];

        $row6 = $this->form->addFields([new TLabel("Intervalo Contato:", null, '14px', null)]);
        $row6->layout = [' col-sm-6'];

        $row7 = $this->form->addFields([$tipo2_ini,new TLabel("dias.", null, '14px', null)],[new TLabel(" ", null, '14px', null),new TLabel("- até -", null, '14px', null)],[$tipo2_fim,new TLabel("dias.", null, '14px', null)]);
        $row7->layout = [' col-sm-2',' col-sm-1',' col-sm-2'];

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

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_representante = new TDataGridColumn('representante', "Representante", 'left');
        $column_categoria = new TDataGridColumn('categoria', "Categoria", 'left');
        $column_codigo_cliente = new TDataGridColumn('codigo_cliente', "Código", 'left');
        $column_nome_cliente = new TDataGridColumn('nome_cliente', "Nome", 'left');
        $column_cidade = new TDataGridColumn('cidade', "Cidade", 'left');
        $column_uf = new TDataGridColumn('uf', "UF", 'left');
        $column_dentro_prazo_tipo1_transformed = new TDataGridColumn('dentro_prazo_tipo1', "", 'right');
        $column_tipo1_transformed = new TDataGridColumn('tipo1', "Física", 'left');
        $column_dentro_prazo_tipo2_transformed = new TDataGridColumn('dentro_prazo_tipo2', "", 'right');
        $column_tipo2_transformed = new TDataGridColumn('tipo2', "Contato", 'left');
        $column_dentro_prazo_ambos_transformed = new TDataGridColumn('dentro_prazo_ambos', "Status do Cliente", 'center');

        $column_dentro_prazo_tipo1_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($value === 'Sim')
            {
                $cor = '#28a745'; // verde
                $texto = 'Ativo';
            }
            elseif ($value === 'Não')
            {
                $cor = '#dc3545'; // vermelho
                $texto = 'Inativo';
            }
            else
            {
                $cor = '#6c757d'; // cinza
                $texto = 'N/A';
            }

            return '<span style="
                display:inline-block;
                min-width:80px;
                padding:2px 8px;
                border-radius:6px;
                background:'.$cor.';
                color:#fff;
                font-size:11px;
                font-weight:bold;
                line-height:1.2;
                text-align:center;
                box-sizing:border-box;
                white-space:nowrap;
            ">'.$texto.'</span>';

        });

        $column_tipo1_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
           if($value == 'Não Aplicável'){
            return $value;
           }else{
             return $value . ' Dias';
            } 
        });

        $column_dentro_prazo_tipo2_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($value === 'Sim')
            {
                $cor = '#28a745'; // verde
                $texto = 'Ativo';
            }
            elseif ($value === 'Não')
            {
                $cor = '#dc3545'; // vermelho
                $texto = 'Inativo';
            }
            else
            {
                $cor = '#6c757d'; // cinza
                $texto = 'N/A';
            }

            return '<span style="
                display:inline-block;
                min-width:80px;
                padding:2px 8px;
                border-radius:6px;
                background:'.$cor.';
                color:#fff;
                font-size:11px;
                font-weight:bold;
                line-height:1.2;
                text-align:center;
                box-sizing:border-box;
                white-space:nowrap;
            ">'.$texto.'</span>';

        });

        $column_tipo2_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
           if($value == 'Não Aplicável'){
            return $value;
           }else{
             return $value . ' Dias';
            } 
        });

        $column_dentro_prazo_ambos_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if ($value === 'Sim')
            {
                $cor = '#28a745'; // verde
                $texto = 'Ativo';
            }
            elseif ($value === 'Não')
            {
                $cor = '#dc3545'; // vermelho
                $texto = 'Inativo';
            }
            else
            {
                $cor = '#6c757d'; // cinza
                $texto = 'N/A';
            }

            return '<span style="
                display:inline-block;
                min-width:80px;
                padding:2px 8px;
                border-radius:6px;
                background:'.$cor.';
                color:#fff;
                font-size:11px;
                font-weight:bold;
                line-height:1.2;
                text-align:center;
                box-sizing:border-box;
                white-space:nowrap;
            ">'.$texto.'</span>';

        });        

        $order_categoria = new TAction(array($this, 'onReload'));
        $order_categoria->setParameter('order', 'categoria');
        $column_categoria->setAction($order_categoria);
        $order_codigo_cliente = new TAction(array($this, 'onReload'));
        $order_codigo_cliente->setParameter('order', 'codigo_cliente');
        $column_codigo_cliente->setAction($order_codigo_cliente);
        $order_nome_cliente = new TAction(array($this, 'onReload'));
        $order_nome_cliente->setParameter('order', 'nome_cliente');
        $column_nome_cliente->setAction($order_nome_cliente);
        $order_cidade = new TAction(array($this, 'onReload'));
        $order_cidade->setParameter('order', 'cidade');
        $column_cidade->setAction($order_cidade);
        $order_uf = new TAction(array($this, 'onReload'));
        $order_uf->setParameter('order', 'uf');
        $column_uf->setAction($order_uf);
        $order_tipo1_transformed = new TAction(array($this, 'onReload'));
        $order_tipo1_transformed->setParameter('order', 'tipo1');
        $column_tipo1_transformed->setAction($order_tipo1_transformed);
        $order_tipo2_transformed = new TAction(array($this, 'onReload'));
        $order_tipo2_transformed->setParameter('order', 'tipo2');
        $column_tipo2_transformed->setAction($order_tipo2_transformed);

        $column_tipo1_transformed->disableHtmlConversion();
        $column_tipo2_transformed->disableHtmlConversion();

        $this->datagrid->addColumn($column_representante);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_codigo_cliente);
        $this->datagrid->addColumn($column_nome_cliente);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_uf);
        $this->datagrid->addColumn($column_dentro_prazo_tipo1_transformed);
        $this->datagrid->addColumn($column_tipo1_transformed);
        $this->datagrid->addColumn($column_dentro_prazo_tipo2_transformed);
        $this->datagrid->addColumn($column_tipo2_transformed);
        $this->datagrid->addColumn($column_dentro_prazo_ambos_transformed);

        $this->applyDatagridProperties();
        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        $td_representante = TElement::tag('td', $representante);
        $tr->add($td_representante);
        $td_categoria1 = TElement::tag('td', $categoria1);
        $tr->add($td_categoria1);
        $td_codigo_cliente1 = TElement::tag('td', $codigo_cliente1);
        $tr->add($td_codigo_cliente1);
        $td_nome_cliente1 = TElement::tag('td', $nome_cliente1);
        $tr->add($td_nome_cliente1);
        $td_cidade1 = TElement::tag('td', $cidade1);
        $tr->add($td_cidade1);
        $td_uf1 = TElement::tag('td', $uf1);
        $tr->add($td_uf1);
        $td_dentro_prazo_tipo1 = TElement::tag('td', $dentro_prazo_tipo1);
        $tr->add($td_dentro_prazo_tipo1);
        $td_tipo11 = TElement::tag('td', $tipo11);
        $tr->add($td_tipo11);
        $td_dentro_prazo_tipo2 = TElement::tag('td', $dentro_prazo_tipo2);
        $tr->add($td_dentro_prazo_tipo2);
        $td_tipo22 = TElement::tag('td', $tipo22);
        $tr->add($td_tipo22);
        $td_dentro_prazo_ambos = TElement::tag('td', $dentro_prazo_ambos);
        $tr->add($td_dentro_prazo_ambos);
        $tr->add(TElement::tag('td', ''));

        $this->datagrid_form->addField($representante);
        $this->datagrid_form->addField($categoria1);
        $this->datagrid_form->addField($codigo_cliente1);
        $this->datagrid_form->addField($nome_cliente1);
        $this->datagrid_form->addField($cidade1);
        $this->datagrid_form->addField($uf1);
        $this->datagrid_form->addField($dentro_prazo_tipo1);
        $this->datagrid_form->addField($tipo11);
        $this->datagrid_form->addField($dentro_prazo_tipo2);
        $this->datagrid_form->addField($tipo22);
        $this->datagrid_form->addField($dentro_prazo_ambos);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Controle de Comissão");
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

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['ViewClassificacaoList', 'onShowCurtainFilters']), "Filtros Dinâmicos");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['ViewClassificacaoList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ViewClassificacaoList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ViewClassificacaoList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ViewClassificacaoList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ViewClassificacaoList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","ViewClassificacaoList"]));
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
    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
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
            $page->setProperty('page-name', 'ViewClassificacaoListSearch');
            $page->setProperty('page_name', 'ViewClassificacaoListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            $style = new TStyle('right-panel > .container-part[page-name=ViewClassificacaoListSearch]');
            $style->width = '60% !important';
            $style->show(true);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if(!empty($this->form))
        {
            $this->form->clear();
        }

        if(!empty($this->datagrid_form))
        {
            $this->datagrid_form->clear();
        }

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

        /*
         * Quando a busca vier dos filtros do cabeçalho da grid,
         * recupera os intervalos que estavam salvos no formulário principal.
         */
        if ((isset($param['static']) && $param['static'] == '1') || !empty($param['globalSearch']))
        {
            $dadosSalvos = TSession::getValue(__CLASS__.'_filter_data');

            if ($dadosSalvos)
            {
                foreach (['tipo1_ini', 'tipo1_fim', 'tipo2_ini', 'tipo2_fim'] as $campoIntervalo)
                {
                    if ((!isset($data->$campoIntervalo) || $data->$campoIntervalo === '')
                        && isset($dadosSalvos->$campoIntervalo))
                    {
                        $data->$campoIntervalo = $dadosSalvos->$campoIntervalo;
                    }
                }
            }
        }

        $limitesIntervalos = self::getLimitesIntervalos();

        $valoresIntervalo = [
            'tipo1_ini' => self::normalizarValorIntervalo(
                $data->tipo1_ini ?? null,
                $limitesIntervalos['tipo1']
            ),
            'tipo1_fim' => self::normalizarValorIntervalo(
                $data->tipo1_fim ?? null,
                $limitesIntervalos['tipo1']
            ),
            'tipo2_ini' => self::normalizarValorIntervalo(
                $data->tipo2_ini ?? null,
                $limitesIntervalos['tipo2']
            ),
            'tipo2_fim' => self::normalizarValorIntervalo(
                $data->tipo2_fim ?? null,
                $limitesIntervalos['tipo2']
            )
        ];

        /*
         * Se o usuário informar os valores invertidos,
         * troca automaticamente o início e o fim.
         */
        if ($valoresIntervalo['tipo1_ini'] !== null
            && $valoresIntervalo['tipo1_fim'] !== null
            && $valoresIntervalo['tipo1_ini'] > $valoresIntervalo['tipo1_fim'])
        {
            [$valoresIntervalo['tipo1_ini'], $valoresIntervalo['tipo1_fim']] =
                [$valoresIntervalo['tipo1_fim'], $valoresIntervalo['tipo1_ini']];
        }

        if ($valoresIntervalo['tipo2_ini'] !== null
            && $valoresIntervalo['tipo2_fim'] !== null
            && $valoresIntervalo['tipo2_ini'] > $valoresIntervalo['tipo2_fim'])
        {
            [$valoresIntervalo['tipo2_ini'], $valoresIntervalo['tipo2_fim']] =
                [$valoresIntervalo['tipo2_fim'], $valoresIntervalo['tipo2_ini']];
        }

        /*
         * tipo1 e tipo2 são textos na view porque também podem possuir
         * o valor "Não Aplicável". Por isso a comparação precisa ser
         * convertida para inteiro antes de aplicar >= e <=.
         */
        $tipo1Numerico = "NULLIF(CAST(NULLIF(NULLIF(BTRIM(tipo1::text), 'Não Aplicável'), '') AS INTEGER), 9999)";
        $tipo2Numerico = "NULLIF(CAST(NULLIF(NULLIF(BTRIM(tipo2::text), 'Não Aplicável'), '') AS INTEGER), 9999)";

        if ($valoresIntervalo['tipo1_ini'] !== null)
        {
            $filters[] = new TFilter(
                $tipo1Numerico,
                '>=',
                $valoresIntervalo['tipo1_ini']
            );
        }

        if ($valoresIntervalo['tipo1_fim'] !== null)
        {
            $filters[] = new TFilter(
                $tipo1Numerico,
                '<=',
                $valoresIntervalo['tipo1_fim']
            );
        }

        if ($valoresIntervalo['tipo2_ini'] !== null)
        {
            $filters[] = new TFilter(
                $tipo2Numerico,
                '>=',
                $valoresIntervalo['tipo2_ini']
            );
        }

        if ($valoresIntervalo['tipo2_fim'] !== null)
        {
            $filters[] = new TFilter(
                $tipo2Numerico,
                '<=',
                $valoresIntervalo['tipo2_fim']
            );
        }

        /*
         * Impede que o código automático do Builder crie novamente
         * os filtros diretamente sobre as colunas de texto.
         * Os valores serão restaurados no bloco onDatagridSearch.
         */
        $data->tipo1_ini = '';
        $data->tipo1_fim = '';
        $data->tipo2_ini = '';
        $data->tipo2_fim = ''; 

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->nome_cliente) AND ( (is_scalar($data->nome_cliente) AND $data->nome_cliente !== '') OR (is_array($data->nome_cliente) AND (!empty($data->nome_cliente)) )) )
        {

            $filters[] = new TFilter('nome_cliente', 'ilike', "%{$data->nome_cliente}%");// create the filter 
        }

        if (isset($data->categoria) AND ( (is_scalar($data->categoria) AND $data->categoria !== '') OR (is_array($data->categoria) AND (!empty($data->categoria)) )) )
        {

            $filters[] = new TFilter('categoria', '=', $data->categoria);// create the filter 
        }

        if (isset($data->codigo_cliente) AND ( (is_scalar($data->codigo_cliente) AND $data->codigo_cliente !== '') OR (is_array($data->codigo_cliente) AND (!empty($data->codigo_cliente)) )) )
        {

            $filters[] = new TFilter('codigo_cliente', '=', $data->codigo_cliente);// create the filter 
        }

        if (isset($data->cidade) AND ( (is_scalar($data->cidade) AND $data->cidade !== '') OR (is_array($data->cidade) AND (!empty($data->cidade)) )) )
        {

            $filters[] = new TFilter('cidade', '=', $data->cidade);// create the filter 
        }

        if (isset($data->uf) AND ( (is_scalar($data->uf) AND $data->uf !== '') OR (is_array($data->uf) AND (!empty($data->uf)) )) )
        {

            $filters[] = new TFilter('uf', '=', $data->uf);// create the filter 
        }

        if (isset($data->tipo1_ini) AND ( (is_scalar($data->tipo1_ini) AND $data->tipo1_ini !== '') OR (is_array($data->tipo1_ini) AND (!empty($data->tipo1_ini)) )) )
        {

            $filters[] = new TFilter('tipo1', '>=', $data->tipo1_ini);// create the filter 
        }

        if (isset($data->tipo1_fim) AND ( (is_scalar($data->tipo1_fim) AND $data->tipo1_fim !== '') OR (is_array($data->tipo1_fim) AND (!empty($data->tipo1_fim)) )) )
        {

            $filters[] = new TFilter('tipo1', '<=', $data->tipo1_fim);// create the filter 
        }

        if (isset($data->tipo2_ini) AND ( (is_scalar($data->tipo2_ini) AND $data->tipo2_ini !== '') OR (is_array($data->tipo2_ini) AND (!empty($data->tipo2_ini)) )) )
        {

            $filters[] = new TFilter('tipo2', '>=', $data->tipo2_ini);// create the filter 
        }

        if (isset($data->tipo2_fim) AND ( (is_scalar($data->tipo2_fim) AND $data->tipo2_fim !== '') OR (is_array($data->tipo2_fim) AND (!empty($data->tipo2_fim)) )) )
        {

            $filters[] = new TFilter('tipo2', '<=', $data->tipo2_fim);// create the filter 
        }

        if (isset($data->representante) AND ( (is_scalar($data->representante) AND $data->representante !== '') OR (is_array($data->representante) AND (!empty($data->representante)) )) )
        {

            $filters[] = new TFilter('representante', '=', $data->representante);// create the filter 
        }

        if (isset($data->categoria1) AND ( (is_scalar($data->categoria1) AND $data->categoria1 !== '') OR (is_array($data->categoria1) AND (!empty($data->categoria1)) )) )
        {

            $filters[] = new TFilter('categoria', '=', $data->categoria1);// create the filter 
        }

        if (isset($data->codigo_cliente1) AND ( (is_scalar($data->codigo_cliente1) AND $data->codigo_cliente1 !== '') OR (is_array($data->codigo_cliente1) AND (!empty($data->codigo_cliente1)) )) )
        {

            $filters[] = new TFilter('codigo_cliente', '=', $data->codigo_cliente1);// create the filter 
        }

        if (isset($data->nome_cliente1) AND ( (is_scalar($data->nome_cliente1) AND $data->nome_cliente1 !== '') OR (is_array($data->nome_cliente1) AND (!empty($data->nome_cliente1)) )) )
        {

            $filters[] = new TFilter('nome_cliente', 'like', "%{$data->nome_cliente1}%");// create the filter 
        }

        if (isset($data->cidade1) AND ( (is_scalar($data->cidade1) AND $data->cidade1 !== '') OR (is_array($data->cidade1) AND (!empty($data->cidade1)) )) )
        {

            $filters[] = new TFilter('cidade', '=', $data->cidade1);// create the filter 
        }

        if (isset($data->uf1) AND ( (is_scalar($data->uf1) AND $data->uf1 !== '') OR (is_array($data->uf1) AND (!empty($data->uf1)) )) )
        {

            $filters[] = new TFilter('uf', '=', $data->uf1);// create the filter 
        }

        if (isset($data->dentro_prazo_tipo1) AND ( (is_scalar($data->dentro_prazo_tipo1) AND $data->dentro_prazo_tipo1 !== '') OR (is_array($data->dentro_prazo_tipo1) AND (!empty($data->dentro_prazo_tipo1)) )) )
        {

            $filters[] = new TFilter('dentro_prazo_tipo1', '=', $data->dentro_prazo_tipo1);// create the filter 
        }

        if (isset($data->tipo11) AND ( (is_scalar($data->tipo11) AND $data->tipo11 !== '') OR (is_array($data->tipo11) AND (!empty($data->tipo11)) )) )
        {

            $filters[] = new TFilter('tipo1', '=', $data->tipo11);// create the filter 
        }

        if (isset($data->dentro_prazo_tipo2) AND ( (is_scalar($data->dentro_prazo_tipo2) AND $data->dentro_prazo_tipo2 !== '') OR (is_array($data->dentro_prazo_tipo2) AND (!empty($data->dentro_prazo_tipo2)) )) )
        {

            $filters[] = new TFilter('dentro_prazo_tipo2', '=', $data->dentro_prazo_tipo2);// create the filter 
        }

        if (isset($data->tipo22) AND ( (is_scalar($data->tipo22) AND $data->tipo22 !== '') OR (is_array($data->tipo22) AND (!empty($data->tipo22)) )) )
        {

            $filters[] = new TFilter('tipo2', '=', $data->tipo22);// create the filter 
        }

        if (isset($data->dentro_prazo_ambos) AND ( (is_scalar($data->dentro_prazo_ambos) AND $data->dentro_prazo_ambos !== '') OR (is_array($data->dentro_prazo_ambos) AND (!empty($data->dentro_prazo_ambos)) )) )
        {

            $filters[] = new TFilter('dentro_prazo_ambos', '=', $data->dentro_prazo_ambos);// create the filter 
        }

         /*
         * Restaura os valores para o formulário e para a sessão,
         * depois de o código automático do Builder ter sido executado.
         */
        if (isset($valoresIntervalo))
        {
            foreach ($valoresIntervalo as $campoIntervalo => $valorIntervalo)
            {
                $data->$campoIntervalo = $valorIntervalo === null
                    ? ''
                    : $valorIntervalo;
            }
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

            // creates a repository for ViewClassificacao
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'tipo1';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'asc';
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

                $filterVar = TSession::getValue("userid");
                $usuariosQueVeemTudo = [1, 16, 13, 20, 4, 11, 12, 17, 10];

                if (!in_array($filterVar, $usuariosQueVeemTudo))
                {
                    $criteria->add(new TFilter('s_id', '=', $filterVar));
                }

            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }
            //</blockLine></btnShowCurtainFiltersAutoCode>

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

        $object = new ViewClassificacao($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    private static function normalizarValorIntervalo($valor, $maximo = null)
    {
        if ($valor === null || trim((string) $valor) === '')
        {
            return null;
        }

        if (!is_numeric($valor))
        {
            return null;
        }

        $valor = (int) $valor;

        if ($valor < 0)
        {
            $valor = 0;
        }

        if ($maximo !== null && $valor > $maximo)
        {
            $valor = (int) $maximo;
        }

        return $valor;
    }

    private static function getLimitesIntervalos()
    {
        $cacheKey = __CLASS__.'_limites_intervalos_v1';
        $cache = TSession::getValue($cacheKey);

        if (is_array($cache)
            && isset($cache['expires_at'])
            && $cache['expires_at'] >= time())
        {
            return [
                'tipo1' => $cache['tipo1'],
                'tipo2' => $cache['tipo2']
            ];
        }

        $abriuTransacao = false;

        try
        {
            if (TTransaction::getDatabase() !== self::$database)
            {
                TTransaction::open(self::$database);
                $abriuTransacao = true;
            }

            $conn = TTransaction::get();

            $sql = "
                SELECT
                    COALESCE(
                        MAX(
                            NULLIF(
                                CAST(
                                    NULLIF(
                                        NULLIF(BTRIM(tipo1::text), 'Não Aplicável'),
                                        ''
                                    ) AS INTEGER
                                ),
                                9999
                            )
                        ),

                    ) AS max_tipo1,

                    COALESCE(
                        MAX(
                            NULLIF(
                                CAST(
                                    NULLIF(
                                        NULLIF(BTRIM(tipo2::text), 'Não Aplicável'),
                                        ''
                                    ) AS INTEGER
                                ),
                                9999
                            )
                        ),

                    ) AS max_tipo2

                FROM view_classificacao
            ";

            $resultado = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);

            $limites = [
                'tipo1' => (int) ($resultado['max_tipo1'] ?? 0),
                'tipo2' => (int) ($resultado['max_tipo2'] ?? 0)
            ];

            if ($abriuTransacao)
            {
                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            if ($abriuTransacao)
            {
                TTransaction::rollback();
            }

            /*
             * Se o MAX falhar, a tela continua funcionando.
             * Nesse caso, apenas não aplica limite máximo no HTML.
             */
            $limites = [
                'tipo1' => null,
                'tipo2' => null
            ];
        }

        TSession::setValue($cacheKey, [
            'tipo1' => $limites['tipo1'],
            'tipo2' => $limites['tipo2'],
            'expires_at' => time() + 3600
        ]);

        return $limites;
    }

}

