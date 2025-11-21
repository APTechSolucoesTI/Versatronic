<?php

class RepresentanteTotvsList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'RepresentanteTotvs';
    private static $primaryKey = 'id';
    private static $formName = 'form_RepresentanteTovtsList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

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
        $this->form->setFormTitle("Listagem de representante tovtss");
        $this->limit = 20;

        $id = new TEntry('id');
        $codigo = new TEntry('codigo');
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $inscrestadual = new TEntry('inscrestadual');
        $razao_social = new TEntry('razao_social');
        $fantasia = new TEntry('fantasia');
        $email = new TEntry('email');
        $ativo = new TEntry('ativo');
        $codcoligada = new TEntry('codcoligada');
        $codigo_col = new TEntry('codigo_col');
        $codcoligada_col = new TEntry('codcoligada_col');
        $razao_social_col = new TEntry('razao_social_col');
        $cpf_cnpj_col = new TEntry('cpf_cnpj_col');
        $email_col = new TEntry('email_col');
        $inscrestadual_col = new TEntry('inscrestadual_col');
        $telefone_col = new TEntry('telefone_col');
        $ativo_com = new TCombo('ativo_com');

        $codigo_col->exitOnEnter();
        $codcoligada_col->exitOnEnter();
        $razao_social_col->exitOnEnter();
        $cpf_cnpj_col->exitOnEnter();
        $email_col->exitOnEnter();
        $inscrestadual_col->exitOnEnter();
        $telefone_col->exitOnEnter();

        $codigo_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $codcoligada_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $razao_social_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cpf_cnpj_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $email_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $inscrestadual_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $telefone_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $ativo_com->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $razao_social_col->forceUpperCase();
        $ativo_com->addItems(["S"=>" Sim","N"=>" Não"]);
        $ativo_com->enableSearch();
        $ativo->setMaxLength(1);
        $email->setMaxLength(40);
        $codigo->setMaxLength(15);
        $cpf_cnpj->setMaxLength(20);
        $fantasia->setMaxLength(255);
        $inscrestadual->setMaxLength(20);
        $razao_social->setMaxLength(255);

        $id->setSize('100%');
        $email->setSize('100%');
        $ativo->setSize('100%');
        $codigo->setSize('100%');
        $codigo_col->setSize(130);
        $cpf_cnpj->setSize('100%');
        $fantasia->setSize('100%');
        $email_col->setSize('100%');
        $ativo_com->setSize('100%');
        $codcoligada->setSize('100%');
        $razao_social->setSize('100%');
        $codcoligada_col->setSize(130);
        $cpf_cnpj_col->setSize('100%');
        $telefone_col->setSize('100%');
        $inscrestadual->setSize('100%');
        $razao_social_col->setSize('100%');
        $inscrestadual_col->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Codigo:", null, '14px', null, '100%'),$codigo],[new TLabel("CNPJ:", null, '14px', null, '100%'),$cpf_cnpj],[new TLabel("Inscrição Estadual:", null, '14px', null, '100%'),$inscrestadual]);
        $row1->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row2 = $this->form->addFields([new TLabel("Razão Social:", null, '14px', null, '100%'),$razao_social],[new TLabel("Fantasia:", null, '14px', null, '100%'),$fantasia],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([new TLabel("Ativo:", null, '14px', null, '100%'),$ativo],[new TLabel("Codcoligada:", null, '14px', null, '100%'),$codcoligada]);
        $row3->layout = [' col-sm-4',' col-sm-4'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $startHidden = true;

        if(TSession::getValue('RepresentanteTotvsList_expand_start_hidden') === false)
        {
            $startHidden = false;
        }
        elseif(TSession::getValue('RepresentanteTotvsList_expand_start_hidden') === true)
        {
            $startHidden = true; 
        }
        $expandButton = $this->form->addExpandButton("Filtros", 'fas:filter #000000', $startHidden);
        $expandButton->addStyleClass('btn-default');
        $expandButton->setAction(new TAction([$this, 'onExpandForm'], ['static'=>1]), "Filtros");
        $this->form->addField($expandButton);

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_codigo = new TDataGridColumn('codigo', "Codigo", 'left');
        $column_codcoligada = new TDataGridColumn('codcoligada', "Coligada", 'left');
        $column_razao_social = new TDataGridColumn('razao_social', "Razão Social", 'left');
        $column_cpf_cnpj = new TDataGridColumn('cpf_cnpj', "CNPJ", 'left');
        $column_email = new TDataGridColumn('email', "Email", 'left');
        $column_inscrestadual = new TDataGridColumn('inscrestadual', "inscrição estadual", 'left');
        $column_telefone = new TDataGridColumn('telefone', "Telefone", 'left');
        $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'left');

        $column_ativo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        });        

        $order_codigo = new TAction(array($this, 'onReload'));
        $order_codigo->setParameter('order', 'codigo');
        $column_codigo->setAction($order_codigo);
        $order_codcoligada = new TAction(array($this, 'onReload'));
        $order_codcoligada->setParameter('order', 'codcoligada');
        $column_codcoligada->setAction($order_codcoligada);
        $order_razao_social = new TAction(array($this, 'onReload'));
        $order_razao_social->setParameter('order', 'razao_social');
        $column_razao_social->setAction($order_razao_social);
        $order_cpf_cnpj = new TAction(array($this, 'onReload'));
        $order_cpf_cnpj->setParameter('order', 'cpf_cnpj');
        $column_cpf_cnpj->setAction($order_cpf_cnpj);
        $order_email = new TAction(array($this, 'onReload'));
        $order_email->setParameter('order', 'email');
        $column_email->setAction($order_email);
        $order_inscrestadual = new TAction(array($this, 'onReload'));
        $order_inscrestadual->setParameter('order', 'inscrestadual');
        $column_inscrestadual->setAction($order_inscrestadual);
        $order_telefone = new TAction(array($this, 'onReload'));
        $order_telefone->setParameter('order', 'telefone');
        $column_telefone->setAction($order_telefone);
        $order_ativo_transformed = new TAction(array($this, 'onReload'));
        $order_ativo_transformed->setParameter('order', 'ativo');
        $column_ativo_transformed->setAction($order_ativo_transformed);

        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_codcoligada);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_inscrestadual);
        $this->datagrid->addColumn($column_telefone);
        $this->datagrid->addColumn($column_ativo_transformed);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        $td_codigo_col = TElement::tag('td', $codigo_col);
        $tr->add($td_codigo_col);
        $td_codcoligada_col = TElement::tag('td', $codcoligada_col);
        $tr->add($td_codcoligada_col);
        $td_razao_social_col = TElement::tag('td', $razao_social_col);
        $tr->add($td_razao_social_col);
        $td_cpf_cnpj_col = TElement::tag('td', $cpf_cnpj_col);
        $tr->add($td_cpf_cnpj_col);
        $td_email_col = TElement::tag('td', $email_col);
        $tr->add($td_email_col);
        $td_inscrestadual_col = TElement::tag('td', $inscrestadual_col);
        $tr->add($td_inscrestadual_col);
        $td_telefone_col = TElement::tag('td', $telefone_col);
        $tr->add($td_telefone_col);
        $td_ativo_com = TElement::tag('td', $ativo_com);
        $tr->add($td_ativo_com);

        $this->datagrid_form->addField($codigo_col);
        $this->datagrid_form->addField($codcoligada_col);
        $this->datagrid_form->addField($razao_social_col);
        $this->datagrid_form->addField($cpf_cnpj_col);
        $this->datagrid_form->addField($email_col);
        $this->datagrid_form->addField($inscrestadual_col);
        $this->datagrid_form->addField($telefone_col);
        $this->datagrid_form->addField($ativo_com);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
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

        $button_sincronizar = new TButton('button_button_sincronizar');
        $button_sincronizar->setAction(new TAction(['RepresentanteTotvsList', 'onSync']), "Sincronizar");
        $button_sincronizar->addStyleClass('btn-default');
        $button_sincronizar->setImage('fas:sync #000000');

        $this->datagrid_form->addField($button_sincronizar);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['RepresentanteTotvsList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['RepresentanteTotvsList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['RepresentanteTotvsList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['RepresentanteTotvsList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['RepresentanteTotvsList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['RepresentanteTotvsList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($button_limpar_filtros);

        $head_right_actions->add($button_sincronizar);
        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Pessoas","Representante totvs"]));
        }
        $container->add($this->form);

        $container->add($panel);

        parent::add($container);

    }

    public function onSync($param = null) 
    {
        try 
        {
            AtualizacaoService::atualizarRepresentante();
            $this->onReload();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
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

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->codigo) AND ( (is_scalar($data->codigo) AND $data->codigo !== '') OR (is_array($data->codigo) AND (!empty($data->codigo)) )) )
        {

            $filters[] = new TFilter('codigo', 'like', "%{$data->codigo}%");// create the filter 
        }

        if (isset($data->cpf_cnpj) AND ( (is_scalar($data->cpf_cnpj) AND $data->cpf_cnpj !== '') OR (is_array($data->cpf_cnpj) AND (!empty($data->cpf_cnpj)) )) )
        {

            $filters[] = new TFilter('cpf_cnpj', 'like', "%{$data->cpf_cnpj}%");// create the filter 
        }

        if (isset($data->inscrestadual) AND ( (is_scalar($data->inscrestadual) AND $data->inscrestadual !== '') OR (is_array($data->inscrestadual) AND (!empty($data->inscrestadual)) )) )
        {

            $filters[] = new TFilter('inscrestadual', 'like', "%{$data->inscrestadual}%");// create the filter 
        }

        if (isset($data->razao_social) AND ( (is_scalar($data->razao_social) AND $data->razao_social !== '') OR (is_array($data->razao_social) AND (!empty($data->razao_social)) )) )
        {

            $filters[] = new TFilter('razao_social', 'like', "%{$data->razao_social}%");// create the filter 
        }

        if (isset($data->fantasia) AND ( (is_scalar($data->fantasia) AND $data->fantasia !== '') OR (is_array($data->fantasia) AND (!empty($data->fantasia)) )) )
        {

            $filters[] = new TFilter('fantasia', 'like', "%{$data->fantasia}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        if (isset($data->ativo) AND ( (is_scalar($data->ativo) AND $data->ativo !== '') OR (is_array($data->ativo) AND (!empty($data->ativo)) )) )
        {

            $filters[] = new TFilter('ativo', '=', $data->ativo);// create the filter 
        }

        if (isset($data->codcoligada) AND ( (is_scalar($data->codcoligada) AND $data->codcoligada !== '') OR (is_array($data->codcoligada) AND (!empty($data->codcoligada)) )) )
        {

            $filters[] = new TFilter('codcoligada', 'like', "%{$data->codcoligada}%");// create the filter 
        }

        if (isset($data->codigo_col) AND ( (is_scalar($data->codigo_col) AND $data->codigo_col !== '') OR (is_array($data->codigo_col) AND (!empty($data->codigo_col)) )) )
        {

            $filters[] = new TFilter('codigo', 'like', "%{$data->codigo_col}%");// create the filter 
        }

        if (isset($data->codcoligada_col) AND ( (is_scalar($data->codcoligada_col) AND $data->codcoligada_col !== '') OR (is_array($data->codcoligada_col) AND (!empty($data->codcoligada_col)) )) )
        {

            $filters[] = new TFilter('codcoligada', 'like', "%{$data->codcoligada_col}%");// create the filter 
        }

        if (isset($data->razao_social_col) AND ( (is_scalar($data->razao_social_col) AND $data->razao_social_col !== '') OR (is_array($data->razao_social_col) AND (!empty($data->razao_social_col)) )) )
        {

            $filters[] = new TFilter('razao_social', 'like', "%{$data->razao_social_col}%");// create the filter 
        }

        if (isset($data->cpf_cnpj_col) AND ( (is_scalar($data->cpf_cnpj_col) AND $data->cpf_cnpj_col !== '') OR (is_array($data->cpf_cnpj_col) AND (!empty($data->cpf_cnpj_col)) )) )
        {

            $filters[] = new TFilter('cpf_cnpj', 'like', "%{$data->cpf_cnpj_col}%");// create the filter 
        }

        if (isset($data->email_col) AND ( (is_scalar($data->email_col) AND $data->email_col !== '') OR (is_array($data->email_col) AND (!empty($data->email_col)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email_col}%");// create the filter 
        }

        if (isset($data->inscrestadual_col) AND ( (is_scalar($data->inscrestadual_col) AND $data->inscrestadual_col !== '') OR (is_array($data->inscrestadual_col) AND (!empty($data->inscrestadual_col)) )) )
        {

            $filters[] = new TFilter('inscrestadual', 'like', "%{$data->inscrestadual_col}%");// create the filter 
        }

        if (isset($data->telefone_col) AND ( (is_scalar($data->telefone_col) AND $data->telefone_col !== '') OR (is_array($data->telefone_col) AND (!empty($data->telefone_col)) )) )
        {

            $filters[] = new TFilter('telefone', 'like', "%{$data->telefone_col}%");// create the filter 
        }

        if (isset($data->ativo_com) AND ( (is_scalar($data->ativo_com) AND $data->ativo_com !== '') OR (is_array($data->ativo_com) AND (!empty($data->ativo_com)) )) )
        {

            $filters[] = new TFilter('ativo', '=', $data->ativo_com);// create the filter 
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

            // creates a repository for RepresentanteTotvs
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

    public static function onExpandForm($param = null)
    {
        try
        {
            $startHidden = true;

            if(TSession::getValue('RepresentanteTotvsList_expand_start_hidden') === false)
            {
                TSession::setValue('RepresentanteTotvsList_expand_start_hidden', true);
            }
            elseif(TSession::getValue('RepresentanteTotvsList_expand_start_hidden') === true)
            {
                TSession::setValue('RepresentanteTotvsList_expand_start_hidden', false);
            }
            else
            {
                TSession::setValue('RepresentanteTotvsList_expand_start_hidden', !$startHidden);
            }

        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
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

        $object = new RepresentanteTotvs($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

