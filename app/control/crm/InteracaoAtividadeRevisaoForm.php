<?php

class InteracaoAtividadeRevisaoForm extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoAtividadeRevisao';
    private static $primaryKey = 'id';
    private static $formName = 'form_InteracaoAtividadeRevisaoForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.60, null);
        parent::setTitle("Revisão de Atividade");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Revisão de Atividade");

        $criteria_interacao_atividade_estado_atividade_id = new TCriteria();

        $criteria_interacao_atividade_estado_atividade_id->add(new TFilter('id', '=', 3), TExpression::OR_OPERATOR);
        $criteria_interacao_atividade_estado_atividade_id   ->add(new TFilter('id', '=', 6), TExpression::OR_OPERATOR);
        $criteria_interacao_atividade_estado_atividade_id   ->add(new TFilter('id', '=', 4), TExpression::OR_OPERATOR);

        $id = new THidden('id');
        $system_users_id = new THidden('system_users_id');
        $interacao_atividade_id = new THidden('interacao_atividade_id');
        $interacao_atividade_estado_atividade_id = new TDBCombo('interacao_atividade_estado_atividade_id', 'minicrm', 'EstadoAtividade', 'id', '{nome}','nome asc' , $criteria_interacao_atividade_estado_atividade_id );
        $observacao = new THtmlEditor('observacao');


        $interacao_atividade_estado_atividade_id->enableSearch();
        $interacao_atividade_id->setValue($param["key"] ?? "");
        $system_users_id->setValue(TSession::getValue("userid"));

        $id->setSize(200);
        $system_users_id->setSize(200);
        $observacao->setSize('100%', 70);
        $interacao_atividade_id->setSize(200);
        $interacao_atividade_estado_atividade_id->setSize('100%');

        $interacao_atividade_id->setValue($param["key"] ?? "");
        $system_users_id->setValue(TSession::getValue("userid"));

        $interacao_atividade_estado_atividade_id->addValidation('Status', new TRequiredValidator);
        $observacao->addValidation('Observação', new TRequiredValidator);
        $row1 = $this->form->addFields([$id,$system_users_id,$interacao_atividade_id],[new TLabel("Status:", null, '14px', null),$interacao_atividade_estado_atividade_id],[new TLabel("Observação:", null, '14px', null),$observacao]);
        $row1->layout = [' col-sm-12',' col-sm-4',' col-sm-12'];

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

            $object = new InteracaoAtividadeRevisao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $data = $this->form->getData();

            if (empty($data->interacao_atividade_id))
            {
                throw new Exception('Atividade não informada');
            }

            if (empty($data->system_users_id))
            {
                throw new Exception('Usuário não informado');
            }

           if (!in_array((int) $data->interacao_atividade_estado_atividade_id, [3,4,6]))
            {
                throw new Exception('Status inválido para revisão');
            }

            $atividade = new InteracaoAtividade($data->interacao_atividade_id);

            if (empty($atividade->id))
            {
                throw new Exception('Atividade não encontrada');
            }
            $object->store(); // save the object 

            $atividade = new InteracaoAtividade($data->interacao_atividade_id);
            $atividade->estado_atividade_id = $data->interacao_atividade_estado_atividade_id;
            $atividade->store();

            $interacao = new Interacao($atividade->interacao_id);

            if (empty($interacao->id))
            {
                throw new Exception('Interação não encontrada');
            }

           if ((int) $data->interacao_atividade_estado_atividade_id === 3)
            {
                $interacao->etapa_interacao_id = 7;
            }
            elseif ((int) $data->interacao_atividade_estado_atividade_id === 4)
            {
                $interacao->etapa_interacao_id = 8;
            }
            elseif ((int) $data->interacao_atividade_estado_atividade_id === 6)
            {
                $interacao->etapa_interacao_id = 10;
            }

            $interacao->store();

            $agora = date('Y-m-d H:i:s');

            $historicoAtividade = new InteracaoHistoricoAtividade;
            $historicoAtividade->interacao_id = $atividade->interacao_id;
            $historicoAtividade->dt_atividade = $agora;
            $historicoAtividade->descricao = $atividade->descricao;
            $historicoAtividade->observacao = $data->observacao;
            $historicoAtividade->horario_inicial = $agora;
            $historicoAtividade->horario_final = $agora;
            $historicoAtividade->tipo_atividade_id = $atividade->tipo_atividade_id;
            $historicoAtividade->estado_atividade_id = $atividade->estado_atividade_id;
            $historicoAtividade->interacao_atividade_id = $atividade->id;
            $historicoAtividade->movimentacao_id = Movimentacao::ALTERADO;
            $historicoAtividade->store();
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

                TWindow::closeWindow(parent::getId()); 

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
        $data = new stdClass;

        $data->interacao_atividade_id = $param['key'] ?? '';
        $data->system_users_id = TSession::getValue('userid');

        $this->form->setData($data);

/*
                $object = new InteracaoAtividadeRevisao($key); // instantiates the Active Record 

                                $object->interacao_atividade_estado_atividade_id = $object->interacao_atividade->estado_atividade_id;

                $this->form->setData($object); // fill the form 

*/
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

