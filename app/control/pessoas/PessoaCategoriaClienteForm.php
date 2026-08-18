<?php

class PessoaCategoriaClienteForm extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'Pessoa';
    private static $primaryKey = 'id';
    private static $formName = 'form_PessoaCategoriaClienteForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(600, null);
        parent::setTitle("Alterar tipo de cliente");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Alterar tipo de cliente");

        $criteria_categoria_cliente_id = new TCriteria();

        $id = new THidden('id');
        $codigo = new THidden('codigo');
        $razao_social = new TEntry('razao_social');
        $categoria_cliente_id = new TDBCombo('categoria_cliente_id', 'minicrm', 'CategoriaCliente', 'id', '{nome}','nome asc' , $criteria_categoria_cliente_id );

        $razao_social->addValidation("Razão Social", new TRequiredValidator()); 
        $categoria_cliente_id->addValidation("Categoria", new TRequiredValidator()); 

        $razao_social->setEditable(false);
        $categoria_cliente_id->enableSearch();
        $id->setSize(200);
        $codigo->setSize(200);
        $razao_social->setSize('100%');
        $categoria_cliente_id->setSize('100%');

        $row1 = $this->form->addFields([$id,$codigo,new TLabel("Razão Social:", null, '14px', null, '100%'),$razao_social]);
        $row1->layout = ['col-sm-8'];

        $row2 = $this->form->addFields([new TLabel("Categoria:", '#FF0000', '14px', null, '100%'),$categoria_cliente_id]);
        $row2->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Pessoa(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            $codigoCategoria = (CategoriaCliente::find($data->categoria_cliente_id))->codigo;
            TTransaction::close(); // close the transaction

            TTransaction::open('corporerm');

            Fcfo::where('CODCFO', '=', $object->codigo)
                  ->set('CODTCF', $codigoCategoria)
                  ->update();

            TTransaction::close();
/*

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ClienteList', 'onShow', $loadPageParam); 

*/
    TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');    
                TWindow::closeWindow(parent::getId()); 

    TApplication::loadPage('ClienteList', 'onRefresh');
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

                $object = new Pessoa($key); // instantiates the Active Record 

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

        TToast::show('error', "Disponivel apenas em edição", 'topRight', 'far:check-circle');
        TWindow::closeWindow(parent::getId());
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

