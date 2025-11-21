<?php

class NotaBaixadaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'NotaBaixada';
    private static $primaryKey = 'id';
    private static $formName = 'form_NotaBaixadaList';
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
        $this->form->setFormTitle("NFSe");
        $this->limit = 50;

        $criteria_coligada_id = new TCriteria();
        $criteria_nota_status_id = new TCriteria();

        $coligada_id = new TDBCombo('coligada_id', 'minicrm', 'Coligada', 'id', '{nome}','nome asc' , $criteria_coligada_id );
        $numero = new TEntry('numero');
        $nota_status_id = new TDBCombo('nota_status_id', 'minicrm', 'NotaStatus', 'id', '{nome}','nome asc' , $criteria_nota_status_id );
        $date_de = new TDate('date_de');
        $date_ate = new TDate('date_ate');


        $numero->setMaxLength(255);
        $coligada_id->enableSearch();
        $nota_status_id->enableSearch();

        $date_de->setMask('dd/mm/yyyy');
        $date_ate->setMask('dd/mm/yyyy');

        $date_de->setDatabaseMask('yyyy-mm-dd');
        $date_ate->setDatabaseMask('yyyy-mm-dd');

        $numero->setSize('100%');
        $coligada_id->setSize('100%');
        $nota_status_id->setSize('100%');
        $date_de->setSize('calc(50% - 15px)');
        $date_ate->setSize('calc(50% - 15px)');

        $row1 = $this->form->addFields([new TLabel("Coligada:", null, '14px', null, '100%'),$coligada_id],[new TLabel("Número:", null, '14px', null, '100%'),$numero]);
        $row1->layout = [' col-sm-6',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Status:", null, '14px', null, '100%'),$nota_status_id],[new TLabel("Data de Emissão:", null, '14px', null, '100%'),$date_de,$date_ate]);
        $row2->layout = [' col-sm-6',' col-sm-6'];

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

        $this->datagrid->setGroupColumn('coligada_id', " {coligada->nome} ");
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_enviado_email_transformed = new TDataGridColumn('enviado_email', "Email", 'left');
        $column_numero = new TDataGridColumn('numero', "Número", 'left');
        $column_data_emissao_transformed = new TDataGridColumn('data_emissao', "Data de Emissão", 'left');
        $column_razao_social = new TDataGridColumn('razao_social', "Razão Social", 'left');
        $column_documento_transformed = new TDataGridColumn('documento', "CNPJ/CPF", 'left');
        $column_valor_total_transformed = new TDataGridColumn('valor_total', "Valor Total", 'left');
        $column_nota_status_nome_transformed = new TDataGridColumn('nota_status->nome', "Status", 'left');

        $column_enviado_email_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

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

        $column_documento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_valor_total_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
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

        $column_nota_status_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            return "<span class='label' style='width:100px;background-color:".$object->nota_status->cor."'><i class='".$object->nota_status->icone."'></i> ".$object->nota_status->nome."<span> ";    

        });        

        $this->builder_datagrid_check_all = new TCheckButton('builder_datagrid_check_all');
        $this->builder_datagrid_check_all->setIndexValue('on');
        $this->builder_datagrid_check_all->onclick = "Builder.checkAll(this)";
        $this->builder_datagrid_check_all->style = 'cursor:pointer';
        $this->builder_datagrid_check_all->setProperty('class', 'filled-in');
        $this->builder_datagrid_check_all->id = 'builder_datagrid_check_all';

        $label = new TLabel('');
        $label->style = 'margin:0';
        $label->class = 'checklist-label';
        $this->builder_datagrid_check_all->after($label);
        $label->for = 'builder_datagrid_check_all';

        $this->builder_datagrid_check = $this->datagrid->addColumn( new TDataGridColumn('builder_datagrid_check', $this->builder_datagrid_check_all, 'center',  '1%') );

        $this->datagrid->addColumn($column_enviado_email_transformed);
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_data_emissao_transformed);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_documento_transformed);
        $this->datagrid->addColumn($column_valor_total_transformed);
        $this->datagrid->addColumn($column_nota_status_nome_transformed);

        $action_onSincronizar = new TDataGridAction(array('NotaBaixadaList', 'onSincronizar'));
        $action_onSincronizar->setUseButton(false);
        $action_onSincronizar->setButtonClass('btn btn-default btn-sm');
        $action_onSincronizar->setLabel("Sincronizar");
        $action_onSincronizar->setImage('fas:sync-alt #2196F3');
        $action_onSincronizar->setField(self::$primaryKey);

        $action_onSincronizar->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onSincronizar);

        $action_onBaixarXml = new TDataGridAction(array('NotaBaixadaList', 'onBaixarXml'));
        $action_onBaixarXml->setUseButton(false);
        $action_onBaixarXml->setButtonClass('btn btn-default btn-sm');
        $action_onBaixarXml->setLabel("XML");
        $action_onBaixarXml->setImage('fas:download #000000');
        $action_onBaixarXml->setField(self::$primaryKey);
        $action_onBaixarXml->setDisplayCondition('NotaBaixadaList::canXml');
        $action_onBaixarXml->setParameter('xml', '{nfs_xml}');

        $this->datagrid->addAction($action_onBaixarXml);

        $action_onBaixarPdf = new TDataGridAction(array('NotaBaixadaList', 'onBaixarPdf'));
        $action_onBaixarPdf->setUseButton(false);
        $action_onBaixarPdf->setButtonClass('btn btn-default btn-sm');
        $action_onBaixarPdf->setLabel("PDF");
        $action_onBaixarPdf->setImage('fas:file-pdf #000000');
        $action_onBaixarPdf->setField(self::$primaryKey);
        $action_onBaixarPdf->setDisplayCondition('NotaBaixadaList::canPdf');
        $action_onBaixarPdf->setParameter('pdf', '{nfs_pdf}');

        $this->datagrid->addAction($action_onBaixarPdf);

        $action_onEnviar = new TDataGridAction(array('NotaBaixadaList', 'onEnviar'));
        $action_onEnviar->setUseButton(false);
        $action_onEnviar->setButtonClass('btn btn-default btn-sm');
        $action_onEnviar->setLabel("Email");
        $action_onEnviar->setImage('fas:envelope #9C27B0');
        $action_onEnviar->setField(self::$primaryKey);
        $action_onEnviar->setDisplayCondition('NotaBaixadaList::canEnviar');
        $action_onEnviar->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onEnviar);

        $action_onShow = new TDataGridAction(array('NotaCancelamentoForm', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Cancelar");
        $action_onShow->setImage('fas:times-circle #F44336');
        $action_onShow->setField(self::$primaryKey);
        $action_onShow->setDisplayCondition('NotaBaixadaList::canCancelar');
        $action_onShow->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onShow);

        $action_onAutorizar = new TDataGridAction(array('NotaBaixadaList', 'onAutorizar'));
        $action_onAutorizar->setUseButton(false);
        $action_onAutorizar->setButtonClass('btn btn-default btn-sm');
        $action_onAutorizar->setLabel("Autorizar");
        $action_onAutorizar->setImage('fab:telegram #000893');
        $action_onAutorizar->setField(self::$primaryKey);
        $action_onAutorizar->setDisplayCondition('NotaBaixadaList::canAutorizar');
        $action_onAutorizar->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onAutorizar);

        $action_onDelete = new TDataGridAction(array('NotaBaixadaList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #FF0000');
        $action_onDelete->setField(self::$primaryKey);
        $action_onDelete->setDisplayCondition('NotaBaixadaList::canExcluir');

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("NFSe");
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
        $btnShowCurtainFilters->setAction(new TAction(['NotaBaixadaList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['NotaBaixadaList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['NotaBaixadaList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $button_sincronizar = new TButton('button_button_sincronizar');
        $button_sincronizar->setAction(new TAction(['NotaBaixadaList', 'foreachSincronizar']), "Sincronizar");
        $button_sincronizar->addStyleClass('btn-default');
        $button_sincronizar->setImage('fas:sync-alt #2196F3');

        $this->datagrid_form->addField($button_sincronizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['NotaBaixadaList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['NotaBaixadaList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['NotaBaixadaList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['NotaBaixadaList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );
        $dropdown_button_enviar = new TDropDown("Enviar", 'fas:search #4CAF50');
        $dropdown_button_enviar->setPullSide('right');
        $dropdown_button_enviar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_enviar->addPostAction( "NFSe", new TAction(['NotaForm', 'onShow']), 'datagrid_'.self::$formName, 'fas:search #4CAF50' );
        $dropdown_button_enviar->addPostAction( "Intervalo", new TAction(['NotasBaixadaForms', 'onShow']), 'datagrid_'.self::$formName, 'fas:search #4CAF50' );
        $dropdown_button_email = new TDropDown("Email", 'fas:filter #000000');
        $dropdown_button_email->setPullSide('right');
        $dropdown_button_email->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_email->addPostAction( "Enviados", new TAction(['NotaBaixadaList', 'onEnviados']), 'datagrid_'.self::$formName, 'fas:filter #000000' );
        $dropdown_button_email->addPostAction( "Não enviados", new TAction(['NotaBaixadaList', 'onNaoEnviado']), 'datagrid_'.self::$formName, 'fas:filter #000000' );
        $dropdown_button_status = new TDropDown("Status", 'fas:filter #000000');
        $dropdown_button_status->setPullSide('right');
        $dropdown_button_status->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_status->addPostAction( "Autorizada", new TAction(['NotaBaixadaList', 'onAutorizada']), 'datagrid_'.self::$formName, 'fas:check-circle #4CAF50' );
        $dropdown_button_status->addPostAction( "Cancelada", new TAction(['NotaBaixadaList', 'onCancelada']), 'datagrid_'.self::$formName, 'fas:times-circle #F44336' );
        $dropdown_button_status->addPostAction( "Erro", new TAction(['NotaBaixadaList', 'onErro']), 'datagrid_'.self::$formName, 'fas:info-circle #F0AD4E' );
        $dropdown_button_empresas = new TDropDown("Empresas", 'fas:filter #000000');
        $dropdown_button_empresas->setPullSide('right');
        $dropdown_button_empresas->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_empresas->addPostAction( "VERSATRONIC", new TAction(['NotaBaixadaList', 'onVersatronic']), 'datagrid_'.self::$formName, 'fas:filter #000000' );
        $dropdown_button_empresas->addPostAction( "CNC", new TAction(['NotaBaixadaList', 'onCnc']), 'datagrid_'.self::$formName, 'fas:filter #000000' );
        $dropdown_button_empresas->addPostAction( "REFORMA", new TAction(['NotaBaixadaList', 'onReforma']), 'datagrid_'.self::$formName, 'fas:filter #000000' );

        $head_left_actions->add($dropdown_button_enviar);
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);
        $head_left_actions->add($dropdown_button_email);
        $head_left_actions->add($dropdown_button_status);
        $head_left_actions->add($dropdown_button_empresas);
        $head_left_actions->add($button_sincronizar);

        $head_right_actions->add($dropdown_button_exportar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gerencia","XML de Notas"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onSincronizar($param = null) 
    {
        try 
        {
            if($param['key']){
                TTransaction::open(self::$database);
                $nota = NotaBaixada::find($param['key']);
                TTransaction::close();

                if($nota){
                    $erros = array();

                    $numero      = $nota->numero;
                    $coligada_id = $nota->coligada_id;

                    $resultado = SigissWebService::obtemXmlRps($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obtemXmlNf($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obterPdf($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    if (count($erros) > 0) {
                        throw new Exception("Erro ao consultar nota {$numero}: ".implode(', ',$erros));
                    } else {
                        TToast::show("success", "Nota {$numero} consultada com sucesso!", "topRight", "fas:check-circle");
                    }
                }
            }

            TApplication::loadPage(__CLASS__, 'onShow');

            //</autoCode>
        }
        catch (Exception $e) 
        {            
            TApplication::loadPage(__CLASS__, 'onShow');
            new TMessage('error', $e->getMessage());
        }
    }
    public function onBaixarXml($param = null) 
    {
        try 
        {
            if($param['xml']){
                TScript::create("window.location.href = 'downloadByFile.php?file={$param['xml']}';");
            }
            TApplication::loadPage(__CLASS__, 'onShow');

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public static function canXml($object)
    {
        try 
        {
            if(in_array($object->nota_status_id, [NotaStatus::AUTORIZADA, NotaStatus::CANCELADA]) && $object->nfs_xml)
            {
                if(file_exists($object->nfs_xml)){
                    return true;
                }
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onBaixarPdf($param = null) 
    {
        try 
        {
            if($param['pdf']){
                TScript::create("window.location.href = 'downloadByFile.php?file={$param['pdf']}';");
                TPage::openFile($param['pdf']);
            }
            TApplication::loadPage(__CLASS__, 'onShow');

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public static function canPdf($object)
    {
        try 
        {
            if(in_array($object->nota_status_id, [NotaStatus::AUTORIZADA, NotaStatus::CANCELADA]) && $object->nfs_pdf)
            {
                if(file_exists($object->nfs_pdf)){
                    return true;
                }
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onEnviar($param = null) 
    {
        try 
        {
            if($param['key']){
                TTransaction::open(self::$database);
                $nota = NotaBaixada::find($param['key']);
                TTransaction::close();

                if($nota){
                    $erros = array();
                    $numero      = $nota->numero;
                    $coligada_id = $nota->coligada_id;
                    if (!isset($param['copia'])) { 
                        new TQuestion(
                            "Enviar cópia para o prestador?", 
                            new TAction([__CLASS__, 'onEnviar'], ['key' => $param['key'], 'copia' => 'S']), 
                            new TAction([__CLASS__, 'onEnviar'], ['key' => $param['key'], 'copia' => 'N']), 
                            'Cópia', 'Sim', 'Não' 
                        );
                    }else{
                        if($numero && $coligada_id){
                            $resultado = SigissWebService::enviarEmail($numero, $coligada_id, $param['copia']);
                            if ($resultado['status'] === 'error') {
                                $erros[] = $resultado['mensagem'];
                            }
                        }
                        if (count($erros) > 0) {
                            throw new Exception("Erro ao enviar nota {$numero}: ".implode(', ',$erros));
                        } else {
                            TToast::show("success", "Nota {$numero} enviada com sucesso!", "topRight", "fas:check-circle");
                        }
                    }
                }
            }
            $this->onReload();

            //</autoCode>
        }
        catch (Exception $e) 
        {            
            TApplication::loadPage(__CLASS__, 'onShow');
            new TMessage('error', $e->getMessage());
        }
    }
    public static function canEnviar($object)
    {
        try 
        {
            if(in_array($object->nota_status_id, [NotaStatus::AUTORIZADA, NotaStatus::CANCELADA]))
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
    public static function canCancelar($object)
    {
        try 
        {
            if(in_array($object->nota_status_id, [NotaStatus::AUTORIZADA]))
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
    public function onAutorizar($param = null) 
    {
        try 
        {
            if($param['key']){
                TTransaction::open(self::$database);
                $nota = NotaBaixada::find($param['key']);
                TTransaction::close();

                if($nota){
                    $erros = array();
                    $baixar = true;

                    $numero      = $nota->numero;
                    $coligada_id = $nota->coligada_id;

                    $resultado = SigissWebService::inserirMovimentacaoXml($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $baixar = false;
                        $erros[] = $resultado['mensagem'];
                    }

                    if($baixar){
                        $resultado = SigissWebService::obtemXmlRps($numero, $coligada_id);
                        if ($resultado['status'] === 'error') {
                            $erros[] = $resultado['mensagem'];
                        }

                        $resultado = SigissWebService::obtemXmlNf($numero, $coligada_id);
                        if ($resultado['status'] === 'error') {
                            $erros[] = $resultado['mensagem'];
                        }

                        $resultado = SigissWebService::obterPdf($numero, $coligada_id);
                        if ($resultado['status'] === 'error') {
                            $erros[] = $resultado['mensagem'];
                        }
                    }

                    if (count($erros) > 0) {
                        throw new Exception("Erro ao processar nota {$numero}: ".implode(', ',$erros));
                    } else {
                        TToast::show("success", "Nota {$numero} processada com sucesso!", "topRight", "fas:check-circle");
                    }
                }
            }

            TApplication::loadPage(__CLASS__, 'onShow');

            //</autoCode>
        }
        catch (Exception $e) 
        {            
            TApplication::loadPage(__CLASS__, 'onShow');
            new TMessage('error', $e->getMessage());
        }
    }
    public static function canAutorizar($object)
    {
        try 
        {
            if(in_array($object->nota_status_id, [NotaStatus::ERRO]))
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

                // instantiates object
                $object = new NotaBaixada($key, FALSE); 
                unlink($object->totvs_xml);
                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                TToast::show('success', AdiantiCoreTranslator::translate('Record deleted'), 'topRight', 'far:check-circle');

            //</autoCode>
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
    public static function canExcluir($object)
    {
        try 
        {
            if(in_array($object->nota_status_id, [NotaStatus::ERRO]))
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
            $page->setProperty('page-name', 'NotaBaixadaListSearch');
            $page->setProperty('page_name', 'NotaBaixadaListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            $style = new TStyle('right-panel > .container-part[page-name=NotaBaixadaListSearch]');
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

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }
    public function onEnviados($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('enviado_email', '=', 1);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onNaoEnviado($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('enviado_email', '=', 0);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onAutorizada($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('nota_status_id', '=', NotaStatus::AUTORIZADA);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onCancelada($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('nota_status_id', '=', NotaStatus::CANCELADA);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onErro($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('nota_status_id', '=', NotaStatus::ERRO);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onVersatronic($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('coligada_id', '=', Coligada::VERSATRONIC);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onCnc($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('coligada_id', '=', Coligada::CNC);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function onReforma($param = null) 
    {
        try 
        {
            $filters = array();
            $filters[] = new TFilter('coligada_id', '=', Coligada::REFORMA);

            TSession::setValue(__CLASS__.'_filters', $filters);

            $this->onReload(['offset' => 0, 'first_page' => 1]);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }
    public function foreachSincronizar($param = null) 
    {
        try 
        {
            if(!isset($param['builder_datagrid_check']) || empty($param['builder_datagrid_check']) || !is_array($param['builder_datagrid_check']) || count($param['builder_datagrid_check'])==0){
                throw new Exception("Selecione pelo menos uma NFSe para sincronizar");
            }
            $erros  = array();
            $sucesso = array();
            foreach($param['builder_datagrid_check'] as $key){
                TTransaction::open(self::$database);
                $nota = NotaBaixada::find($key);
                TTransaction::close();

                if($nota){
                    $errosNota = array();

                    $numero      = $nota->numero;
                    $coligada_id = $nota->coligada_id;

                    $resultado = SigissWebService::obtemXmlRps($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $errosNota[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obtemXmlNf($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $errosNota[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obterPdf($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $errosNota[] = $resultado['mensagem'];
                    }

                    if (count($errosNota) > 0) {
                        $erros[] = "Erro ao sincronizar nota {$numero}: ".implode(', ',$errosNota);
                    } else {
                        $sucesso[] = $numero;
                    }
                }
            }
            if (count($erros) > 0) {
                throw new Exception(implode(', ',$erros));
            } else {
                TToast::show("success", "Notas sincronizadas com sucesso!", "topRight", "fas:check-circle");
            }
            TApplication::loadPage(__CLASS__, 'onShow');

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
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->coligada_id) AND ( (is_scalar($data->coligada_id) AND $data->coligada_id !== '') OR (is_array($data->coligada_id) AND (!empty($data->coligada_id)) )) )
        {

            $filters[] = new TFilter('coligada_id', '=', $data->coligada_id);// create the filter 
        }

        if (isset($data->numero) AND ( (is_scalar($data->numero) AND $data->numero !== '') OR (is_array($data->numero) AND (!empty($data->numero)) )) )
        {

            $filters[] = new TFilter('numero', 'like', "%{$data->numero}%");// create the filter 
        }

        if (isset($data->nota_status_id) AND ( (is_scalar($data->nota_status_id) AND $data->nota_status_id !== '') OR (is_array($data->nota_status_id) AND (!empty($data->nota_status_id)) )) )
        {

            $filters[] = new TFilter('nota_status_id', '=', $data->nota_status_id);// create the filter 
        }

        if (isset($data->date_de) AND ( (is_scalar($data->date_de) AND $data->date_de !== '') OR (is_array($data->date_de) AND (!empty($data->date_de)) )) )
        {

            $filters[] = new TFilter('data_emissao', '>=', $data->date_de);// create the filter 
        }

        if (isset($data->date_ate) AND ( (is_scalar($data->date_ate) AND $data->date_ate !== '') OR (is_array($data->date_ate) AND (!empty($data->date_ate)) )) )
        {

            $filters[] = new TFilter('data_emissao', '<=', $data->date_ate);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
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

            // creates a repository for NotaBaixada
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'coligada_id, numero';    
            }
            elseif($param['order'] != 'coligada_id, numero')
            {
                $param['order'] = "coligada_id, numero,{$param['order']}"; 
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

            if($param['order'] == "coligada_id, numero,{$param['order']}"){
                $param['order'] = "coligada_id, numero::numeric,{$param['order']}";
            } 
            if($param['order'] == 'coligada_id, numero'){
                $param['order'] = 'coligada_id, numero::numeric';
            }
            $criteria->setProperties($param); // order, offset

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
                    $check = new TCheckButton('builder_datagrid_check[]');
                    $check->setIndexValue($object->id);
                    $check->onclick = 'event.stopPropagation();';
                    $object->builder_datagrid_check = $check;

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

        $object = new NotaBaixada($id);

        $check = new TCheckButton('builder_datagrid_check[]');
        $check->setIndexValue($object->id);
        $check->onclick = 'event.stopPropagation();';
        $object->builder_datagrid_check = $check;

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

