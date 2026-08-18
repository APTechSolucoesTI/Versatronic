<?php

class InteracaoAtividadeCalendarForm extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoAtividade';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoAtividadeCalendarForm';
    private static $startDateField = 'horario_inicial';
    private static $endDateField = 'horario_final';

    use Adianti\Base\AdiantiFileSaveTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.50, null);
        parent::setTitle("Atividades da Interação");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Atividades da Interação");

        $view = new THidden('view');

        $criteria_tipo_atividade_id = new TCriteria();
        $criteria_estado_atividade_id = new TCriteria();

        $criteria_estado_atividade_id->add(new TFilter('id', '<>', 3));
        $criteria_estado_atividade_id->add(new TFilter('id', '<>', 4));
        $criteria_estado_atividade_id->add(new TFilter('id', '<>', 5));
        $criteria_estado_atividade_id->add(new TFilter('id', '<>', 6));

        $id = new TEntry('id');
        $interacao_id = new THidden('interacao_id');
        $tipo_atividade_id = new TDBCombo('tipo_atividade_id', 'minicrm', 'TipoAtividade', 'id', '{icone_formatado} {nome}','nome asc' , $criteria_tipo_atividade_id );
        $estado_atividade_id = new TDBCombo('estado_atividade_id', 'minicrm', 'EstadoAtividade', 'id', '{nome}','nome asc' , $criteria_estado_atividade_id );
        $horario_inicial = new TDateTime('horario_inicial');
        $horario_final = new TDateTime('horario_final');
        $conteudo_arquivo = new TMultiFile('conteudo_arquivo');
        $cc = new TLabel("Cc (Com Cópia):", null, '14px', null, '100%');
        $copia = new TEntry('copia');
        $dest = new TLabel("Destinatário:", null, '14px', null, '100%');
        $destinatario = new TEntry('destinatario');
        $ass = new TLabel("Assunto:", null, '14px', null, '100%');
        $assunto = new TEntry('assunto');
        $observacao = new THtmlEditor('observacao');

        $estado_atividade_id->setChangeAction(new TAction([$this,'onGetCoord']));

        $horario_inicial->setExitAction(new TAction([$this,'onSelectDataInicial']));

        $tipo_atividade_id->addValidation("Tipo atividade", new TRequiredValidator()); 
        $estado_atividade_id->addValidation("Estado", new TRequiredValidator()); 
        $observacao->addValidation("Observação", new TRequiredValidator()); 

        $id->setEditable(false);
        $conteudo_arquivo->enableFileHandling();
        $conteudo_arquivo->setAllowedExtensions(["csv","pdf","jpg","png","jpeg","gif","mp4","mp3","xlsx"]);
        $tipo_atividade_id->enableSearch();
        $estado_atividade_id->enableSearch();

        $horario_final->setMask('dd/mm/yyyy hh:ii');
        $horario_inicial->setMask('dd/mm/yyyy hh:ii');

        $horario_final->setDatabaseMask('yyyy-mm-dd hh:ii');
        $horario_inicial->setDatabaseMask('yyyy-mm-dd hh:ii');

        $horario_final->setValue(DATE("d-m-Y H:i:s"));
        $horario_inicial->setValue(DATE("d-m-Y H:i:s"));
        $estado_atividade_id->setValue(EstadoAtividade::ANDAMENTO);
        $interacao_id->setValue(TSession::getValue('interacao_id'));

        $id->setSize(100);
        $copia->setSize('100%');
        $assunto->setSize('100%');
        $interacao_id->setSize(200);
        $horario_final->setSize(270);
        $horario_inicial->setSize(270);
        $destinatario->setSize('100%');
        $observacao->setSize('100%', 100);
        $conteudo_arquivo->setSize('100%');
        $tipo_atividade_id->setSize('100%');
        $estado_atividade_id->setSize('100%');

        $geo_latitude = new THidden('geo_latitude');
        $geo_longitude = new THidden('geo_longitude');
        $geo_endereco = new THidden('geo_endereco');

        $geo_latitude->setValue($param['geo_latitude'] ?? null);
        $geo_longitude->setValue($param['geo_longitude'] ?? null);
        $geo_endereco->setValue($param['geo_endereco'] ?? null);

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id,$interacao_id],[new TLabel("Tipo de atividade:", '#ff0000', '14px', null, '100%'),$tipo_atividade_id],[new TLabel("Estado:", '#FF0000', '14px', null, '100%'),$estado_atividade_id]);
        $row1->layout = [' col-sm-4','col-sm-4','col-sm-4'];

        $row2 = $this->form->addFields([new TLabel("Horário inicial:", null, '14px', null, '100%'),$horario_inicial],[new TLabel("Horário final:", null, '14px', null, '100%'),$horario_final],[new TLabel("Anexar arquivo:", '#000000', '14px', null, '100%'),$conteudo_arquivo]);
        $row2->layout = [' col-sm-3',' col-sm-3',' col-sm-6'];

        $row3 = $this->form->addFields([$cc,$copia]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([$dest,$destinatario]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([$ass,$assunto]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([new TLabel("Observação:", '#FF0000', '14px', null, '100%'),$observacao]);
        $row6->layout = [' col-sm-12'];

        $this->form->addFields([$geo_latitude], [$geo_longitude], [$geo_endereco]);

        $row3->id = 'row_cc';
        $row4->id = 'row_destinatario';
        $row5->id = 'row_assunto';

        TScript::create("$(\"label:contains('Cc (Com Cópia):')\").hide();");
        TScript::create("$(\"[name='copia']\").hide()");

        TScript::create("$(\"label:contains('Destinatário:')\").hide();");
        TScript::create("$(\"[name='destinatario']\").hide()");

        TScript::create("$(\"label:contains('Assunto:')\").hide();");
        TScript::create("$(\"[name='assunto']\").hide()");

        TScript::create("
            $('#row_cc').closest('.tformrow').hide();
            $('#row_destinatario').closest('.tformrow').hide();
            $('#row_assunto').closest('.tformrow').hide();
        ");

        $this->form->addFields([$view]);

        // create the form actions
        $savefalso = $this->form->addAction("Salvar", new TAction([$this, 'onSaveFalso']), 'fas:save #FFFFFF');
        $this->savefalso = $savefalso;
        $savefalso->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_ondelete = $this->form->addAction("Excluir", new TAction([$this, 'onDelete']), 'fas:trash-alt #dd5a43');
        $this->btn_ondelete = $btn_ondelete;

        $savereal = $this->form->addAction("SalvarReal", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->savereal = $savereal;
        $savereal->addStyleClass('btn-primary'); 

        $savereal->id = 'btn_savereal';
        $savereal->style = 'display:none';
        TScript::create("$('#btn_savereal').hide();");

        parent::add($this->form);

    }

    public static function onSelectDataInicial($param = null) 
    {
        try 
        {
            $dataHora = $param['_field_value'];

            $dateTime = DateTime::createFromFormat('d/m/Y H:i', $dataHora);
            $dateTime->modify('+1 hour');
            $dataHoraMaisUmaHora = $dateTime->format('d/m/Y H:i');

            $object = new stdClass();

            $object->horario_final =$dataHoraMaisUmaHora;

            TForm::sendData(self::$formName, $object);

        }
        catch (Exception $e) 
        {
             $error = " Mensagem: " . $errorMessage = $e->getMessage();

            new TMessage('error', $error); // shows the exception error message
        }
    }

    public static function onGetCoord($param = null) 
    {
        try 
        {
            if ($param['estado_atividade_id'] == 2) {
                echo "
                    <script>
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {

                                const latitude = position.coords.latitude;
                                const longitude = position.coords.longitude;

                                fetch('./Geolocalizacao.php', {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        acao: 'salvar_localizacao',
                                        latitude: latitude,
                                        longitude: longitude
                                    }),
                                })
                                .then(response => response.text())
                                .then(text => {
                                    console.log('RESPOSTA BRUTA:', text);

                                    try {
                                        const data = JSON.parse(text);

                                        if (data.status !== 'ok') {
                                            alert('Não foi possível salvar a localização: ' + data.message);
                                            return;
                                        }   

                                        const latInput = document.querySelector(\"[name='geo_latitude']\");
                                        const longInput = document.querySelector(\"[name='geo_longitude']\");

                                        if (!latInput || !longInput) {
                                            alert('Erro ao preparar dados de localização.');
                                            return;
                                        }

                                        latInput.value = latitude;
                                        longInput.value = longitude;
                                    }
                                    catch (e) {
                                        alert('Resposta inválida do servidor: ' + text);
                                    }
                                })
                                .catch((error) => {
                                    console.log('ERRO FETCH:', error);
                                    alert('Não foi possível capturar localização. Tente novamente mais tarde!');
                                });

                            },
                            function(error) {
                                console.error('Erro ao obter localização:', error.message);
                                alert('Não foi possível capturar localização. Tente novamente mais tarde!');
                            },
                            {
                                timeout: 8000
                            });

                        } else { 
                            console.log('Geolocation não suportada.');
                            alert('Geolocalização não suportada.');
                        }
                    </script>
                ";
                return;
            }
            else {
                return;
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSaveFalso($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);

            $messageAction = null;

            $this->form->validate();
            $data = $this->form->getData();
            $estadoAnterior = null;

            $lat = $param['geo_latitude'] ?? null;
            $long = $param['geo_longitude'] ?? null;

            if ($data->id)
            {
                $newObject = InteracaoAtividade::find($data->id);

                if (!$newObject)
                {
                    throw new Exception('Atividade não encontrada!');
                }

                $estadoAnterior = (int) $newObject->estado_atividade_id;

                if ($estadoAnterior === (int) EstadoAtividade::CONCLUIDO) {
                    throw new Exception('Não é possível alterar uma atividade concluída!');
                }
            }

            $object = new InteracaoAtividade();
            $findInt = Interacao::find($param['interacao_id']);
            $etapaInteracaoAnterior = (int) $findInt->etapa_interacao_id;

            if ($findInt->etapa_interacao_id == 6)
            {
                throw new Exception('Não é possivel alterar uma interação finalizada!');
            }
            else
            {
                $object->fromArray((array) $data);

                if (($object->tipo_atividade_id == 9 || $object->tipo_atividade_id == 5) && empty($object->conteudo_arquivo)) {
                        throw new Exception('O campo Anexar arquivo é obrigatório!');
                }

                if ($data->id && (int) $estadoAnterior === 4)
                {
                    $object->estado_atividade_id = 5;
                    $data->estado_atividade_id   = 5;
                }
                TTransaction::close();
                if ((int) $object->estado_atividade_id === (int) EstadoAtividade::CONCLUIDO)
                {
                    if (empty($object->horario_final))
                    {
                        throw new Exception('O campo Horário final é obrigatório para concluir a atividade!');
                    }

                    $dataFinal = new DateTime($object->horario_final);
                    $hoje = new DateTime(date('Y-m-d'));

                    if ($dataFinal->format('Y-m-d') > $hoje->format('Y-m-d'))
                    {
                        throw new Exception('Não é permitido concluir atividade com data futura no Horário final!');
                    }

                    self::onGetAdressFromCoord($lat, $long);
                }    
                else {
                    $this->onSave($param);
                }                                                     
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
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
                    TTransaction::close();
                    new TMessage('info', 'Não é possível excluir');
                    return;
                }

                if($object->estado_atividade_id === (int)EstadoAtividade::CONCLUIDO)
                {
                    TTransaction::close();
                    new TMessage('info', 'Não é possivel excluir uma atividade concluida!');
                    return;
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

                $arq = InteracaoArquivo::where('interacao_atividade', '=', $object->id)->load();
                if (!empty($arq)) {
                    foreach ($arq as $arquivo){                        
                        $interacaoHistoricoArquivo = new InteracaoHistoricoArquivo;
                        $interacaoHistoricoArquivo->interacao_id = $arquivo->interacao_id;
                        $interacaoHistoricoArquivo->dt_arquivo = date('Y-m-d H:i:s');
                        $interacaoHistoricoArquivo->descricao = $arquivo-conteudo_arquivo;
                        $interacaoHistoricoArquivo->movimentacao_id = Movimentacao::EXCLUIDO;
                        $interacaoHistoricoArquivo->interacao_arquivo_id = null;
                        $interacaoHistoricoArquivo->store();
                        $arquivo->delete();
                    }
                }   

                // $loc = InteracaoLocalizacao::where('interacao_atividade', '=', $object->id)->load();
                // if (!empty($loc)) {
                //     foreach ($loc as $localizacao)  {
                //         $localizacao->delete();
                //     }
                // }                                   
                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                $messageAction = new TAction(array(__CLASS__.'View', 'onReload'));
                $messageAction->setParameter('view', $param['view']);
                $messageAction->setParameter('date', explode(' ',$param[self::$startDateField])[0]);

                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'), $messageAction);
               TScript::create("window.location.reload();");

            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                $errorMessage = $e->getMessage();
                $error = "Mensagem: " . $errorMessage;

            new TMessage('error', $error); // shows the exception error message
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

    public function onSave($param = null) 
    {

        try
        {   
            TTransaction::open(self::$database);

            $messageAction = null;

            $this->form->validate();
            $data = $this->form->getData();
            $estadoAnterior = null;

            if ($data->id)
            {
                $newObject = InteracaoAtividade::find($data->id);

                if (!$newObject)
                {
                    throw new Exception('Atividade não encontrada!');
                }

                $estadoAnterior = (int) $newObject->estado_atividade_id;

                if ($estadoAnterior === (int) EstadoAtividade::CONCLUIDO) {
                    throw new Exception('Não é possível alterar uma atividade concluída!');
                }
            }

            $object = new InteracaoAtividade();
            $findInt = Interacao::find($param['interacao_id']);
            $etapaInteracaoAnterior = (int) $findInt->etapa_interacao_id;

            if ($findInt->etapa_interacao_id == 6)
            {
                throw new Exception('Não é possivel alterar uma interação finalizada!');
            }
            else
            {
                $object->fromArray((array) $data);

                if (($object->tipo_atividade_id == 9 || $object->tipo_atividade_id == 5) && empty($object->conteudo_arquivo)) {
                        throw new Exception('O campo Anexar arquivo é obrigatório!');
                }

                if ($data->id && (int) $estadoAnterior === 4)
                {
                    $object->estado_atividade_id = 5;
                    $data->estado_atividade_id   = 5;
                }

                if ((int) $object->estado_atividade_id === (int) EstadoAtividade::CONCLUIDO)
                {
                    if (empty($object->horario_final))
                    {
                        throw new Exception('O campo Horário final é obrigatório para concluir a atividade!');
                    }

                    $dataFinal = new DateTime($object->horario_final);
                    $hoje = new DateTime(date('Y-m-d'));

                    if ($dataFinal->format('Y-m-d') > $hoje->format('Y-m-d'))
                    {
                        throw new Exception('Não é permitido concluir atividade com data futura no Horário final!');
                    }
                }

                $lat = $data->geo_latitude ?? null;
                $long = $data->geo_longitude ?? null;
                $end = $data->geo_endereco ?? null;

                if ((int) $object->estado_atividade_id === (int) EstadoAtividade::CONCLUIDO) {
                    if (!$end || !$lat || !$long) {
                        throw new Exception('Não foi possível capturar localização!');
                    }
                }

                $object->store();

                $att_id = $object->id;

                if ($data->id && $etapaInteracaoAnterior === 8)
                {
                    $interacaoRetorno = Interacao::find($object->interacao_id);

                    if (!$interacaoRetorno)
                    {
                        throw new Exception('Interação não encontrada para retorno de etapa!');
                    }

                    $interacaoRetorno->etapa_interacao_id = 9;
                    $interacaoRetorno->store();
                }
                $historico_atividade_id = null;
                $interacao_arquivo_ids = [];
                $historico_arquivo_ids = [];
                $interacaoArquivo = null;

                $conteudo_arquivo_dir = 'anexos';

                $dtBase = date('Y-m-d H:i:s');

                $interacaoHistoricoAtividade = new InteracaoHistoricoAtividade;
                $interacaoHistoricoAtividade->interacao_id = $object->interacao_id;
                $interacaoHistoricoAtividade->dt_atividade = $dtBase;
                $interacaoHistoricoAtividade->descricao = $object->descricao;
                $interacaoHistoricoAtividade->observacao = $object->observacao;
                $interacaoHistoricoAtividade->horario_inicial = $object->horario_inicial;
                $interacaoHistoricoAtividade->horario_final = $object->horario_final;
                $interacaoHistoricoAtividade->tipo_atividade_id = $object->tipo_atividade_id;
                $interacaoHistoricoAtividade->estado_atividade_id = $object->estado_atividade_id;
                $interacaoHistoricoAtividade->interacao_atividade_id = $att_id;

                if (!$data->id)
                {
                    $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::CRIADO;
                }
                else
                {
                    $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::ALTERADO;                
                }

                $interacaoHistoricoAtividade->store();
                $historico_atividade_id = $interacaoHistoricoAtividade->id;

                if (!empty($object->conteudo_arquivo) && !$data->id)
                {
                    $retornoArquivos = self::salvarArquivosDaAtividade(
                        $object->interacao_id,
                        $object->id,
                        $object->conteudo_arquivo,
                        $dtBase
                    );

                    if (!empty($retornoArquivos['interacao_arquivo_ids']) && is_array($retornoArquivos['interacao_arquivo_ids'])) {
                        $interacao_arquivo_ids = $retornoArquivos['interacao_arquivo_ids'];
                    } elseif (!empty($retornoArquivos['interacao_arquivo_id'])) {
                        $interacao_arquivo_ids = [$retornoArquivos['interacao_arquivo_id']];
                    }

                    if (!empty($retornoArquivos['historico_arquivo_ids']) && is_array($retornoArquivos['historico_arquivo_ids'])) {
                        $historico_arquivo_ids = $retornoArquivos['historico_arquivo_ids'];
                    } elseif (!empty($retornoArquivos['historico_arquivo_id'])) {
                        $historico_arquivo_ids = [$retornoArquivos['historico_arquivo_id']];
                    }
                }

                $messageAction = new TAction(['InteracaoAtividadeCalendarFormView', 'onReload']);
                $messageAction->setParameter('view', $data->view);
                $messageAction->setParameter('date', explode(' ', $data->horario_inicial)[0]);

                $data->id = $object->id;
                $this->form->setData($data);                

                if ((int) $object->estado_atividade_id == 2) {                
                    $localizacao = new InteracaoLocalizacao;
                    $localizacao->interacao_id = $object->interacao_id;
                    $localizacao->interacao_atividade = $att_id;
                    $localizacao->descricao = $end;
                    $localizacao->latitude = $lat;
                    $localizacao->longitude = $long;
                    $localizacao->dt_localizacao = date('Y-m-d H:i:s', strtotime($dtBase . ' +2 seconds'));
                    $localizacao->store();
                }

                TTransaction::close();

                TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
                TWindow::closeWindow(parent::getId());
                TScript::create("window.location.reload();");
                /*
            $object = new InteracaoAtividade(); // create an empty object 

            $conteudo_arquivo_dir = 'anexos';  

            $object->store(); // save the object 

            $this->saveFilesByComma($object, $data, 'conteudo_arquivo', $conteudo_arquivo_dir);
            $messageAction = new TAction(['InteracaoAtividadeCalendarFormView', 'onReload']);
            $messageAction->setParameter('view', $data->view);
            $messageAction->setParameter('date', explode(' ', $data->horario_inicial)[0]);

            $data->id = $object->id; 

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle'); 

                TWindow::closeWindow(parent::getId()); 

             TApplication::loadPage('InteracaoAtividadeCalendarForm', 'onReload', $param['key']);*/
            }
        }
        catch (Exception $e) // in case of exception
        {

            $error = " Mensagem: " . $errorMessage = $e->getMessage();

            new TMessage('error', $error); // shows the exception error message
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

                $object = new InteracaoAtividade($key); // instantiates the Active Record 

                if ($object->tipo_atividade_id == 5 && !empty($object->destinatario)) {
                    TScript::create("
                        $('#row_cc').closest('.tformrow').show();
                        $('#row_destinatario').closest('.tformrow').show();
                        $('#row_assunto').closest('.tformrow').show();
                    ");

                    TScript::create("$(\"label:contains('Cc (Com Cópia):')\").show();");
                    TScript::create("$(\"[name='copia']\").show()");

                    TScript::create("$(\"label:contains('Destinatário:')\").show();");
                    TScript::create("$(\"[name='destinatario']\").show()");

                    TScript::create("$(\"label:contains('Assunto:')\").show();");
                    TScript::create("$(\"[name='assunto']\").show()");

                    TScript::create("$('label:contains(\"Observação:\")').html('Corpo do e-mail:')");
                }
/*
                                $object->conteudo_arquivo = explode(',', $object->conteudo_arquivo);            $object->view = !empty($param['view']) ? $param['view'] : 'agendaWeek'; 

*/
            $arquivos = InteracaoArquivo::where('interacao_atividade', '=', $object->id)->load();

            $listaArquivos = [];

            if ($arquivos)
            {
                foreach ($arquivos as $arquivo)
                {
                    if (!empty($arquivo->conteudo_arquivo))
                    {
                        $listaArquivos[] = $arquivo->conteudo_arquivo;
                    }
                }
            }

            $object->conteudo_arquivo = $listaArquivos;
            $object->view = !empty($param['view']) ? $param['view'] : 'agendaWeek';

                $this->form->setData($object); // fill the form 

            if ($object->estado_atividade_id == 2 || $object->estado_atividade_id == 3) {
                foreach ($this->form->getFields() as $field){
                    $field->setEditable(FALSE);
                }
            }

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            $error = " Mensagem: " . $errorMessage = $e->getMessage();

            new TMessage('error', $error); // shows the exception error message
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
        $data->tipo_atividade = new stdClass();
        $data->tipo_atividade->cor = '#3a87ad';

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

  private static function extrairListaAnexos($valor)
    {
        if (empty($valor))
        {
            return [];
        }

        if (is_array($valor))
        {
            return $valor;
        }

        if (is_string($valor))
        {
            $valorDecodificado = urldecode($valor);
            $json = json_decode($valorDecodificado);

            if (json_last_error() === JSON_ERROR_NONE)
            {
                if (is_array($json))
                {
                    return $json;
                }

                if (is_object($json))
                {
                    return [$json];
                }
            }

            return [$valor];
        }

        return [$valor];
    }

    private static function extrairCaminhoArquivo($item)
    {
        if (is_string($item))
        {
            $itemDecodificado = urldecode($item);
            $json = json_decode($itemDecodificado);

            if (json_last_error() === JSON_ERROR_NONE && is_object($json))
            {
                return trim($json->newFile ?? $json->fileName ?? $json->name ?? '');
            }

            return trim($item);
        }

        if (is_object($item))
        {
            return trim($item->newFile ?? $item->fileName ?? $item->name ?? '');
        }

        if (is_array($item))
        {
            return trim($item['newFile'] ?? $item['fileName'] ?? $item['name'] ?? '');
        }

        return trim((string) $item);
    }

    private static function salvarArquivosDaAtividade($interacaoId, $atividadeId, $conteudoArquivo, $dtBase)
    {
        $interacaoArquivoIds = [];
        $historicoArquivoIds = [];

        if (empty($conteudoArquivo))
        {
            return [
                'interacao_arquivo_ids' => [],
                'historico_arquivo_ids' => [],
            ];
        }

        $anexos = self::normalizarAnexosParaAtividade($conteudoArquivo, $interacaoId, $atividadeId);

        foreach ($anexos as $anexo)
        {
            $caminhoArquivo = $anexo[0];
            $nomeOriginal   = $anexo[1];

            $arquivoExistente = InteracaoArquivo::where('interacao_atividade', '=', $atividadeId)
                ->where('nome_arquivo', '=', $nomeOriginal)
                ->first();

            if ($arquivoExistente)
            {
                if (is_file($caminhoArquivo))
                {
                    @unlink($caminhoArquivo);
                }

                $interacaoArquivoIds[] = $arquivoExistente->id;
                continue;
            }

            $interacaoArquivo = new InteracaoArquivo();
            $interacaoArquivo->interacao_id = $interacaoId;
            $interacaoArquivo->nome_arquivo = $nomeOriginal;
            $interacaoArquivo->conteudo_arquivo = $caminhoArquivo;
            $interacaoArquivo->dt_arquivo = $dtBase;
            $interacaoArquivo->interacao_atividade = $atividadeId;
            $interacaoArquivo->store();

            $interacaoArquivoIds[] = $interacaoArquivo->id;

            $interacaoHistoricoArquivo = new InteracaoHistoricoArquivo();
            $interacaoHistoricoArquivo->interacao_id = $interacaoId;
            $interacaoHistoricoArquivo->dt_arquivo = date('Y-m-d H:i:s', strtotime($dtBase . ' +1 second'));
            $interacaoHistoricoArquivo->descricao = $caminhoArquivo;
            $interacaoHistoricoArquivo->movimentacao_id = Movimentacao::CRIADO;
            $interacaoHistoricoArquivo->interacao_arquivo_id = $interacaoArquivo->id;
            $interacaoHistoricoArquivo->store();

            $historicoArquivoIds[] = $interacaoHistoricoArquivo->id;
        }

        return [
            'interacao_arquivo_ids' => $interacaoArquivoIds,
            'historico_arquivo_ids' => $historicoArquivoIds
        ];
    }

    private static function normalizarAnexosParaAtividade($valor, $interacaoId, $atividadeId)
    {
        if (empty($valor))
        {
            return [];
        }

        if (empty($interacaoId))
        {
            throw new Exception('Interação não informada para salvar os anexos.');
        }

        if (empty($atividadeId))
        {
            throw new Exception('Atividade não informada para salvar os anexos.');
        }

        $lista = self::extrairListaAnexos($valor);

        $interacaoId = (int) $interacaoId;
        $atividadeId = (int) $atividadeId;

        $destinoFinal = "anexos/interacao_{$interacaoId}/atividade_{$atividadeId}";

        if (!is_dir($destinoFinal))
        {
            if (!mkdir($destinoFinal, 0777, true))
            {
                throw new Exception('Não foi possível criar a pasta da atividade: ' . $destinoFinal);
            }
        }

        if (!is_writable($destinoFinal))
        {
            throw new Exception('A pasta da atividade não tem permissão de escrita: ' . $destinoFinal);
        }

        $anexos = [];

        foreach ($lista as $item)
        {
            $arquivo = self::extrairCaminhoArquivo($item);

            if ($arquivo === '')
            {
                continue;
            }

            $candidatos = [
                $arquivo,
                ltrim($arquivo, '/'),
                getcwd() . '/' . ltrim($arquivo, '/'),
            ];

            $origemEncontrada = null;

            foreach ($candidatos as $caminho)
            {
                if (is_file($caminho))
                {
                    $origemEncontrada = $caminho;
                    break;
                }
            }

            if (!$origemEncontrada)
            {
                continue;
            }

            $nomeOriginal   = basename($arquivo);
            $destinoArquivo = $destinoFinal . '/' . uniqid() . '_' . $nomeOriginal;

            if (!copy($origemEncontrada, $destinoArquivo))
            {
                throw new Exception('Não foi possível copiar o anexo para a pasta da atividade: ' . $nomeOriginal);
            }

            $anexos[] = [$destinoArquivo, $nomeOriginal];
        }

        return $anexos;
    }

    public static function registrarEmailEnviadoNaAtividade($interacaoId, $tos, $ccs = null, $assunto, $mensagem, $conteudoArquivo = null)
    {
        try
        {
            TTransaction::open(self::$database);

            if (!NegociacaoService::podeEditar($interacaoId))
            {
                throw new Exception('Não é possivel alterar uma interação finalizada!');
            }

            $agora = date('Y-m-d H:i:s');

            $tipo_interacao = Interacao::where('id', '=', $interacaoId)->first();

            if ($tipo_interacao) {
                $tipo_interacao = $tipo_interacao->tipo_interacao_id;            

                if ($tipo_interacao == 2) {                
                    $att = date('Y-m-d H:i:30');
                    $arq = date('Y-m-d H:i:45');
                }
                else if ($tipo_interacao == 1) {
                    $att = date('Y-m-d H:i:s', strtotime($agora . ' +1 second'));;
                    $arq = date('Y-m-d H:i:s', strtotime($agora . ' +2 seconds'));;
                }
            }
            else {
                throw new Exception('Não foi encontrada interação de referencia!');
            }

            $atividade = new InteracaoAtividade();
            $atividade->interacao_id = $interacaoId;
            $atividade->destinatario = $tos;
            $atividade->copia = $ccs;
            $atividade->assunto = $assunto;
            $atividade->observacao = $mensagem;
            $atividade->tipo_atividade_id = 5;
            $atividade->estado_atividade_id = 2;
            $atividade->horario_inicial = $agora;
            $atividade->horario_final = $agora;
            $atividade->store();

            $historicoAtividade = new InteracaoHistoricoAtividade();
            $historicoAtividade->interacao_id = $interacaoId;
            $historicoAtividade->dt_atividade = $agora;
            $historicoAtividade->descricao = $assunto;
            $historicoAtividade->observacao = $mensagem;
            $historicoAtividade->horario_inicial = $att;
            $historicoAtividade->horario_final = $att;
            $historicoAtividade->tipo_atividade_id = 5;
            $historicoAtividade->estado_atividade_id = 2;
            $historicoAtividade->movimentacao_id = Movimentacao::CRIADO;
            $historicoAtividade->interacao_atividade_id = $atividade->id;
            $historicoAtividade->store();

            $interacaoArquivoIds = [];
            $historicoArquivoIds = [];

            $anexos = self::normalizarAnexosParaAtividade($conteudoArquivo, $interacaoId, $atividade->id);

            if (!empty($anexos))
            {
                $atividade->conteudo_arquivo = self::montarConteudoArquivoMultifile($anexos);
                $atividade->store();
            }

            foreach ($anexos as $anexo)
            {
                $caminhoArquivo = $anexo[0];
                $nomeOriginal   = $anexo[1];

                $interacaoArquivo = new InteracaoArquivo();
                $interacaoArquivo->interacao_id = $interacaoId;
                $interacaoArquivo->nome_arquivo = $nomeOriginal;
                $interacaoArquivo->conteudo_arquivo = $caminhoArquivo;
                $interacaoArquivo->dt_arquivo = $agora;
                $interacaoArquivo->interacao_atividade = $atividade->id;
                $interacaoArquivo->store();

                $interacaoArquivoIds[] = $interacaoArquivo->id;

                $historicoArquivo = new InteracaoHistoricoArquivo();
                $historicoArquivo->interacao_id = $interacaoId;
                $historicoArquivo->dt_arquivo = $arq;
                $historicoArquivo->descricao = $caminhoArquivo;
                $historicoArquivo->movimentacao_id = Movimentacao::CRIADO;
                $historicoArquivo->interacao_arquivo_id = $interacaoArquivo->id;
                $historicoArquivo->store();

                $historicoArquivoIds[] = $historicoArquivo->id;
            }

            TTransaction::close();

            return [
                'interacao_id'           => $interacaoId,
                'interacao_atividade'    => $atividade->id,
                'historico_atividade_id' => $historicoAtividade->id,
                'interacao_arquivo_ids'  => $interacaoArquivoIds,
                'historico_arquivo_ids'  => $historicoArquivoIds,
                'historico_etapa_id'     => null
            ];
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            throw $e;
        }
    }

    private static function montarConteudoArquivoMultifile($anexos)
    {
        $arquivos = [];

        foreach ($anexos as $anexo)
        {
            $caminhoArquivo = $anexo[0] ?? '';

            if ($caminhoArquivo === '')
            {
                continue;
            }

            $arquivos[] = $caminhoArquivo;
        }

        return implode(',', $arquivos);
    }

    public static function onGetAdressFromCoord($lat, $long)
    {
        if (empty($lat) || empty($long)) {
            throw new Exception('Coordenadas não encontradas para buscar endereço.');
        }

        $lat  = str_replace(',', '.', $lat);
        $long = str_replace(',', '.', $long);  

        echo "
            <script>
                (function() {
                    const lat = {$lat};
                    const lng = {$long};

                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1')
                    .then(response => response.json())
                    .then(data => {
                        console.log('RESPOSTA NOMINATIM:', data);

                        const latInput = document.querySelector(\"[name='geo_latitude']\");
                        const longInput = document.querySelector(\"[name='geo_longitude']\");
                        const endInput = document.querySelector(\"[name='geo_endereco']\");

                        if (!latInput || !longInput || !endInput) {
                            alert('Erro ao preparar dados de localização.');
                            return;
                        }

                        let address = null;

                        if (data && data.address) {
                            address = [
                                data.address.road,
                                data.address.city || data.address.town || data.address.village,
                                data.address.state,
                                data.address.postcode
                            ].filter(Boolean).join(' - ');
                        }

                        latInput.value = lat;
                        longInput.value = lng;
                        endInput.value = address || 'Localização capturada';

                            const btnSalvarReal = document.getElementById('btn_savereal');

                            if (!btnSalvarReal) {
                                alert('Botão de salvamento não encontrado.');
                                return;
                            }

                            btnSalvarReal.click();                                                
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Não foi possível obter sua localização. Tente novamente.');
                    });

                })();
                </script>
        ";
    }    

}

