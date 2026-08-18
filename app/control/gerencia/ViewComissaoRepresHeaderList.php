<?php

class ViewComissaoRepresHeaderList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'ViewComissaoRepres';
    private static $primaryKey = 'id';
    private static $formName = 'formList_ViewComissaoRepres';
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
        // creates the form

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 0;

        $criteria_coligada_id = new TCriteria();
        $criteria_representante = new TCriteria();
        $criteria_fantasia = new TCriteria();
        $criteria_categoria_cliente = new TCriteria();

        $coligada_id = new TDBCombo('coligada_id', 'minicrm', 'Coligada', 'id', '{nome}','nome asc' , $criteria_coligada_id );
        $representante = new TDBCombo('representante', 'minicrm', 'Representante', 'razao_social', '{razao_social}','razao_social asc' , $criteria_representante );
        $numero_nota = new TEntry('numero_nota');
        $data_emissao = new BDateRange('data_emissao', 'data_emissao_fim');
        $data_emissao_os = new BDateRange('data_emissao_os', 'data_emissao_os_fim');
        $codigo_cliente = new TEntry('codigo_cliente');
        $fantasia = new TDBCombo('fantasia', 'minicrm', 'Pessoa', 'nome_fantasia', '{nome_fantasia}','nome_fantasia asc' , $criteria_fantasia );
        $categoria_cliente = new TDBCombo('categoria_cliente', 'minicrm', 'CategoriaCliente', 'nome', '{nome}','nome asc' , $criteria_categoria_cliente );
        $valor = new TEntry('valor');
        $tem_comissao = new TCombo('tem_comissao');
        $comissao = new TEntry('comissao');

        $numero_nota->exitOnEnter();
        $codigo_cliente->exitOnEnter();
        $valor->exitOnEnter();
        $comissao->exitOnEnter();

        $numero_nota->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_emissao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_emissao_os->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $codigo_cliente->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $comissao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $coligada_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $representante->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $fantasia->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $categoria_cliente->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $tem_comissao->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $tem_comissao->addItems(["S"=>"Sim","N"=>"Não"]);
        $data_emissao->setMask('dd/mm/yyyy');
        $data_emissao_os->setMask('dd/mm/yyyy');

        $data_emissao->setDatabaseMask('yyyy-mm-dd');
        $data_emissao_os->setDatabaseMask('yyyy-mm-dd');

        $fantasia->enableSearch();
        $coligada_id->enableSearch();
        $tem_comissao->enableSearch();
        $representante->enableSearch();
        $categoria_cliente->enableSearch();

        $valor->setSize('100%');
        $fantasia->setSize('100%');
        $comissao->setSize('100%');
        $data_emissao->setSize(220);
        $coligada_id->setSize('100%');
        $numero_nota->setSize('100%');
        $data_emissao_os->setSize(220);
        $tem_comissao->setSize('100%');
        $representante->setSize('100%');
        $codigo_cliente->setSize('100%');
        $categoria_cliente->setSize('100%');


        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_coligada_id_transformed = new TDataGridColumn('coligada_id', "Coligada", 'left');
        $column_representante = new TDataGridColumn('representante', "Representante", 'left');
        $column_numero_nota = new TDataGridColumn('numero_nota', "Número NF", 'left' , '6%');
        $column_data_emissao_transformed = new TDataGridColumn('data_emissao', "Data Emissão", 'left');
        $column_data_emissao_os_transformed = new TDataGridColumn('data_emissao_os', "Data da O.S", 'left');
        $column_codigo_cliente = new TDataGridColumn('codigo_cliente', "Código", 'left' , '6%');
        $column_fantasia = new TDataGridColumn('fantasia', "Cliente", 'left' , '20%');
        $column_categoria_cliente = new TDataGridColumn('categoria_cliente', "Categoria", 'left');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor", 'left' , '8%');
        $column_tem_comissao_transformed = new TDataGridColumn('tem_comissao', "Tem Comissão?", 'left' , '8%');
        $column_comissao_transformed = new TDataGridColumn('comissao', "Comissao", 'left' , '8%');

        $column_valor_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_comissao_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_coligada_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $value = Coligada::find($object->coligada_id);

            return $value->nome;

        });

        $column_data_emissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_data_emissao_os_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_tem_comissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            $valor = strtoupper(trim((string) $value));

            if ($valor === 'S')
            {
                $texto = 'SIM';
                $cor   = '#198754';
            }
            elseif ($valor === 'C')
            {
                $texto = 'CANCELADA';
                $cor   = '#DC3545';
            }
            elseif ($valor === 'P')
            {
                $texto = 'CONFIRMADA';
                $cor   = '#0D6EFD';
            }
            else
            {
                $texto = 'NÃO';
                $cor   = '#DC3545';
            }

            $label = new TElement('span');

            $label->style = "
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 90px;
                height: 25px;
                padding: 5px 8px;
                box-sizing: border-box;
                border-radius: 6px;
                background-color: {$cor}
                ;
                color: #FFFFFF;
                font-size: 11px;
                font-weight: 700;
                text-align: center;
                line-height: 1;
                letter-spacing: 0.3px;
                text-transform: uppercase;
                box-shadow: 0 1px 2px rgba(0,0,0,0.12);
            ";

            $label->add($texto);

            /*
             * Cancelada ou Confirmada:
             * permite clicar para visualizar a justificativa.
             */
            if ($valor === 'C' || $valor === 'P')
            {
                $controle = ControleNota::where(
                    'nota_baixada_id',
                    '=',
                    $object->id
                )
                ->orderBy('id', 'desc')
                ->first();

                if ($controle)
                {
                    $label->style .= "
                        cursor: pointer;
                    ";

                    /*
                     * Define qual formulário abrir.
                     */
                    if ($valor === 'C')
                    {
                        $classeForm = 'ControleNotaForm';
                        $tituloLink = 'Visualizar justificativa do cancelamento';
                    }
                    else
                    {
                        $classeForm = 'ControleNotaFormConfirmar';
                        $tituloLink = 'Visualizar justificativa da confirmação';
                    }

                    $link = new TElement('a');

                    $link->href =
                        "index.php?class={$classeForm}" .
                        "&method=onEdit" .
                        "&key={$controle->id}" .
                        "&modo=visualizar";

                    $link->generator = 'adianti';
                    $link->title = $tituloLink;

                    $link->style = "
                        display: inline-block;
                        text-decoration: none;
                    ";

                    $link->add($label);

                    return $link;
                }
            }

            return $label;

        });

        $column_comissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });        

        $order_data_emissao_transformed = new TAction(array($this, 'onReload'));
        $order_data_emissao_transformed->setParameter('order', 'data_emissao');
        $column_data_emissao_transformed->setAction($order_data_emissao_transformed);

        $column_tem_comissao_transformed->disableHtmlConversion();

        $this->datagrid->addColumn($column_coligada_id_transformed);
        $this->datagrid->addColumn($column_representante);
        $this->datagrid->addColumn($column_numero_nota);
        $this->datagrid->addColumn($column_data_emissao_transformed);
        $this->datagrid->addColumn($column_data_emissao_os_transformed);
        $this->datagrid->addColumn($column_codigo_cliente);
        $this->datagrid->addColumn($column_fantasia);
        $this->datagrid->addColumn($column_categoria_cliente);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_tem_comissao_transformed);
        $this->datagrid->addColumn($column_comissao_transformed);

        $action_onShow = new TDataGridAction(array('ControleNotaForm', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Cancelar Comissão");
        $action_onShow->setImage('fas:ban #F44336');
        $action_onShow->setField(self::$primaryKey);
        $action_onShow->setDisplayCondition('ViewComissaoRepresHeaderList::canCancelar');
        $action_onShow->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onShow);

        $action_ControleNotaFormConfirmar_onShow = new TDataGridAction(array('ControleNotaFormConfirmar', 'onShow'));
        $action_ControleNotaFormConfirmar_onShow->setUseButton(false);
        $action_ControleNotaFormConfirmar_onShow->setButtonClass('btn btn-default btn-sm');
        $action_ControleNotaFormConfirmar_onShow->setLabel("");
        $action_ControleNotaFormConfirmar_onShow->setImage('fas:check-circle #4CAF50');
        $action_ControleNotaFormConfirmar_onShow->setField(self::$primaryKey);
        $action_ControleNotaFormConfirmar_onShow->setDisplayCondition('ViewComissaoRepresHeaderList::onExibirConfirm');
        $action_ControleNotaFormConfirmar_onShow->setParameter('key', '{id}');

        $this->datagrid->addAction($action_ControleNotaFormConfirmar_onShow);

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
        if(!$action_ControleNotaFormConfirmar_onShow->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_coligada_id = TElement::tag('td', $coligada_id);
        $tr->add($td_coligada_id);
        $td_representante = TElement::tag('td', $representante);
        $tr->add($td_representante);
        $td_numero_nota = TElement::tag('td', $numero_nota);
        $tr->add($td_numero_nota);
        $td_data_emissao = TElement::tag('td', $data_emissao);
        $tr->add($td_data_emissao);
        $td_data_emissao_os = TElement::tag('td', $data_emissao_os);
        $tr->add($td_data_emissao_os);
        $td_codigo_cliente = TElement::tag('td', $codigo_cliente);
        $tr->add($td_codigo_cliente);
        $td_fantasia = TElement::tag('td', $fantasia);
        $tr->add($td_fantasia);
        $td_categoria_cliente = TElement::tag('td', $categoria_cliente);
        $tr->add($td_categoria_cliente);
        $td_valor = TElement::tag('td', $valor);
        $tr->add($td_valor);
        $td_tem_comissao = TElement::tag('td', $tem_comissao);
        $tr->add($td_tem_comissao);
        $td_comissao = TElement::tag('td', $comissao);
        $tr->add($td_comissao);
        $tr->add(TElement::tag('td', ''));

        $this->datagrid_form->addField($coligada_id);
        $this->datagrid_form->addField($representante);
        $this->datagrid_form->addField($numero_nota);
        $this->datagrid_form->addField($data_emissao);
        $this->datagrid_form->addField($data_emissao_os);
        $this->datagrid_form->addField($codigo_cliente);
        $this->datagrid_form->addField($fantasia);
        $this->datagrid_form->addField($categoria_cliente);
        $this->datagrid_form->addField($valor);
        $this->datagrid_form->addField($tem_comissao);
        $this->datagrid_form->addField($comissao);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $panel->getHeader()->style = ' display:none !important; ';
        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($headerActions);
        $panel->add($this->datagrid_form);

        $button_gerar_analitico = new TButton('button_button_gerar_analitico');
        $button_gerar_analitico->setAction(new TAction(['ViewComissaoRepresHeaderList', 'onGerarAnalitico']), "Gerar Analítico");
        $button_gerar_analitico->addStyleClass('btn-default');
        $button_gerar_analitico->setImage('fas:file-excel #4CAF50');

        $this->datagrid_form->addField($button_gerar_analitico);

        $head_right_actions->add($button_gerar_analitico);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gerencia","Comissão de Representante"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public static function canCancelar($object)
    {
        try 
        {
            if($object->tem_comissao == 'S')
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onExibirConfirm($object)
    {
        try 
        {
            if($object->tem_comissao == 'S')
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onGerarAnalitico($param = null) 
    {
        try 
        {
            TScript::create("
                window.open(
                    'http://194.140.198.97:3001/public/dashboard/a4f8f179-0fbc-49b6-91d0-49d20c569dc7',
                    '_blank'
                );
            ");
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        // get the search form data
        $data = $this->datagrid_form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->coligada_id) AND ( (is_scalar($data->coligada_id) AND $data->coligada_id !== '') OR (is_array($data->coligada_id) AND (!empty($data->coligada_id)) )) )
        {

            $filters[] = new TFilter('coligada_id', '=', $data->coligada_id);// create the filter 
        }

        if (isset($data->representante) AND ( (is_scalar($data->representante) AND $data->representante !== '') OR (is_array($data->representante) AND (!empty($data->representante)) )) )
        {

            $filters[] = new TFilter('representante', '=', $data->representante);// create the filter 
        }

        if (isset($data->numero_nota) AND ( (is_scalar($data->numero_nota) AND $data->numero_nota !== '') OR (is_array($data->numero_nota) AND (!empty($data->numero_nota)) )) )
        {

            $filters[] = new TFilter('numero_nota', '=', $data->numero_nota);// create the filter 
        }

        if (isset($data->data_emissao_fim) AND ( (is_scalar($data->data_emissao_fim) AND $data->data_emissao_fim !== '') OR (is_array($data->data_emissao_fim) AND (!empty($data->data_emissao_fim)) )) )
        {

            $filters[] = new TFilter('data_emissao', '<=', $data->data_emissao_fim);// create the filter 
        }

        if (isset($data->data_emissao) AND ( (is_scalar($data->data_emissao) AND $data->data_emissao !== '') OR (is_array($data->data_emissao) AND (!empty($data->data_emissao)) )) )
        {

            $filters[] = new TFilter('data_emissao', '>=', $data->data_emissao);// create the filter 
        }

        if (isset($data->data_emissao_os_fim) AND ( (is_scalar($data->data_emissao_os_fim) AND $data->data_emissao_os_fim !== '') OR (is_array($data->data_emissao_os_fim) AND (!empty($data->data_emissao_os_fim)) )) )
        {

            $filters[] = new TFilter('data_emissao_os', '<=', $data->data_emissao_os_fim);// create the filter 
        }

        if (isset($data->data_emissao_os) AND ( (is_scalar($data->data_emissao_os) AND $data->data_emissao_os !== '') OR (is_array($data->data_emissao_os) AND (!empty($data->data_emissao_os)) )) )
        {

            $filters[] = new TFilter('data_emissao_os', '>=', $data->data_emissao_os);// create the filter 
        }

        if (isset($data->codigo_cliente) AND ( (is_scalar($data->codigo_cliente) AND $data->codigo_cliente !== '') OR (is_array($data->codigo_cliente) AND (!empty($data->codigo_cliente)) )) )
        {

            $filters[] = new TFilter('codigo_cliente', '=', $data->codigo_cliente);// create the filter 
        }

        if (isset($data->fantasia) AND ( (is_scalar($data->fantasia) AND $data->fantasia !== '') OR (is_array($data->fantasia) AND (!empty($data->fantasia)) )) )
        {

            $filters[] = new TFilter('fantasia', 'ilike', "%{$data->fantasia}%");// create the filter 
        }

        if (isset($data->categoria_cliente) AND ( (is_scalar($data->categoria_cliente) AND $data->categoria_cliente !== '') OR (is_array($data->categoria_cliente) AND (!empty($data->categoria_cliente)) )) )
        {

            $filters[] = new TFilter('categoria_cliente', '=', $data->categoria_cliente);// create the filter 
        }

        if (isset($data->valor) AND ( (is_scalar($data->valor) AND $data->valor !== '') OR (is_array($data->valor) AND (!empty($data->valor)) )) )
        {

            $filters[] = new TFilter('valor', '=', $data->valor);// create the filter 
        }

        if (isset($data->tem_comissao) AND ( (is_scalar($data->tem_comissao) AND $data->tem_comissao !== '') OR (is_array($data->tem_comissao) AND (!empty($data->tem_comissao)) )) )
        {

            $filters[] = new TFilter('tem_comissao', '=', $data->tem_comissao);// create the filter 
        }

        if (isset($data->comissao) AND ( (is_scalar($data->comissao) AND $data->comissao !== '') OR (is_array($data->comissao) AND (!empty($data->comissao)) )) )
        {

            $filters[] = new TFilter('comissao', '=', $data->comissao);// create the filter 
        }

        // fill the form with data again
        $this->datagrid_form->setData($data);

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

            // creates a repository for ViewComissaoRepres
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

        $object = new ViewComissaoRepres($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

