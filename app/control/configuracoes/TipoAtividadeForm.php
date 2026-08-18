<?php

class TipoAtividadeForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'TipoAtividade';
    private static $primaryKey = 'id';
    private static $formName = 'form_TipoAtividadeForm';

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
        $this->form->setFormTitle("Cadastro de tipo de atividade");

        $criteria_tipo_interacao = new TCriteria();
        $criteria_regras_tipo_atividade_id = new TCriteria();

        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cor = new TColor('cor');
        $icone = new TIcon('icone');
        $tipo_interacao = new TDBCheckGroup('tipo_interacao', 'minicrm', 'TipoInteracao', 'id', '{nome}','nome asc' , $criteria_tipo_interacao );
        $regras_tipo_atividade_id = new TDBCombo('regras_tipo_atividade_id', 'minicrm', 'RegrasTipoAtividade', 'id', '{nome}','id asc' , $criteria_regras_tipo_atividade_id );

        $regras_tipo_atividade_id->addValidation("Tipo de Atividade", new TRequiredValidator()); 

        $id->setEditable(false);
        $tipo_interacao->setLayout('horizontal');
        $tipo_interacao->setUseButton();
        $regras_tipo_atividade_id->enableSearch();
        $id->setSize(100);
        $cor->setSize('100%');
        $nome->setSize('100%');
        $icone->setSize('100%');
        $tipo_interacao->setSize(80);
        $regras_tipo_atividade_id->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id]);
        $row1->layout = ['col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$nome],[new TLabel("Cor:", null, '14px', null, '100%'),$cor],[new TLabel("Icone:", null, '14px', null, '100%'),$icone]);
        $row2->layout = ['col-sm-6',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([new TLabel("Tipo de interação:", null, '14px', null, '100%'),$tipo_interacao]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Tipo de Atividade:", '#F12C2C', '14px', null),$regras_tipo_atividade_id]);
        $row4->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['TipoAtividadeHeaderList', 'onShow']), 'fas:arrow-left #000000');
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

            $object = new TipoAtividade(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $repository = TipoAtividadeInteracao::where('tipo_atividade_id', '=', $object->id);
            $repository->delete(); 

            if ($data->tipo_interacao) 
            {
                foreach ($data->tipo_interacao as $tipo_interacao_value) 
                {
                    $tipo_atividade_interacao = new TipoAtividadeInteracao;

                    $tipo_atividade_interacao->tipo_interacao_id = $tipo_interacao_value;
                    $tipo_atividade_interacao->tipo_atividade_id = $object->id;
                    $tipo_atividade_interacao->store();
                }
            }

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
            TApplication::loadPage('TipoAtividadeHeaderList', 'onShow', $loadPageParam); 

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

                $object = new TipoAtividade($key); // instantiates the Active Record 

                $object->tipo_interacao = TipoAtividadeInteracao::where('tipo_atividade_id', '=', $object->id)->getIndexedArray('tipo_interacao_id', 'tipo_interacao_id');

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

