<?php

class PrazoAtividadeForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'PrazoAtividade';
    private static $primaryKey = 'id';
    private static $formName = 'form_PrazoAtividadeForm';

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
        $this->form->setFormTitle("Junção de Atividade e Categoria");

        $criteria_regras_tipo_atividade_id = new TCriteria();
        $criteria_categoria_cliente_id = new TCriteria();

        $id = new TEntry('id');
        $regras_tipo_atividade_id = new TDBCombo('regras_tipo_atividade_id', 'minicrm', 'RegrasTipoAtividade', 'id', '{tipo} - {nome}','id asc' , $criteria_regras_tipo_atividade_id );
        $categoria_cliente_id = new TDBCombo('categoria_cliente_id', 'minicrm', 'CategoriaCliente', 'id', '{nome}','nome asc' , $criteria_categoria_cliente_id );
        $dias = new TEntry('dias');
        $ambos = new TCombo('ambos');

        $regras_tipo_atividade_id->addValidation("Regras tipo atividade id", new TRequiredValidator()); 
        $categoria_cliente_id->addValidation("Categoria cliente id", new TRequiredValidator()); 

        $id->setEditable(false);
        $ambos->addItems(["S"=>"Sim","N"=>"Não"]);
        $ambos->enableSearch();
        $categoria_cliente_id->enableSearch();
        $regras_tipo_atividade_id->enableSearch();

        $id->setSize(100);
        $dias->setSize('100%');
        $ambos->setSize('100%');
        $categoria_cliente_id->setSize('100%');
        $regras_tipo_atividade_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("Tipo de Atividade", '#ff0000', '14px', null)],[$regras_tipo_atividade_id]);
        $row3 = $this->form->addFields([new TLabel("Categoria:", '#ff0000', '14px', null)],[$categoria_cliente_id]);
        $row4 = $this->form->addFields([new TLabel("Dias:", null, '14px', null)],[$dias]);
        $row5 = $this->form->addFields([new TLabel("Atividade física também válida?", null, '12px', null, '100%'),new TLabel("Ex: Ao marcar Sim, uma atividade física concluída desta categoria de cliente também renovará o prazo do contato e poderá ativar o cliente.", '#727272', '10px', null),$ambos]);
        $row5->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['PrazoAtividadeHeaderList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new PrazoAtividade(); // create an empty object 

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
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('PrazoAtividadeHeaderList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();"); 
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

                $object = new PrazoAtividade($key); // instantiates the Active Record 

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

