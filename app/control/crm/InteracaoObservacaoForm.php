<?php

class InteracaoObservacaoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoObservacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoObservacaoForm';

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
        $this->form->setFormTitle("Adicione uma observação a interação");


        $id = new THidden('id');
        $interacao_id = new THidden('interacao_id');
        $observacao = new THtmlEditor('observacao');


        $interacao_id->setValue(TSession::getValue('interacao_id'));
        $id->setSize(200);
        $interacao_id->setSize(200);
        $observacao->setSize('100%', 200);

        $row1 = $this->form->addFields([$id,$interacao_id],[]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Observacão:", null, '14px', null, '100%'),$observacao]);
        $row2->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['InteracaoObservacaoHeaderList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Cadastro de observação da Interação"]));
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

            $object = new InteracaoObservacao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if(!$data->id)
            {
                $object->dt_observacao = date('Y-m-d H:i:s');
            }

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $interacaoHistoricoObservacao = new InteracaoHistoricoObservacao;
            $interacaoHistoricoObservacao->interacao_id = $object->interacao_id;
            $interacaoHistoricoObservacao->dt_observacao = date('Y-m-d H:i:s');
            $interacaoHistoricoObservacao->descricao = $object->observacao;

            if(!$data->id)
            {
                $interacaoHistoricoObservacao->movimentacao_id = Movimentacao::CRIADO;
            }
            else 
            {
                $interacaoHistoricoObservacao->movimentacao_id = Movimentacao::ALTERADO;
            }
            $interacaoHistoricoObservacao->store();

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            $paramTimeline = [

                'key' => $object->interacao_id
            ];
            TApplication::loadPage(
                'ViewInteracaoTimelineTimeLine',
                'onShow',
                [
                    'target_container' => 'container_timeline'
                ]
            );

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('InteracaoObservacaoHeaderList', 'onShow', $loadPageParam); 

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

                $object = new InteracaoObservacao($key); // instantiates the Active Record 

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

