<?php

class InteracaoAtividadeHeaderList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoAtividade';
    private static $primaryKey = 'id';
    private static $formName = 'formList_InteracaoAtividade';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

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

        $this->limit = 20;

        $criteria_tipo_atividade_id = new TCriteria();
        $criteria_interacao_vendedor_razao_social = new TCriteria();
        $criteria_estado_atividade_id = new TCriteria();

        $filter = new TFilter('id', 'in', "(SELECT vendedor_id FROM interacao)");
         TTransaction::open(self::$database);
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
             $filter = new TFilter('id', 'in', "(SELECT vendedor_id FROM interacao WHERE vendedor_id in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");

        }
        TTransaction::close();

        $criteria_interacao_vendedor_razao_social->add($filter);

        $tipo_atividade_id = new TDBCombo('tipo_atividade_id', 'minicrm', 'TipoAtividade', 'id', '{nome}','nome asc' , $criteria_tipo_atividade_id );
        $interacao_vendedor_razao_social = new TDBUniqueSearch('interacao_vendedor_razao_social', 'minicrm', 'Representante', 'id', 'razao_social','razao_social asc' , $criteria_interacao_vendedor_razao_social );
        $observacao = new TEntry('observacao');
        $estado_atividade_id = new TDBCombo('estado_atividade_id', 'minicrm', 'EstadoAtividade', 'id', '{nome}','nome asc' , $criteria_estado_atividade_id );

        $observacao->exitOnEnter();

        $observacao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $tipo_atividade_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $interacao_vendedor_razao_social->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $estado_atividade_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $interacao_vendedor_razao_social->setMinLength(2);
        $interacao_vendedor_razao_social->setMask('{razao_social}');
        $tipo_atividade_id->enableSearch();
        $estado_atividade_id->enableSearch();

        $observacao->setSize('100%');
        $tipo_atividade_id->setSize('100%');
        $estado_atividade_id->setSize('100%');
        $interacao_vendedor_razao_social->setSize('100%');

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid->setGroupColumn('interacao_id', "<div style=\"display: flex; align-items: center;\">
    <span class='estado_atividade' style='background-color: {interacao->vendedor->cor}; margin-right: 5px; '></span>

    {interacao->cliente->razao_social}  
</div>

");
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_horario_inicial_transformed = new TDataGridColumn('horario_inicial', "Data", 'left');
        $column_horario_inicial_transformed1 = new TDataGridColumn('horario_inicial', "Início", 'left');
        $column_horario_final_transformed = new TDataGridColumn('horario_final', "Fim", 'left');
        $column_tipo_atividade_nome_transformed = new TDataGridColumn('tipo_atividade->nome', "Tipo atividade", 'left');
        $column_interacao_vendedor_razao_social = new TDataGridColumn('interacao->vendedor->razao_social', "Representante", 'left');
        $column_observacao = new TDataGridColumn('observacao', "Observacao", 'left');
        $column_estado_atividade_nome_transformed = new TDataGridColumn('estado_atividade->nome', "Estado", 'left' , '150px');

        $column_horario_inicial_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
          if (!empty($value)) {
                $date = new DateTime($value); 
                return $date->format('d/m/Y');
            }

            return null;

        });

        $column_horario_inicial_transformed1->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!empty($value)) {
                $date = new DateTime($value); 
                return $date->format('H:i');
            }

            return null;
        });

        $column_horario_final_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (!empty($value)) {
                $date = new DateTime($value); 
                return $date->format('H:i');
            }

            return null;
        });

        $column_tipo_atividade_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
           return "<span class='label' style='width: 100%; max-width: 200px; background-color: {$object->tipo_atividade->cor}'>
            {$object->tipo_atividade->icone_formatado} - {$object->tipo_atividade->nome}
        </span>";
        });

        $column_estado_atividade_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            //code here
              return "<span class='label' style='width:235px;background-color:{$object->estado_atividade->cor}'> {$value} <span> "; 
        });        

        $order_horario_inicial_transformed = new TAction(array($this, 'onReload'));
        $order_horario_inicial_transformed->setParameter('order', 'horario_inicial');
        $column_horario_inicial_transformed->setAction($order_horario_inicial_transformed);
        $order_horario_inicial_transformed = new TAction(array($this, 'onReload'));
        $order_horario_inicial_transformed->setParameter('order', 'horario_inicial');
        $column_horario_inicial_transformed->setAction($order_horario_inicial_transformed);
        $order_horario_final_transformed = new TAction(array($this, 'onReload'));
        $order_horario_final_transformed->setParameter('order', 'horario_final');
        $column_horario_final_transformed->setAction($order_horario_final_transformed);
        $order_observacao = new TAction(array($this, 'onReload'));
        $order_observacao->setParameter('order', 'observacao');
        $column_observacao->setAction($order_observacao);

        $this->datagrid->addColumn($column_horario_inicial_transformed);
        $this->datagrid->addColumn($column_horario_inicial_transformed1);
        $this->datagrid->addColumn($column_horario_final_transformed);
        $this->datagrid->addColumn($column_tipo_atividade_nome_transformed);
        $this->datagrid->addColumn($column_interacao_vendedor_razao_social);
        $this->datagrid->addColumn($column_observacao);
        $this->datagrid->addColumn($column_estado_atividade_nome_transformed);

        $action_onEdit = new TDataGridAction(array('InteracaoAtividadeGlobalCalendarForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("");
        $action_onEdit->setImage('fas:edit #2196F3');
        $action_onEdit->setField(self::$primaryKey);

        $action_onEdit->setParameter('id', '{id}');
        $action_onEdit->setParameter("voltar", "true");

        $this->datagrid->addAction($action_onEdit);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        if(!$action_onEdit->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_empty = TElement::tag('td', "");
        $tr->add($td_empty);
        $td_empty = TElement::tag('td', "");
        $tr->add($td_empty);
        $td_empty = TElement::tag('td', "");
        $tr->add($td_empty);
        $td_tipo_atividade_id = TElement::tag('td', $tipo_atividade_id);
        $tr->add($td_tipo_atividade_id);
        $td_interacao_vendedor_razao_social = TElement::tag('td', $interacao_vendedor_razao_social);
        $tr->add($td_interacao_vendedor_razao_social);
        $td_observacao = TElement::tag('td', $observacao);
        $tr->add($td_observacao);
        $td_estado_atividade_id = TElement::tag('td', $estado_atividade_id);
        $tr->add($td_estado_atividade_id);

        $this->datagrid_form->addField($tipo_atividade_id);
        $this->datagrid_form->addField($interacao_vendedor_razao_social);
        $this->datagrid_form->addField($observacao);
        $this->datagrid_form->addField($estado_atividade_id);

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

        $button_dia = new TButton('button_button_dia');
        $button_dia->setAction(new TAction(['InteracaoAtividadeHeaderList', 'onDay']), "Dia");
        $button_dia->addStyleClass('btn-primary');
        $button_dia->setImage('fas:calendar-day #FFFFFF');

        $this->datagrid_form->addField($button_dia);

        $button_semana = new TButton('button_button_semana');
        $button_semana->setAction(new TAction(['InteracaoAtividadeHeaderList', 'onWeek']), "Semana");
        $button_semana->addStyleClass('btn-primary');
        $button_semana->setImage('fas:calendar-minus #FFFFFF');

        $this->datagrid_form->addField($button_semana);

        $button_mes = new TButton('button_button_mes');
        $button_mes->setAction(new TAction(['InteracaoAtividadeHeaderList', 'onMonth']), "Mes");
        $button_mes->addStyleClass('btn-primary');
        $button_mes->setImage('fas:calendar-alt #FFFFFF');

        $this->datagrid_form->addField($button_mes);

        $button_agenda = new TButton('button_button_agenda');
        $button_agenda->setAction(new TAction(['BuscaAgendaGlobalAtividadeForm', 'onShow']), "Agenda");
        $button_agenda->addStyleClass('btn-default');
        $button_agenda->setImage('fas:calendar-alt #FF9800');

        $this->datagrid_form->addField($button_agenda);

        $button_imprimir_pdf = new TButton('button_button_imprimir_pdf');
        $button_imprimir_pdf->setAction(new TAction(['InteracaoAtividadeHeaderList', 'onExportPdf'],['static' => 1]), "Imprimir PDF");
        $button_imprimir_pdf->addStyleClass('btn-default');
        $button_imprimir_pdf->setImage('far:file-pdf #e74c3c');

        $this->datagrid_form->addField($button_imprimir_pdf);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['InteracaoAtividadeHeaderList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['InteracaoAtividadeHeaderList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_limpar_filtros);

        $head_right_actions->add($button_dia);
        $head_right_actions->add($button_semana);
        $head_right_actions->add($button_mes);
        $head_right_actions->add($button_agenda);
        $head_right_actions->add($button_imprimir_pdf);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Listagem de Atividades"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onDay($param = null) 
    {
        try 
        {
            $filters = [];

            $filters[] = new TFilter('horario_inicial', '>=', date('Y-m-d 00:00:00'));
            $filters[] = new TFilter('horario_final', '<=', date('Y-m-d 23:59:59'));

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onWeek($param = null) 
    {
        try 
        {
            $hoje = new DateTime();

            // Define a data de início da semana (segunda-feira)
            $inicioSemana = clone $hoje;
            $inicioSemana->modify('monday this week');

            // Define a data de fim da semana (domingo)
            $fimSemana = clone $hoje;
            $fimSemana->modify('sunday this week');

            // Formata as datas no formato 'Y-m-d'
            $inicioSemanaStr = $inicioSemana->format('Y-m-d 00:00:00');
            $fimSemanaStr = $fimSemana->format('Y-m-d 23:59:59');

             $filters = [];

            $filters[] = new TFilter('horario_inicial', '>=',$inicioSemanaStr);
            $filters[] = new TFilter('horario_final', '<=', $fimSemanaStr);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onMonth($param = null) 
    {
        try 
        {

            $inicioMes = new DateTime('first day of this month 00:00:00');
            $fimMes = new DateTime('last day of this month 23:59:59');

            $inicioMesStr = $inicioMes->format('Y-m-d H:i:s');
            $fimMesStr = $fimMes->format('Y-m-d H:i:s');

            $filters = [];
            $filters[] = new TFilter('horario_inicial', '>=', $inicioMesStr);
            $filters[] = new TFilter('horario_final', '<=', $fimMesStr);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
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
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

        if (isset($data->tipo_atividade_id) AND ( (is_scalar($data->tipo_atividade_id) AND $data->tipo_atividade_id !== '') OR (is_array($data->tipo_atividade_id) AND (!empty($data->tipo_atividade_id)) )) )
        {

            $filters[] = new TFilter('tipo_atividade_id', '=', $data->tipo_atividade_id);// create the filter 
        }

        if (isset($data->interacao_vendedor_razao_social) AND ( (is_scalar($data->interacao_vendedor_razao_social) AND $data->interacao_vendedor_razao_social !== '') OR (is_array($data->interacao_vendedor_razao_social) AND (!empty($data->interacao_vendedor_razao_social)) )) )
        {

            $filters[] = new TFilter('interacao_id', 'in', "(SELECT id FROM interacao WHERE vendedor_id = '{$data->interacao_vendedor_razao_social}')");// create the filter 
        }

        if (isset($data->observacao) AND ( (is_scalar($data->observacao) AND $data->observacao !== '') OR (is_array($data->observacao) AND (!empty($data->observacao)) )) )
        {

            $filters[] = new TFilter('observacao', 'ilike', "%{$data->observacao}%");// create the filter 
        }

        if (isset($data->estado_atividade_id) AND ( (is_scalar($data->estado_atividade_id) AND $data->estado_atividade_id !== '') OR (is_array($data->estado_atividade_id) AND (!empty($data->estado_atividade_id)) )) )
        {

            $filters[] = new TFilter('estado_atividade_id', '=', $data->estado_atividade_id);// create the filter 
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

            // creates a repository for InteracaoAtividade
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'interacao_id, horario_inicial';    
            }
            elseif($param['order'] != 'interacao_id, horario_inicial')
            {
                $param['order'] = "interacao_id, horario_inicial,{$param['order']}"; 
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

            TTransaction::open(self::$database);
            $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
            if($representante){
                $filter = new TFilter('id', 'in', "(SELECT id FROM interacao_atividade WHERE interacao_id in (SELECT id FROM interacao WHERE vendedor_id in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid').")))");
                $criteria->add($filter); 
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

        $object = new InteracaoAtividade($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

