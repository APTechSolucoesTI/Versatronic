<?php

class NotaBaixadaForm extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'NotaBaixada';
    private static $primaryKey = 'id';
    private static $formName = 'form_NotaBaixadaForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.50, null);
        parent::setTitle("Buscar XML de Nota");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Buscar XML de Nota");

        $criteria_coligada_id = new TCriteria();

        $coligada_id = new TDBCombo('coligada_id', 'minicrm', 'Coligada', 'id', '{nome}','nome asc' , $criteria_coligada_id );
        $id = new THidden('id');
        $numero = new TEntry('numero');

        $coligada_id->addValidation("Coligada id", new TRequiredValidator()); 
        $numero->addValidation("Número", new TRequiredValidator()); 

        $coligada_id->enableSearch();
        $numero->setMaxLength(255);
        $id->setSize(200);
        $numero->setSize('100%');
        $coligada_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Coligada:", '#ff0000', '14px', null, '100%'),$coligada_id,$id],[new TLabel("Número:", '#ff0000', '14px', null, '100%'),$numero]);
        $row1->layout = [' col-sm-4',' col-sm-8'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
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

            $object = new NotaBaixada(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->numero = str_pad($object->numero, 6,'0', STR_PAD_LEFT);

            $coligada = (int) $object->coligada_id;
            $cnpj = ($object->get_coligada())->cnpj;
            $nome = ($object->get_coligada())->nome;
            $numero = $object->numero;

            if(NotaBaixada::where('numero','=',$numero)->where('coligada_id','=',$coligada)->count() > 0){
                throw new Exception("NFSe {$numero} da coligada {$nome} já gerada.");
            }

            TTransaction::close();
            $erros_service = BuscaXMLService::buscarNota($coligada, $numero, $object, $cnpj, $nome);

            if(count($erros_service) > 0){
                throw new Exception(implode("<br/>", $erros));
            }

            TTransaction::open(self::$database);/*
            $object->store(); // save the object 
*/

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            /*
            $data->id = $object->id; 
*/
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('NotaBaixadaList', 'onShow', $loadPageParam); 

                TWindow::closeWindow(parent::getId()); 

        }
        catch (Exception $e) // in case of exception
        {

            TToast::show("error", $e->getMessage(), "topRight", "fas:info-circle");
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

                $object = new NotaBaixada($key); // instantiates the Active Record 

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

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

