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
        $label22 = new TLabel("Nome Fantasia:", '', '14px', 'B', '100%');
        $text66 = new TTextDisplay($interacao->cliente->nome_fantasia, '', '16px', '');
        $label3 = new TLabel("Documento:", '', '14px', 'B', '100%');
        $cpf_cnpj = new TTextDisplay($interacao->cliente->cpf_cnpj, '', '16px', '');
        $label44 = new TLabel("Código:", '', '14px', 'B', '100%');
        $text88 = new TTextDisplay($interacao->cliente->codigo, '', '16px', '');
        $label66 = new TLabel("Categoria:", '', '14px', 'B', '100%');
        $text100 = new TTextDisplay($interacao->cliente->categoria_cliente->nome, '', '15px', '');
        $label5 = new TLabel("Cidade:", '', '14px', 'B', '100%');
        $text8 = new TTextDisplay($interacao->cidade, '', '16px', '');
        $label7 = new TLabel("Estado:", '', '14px', 'B', '100%');
        $estado = new TTextDisplay($interacao->estado, '', '16px', '');
        $label4 = new TLabel("Representante", '', '14px', 'B', '100%');
        $text3 = new TTextDisplay($interacao->vendedor->razao_social, '', '16px', '');
        $label6 = new TLabel("Etapa:", '', '14px', 'B', '100%');
        $text5 = new TTextDisplay($transformed_interacao_etapa_interacao_nome, '', '16px', '');
        $label8 = new TLabel("Origem do contato:", '', '14px', 'B', '100%');
        $text4 = new TTextDisplay($interacao->origem_contato->nome, '', '16px', '');
        $label12 = new TLabel("Data de início:", '', '14px', 'B', '100%');
        $text6 = new TTextDisplay(TDate::convertToMask($interacao->data_inicio, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
        $label14 = new TLabel("Data esperada de fechamento:", '', '14px', 'B', '100%');
        $dt_fechamento = new TTextDisplay(TDate::convertToMask($interacao->data_fechamento_esperada, 'yyyy-mm-dd', 'dd/mm/yyyy'), '', '16px', '');
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
        $getLocalizacao->addStyleClass('btn-success');

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

        $row2 = $this->form->addFields([$label2,$text2,$text10],[$label22,$text66],[$label3,$cpf_cnpj],[$label44,$text88],[$label66,$text100],[$label5,$text8],[$label7,$estado]);
        $row2->layout = ['col-sm-3',' col-sm-3',' col-sm-3',' col-sm-2',' col-sm-1',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([$label4,$text3],[$label6,$text5],[$label8,$text4],[$label12,$text6]);
        $row3->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$label14,$dt_fechamento],[$label16,$text7]);
        $row4->layout = [' col-sm-3',' col-sm-3'];

        $row5 = $this->form->addFields([$getLocalizacao],[],[],[$tbutton2]);
        $row5->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $tab_63210fe87cb58 = new BootstrapFormBuilder('tab_63210fe87cb58');
        $this->tab_63210fe87cb58 = $tab_63210fe87cb58;
        $tab_63210fe87cb58->setProperty('style', 'border:none; box-shadow:none;');

        $tab_63210fe87cb58->appendPage("Atividades");

        $tab_63210fe87cb58->addFields([new THidden('current_tab_tab_63210fe87cb58')]);
        $tab_63210fe87cb58->setTabFunction("$('[name=current_tab_tab_63210fe87cb58]').val($(this).attr('data-current_page'));");

        $row6 = $tab_63210fe87cb58->addFields([$atividades]);
        $row6->layout = [' col-sm-12'];

        $tab_63210fe87cb58->appendPage("Arquivos");
        $row7 = $tab_63210fe87cb58->addFields([$arquivos]);
        $row7->layout = [' col-sm-12'];

        $tab_63210fe87cb58->appendPage("Observações");
        $row8 = $tab_63210fe87cb58->addFields([$observacoes]);
        $row8->layout = [' col-sm-12'];

        $tab_63210fe87cb58->appendPage("Localizações");
        $row9 = $tab_63210fe87cb58->addFields([$bpagecontainer2]);
        $row9->layout = [' col-sm-12'];

        $row10 = $this->form->addFields([$timeline],[$tab_63210fe87cb58]);
        $row10->layout = [' col-sm-4',' col-sm-8'];

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

        $btnEmailAction = new TAction(['InteracaoFormView', 'onGetLocalizacaoEmailForm'],['interacao_id'=>$interacao->id]);
        $btnEmailLabel = new TLabel("Enviar e-mail");

        $btnEmail = $this->form->addHeaderAction($btnEmailLabel, $btnEmailAction, 'far:envelope #F44336'); 
        $btnEmailLabel->setFontSize('12px'); 
        $btnEmailLabel->setFontColor('#333'); 

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

        $btn_onimprimirAction = new TAction([$this, 'onImprimir'],['key'=>$interacao->id]);
        $btn_onimprimirLabel = new TLabel("Gerar Relatório");

        $btn_onimprimir = $this->form->addHeaderAction($btn_onimprimirLabel, $btn_onimprimirAction, 'fas:file-pdf #FF5722'); 
        $btn_onimprimirLabel->setFontSize('12px'); 
        $btn_onimprimirLabel->setFontColor('#333'); 

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
        if ($interacao->etapa_interacao_id == 6) {
            $btnEmail->disabled = 1;
            $getLocalizacao->disabled = 1;            
            $tbutton2->disabled = 1;                
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
                    $atividade = InteracaoAtividade::where('interacao_id', '=', $interacao->id)->load();
                    if (!empty($atividade)) {
                        $qnt = count($atividade);
                        for($i = 0; $i < $qnt; $i++){
                            if($atividade[$i]->tipo_atividade_id == 8){
                                $loc = InteracaoLocalizacao::where('interacao_id', '=', $interacao->id)->first();

                                if (empty($loc->descricao)) {
                                    new TMessage('warning', "Esta interação exige localização para ser finalizada!");
                                    TTransaction::close();
                                    return;   
                                }                                
                            }
                        }
                    }

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
            TApplication::loadPage('InteracaoList', 'onShow');

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

    public function onImprimir($param = null) 
    {
        try 
        {         
            $id = $param['key'] ?? $param['id'] ?? null;

        if (empty($id))
        {
            throw new Exception('ID da interação não informado. Contate a equipe de desenvolvimento');
        }

        $filePath = RelatorioService::gerarRelatorioInteracao($id);

        TScript::create("window.open('{$filePath}', '_blank');");

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

     public static function onGetLocalizacaoAtividade($param = null) 
    {
        try 
        {
            $interacaoId           = $param['interacao_id'] ?? null;
            $interacaoAtividade    = $param['interacao_atividade'] ?? null;
            $historicoAtividadeId  = $param['historico_atividade_id'] ?? null;
            $interacaoArquivoIds   = $param['interacao_arquivo_ids'] ?? [];
            $historicoArquivoIds   = $param['historico_arquivo_ids'] ?? [];
            $historicoEtapaId      = $param['historico_etapa_id'] ?? null;

            if (!is_array($interacaoArquivoIds)) {
                $interacaoArquivoIds = [];
            }

            if (!is_array($historicoArquivoIds)) {
                $historicoArquivoIds = [];
            }

            echo "
            <script>
                (function() {
                    if (!navigator.geolocation) {
                        limparAtividadePorFalha('Geolocalização não suportada pelo navegador.');
                        return;
                    }

                    if (window.__geo_em_execucao__) {
                        return;
                    }

                    window.__geo_em_execucao__ = true;

                    const id = " . json_encode($interacaoId) . ";
                    const interacaoAtividade = " . json_encode($interacaoAtividade) . ";
                    const historicoAtividadeId = " . json_encode($historicoAtividadeId) . ";
                    const interacaoArquivoIds = " . json_encode(array_values($interacaoArquivoIds)) . ";
                    const historicoArquivoIds = " . json_encode(array_values($historicoArquivoIds)) . ";
                    const historicoEtapaId = " . json_encode($historicoEtapaId) . ";

                    function finalizarExecucao() {
                        window.__geo_em_execucao__ = false;
                    }

                    function limparAtividadePorFalha(mensagem) {
                        fetch('./Geolocalizacao.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                acao: 'delete_atividade',
                                interacao_id: id,
                                interacao_atividade: interacaoAtividade,
                                historico_atividade_id: historicoAtividadeId,
                                interacao_arquivo_ids: interacaoArquivoIds,
                                historico_arquivo_ids: historicoArquivoIds,
                                historico_etapa_id: historicoEtapaId
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Resposta delete:', data);

                            const msg = mensagem || (data && data.message) || 'Não foi possível obter a localização.';
                            alert(msg);
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Erro ao limpar atividade:', error);
                            alert(mensagem || 'Não foi possível obter a localização.');
                            window.location.reload();
                        })
                        .finally(() => {
                            finalizarExecucao();
                        });
                    }

                    function getAddressFromCoordinates(lat, lng) {
                        const url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1';

                        return fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.address) {
                                    const road = data.address.road || '';
                                    const city = data.address.city || data.address.town || data.address.village || '';
                                    const uf = data.address['ISO3166-2-lvl4'] || '';
                                    const postcode = data.address.postcode || '';
                                    return road + ' - ' + city + ' - ' + uf + ', ' + postcode;
                                }

                                return null;
                            })
                            .catch(error => {
                                console.error('Erro ao consultar endereço:', error);
                                return null;
                            });
                    }

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;

                            getAddressFromCoordinates(latitude, longitude).then(address => {
                                if (!address) {
                                    limparAtividadePorFalha('Não foi possível obter o endereço da localização.');
                                    return;
                                }

                                const dados = {
                                    acao: 'store',
                                    id: id,
                                    interacao_atividade: interacaoAtividade,
                                    address: address,
                                    latitude: latitude,
                                    longitude: longitude
                                };

                                fetch('./Geolocalizacao.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(dados),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Resposta store:', data);

                                    if (!data || data.error) {
                                        limparAtividadePorFalha((data && data.message) ? data.message : 'Erro ao salvar localização.');
                                        return;
                                    }

                                    finalizarExecucao();
                                    window.location.reload();
                                })
                                .catch(error => {
                                    console.error('Erro ao salvar localização:', error);
                                    limparAtividadePorFalha('Erro ao salvar localização.');
                                });
                            });
                        },
                        function(error) {
                            console.error('Erro ao obter geolocalização:', error);
                            limparAtividadePorFalha('Localização obrigatória. Permita o acesso à localização para criar a atividade.');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0
                        }
                    );
                })();
            </script>
            ";

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onGetLocalizacaoAtividadeIntSimples($param = null) 
    {
        try 
        {
            $interacaoId           = $param['interacao_id'] ?? null;
            $interacaoAtividade    = $param['interacao_atividade'] ?? null;
            $historicoAtividadeId  = $param['historico_atividade_id'] ?? null;
            $interacaoArquivoIds   = $param['interacao_arquivo_ids'] ?? [];
            $historicoArquivoIds   = $param['historico_arquivo_ids'] ?? [];
            $historicoEtapaId      = $param['historico_etapa_id'] ?? null;

            if (!is_array($interacaoArquivoIds)) {
                $interacaoArquivoIds = [];
            }

            if (!is_array($historicoArquivoIds)) {
                $historicoArquivoIds = [];
            }

            echo "
            <script>
                (function() {
                    if (!navigator.geolocation) {
                        limparAtividadePorFalha('Geolocalização não suportada pelo navegador.');
                        return;
                    }

                    if (window.__geo_em_execucao__) {
                        return;
                    }

                    window.__geo_em_execucao__ = true;

                    const id = " . json_encode($interacaoId) . ";
                    const interacaoAtividade = " . json_encode($interacaoAtividade) . ";
                    const historicoAtividadeId = " . json_encode($historicoAtividadeId) . ";
                    const interacaoArquivoIds = " . json_encode(array_values($interacaoArquivoIds)) . ";
                    const historicoArquivoIds = " . json_encode(array_values($historicoArquivoIds)) . ";
                    const historicoEtapaId = " . json_encode($historicoEtapaId) . ";

                    function finalizarExecucao() {
                        window.__geo_em_execucao__ = false;
                    }

                    function limparAtividadePorFalha(mensagem) {
                        fetch('./Geolocalizacao.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                acao: 'delete_atividade',
                                interacao_id: id,
                                interacao_atividade: interacaoAtividade,
                                historico_atividade_id: historicoAtividadeId,
                                interacao_arquivo_ids: interacaoArquivoIds,
                                historico_arquivo_ids: historicoArquivoIds,
                                historico_etapa_id: historicoEtapaId
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Resposta delete:', data);

                            const msg = mensagem || (data && data.message) || 'Não foi possível obter a localização.';
                            alert(msg);

                            window.location.href = 'index.php?class=InteracaoList&method=onShow';
                        })
                        .catch(error => {
                            console.error('Erro ao limpar atividade:', error);
                            alert(mensagem || 'Não foi possível obter a localização.');

                            window.location.href = 'index.php?class=InteracaoList&method=onShow';
                        })
                        .finally(() => {
                            finalizarExecucao();
                        });
                    }

                    function getAddressFromCoordinates(lat, lng) {
                        const url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1';

                        return fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.address) {
                                    const road = data.address.road || '';
                                    const city = data.address.city || data.address.town || data.address.village || '';
                                    const uf = data.address['ISO3166-2-lvl4'] || '';
                                    const postcode = data.address.postcode || '';
                                    return road + ' - ' + city + ' - ' + uf + ', ' + postcode;
                                }

                                return null;
                            })
                            .catch(error => {
                                console.error('Erro ao consultar endereço:', error);
                                return null;
                            });
                    }

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;

                            getAddressFromCoordinates(latitude, longitude).then(address => {
                                if (!address) {
                                    limparAtividadePorFalha('Não foi possível obter o endereço da localização.');
                                    return;
                                }

                                const dados = {
                                    acao: 'store',
                                    id: id,
                                    interacao_atividade: interacaoAtividade,
                                    address: address,
                                    latitude: latitude,
                                    longitude: longitude
                                };

                                fetch('./Geolocalizacao.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(dados),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Resposta store:', data);

                                    if (!data || data.error) {
                                        limparAtividadePorFalha((data && data.message) ? data.message : 'Erro ao salvar localização.');
                                        return;
                                    }

                                    finalizarExecucao();

                                })
                                .catch(error => {
                                    console.error('Erro ao salvar localização:', error);
                                    limparAtividadePorFalha('Erro ao salvar localização.');
                                });
                            });
                        },
                        function(error) {
                            console.error('Erro ao obter geolocalização:', error);
                            limparAtividadePorFalha('Localização obrigatória. Permita o acesso à localização para criar a atividade.');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0
                        }
                    );
                })();
            </script>
            ";
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onGetLocalizacaoEmailForm($param = null) 
    {
        try 
        {
            $id = $param['interacao_id'] ?? null;

            if (empty($id)) {
                throw new Exception('Interação não encontrada para envio de e-mail.');
            }

            $idJson = json_encode($id);

            echo "
                <script>
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {

                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            const id = {$idJson};

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

                                    __adianti_load_page(
                                        'index.php?class=InteracaoFormView&method=onGetAdressFromCoord'
                                        + '&key=' + encodeURIComponent(id)
                                        + '&lat=' + encodeURIComponent(latitude)
                                        + '&long=' + encodeURIComponent(longitude)
                                        + '&interacao_id=' + encodeURIComponent(id)
                                    );
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

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
            //</autoCode>

    public static function onGetAdressFromCoord($param = null)
    {
        try
        {
            $lat = $param['lat'] ?? null;
            $long = $param['long'] ?? null;
            $interacao_id = $param['interacao_id'] ?? ($param['key'] ?? null);

            if (empty($lat) || empty($long) || empty($interacao_id)) {
                throw new Exception('Dados de localização incompletos.');
            }

            $lat  = str_replace(',', '.', $lat);
            $long = str_replace(',', '.', $long);

            $interacaoIdJson = json_encode($interacao_id);

            echo "
                <script>
                    (function() {
                        const lat = {$lat};
                        const lng = {$long};
                        const interacaoId = {$interacaoIdJson};

                        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&addressdetails=1')
                        .then(response => response.json())
                        .then(data => {
                            console.log('RESPOSTA NOMINATIM:', data);

                            let address = null;

                            if (data && data.address) {
                                address = [
                                    data.address.road,
                                    data.address.city || data.address.town || data.address.village,
                                    data.address.state,
                                    data.address.postcode
                                ].filter(Boolean).join(' - ');
                            }

                            address = address || 'Localização capturada';

                            __adianti_load_page(
                                'index.php?class=InteracaoEmailForm'
                                + '&method=onShow'
                                + '&target_container=adianti_right_panel'
                                + '&interacao_id=' + encodeURIComponent(interacaoId)
                                + '&geo_latitude=' + encodeURIComponent(lat)
                                + '&geo_longitude=' + encodeURIComponent(lng)
                                + '&geo_endereco=' + encodeURIComponent(address),
                                'adianti_right_panel'
                            );
                        })
                        .catch(error => {
                            console.error(error);
                            alert('Não foi possível obter o endereço da localização. Tente novamente.');
                        });

                    })();
                </script>
            ";
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

}

