<?php

class TransportadoraList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'Transportadora';
    private static $primaryKey = 'id';
    private static $formName = 'form_TransportadoraList';
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
        $this->form->setFormTitle("Transportadoras");
        $this->limit = 20;

        $codtra = new TEntry('codtra');
        $cgc = new TEntry('cgc');
        $nome = new TEntry('nome');
        $fantasia = new TEntry('fantasia');
        $telefone = new TEntry('telefone');
        $email = new TEntry('email');
        $codtra_col = new TEntry('codtra_col');
        $nome_col = new TEntry('nome_col');
        $cgc_col = new TEntry('cgc_col');
        $telefone_col = new TEntry('telefone_col');
        $email_col = new TEntry('email_col');
        $ativo = new TCombo('ativo');

        $codtra_col->exitOnEnter();
        $nome_col->exitOnEnter();
        $cgc_col->exitOnEnter();
        $telefone_col->exitOnEnter();
        $email_col->exitOnEnter();

        $codtra_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $nome_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cgc_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $telefone_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $email_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $ativo->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $nome_col->forceUpperCase();
        $ativo->addItems(["S"=>"Sim","N"=>"Não"]);
        $ativo->enableSearch();
        $cgc->setMaxLength(20);
        $nome->setMaxLength(40);
        $codtra->setMaxLength(5);
        $email->setMaxLength(60);
        $fantasia->setMaxLength(60);
        $telefone->setMaxLength(15);

        $cgc->setSize('100%');
        $nome->setSize('100%');
        $email->setSize('100%');
        $ativo->setSize('100%');
        $codtra->setSize('100%');
        $cgc_col->setSize('100%');
        $fantasia->setSize('100%');
        $telefone->setSize('100%');
        $nome_col->setSize('100%');
        $email_col->setSize('100%');
        $codtra_col->setSize('100%');
        $telefone_col->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Código:", null, '14px', null, '100%'),$codtra],[new TLabel("Documento:", null, '14px', null, '100%'),$cgc]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Fantasia:", null, '14px', null, '100%'),$fantasia]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Telefone:", null, '14px', null, '100%'),$telefone],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

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

        $column_codtra = new TDataGridColumn('codtra', "Código", 'left');
        $column_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_cgc_transformed = new TDataGridColumn('cgc', "Documento", 'left');
        $column_telefone_transformed = new TDataGridColumn('telefone', "Telefone", 'left');
        $column_email = new TDataGridColumn('email', "Email", 'left');
        $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'left');

        $column_cgc_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if (strlen($value) === 14) {
                // Formato CNPJ: XX.XXX.XXX/XXXX-XX
                return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $value);
            }
            if (strlen($value) === 11) {
                // Formato CPF: XXX.XXX.XXX-XX
                return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $value);
            }
            return $value;

        });

        $column_telefone_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if (strlen($value) === 10) {
                // Formato (XX) XXXX-XXXX
                return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $value);
            } elseif (strlen($value) === 11) {
                // Formato (XX) XXXXX-XXXX
                return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $value);
            } else {
                // Retorna o valor original se não tiver o comprimento esperado
                return $value;
            }

        });

        $column_ativo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        });        

        $order_codtra = new TAction(array($this, 'onReload'));
        $order_codtra->setParameter('order', 'codtra');
        $column_codtra->setAction($order_codtra);
        $order_nome = new TAction(array($this, 'onReload'));
        $order_nome->setParameter('order', 'nome');
        $column_nome->setAction($order_nome);
        $order_cgc_transformed = new TAction(array($this, 'onReload'));
        $order_cgc_transformed->setParameter('order', 'cgc');
        $column_cgc_transformed->setAction($order_cgc_transformed);
        $order_telefone_transformed = new TAction(array($this, 'onReload'));
        $order_telefone_transformed->setParameter('order', 'telefone');
        $column_telefone_transformed->setAction($order_telefone_transformed);
        $order_ativo_transformed = new TAction(array($this, 'onReload'));
        $order_ativo_transformed->setParameter('order', 'ativo');
        $column_ativo_transformed->setAction($order_ativo_transformed);

        $this->datagrid->addColumn($column_codtra);
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_cgc_transformed);
        $this->datagrid->addColumn($column_telefone_transformed);
        $this->datagrid->addColumn($column_email);
        $this->datagrid->addColumn($column_ativo_transformed);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        $td_codtra_col = TElement::tag('td', $codtra_col);
        $tr->add($td_codtra_col);
        $td_nome_col = TElement::tag('td', $nome_col);
        $tr->add($td_nome_col);
        $td_cgc_col = TElement::tag('td', $cgc_col);
        $tr->add($td_cgc_col);
        $td_telefone_col = TElement::tag('td', $telefone_col);
        $tr->add($td_telefone_col);
        $td_email_col = TElement::tag('td', $email_col);
        $tr->add($td_email_col);
        $td_ativo = TElement::tag('td', $ativo);
        $tr->add($td_ativo);

        $this->datagrid_form->addField($codtra_col);
        $this->datagrid_form->addField($nome_col);
        $this->datagrid_form->addField($cgc_col);
        $this->datagrid_form->addField($telefone_col);
        $this->datagrid_form->addField($email_col);
        $this->datagrid_form->addField($ativo);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Transportadoras");
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
        $button_sincronizar->setAction(new TAction(['TransportadoraList', 'onSync']), "Sincronizar");
        $button_sincronizar->addStyleClass('btn-default');
        $button_sincronizar->setImage('fas:sync-alt #000000');

        $this->datagrid_form->addField($button_sincronizar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['TransportadoraList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['TransportadoraList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['TransportadoraList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['TransportadoraList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['TransportadoraList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['TransportadoraList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['TransportadoraList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);

        $head_right_actions->add($dropdown_button_exportar);
        $head_right_actions->add($button_sincronizar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        $this->btnShowCurtainFilters->name = 'btnShowCurtainFilters';
        TScript::create("$(\"[name='btnShowCurtainFilters']\").hide()");

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Pessoas","Transportadoras"]));
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
    public function onSync($param = null) 
    {
        try 
        {
           AtualizacaoService::atualizarTransportadora();
           $this->onReload();

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
            $page->setProperty('page-name', 'TransportadoraListSearch');
            $page->setProperty('page_name', 'TransportadoraListSearch');
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
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

        if (isset($data->codtra) AND ( (is_scalar($data->codtra) AND $data->codtra !== '') OR (is_array($data->codtra) AND (!empty($data->codtra)) )) )
        {

            $filters[] = new TFilter('codtra', 'like', "%{$data->codtra}%");// create the filter 
        }

        if (isset($data->cgc) AND ( (is_scalar($data->cgc) AND $data->cgc !== '') OR (is_array($data->cgc) AND (!empty($data->cgc)) )) )
        {

            $filters[] = new TFilter('cgc', 'like', "%{$data->cgc}%");// create the filter 
        }

        if (isset($data->nome) AND ( (is_scalar($data->nome) AND $data->nome !== '') OR (is_array($data->nome) AND (!empty($data->nome)) )) )
        {

            $filters[] = new TFilter('nome', 'like', "%{$data->nome}%");// create the filter 
        }

        if (isset($data->fantasia) AND ( (is_scalar($data->fantasia) AND $data->fantasia !== '') OR (is_array($data->fantasia) AND (!empty($data->fantasia)) )) )
        {

            $filters[] = new TFilter('nomefantasia', 'like', "%{$data->fantasia}%");// create the filter 
        }

        if (isset($data->telefone) AND ( (is_scalar($data->telefone) AND $data->telefone !== '') OR (is_array($data->telefone) AND (!empty($data->telefone)) )) )
        {

            $filters[] = new TFilter('telefone', 'like', "%{$data->telefone}%");// create the filter 
        }

        if (isset($data->email) AND ( (is_scalar($data->email) AND $data->email !== '') OR (is_array($data->email) AND (!empty($data->email)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email}%");// create the filter 
        }

        if (isset($data->codtra_col) AND ( (is_scalar($data->codtra_col) AND $data->codtra_col !== '') OR (is_array($data->codtra_col) AND (!empty($data->codtra_col)) )) )
        {

            $filters[] = new TFilter('codtra', 'like', "%{$data->codtra_col}%");// create the filter 
        }

        if (isset($data->nome_col) AND ( (is_scalar($data->nome_col) AND $data->nome_col !== '') OR (is_array($data->nome_col) AND (!empty($data->nome_col)) )) )
        {

            $filters[] = new TFilter('nome', 'like', "%{$data->nome_col}%");// create the filter 
        }

        if (isset($data->cgc_col) AND ( (is_scalar($data->cgc_col) AND $data->cgc_col !== '') OR (is_array($data->cgc_col) AND (!empty($data->cgc_col)) )) )
        {

            $filters[] = new TFilter('cgc', 'like', "%{$data->cgc_col}%");// create the filter 
        }

        if (isset($data->telefone_col) AND ( (is_scalar($data->telefone_col) AND $data->telefone_col !== '') OR (is_array($data->telefone_col) AND (!empty($data->telefone_col)) )) )
        {

            $filters[] = new TFilter('telefone', 'like', "%{$data->telefone_col}%");// create the filter 
        }

        if (isset($data->email_col) AND ( (is_scalar($data->email_col) AND $data->email_col !== '') OR (is_array($data->email_col) AND (!empty($data->email_col)) )) )
        {

            $filters[] = new TFilter('email', 'like', "%{$data->email_col}%");// create the filter 
        }

        if (isset($data->ativo) AND ( (is_scalar($data->ativo) AND $data->ativo !== '') OR (is_array($data->ativo) AND (!empty($data->ativo)) )) )
        {

            $filters[] = new TFilter('ativo', '=', $data->ativo);// create the filter 
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

            // creates a repository for Transportadora
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

        $object = new Transportadora($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

