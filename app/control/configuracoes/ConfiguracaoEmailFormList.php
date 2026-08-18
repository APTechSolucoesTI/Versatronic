<?php

class ConfiguracaoEmailFormList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private static $database = 'minicrm';
    private static $activeRecord = 'ConfiguracaoEmail';
    private static $primaryKey = 'id';
    private static $formName = 'form_ConfiguracaoEmailFormList';
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param)
    {
        parent::__construct();
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Configuraçôes de E-mail");
        $this->limit = 20;


        $id = new THidden('id');
        $system_users_id = new THidden('system_users_id');
        $mail_from = new TEntry('mail_from');
        $smtp_auth = new TCombo('smtp_auth');
        $smtp_host = new TEntry('smtp_host');
        $smtp_port = new TEntry('smtp_port');
        $smtp_user = new TEntry('smtp_user');
        $smtp_pass = new TPassword('smtp_pass');

        $mail_from->addValidation("E-mail de origem", new TRequiredValidator()); 
        $smtp_auth->addValidation("Autentica SMTP", new TRequiredValidator()); 
        $smtp_host->addValidation("Host SMTP", new TRequiredValidator()); 
        $smtp_port->addValidation("Porta SMTP", new TRequiredValidator()); 
        $smtp_user->addValidation("Usuário SMTP", new TRequiredValidator()); 
        $smtp_pass->addValidation("Senha SMTP", new TRequiredValidator()); 

        $system_users_id->setValue($param["key"] ?? "");
        $smtp_auth->addItems(["S"=>"Sim","N"=>"Não"]);
        $smtp_auth->enableSearch();
        $id->setSize(200);
        $mail_from->setSize('100%');
        $smtp_auth->setSize('100%');
        $smtp_host->setSize('100%');
        $smtp_port->setSize('100%');
        $smtp_user->setSize('100%');
        $smtp_pass->setSize('100%');
        $system_users_id->setSize(200);

        $row1 = $this->form->addFields([$id],[$system_users_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("E-mail de origem:", null, '14px', null, '100%'),$mail_from],[new TLabel("Autentica SMTP:", null, '14px', null, '100%'),$smtp_auth]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Host SMTP:", null, '14px', null, '100%'),$smtp_host],[new TLabel("Porta SMTP:", null, '14px', null, '100%'),$smtp_port]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Usuário SMTP:", null, '14px', null, '100%'),$smtp_user],[new TLabel("Senha SMTP:", null, '14px', null, '100%'),$smtp_pass]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        if(!empty($param["key"] ?? ""))
        {
            TSession::setValue(__CLASS__.'load_filter_system_users_id', $param["key"] ?? "");
        }
        $filterVar = TSession::getValue(__CLASS__.'load_filter_system_users_id');
        $this->filter_criteria->add(new TFilter('system_users_id', '=', $filterVar));

        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_mail_from = new TDataGridColumn('mail_from', "E-mail de origem", 'left');
        $column_smtp_auth = new TDataGridColumn('smtp_auth', "Autentica SMTP", 'left');
        $column_smtp_host = new TDataGridColumn('smtp_host', "Host SMTP", 'left');
        $column_smtp_port = new TDataGridColumn('smtp_port', "Porta SMTP", 'left');
        $column_smtp_user = new TDataGridColumn('smtp_user', "Usuário SMTP", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_mail_from);
        $this->datagrid->addColumn($column_smtp_auth);
        $this->datagrid->addColumn($column_smtp_host);
        $this->datagrid->addColumn($column_smtp_port);
        $this->datagrid->addColumn($column_smtp_user);

        $action_onEdit = new TDataGridAction(array('ConfiguracaoEmailFormList', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('ConfiguracaoEmailFormList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('fas:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $this->datagrid->disableDefaultClick();

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(["Configurações","Configuração de E-mail"]));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

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
                $object = new ConfiguracaoEmail($key, FALSE); 

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
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new ConfiguracaoEmail(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $chaveSecreta = 'lakjsdlkasjdalksjdlakjdlk';
            $senhaCriptografada = openssl_encrypt($object->smtp_pass, 'AES-128-CTR', $chaveSecreta, 0, '1234567891011121');
            $object->smtp_pass = $senhaCriptografada;            

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

            $this->onReload();

        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new ConfiguracaoEmail($key); // instantiates the Active Record 

                unset($object->smtp_pass);
                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
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

            // creates a repository for ConfiguracaoEmail
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = clone $this->filter_criteria;

            if(!empty($param["key"] ?? ""))
        {
            TSession::setValue(__CLASS__.'load_filter_system_users_id', $param["key"] ?? "");
        }
        $filterVar = TSession::getValue(__CLASS__.'load_filter_system_users_id');
            $criteria->add(new TFilter('system_users_id', '=', $filterVar));

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

    public function onClear( $param )
    {
        $this->form->clear(true);

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
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload')))) )
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

        $object = new ConfiguracaoEmail($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

