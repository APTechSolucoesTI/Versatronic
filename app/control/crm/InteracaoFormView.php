<?php

class InteracaoFormView extends TPage
{
    protected $form; // form
    private static $database = 'minicrm';
    private static $activeRecord = 'Interacao';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Interacao';

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

        TTransaction::open(self::$database);
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setTagName('div');

        $interacao = new Interacao($param['key']);
        // define the form title
        $this->form->setFormTitle("Interação #{$param['key']}");

        $transformed_interacao_etapa_interacao_nome = call_user_func(function($value, $object, $row)
        {

            if(!empty($object->etapa_interacao_id))
            {
                return "<span class='label ' style='width: 100%; max-width: 200px; background-color:{$object->etapa_interacao->cor}'>{$object->etapa_interacao->nome}</span>";
            }

        }, $interacao->etapa_interacao->nome, $interacao, null);

        $criteria_etapa_interacao_id = new TCriteria();

        $filterVar = "T";
        $criteria_etapa_interacao_id->add(new TFilter('kanban', '=', $filterVar)); 

        TSession::setValue('interacao_id', $interacao->id);

        $etapa_interacao_id = new TDBArrowStep('etapa_interacao_id', 'minicrm', 'EtapaInteracao', 'id', '{nome}','ordem asc' , $criteria_etapa_interacao_id);
        $label2 = new TLabel("Cliente:", '', '14px', 'B', '100%');
        $text2 = new TTextDisplay($interacao->cliente->razao_social, '', '16px', '');
        $text10 = new TTextDisplay($interacao->cliente_nome, '', '16px', '');
        $label4 = new TLabel("Representante", '', '14px', 'B', '100%');
        $text3 = new TTextDisplay($interacao->vendedor->razao_social, '', '16px', '');
        $label6 = new TLabel("Etapa:", '', '14px', 'B', '100%');
        $text5 = new TTextDisplay($transformed_interacao_etapa_interacao_nome, '', '16px', '');
        $label8 = new TLabel("Origem do contato:", '', '14px', 'B', '100%');
        $text4 = new TTextDisplay($interacao->origem_contato->nome, '', '16px', '');
        $label12 = new TLabel("Data de início:", '', '14px', 'B', '100%');
        $text6 = new TTextDisplay(TDate::convertToMask($interacao->data_inicio, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $label14 = new TLabel("Data esperada de fechamento:", '', '14px', 'B', '100%');
        $text8 = new TTextDisplay(TDate::convertToMask($interacao->data_fechamento_esperada, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $label16 = new TLabel("Data de fechamento:", '', '14px', 'B', '100%');
        $text7 = new TTextDisplay(TDate::convertToMask($interacao->data_fechamento, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $getLocalizacao = new TButton('getLocalizacao');
        $tbutton2 = new TButton('tbutton2');
        $timeline = new BPageContainer();
        $atividades = new BPageContainer();
        $arquivos = new BPageContainer();
        $observacoes = new BPageContainer();
        $bpagecontainer2 = new BPageContainer();

        $etapa_interacao_id->setAction(new TAction([$this,'onChangeEtapa']));

        $etapa_interacao_id->setColorColumn('cor');
        $etapa_interacao_id->setFilledColor('#fd9308');
        $etapa_interacao_id->setFilledFontColor('#ffffff');
        $etapa_interacao_id->setUnfilledColor('#d3d3d3');
        $etapa_interacao_id->setUnfilledFontColor('#333333');
        $etapa_interacao_id->setWidth('100%');
        $etapa_interacao_id->setHeight('60');
        $etapa_interacao_id->setValue($interacao->etapa_interacao_id);
        $tbutton2->addStyleClass('btn-primary');
        $getLocalizacao->addStyleClass('btn-default');

        $getLocalizacao->setImage(' #000000');
        $tbutton2->setImage('fas:plus #FFFFFF');

        $timeline->setSize('100%');
        $arquivos->setSize('100%');
        $atividades->setSize('100%');
        $observacoes->setSize('100%');
        $bpagecontainer2->setSize('100%');

        $arquivos->setId('b633f68ef00a2c');
        $atividades->setId('b6347050b91e4a');
        $observacoes->setId('b633f66f653e7b');
        $timeline->setId('container_timeline');
        $bpagecontainer2->setId('b66d0509028a45');

        $getLocalizacao->setAction(new TAction([$this, 'onGetLocalizacao']), "Pegar Localização");
        $tbutton2->setAction(new TAction(['InteracaoAtividadeCalendarForm', 'onShow']), "Adicionar Atividade");
        $arquivos->setAction(new TAction(['InteracaoArquivoHeaderList', 'onShow'], ['interacao_id' => $interacao->id]));
        $timeline->setAction(new TAction(['ViewInteracaoTimelineTimeLine', 'onShow'], ['interacao_id' => $interacao->id]));
        $observacoes->setAction(new TAction(['InteracaoObservacaoHeaderList', 'onShow'], ['interacao_id' => $interacao->id]));
        $bpagecontainer2->setAction(new TAction(['InteracaoLocalizacaoHeaderList', 'onShow'], ['interacao_id' => $interacao->id]));
        $atividades->setAction(new TAction(['InteracaoAtividadeCalendarFormView', 'onReload'], ['interacao_id' => $interacao->id]));

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $timeline->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $atividades->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $arquivos->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $observacoes->add($loadingContainer);
        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $bpagecontainer2->add($loadingContainer);


        $row1 = $this->form->addFields([$etapa_interacao_id]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([$label2,$text2,$text10],[$label4,$text3],[$label6,$text5],[$label8,$text4]);
        $row2->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([$label12,$text6],[$label14,$text8],[$label16,$text7]);
        $row3->layout = ['col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$getLocalizacao],[],[],[$tbutton2]);
        $row4->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $tab_63210fe87cb58 = new BootstrapFormBuilder('tab_63210fe87cb58');
        $this->tab_63210fe87cb58 = $tab_63210fe87cb58;
        $tab_63210fe87cb58->setProperty('style', 'border:none; box-shadow:none;');

        $tab_63210fe87cb58->appendPage("Atividades");

        $tab_63210fe87cb58->addFields([new THidden('current_tab_tab_63210fe87cb58')]);
        $tab_63210fe87cb58->setTabFunction("$('[name=current_tab_tab_63210fe87cb58]').val($(this).attr('data-current_page'));");

        $row5 = $tab_63210fe87cb58->addFields([$atividades]);
        $row5->layout = [' col-sm-12'];

        $tab_63210fe87cb58->appendPage("Arquivos");
        $row6 = $tab_63210fe87cb58->addFields([$arquivos]);
        $row6->layout = [' col-sm-12'];

        $tab_63210fe87cb58->appendPage("Observações");
        $row7 = $tab_63210fe87cb58->addFields([$observacoes]);
        $row7->layout = [' col-sm-12'];

        $tab_63210fe87cb58->appendPage("Localizações");
        $row8 = $tab_63210fe87cb58->addFields([$bpagecontainer2]);
        $row8->layout = [' col-sm-12'];

        $row9 = $this->form->addFields([$timeline],[$tab_63210fe87cb58]);
        $row9->layout = [' col-sm-4',' col-sm-8'];

        if(!empty($param['current_tab']))
        {
            $this->form->setCurrentPage($param['current_tab']);
        }

        if(!empty($param['current_tab_tab_63210fe87cb58']))
        {
            $this->tab_63210fe87cb58->setCurrentPage($param['current_tab_tab_63210fe87cb58']);
        }

        $btn_oncloseAction = new TAction([$this, 'onClose'],['key'=>$interacao->id]);
        $btn_oncloseLabel = new TLabel("Voltar");

        $btn_onclose = $this->form->addHeaderAction($btn_oncloseLabel, $btn_oncloseAction, 'fas:arrow-left #000000'); 
        $btn_oncloseLabel->setFontSize('12px'); 
        $btn_oncloseLabel->setFontColor('#333'); 

        $btnInteracaoEmailFormOnShowAction = new TAction(['InteracaoEmailForm', 'onShow'],['interacao_id'=>$interacao->id]);
        $btnInteracaoEmailFormOnShowLabel = new TLabel("Enviar email");

        $btnInteracaoEmailFormOnShow = $this->form->addHeaderAction($btnInteracaoEmailFormOnShowLabel, $btnInteracaoEmailFormOnShowAction, 'far:envelope #F44336'); 
        $btnInteracaoEmailFormOnShowLabel->setFontSize('12px'); 
        $btnInteracaoEmailFormOnShowLabel->setFontColor('#333'); 

        $btnEditarAction = new TAction(['InteracaoForm', 'onEdit'],['key'=>$interacao->id]);
        $btnEditarLabel = new TLabel("Editar");

        $btnEditar = $this->form->addHeaderAction($btnEditarLabel, $btnEditarAction, 'fas:edit #2196F3'); 
        $btnEditarLabel->setFontSize('14px'); 
        $btnEditarLabel->setFontColor('#333'); 

        $btnExcluirAction = new TAction([$this, 'onDelete'],['key'=>$interacao->id]);
        $btnExcluirLabel = new TLabel("Excluír");

        $btnExcluir = $this->form->addHeaderAction($btnExcluirLabel, $btnExcluirAction, 'fas:trash-alt #F44336'); 
        $btnExcluirLabel->setFontSize('14px'); 
        $btnExcluirLabel->setFontColor('#333'); 

        $btnInteracaoDocumentOnGenerateAction = new TAction(['InteracaoDocument', 'onGenerate'],['key'=>$interacao->id]);
        $btnInteracaoDocumentOnGenerateLabel = new TLabel("Imprimir");

        $btnInteracaoDocumentOnGenerate = $this->form->addHeaderAction($btnInteracaoDocumentOnGenerateLabel, $btnInteracaoDocumentOnGenerateAction, 'fas:file-pdf #F44336'); 
        $btnInteracaoDocumentOnGenerateLabel->setFontSize('12px'); 
        $btnInteracaoDocumentOnGenerateLabel->setFontColor('#333'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Consulta de Interação"]));
        }
        $container->add($this->form);

        if(!NegociacaoService::podeEditar($interacao->id))
        {
            $btnEditar->disabled = 1; // desabilita o botão
            //$btnEditar->style = 'display:none';
        }

        if(!NegociacaoService::podeExcluir($interacao->id))
        {
            //$btnExcluir->disabled = 1; // desabilita o botão
            $btnExcluir->style = 'display:none';
        }

        TTransaction::close();
        parent::add($container);

    }

    public static function onChangeEtapa($param = null) 
    {
        try 
        {
            if(!empty($param['key']))
            {
                TTransaction::open(self::$database);

                $interacao = new Interacao(TSession::getValue('interacao_id'));
                $interacao->etapa_interacao_id = $param['key'];

                 if($param['key'] == 6)
                {

                    $pageParam = ['key'=>TSession::getValue('interacao_id')]; // ex.: = ['key' => 10]

                    TApplication::loadPage('InteracaoFormEdit', 'onEdit', $pageParam);

                    $interacao->data_fechamento =  date('Y-m-d H:i:s');
                }
                else 
                {
                     if(!NegociacaoService::podeEditar($interacao->id))
                    {

                        TToast::show("error", "Não é possivel alterar uma Interação finalizada!", "center", "fas:exclamation-circle");

                    }
                    else 
                    {
                        $interacao->store();

                        $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
                        $interacaoHistoricoEtapa->etapa_interacao_id = $interacao->etapa_interacao_id;
                        $interacaoHistoricoEtapa->interacao_id = $interacao->id;
                        $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:s');
                        $interacaoHistoricoEtapa->store();

                        TTransaction::close();

                        new TMessage('info', 'Etapa alterada com sucesso!', new TAction(['InteracaoFormView', 'onShow'], ['key'=>$interacao->id]));
                    }   
                }
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onGetLocalizacao($param = null) 
    {
        try 
        {
            echo "
                <script>
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {

                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;

                            const url = new URL(window.location.href);
                            const params = new URLSearchParams(url.search);

                            const id = params.get('key');
                            console.log(id);

                            function getAddressFromCoordinates(lat, lng) {
                                const url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1';

                                return fetch(url)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && data.address) {
                                            const address = data.address.road + ' - ' + data.address.city + ' - ' + data.address['ISO3166-2-lvl4'] + ', ' + data.address.postcode;
                                            return address; 
                                        } else {
                                            console.log('Nenhum endereço encontrado.');
                                            return null; 
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Erro:', error);
                                        return null; 
                                    });
                            }

                           getAddressFromCoordinates(latitude, longitude).then(address => {
                            if (address === null) {
                                console.error('Não foi possível obter o endereço.');
                                return; // Interrompe o envio se o endereço for null
                            }

                            const dados = {
                                acao: 'store',
                                id: id,
                                address: address,
                                latitude: latitude,
                                longitude: longitude,
                            };

                            fetch('./Geolocalizacao.php', {
                                method: 'POST',
                                headers: {
                                  'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(dados),
                            })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.error) {
                                    console.log('Erro: ' + data.message);
                                } else {
                                    window.location.reload();
                                }
                            })
                            .catch((error) => {
                                console.log('Erro na verificação de favoritos: ' + error);
                            });
                           });
                        });

                    } else { 
                        console.log('Geolocation is not supported by this browser.');
                    }
                </script>

            ";

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onClose($param = null) 
    {
        try 
        {
        if(TSession::getValue('key'))
        { 
            $loadPageParam = [];
            $loadPageParam["key"] = TSession::getValue('key');
            $loadPageParam['voltar'] = true;
            TApplication::loadPage(TSession::getValue('origem'), TSession::getValue('method'), $loadPageParam);
        }
        else {
             TApplication::loadPage(TSession::getValue('origem'), TSession::getValue('method'));
        }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onDelete($param = null) 
    {
        try 
        {

            if(isset($param['delete']) && $param['delete'] == 1)
            {
                try
                {
                    // get the paramseter $key
                    $key = TSession::getValue('interacao_id');
                    // open a transaction with database
                    TTransaction::open(self::$database);

                    // instantiates object
                    $object = new Interacao($key, FALSE); 

                    if(!NegociacaoService::podeExcluir($object->id))
                    {
                        throw new Exception('Não é possível excluir');
                    }

                     $timeline = (ViewInteracaoTimeline::where('interacao_id','=',$object->id))->count();

                    if($timeline <= 1)
                    {

                        // deletes the object from the database

                        $atividades = (InteracaoAtividade::where('interacao_id','=',$object->id));

                        $atividades->delete();
                        $object->delete();

                        // close the transaction
                        TTransaction::close();

                        new TMessage('info', 'Interacao deletada', new TAction(['InteracaoList', 'onShow']));
                    }
                    else 
                    {
                        new TMessage('error', "Não é possivel deletar essa interação!");
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
                $action = new TAction(array('InteracaoFormView', 'onDelete'));
                $action->setParameters($param); // pass the key paramseter ahead
                $action->setParameter('delete', 1);
                // shows a dialog to the user
                new TQuestion('Você tem certeza que quer deletar?', $action);   
            }

        }
        catch (Exception $e) 
        {

            new TMessage('error', $e->getMessage());    
        }
    }

    public function onShow($param = null)
    {     

        TTransaction::open(self::$database);

        if(!NegociacaoService::podeEditar(TSession::getValue('interacao_id')))
        {
            TDBArrowStep::disableField(self::$formName, 'etapa_interacao_id');
            TButton::disableField(self::$formName,'tbutton2');
            TButton::disableField(self::$formName,'getLocalizacao');

        }
        TTransaction::close();

    }

}

