<?php

class InteracaoAtividadeGlobalCalendarForm extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoAtividade';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoAtividadeCalendarForm';
    private static $startDateField = 'horario_inicial';
    private static $endDateField = 'horario_final';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::setTitle("Atividades da Interacao");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Atividades da Interacao");

        $view = new THidden('view');

        $criteria_estado_atividade_id = new TCriteria();
        $criteria_interacao_id = new TCriteria();
        $criteria_tipo_atividade_id = new TCriteria();

        $filterVar = TipoInteracao::COMPLETA;
        $criteria_tipo_atividade_id->add(new TFilter('id', 'in', "(SELECT tipo_atividade_id FROM tipo_atividade_interacao WHERE tipo_interacao_id = '{$filterVar}')")); 

        $id = new TEntry('id');
        $interacao_vendedor_razao_social = new TEntry('interacao_vendedor_razao_social');
        $estado_atividade_id = new TDBCombo('estado_atividade_id', 'minicrm', 'EstadoAtividade', 'id', '{nome}','nome asc' , $criteria_estado_atividade_id );
        $interacao_id = new TDBCombo('interacao_id', 'minicrm', 'Interacao', 'id', '#{id} - {cliente->razao_social} - {etapa_interacao->nome}','id asc' , $criteria_interacao_id );
        $tipo_atividade_id = new TDBCombo('tipo_atividade_id', 'minicrm', 'TipoAtividade', 'id', '{icone_formatado} {nome}','nome asc' , $criteria_tipo_atividade_id );
        $descricao = new TEntry('descricao');
        $horario_inicial = new TDateTime('horario_inicial');
        $horario_final = new TDateTime('horario_final');
        $observacao = new TText('observacao');

        $estado_atividade_id->addValidation("Estado obrigatório", new TRequiredValidator()); 
        $tipo_atividade_id->addValidation("Tipo atividade", new TRequiredValidator()); 

        $estado_atividade_id->setValue(EstadoAtividade::ANDAMENTO);
        $horario_final->setMask('dd/mm/yyyy hh:ii');
        $horario_inicial->setMask('dd/mm/yyyy hh:ii');

        $horario_final->setDatabaseMask('yyyy-mm-dd hh:ii');
        $horario_inicial->setDatabaseMask('yyyy-mm-dd hh:ii');

        $id->setEditable(false);
        $interacao_id->setEditable(false);
        $interacao_vendedor_razao_social->setEditable(false);

        $interacao_id->enableSearch();
        $tipo_atividade_id->enableSearch();
        $estado_atividade_id->enableSearch();

        $id->setSize('100%');
        $descricao->setSize('100%');
        $horario_final->setSize(150);
        $interacao_id->setSize('100%');
        $horario_inicial->setSize(150);
        $observacao->setSize('100%', 100);
        $tipo_atividade_id->setSize('100%');
        $estado_atividade_id->setSize('100%');
        $interacao_vendedor_razao_social->setSize('100%');

        $voltar = false;
        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Representante: ", '#FF0000', '14px', null, '100%'),$interacao_vendedor_razao_social],[new TLabel("Estado:", '#FF0000', '14px', null, '100%'),$estado_atividade_id]);
        $row1->layout = [' col-sm-3',' col-sm-6',' col-sm-3'];

        $row2 = $this->form->addFields([new TLabel("Interação", '#FF0000', '14px', null, '100%'),$interacao_id],[new TLabel("Tipo atividade:", '#ff0000', '14px', null, '100%'),$tipo_atividade_id]);
        $row2->layout = ['col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([new TLabel("Descricao:", null, '14px', null, '100%'),$descricao]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Horario inicial:", null, '14px', null, '100%'),$horario_inicial],[new TLabel("Horario final:", null, '14px', null, '100%'),$horario_final]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addFields([new TLabel("Observacao:", null, '14px', null, '100%'),$observacao]);
        $row5->layout = [' col-sm-12'];

        $this->form->addFields([$view]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_ondelete = $this->form->addAction("Excluir", new TAction([$this, 'onDelete']), 'fas:trash-alt #dd5a43');
        $this->btn_ondelete = $btn_ondelete;

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new InteracaoAtividade(); // create an empty object 

            if(!NegociacaoService::podeEditar($param['interacao_id']))
            {
                throw new Exception('Não é possivel alterar uma interação finalizada!');
            }
            else 
            {

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if($data->id)
            {
                $newObject = InteracaoAtividade::find($data->id);
                if ($newObject->estado_atividade_id === (int)EstadoAtividade::CONCLUIDO) {
                    throw new Exception('Não é possível alterar uma atividade concluída!');
                }
            }

            $object->store(); // save the object 

            $messageAction = new TAction(['InteracaoAtividadeGlobalCalendarFormView', 'onReload']);
            $messageAction->setParameter('view', $data->view);
            $messageAction->setParameter('date', explode(' ', $data->horario_inicial)[0]);

            $interacaoHistoricoAtividade = new InteracaoHistoricoAtividade;
            $interacaoHistoricoAtividade->interacao_id = $object->interacao_id;
            $interacaoHistoricoAtividade->dt_atividade = date('Y-m-d H:i:s');
            $interacaoHistoricoAtividade->descricao = $object->descricao;
            $interacaoHistoricoAtividade->observacao = $object->observacao;
            $interacaoHistoricoAtividade->horario_inicial = $object->horario_inicial;
            $interacaoHistoricoAtividade->horario_final = $object->horario_final;
            $interacaoHistoricoAtividade->tipo_atividade_id = $object->tipo_atividade_id;
            $interacaoHistoricoAtividade->estado_atividade_id = $object->estado_atividade_id;

            if(!$data->id)
            {
                $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::CRIADO;
            }
            else 
            {
                $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::ALTERADO;
            }
            $interacaoHistoricoAtividade->store();
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

            $paramTimeline = [
                'target_container' => 'container_timeline',
                'interacao_id' => $object->interacao_id
            ];

            if ((bool) TSession::getValue('voltar') === true)
            {
                TScript::create(" window.location.href = 'index.php?class=InteracaoAtividadeHeaderList&method=onShow'; ");
                // TApplication::loadPage('InteracaoAtividadeHeaderList', 'onShow', $paramTimeline);
            }
            else {
                 TApplication::loadPage('InteracaoTimeline', 'onShow', $paramTimeline);
            }

                TWindow::closeWindow(parent::getId()); 

            }
        }
        catch (Exception $e) // in case of exception
        {

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function onDelete($param = null) 
    {
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                $key = $param[self::$primaryKey];

                // open a transaction with database
                TTransaction::open(self::$database);

                $class = self::$activeRecord;

                // instantiates object
                $object = new $class($key, FALSE);

                if(!NegociacaoService::podeExcluir($object->interacao_id))
                {
                    throw new Exception('Não é possível excluir');
                }

                if($object->estado_atividade_id === (int)EstadoAtividade::CONCLUIDO)
                {
                     throw new Exception('Não é possivel excluir uma atividade concluida!');
                }

                $interacaoHistoricoAtividade = new InteracaoHistoricoAtividade;
                $interacaoHistoricoAtividade->interacao_id = $object->interacao_id;
                $interacaoHistoricoAtividade->dt_atividade = date('Y-m-d H:i:s');
                $interacaoHistoricoAtividade->descricao = $object->descricao;
                $interacaoHistoricoAtividade->observacao = $object->observacao;
                $interacaoHistoricoAtividade->horario_inicial = $object->horario_inicial;
                $interacaoHistoricoAtividade->horario_final = $object->horario_final;
                $interacaoHistoricoAtividade->tipo_atividade_id = $object->tipo_atividade_id;
                $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::EXCLUIDO;
                $interacaoHistoricoAtividade->estado_atividade_id = $object->estado_atividade_id;

                $interacaoHistoricoAtividade->store();

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                $messageAction = new TAction(array(__CLASS__.'View', 'onReload'));
                $messageAction->setParameter('view', $param['view']);
                $messageAction->setParameter('date', explode(' ',$param[self::$startDateField])[0]);

                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $messageAction);

                if ((bool) TSession::getValue('voltar') === true)
                {
                    TScript::create(" window.location.href = 'index.php?class=InteracaoAtividadeHeaderList&method=onShow'; ");
                    // TApplication::loadPage('InteracaoAtividadeHeaderList', 'onShow', $paramTimeline);
                }
                else {
                    TScript::create(" window.location.reload;");
                }
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
            $action->setParameters((array) $this->form->getData());
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
         TWindow::closeWindow(parent::getId());
    }

    public function onEdit( $param )
    {
        try
        {
            TSession::setValue('voltar', false);
            if(isset($param['voltar']) )
            {
                 TSession::setValue('voltar', true);
            }

            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new InteracaoAtividade($key); // instantiates the Active Record 

                                $object->interacao_vendedor_razao_social = $object->interacao->vendedor->razao_social;
                $object->view = !empty($param['view']) ? $param['view'] : 'agendaWeek'; 

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

    public function onStartEdit($param)
    {

        $this->form->clear(true);

        $data = new stdClass;
        $data->view = $param['view'] ?? 'agendaWeek'; // calendar view
        $data->interacao = new stdClass();
        $data->interacao->vendedor = new stdClass();
        $data->interacao->vendedor->cor = '#3a87ad';

        if (!empty($param['date']))
        {
            if(strlen($param['date']) == '10')
                $param['date'].= ' 09:00';

            $data->horario_inicial = str_replace('T', ' ', $param['date']);

            $horario_final = new DateTime($data->horario_inicial);
            $horario_final->add(new DateInterval('PT1H'));
            $data->horario_final = $horario_final->format('Y-m-d H:i:s');

        }

        $this->form->setData( $data );
    }

    public static function onUpdateEvent($param)
    {
        try
        {
            if (isset($param['id']))
            {
                TTransaction::open(self::$database);

                $class = self::$activeRecord;
                $object = new $class($param['id']);

                $object->horario_inicial = str_replace('T', ' ', $param['start_time']);
                $object->horario_final   = str_replace('T', ' ', $param['end_time']);

                $object->store();

                // close the transaction
                TTransaction::close();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function getFormName()
    {
        return self::$formName;
    }

}

