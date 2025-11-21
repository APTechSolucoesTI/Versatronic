<?php

class PreferenciaSistemaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'PreferenciaSistema';
    private static $primaryKey = 'id';
    private static $formName = 'form_PreferenciaSistemaForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Preferencia do Sistema");


        $id = new THidden('id');
        $system_users_id = new THidden('system_users_id');
        $zoom = new TSpinner('zoom');
        $menu_fixado = new TCombo('menu_fixado');

        $zoom->addValidation("Zoom", new TRequiredValidator()); 
        $menu_fixado->addValidation("Menu fixado", new TRequiredValidator()); 

        $zoom->setRange(1, 2000, 1);
        $menu_fixado->addItems(["1"=>"Sim","0"=>"Não"]);
        $menu_fixado->enableSearch();
        $zoom->setValue('100');
        $menu_fixado->setValue('1');

        $id->setSize(200);
        $zoom->setSize('100%');
        $menu_fixado->setSize('100%');
        $system_users_id->setSize(200);

        $row1 = $this->form->addFields([$id],[$system_users_id]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Zoom:", '#ff0000', '14px', null, '100%'),$zoom],[new TLabel("Menu fixado:", '#ff0000', '14px', null, '100%'),$menu_fixado]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Preferências do Usuário","Preferencia do Usuário"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new PreferenciaSistema(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle'); 

                echo "
                    <script>
                        window.location.href = 'index.php?class=InteracaoList&method=onShow';
                    </script>";
            exit();

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
                TTransaction::open(self::$database); // open a transaction

                $pref = PreferenciaSistema::where('system_users_id','=',TSession::getValue('userid'))->first();
                if($pref){
                    $param['key'] = $pref->id;
                }else{
                    $pref = new PreferenciaSistema();
                    $pref->system_users_id = TSession::getValue('userid');
                    $pref->zoom = 100;
                    $pref->menu_fixado = 0;
                    $pref->store();

                    $param['key'] = $pref->id;
                }
                $key = $param['key'];  // get the parameter $key

                $object = new PreferenciaSistema($key); // instantiates the Active Record 

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
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

        TTransaction::open(self::$database);

        $pref = PreferenciaSistema::where('system_users_id','=',TSession::getValue('userid'))->first();
        if($pref){
            TApplication::loadPage(__CLASS__,'onEdit',['key'=>$pref->id]);
        }else{
            $pref = new PreferenciaSistema();
            $pref->system_users_id = TSession::getValue('userid');
            $pref->zoom = 100;
            $pref->menu_fixado = 0;
            $pref->store();

            TApplication::loadPage(__CLASS__,'onEdit',['key'=>$pref->id]);
        }
        TTransaction::close();
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

