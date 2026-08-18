<?php

class InteracaoArquivoHeaderList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoArquivo';
    private static $primaryKey = 'id';
    private static $formName = 'formList_InteracaoArquivo';
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

        $this->limit = 0;


        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $filterVar = TSession::getValue('interacao_id');
        $this->filter_criteria->add(new TFilter('interacao_id', '=', $filterVar));

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_dt_arquivo_transformed = new TDataGridColumn('dt_arquivo', "Data", 'center');
        $column_interacao_atividade_transformed = new TDataGridColumn('interacao_atividade', "Atividade", 'center');
        $column_conteudo_arquivo = new TDataGridColumn('conteudo_arquivo', "Arquivo", 'center');

        $column_dt_arquivo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y H:i');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_interacao_atividade_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value){
                TTransaction::open('minicrm');
                $atividade = InteracaoAtividade::find($value);
                if (!empty($atividade)) {
                    $tipo_atividade = TipoAtividade::find($atividade->tipo_atividade_id);                
                }
                TTransaction::close();        
            }    

            if(empty($tipo_atividade->nome)){
                return 'Nenhuma';
            }
            else{
                return $tipo_atividade->nome ?? '';
            }
        });        

        $this->datagrid->addColumn($column_dt_arquivo_transformed);
        $this->datagrid->addColumn($column_interacao_atividade_transformed);
        $this->datagrid->addColumn($column_conteudo_arquivo);

        $action_onBaixar = new TDataGridAction(array('InteracaoArquivoHeaderList', 'onBaixar'));
        $action_onBaixar->setUseButton(false);
        $action_onBaixar->setButtonClass('btn btn-default btn-sm');
        $action_onBaixar->setLabel("");
        $action_onBaixar->setImage('fas:download #3F51B5');
        $action_onBaixar->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onBaixar);

        $action_onDelete = new TDataGridAction(array('InteracaoArquivoHeaderList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);
        $action_onDelete->setDisplayCondition('InteracaoArquivoHeaderList::canExcluir');

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $panel->getHeader()->style = ' display:none !important; ';
        $panel->getBody()->class .= ' table-responsive';

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

        $cadastrar = new TButton('button_cadastrar');
        $cadastrar->setAction(new TAction(['InteracaoArquivoForm', 'onShow']), "Cadastrar");
        $cadastrar->addStyleClass('btn-default');
        $cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($cadastrar);

        $head_left_actions->add($cadastrar);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Arquivos da Interação"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onBaixar($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);
            $object = InteracaoArquivo::find($param['key']);

            TPage::openFile($object->conteudo_arquivo);
            TTransaction::close();

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

                // instantiates object
                $object = new InteracaoArquivo($key, FALSE); 

                // deletes the object from the database

                if (!empty($object->interacao_atividade)) {
                    $interacaoAtividade = InteracaoAtividade::where('id', '=', $object->interacao_atividade)->first();

                    if (!empty($interacaoAtividade)) {

                        if ($interacaoAtividade->tipo_atividade_id == 1 || $interacaoAtividade->tipo_atividade_id == 5 || $interacaoAtividade->tipo_atividade_id == 9) {
                            TTransaction::close();
                            new TMessage('info', "Não é permitido deletar arquivos deste tipo de atividade!");
                            return;
                        }
                        else {

                            $caminhoArquivo = trim((string) $object->conteudo_arquivo);
                            if (!empty($caminhoArquivo) && is_file($caminhoArquivo)) {
                                @unlink($caminhoArquivo);
                            }                                                                          

                            $interacaoHistoricoArquivo = new InteracaoHistoricoArquivo;
                            $interacaoHistoricoArquivo->interacao_id = $object->interacao_id;
                            $interacaoHistoricoArquivo->dt_arquivo = date('Y-m-d H:i:s');
                            $interacaoHistoricoArquivo->descricao = $object->conteudo_arquivo;
                            $interacaoHistoricoArquivo->movimentacao_id = Movimentacao::EXCLUIDO;
                            $interacaoHistoricoArquivo->interacao_arquivo_id = null;
                            $interacaoHistoricoArquivo->store();      

                            $object->delete();         
                        }
                    }                   
                }

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                TToast::show('success', AdiantiCoreTranslator::translate('Record deleted'), 'topRight', 'far:check-circle');

                TScript::create("window.location.reload();");
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

        TTransaction::open(self::$database);

        if(!NegociacaoService::podeEditar(TSession::getValue('interacao_id')))
        {
            return false;
        }
        TTransaction::close();

       return true;

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

            // creates a repository for InteracaoArquivo
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'dt_arquivo';    
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

       TTransaction::open(self::$database);

        if(!NegociacaoService::podeEditar(TSession::getValue('interacao_id')))
        {
            TButton::disableField(self::$formName,'button_cadastrar');
        }
        TTransaction::close();

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

        $object = new InteracaoArquivo($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

    public static function downloadArquivo($param = [])
    {
        try 
        {
            TTransaction::open(self::$database);

            $interacaoArquivo = new InteracaoArquivo($param['key']);

            $caminho_arquivo = "tmp/{$interacaoArquivo->id}-{$interacaoArquivo->nome_arquivo}";

            if(!is_file($caminho_arquivo))
            {
                file_put_contents($caminho_arquivo, base64_decode($interacaoArquivo->conteudo_arquivo));    
            }

            TTransaction::close();

            TPage::openFile($caminho_arquivo);
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
        }
    }

}

