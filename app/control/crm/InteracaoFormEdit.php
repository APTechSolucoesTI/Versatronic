<?php

class InteracaoFormEdit extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'Interacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoFormEdit';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.20, null);
        parent::setTitle("Finalizar Interação");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Finalizar Interação");


        $data_fechamento = new TDate('data_fechamento');
        $id = new THidden('id');


        $data_fechamento->setMask('dd/mm/yyyy');
        $data_fechamento->setValue(date('d/m/Y'));
        $data_fechamento->setDatabaseMask('yyyy-mm-dd');
        $id->setSize(200);
        $data_fechamento->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Data de fechamento:", null, '14px', null, '100%'),$data_fechamento,$id]);
        $row1->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
       try {
        // Validação da data de fechamento
        $data_fechamento = $param['data_fechamento'] ?? null;
        if (!$data_fechamento) {
            throw new Exception('Data de fechamento não fornecida.');
        }

        $data_fechamento_formatada = DateTime::createFromFormat('d/m/Y', $data_fechamento);
        if (!$data_fechamento_formatada) {
            throw new Exception('Formato de data de fechamento inválido. Esperado: dd/mm/yyyy.');
        }

        $data_fechamento_obj = new DateTime($data_fechamento_formatada->format('Y-m-d'));

        // Verifica se a data de fechamento não é maior que a data atual
        if ($data_fechamento_obj <= new DateTime()) {
            TTransaction::open(self::$database);

            $data_fechamento_formatted = $data_fechamento_obj->format('Y-m-d H:i:s');

            // Verifica se existem atividades pendentes após a data de fechamento
            $atividades = InteracaoAtividade::where('interacao_id', '=', $param['id'])
                ->where('horario_final', '>', $data_fechamento_formatted)->where('estado_atividade_id','=','1')
                ->count();

            if ($atividades == 0) {

            // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Interacao(); // create an empty object 
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->etapa_interacao_id = 6;
            $object->store(); // save the object 

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = $object->etapa_interacao_id;
            $interacaoHistoricoEtapa->interacao_id = $object->id;
            $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:s');
            $interacaoHistoricoEtapa->store();

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            /*

            new TMessage('info', "Registro salvo", $messageAction); 

            */

                new TMessage('info', 'Etapa alterada com sucesso!', new TAction(['InteracaoFormView', 'onShow'], ['key'=>$object->id]));

                TWindow::closeWindow(parent::getId()); 

                }
                else {
                    new TMessage('error', "Não é possivel finalizar uma interacao com atividades marcadas!", new TAction(['InteracaoFormView', 'onShow'], ['key'=>TSession::getValue('interacao_id')]));
                }
            } else {
                new TMessage('error', "Não é possivel selecionar uma data maior que a atual!", new TAction(['InteracaoFormView', 'onShow'], ['key'=>TSession::getValue('interacao_id')]));
            }
        }
        catch (Exception $e) // in case of exception
        {

         $errorMessage = 'Erro ao processar a requisição: ' . $e->getMessage() . 
                            ' | Arquivo: ' . $e->getFile() . 
                            ' | Linha: ' . $e->getLine();

            // Mostra a mensagem de erro ao usuário
            new TMessage('error', $errorMessage); 
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

                $object = new Interacao($key); // instantiates the Active Record 

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

