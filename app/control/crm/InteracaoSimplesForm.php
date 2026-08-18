<?php

class InteracaoSimplesForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_InteracaoSimplesForm';

    use Adianti\Base\AdiantiFileSaveTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();
        parent::setSize(0.60, null);
        parent::setTitle("Cadastro de interação simples");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de interação simples");

        $criteria_representante_id = new TCriteria();
        $criteria_cliente_id = new TCriteria();
        $criteria_cliente_cpf_cnpj = new TCriteria();
        $criteria_tipo_atividade_id = new TCriteria();

        $filterVar = TSession::getValue("userid");
        $criteria_representante_id->add(new TFilter('system_user_id', '=', $filterVar)); 
        $filterVar = Grupo::CLIENTE;
        $criteria_cliente_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_id = '{$filterVar}')")); 

        $representante_id = new TDBCombo('representante_id', 'minicrm', 'Representante', 'id', '{razao_social}','razao_social asc' , $criteria_representante_id );
        $cliente_id = new TDBUniqueSearch('cliente_id', 'minicrm', 'Pessoa', 'id', 'nome_fantasia','nome_fantasia asc' , $criteria_cliente_id );
        $cliente_cpf_cnpj = new TDBCombo('cliente_cpf_cnpj', 'minicrm', 'Pessoa', 'cpf_cnpj', '{cpf_cnpj}','cpf_cnpj asc' , $criteria_cliente_cpf_cnpj );
        $cliente_nome = new TEntry('cliente_nome');
        $cidade_uf = new TEntry('cidade_uf');
        $classificacao = new TEntry('classificacao');
        $buttonsendemail = new TButton('buttonsendemail');
        $tipo_atividade_id = new TDBCombo('tipo_atividade_id', 'minicrm', 'TipoAtividade', 'id', '{nome}','nome asc' , $criteria_tipo_atividade_id );
        $conteudo_arquivo = new TFile('conteudo_arquivo');
        $observacao = new THtmlEditor('observacao');

        $cliente_id->setChangeAction(new TAction([$this,'onSelect']));
        $cliente_cpf_cnpj->setChangeAction(new TAction([$this,'onSelectCpfCnpj']));
        $tipo_atividade_id->setChangeAction(new TAction([$this,'onCaseEmail']));

        $representante_id->addValidation("Representante", new TRequiredValidator()); 
        $cliente_id->addValidation("Cliente", new TRequiredValidator()); 
        $cliente_cpf_cnpj->addValidation("Documento", new TRequiredValidator()); 
        $tipo_atividade_id->addValidation("Tipo de atividade", new TRequiredValidator()); 

        $representante_id->setDefaultOption(false);
        $cliente_id->setMinLength(2);
        $cliente_id->setMask('{nome_fantasia}');
        $cliente_id->setFilterColumns(["nome_fantasia"]);
        $buttonsendemail->setAction(new TAction([$this, 'onChamaLocEmail']), "Enviar e-mail");
        $buttonsendemail->addStyleClass('btn-default');
        $buttonsendemail->setImage('far:envelope #F44336');
        $conteudo_arquivo->enableFileHandling();
        $conteudo_arquivo->setAllowedExtensions(["csv","pdf","jpg","png","jpeg","gif","mp4","mp3"]);
        $cidade_uf->setEditable(false);
        $classificacao->setEditable(false);
        $representante_id->setEditable(false);

        $representante_id->enableSearch();
        $cliente_cpf_cnpj->enableSearch();
        $tipo_atividade_id->enableSearch();

        $cidade_uf->setSize('100%');
        $cliente_nome->setSize('100%');
        $classificacao->setSize('100%');
        $observacao->setSize('100%', 250);
        $representante_id->setSize('100%');
        $cliente_cpf_cnpj->setSize('100%');
        $conteudo_arquivo->setSize('100%');
        $tipo_atividade_id->setSize('100%');
        $cliente_id->setSize('calc(100% - 6px)');


        $geo_latitude = new THidden('geo_latitude');
        $geo_longitude = new THidden('geo_longitude');
        $geo_endereco = new THidden('geo_endereco');

        $geo_latitude->setValue($param['geo_latitude'] ?? null);
        $geo_longitude->setValue($param['geo_longitude'] ?? null);
        $geo_endereco->setValue($param['geo_endereco'] ?? null);

        $row1 = $this->form->addFields([new TLabel("Representante:", '#FF0000', '14px', null, '100%'),$representante_id]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Cliente:", '#FF0000', '14px', null, '100%'),$cliente_id],[new TLabel(" CPF/CNPJ:", '#FF0000', '14px', null, '100%'),$cliente_cpf_cnpj],[new TLabel(" Cidade/UF:", '#000000', '14px', null, '100%'),$cliente_nome,$cidade_uf],[new TLabel("Classificação:", null, '14px', null),$classificacao],[new TLabel(" ", null, '14px', null, '100%'),$buttonsendemail]);
        $row2->layout = ['col-sm-4','col-sm-4','col-sm-4','col-sm-4',' col-sm-8'];

        $row3 = $this->form->addFields([new TLabel("Tipo de atividade:", '#FF0000', '14px', null, '100%'),$tipo_atividade_id],[new TLabel("Anexar arquivos:", '#000000', '14px', null),$conteudo_arquivo]);
        $row3->layout = [' col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Observação:", '#FF0000', '14px', null, '100%'),$observacao]);
        $row4->layout = [' col-sm-12'];

        $this->form->addFields([$geo_latitude], [$geo_longitude], [$geo_endereco]);

        // create the form actions
        $btn_onsavefalso = $this->form->addAction("Salvar", new TAction([$this, 'onSaveFalso']), 'fas:save #ffffff');
        $this->btn_onsavefalso = $btn_onsavefalso;
        $btn_onsavefalso->addStyleClass('btn-primary'); 

        $btn_onformclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onFormClear']), 'fas:eraser #DD5A43');
        $this->btn_onformclear = $btn_onformclear;

        $btn_onvoltar = $this->form->addAction("Voltar", new TAction([$this, 'onVoltar']), 'fas:arrow-left #000000');
        $this->btn_onvoltar = $btn_onvoltar;


        $btn_onsaveverdadeiro = $this->form->addAction("SalvarReal", new TAction([$this, 'onSaveVerdadeiro']), 'fas:save #ffffff');
        $btn_onsaveverdadeiro->id = 'btn_onsaveverdadeiro';
        $btn_onsaveverdadeiro->style = 'display:none';

        $btn_email = $this->form->addAction("SendEmail", new TAction([$this, 'onSendEmailSimples']), 'fas:save #ffffff');
        $btn_email->id = 'btn_email';
        $btn_email->style = 'display:none';

        TTransaction::open('minicrm');
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
            $representante_id->setValue($representante->id);
            $representante_id->setEditable(false);
        }
        TTransaction::close();

        TScript::create("$(\"[name='cliente_nome']\").closest('.fb-inline-field-container').hide()");
        TScript::create("$(\"[name='buttonsendemail']\").hide()");        
        TScript::create("$('#btn_onsaveverdadeiro').hide();");
        TScript::create("$('#btn_email').hide();");

        parent::add($this->form);

    }

    public static function onSelect($param = null) 
    {
        try 
    {
        if ($param['cliente_id'])
        {
            TTransaction::open('minicrm');
            $pessoa = Pessoa::find($param['cliente_id']);

            if (!empty($pessoa)) {
                $cpf = preg_replace('/[^0-9]/', '', $pessoa->cpf_cnpj);
                    $categoriaNome = '';

                    if (!empty($pessoa->categoria_cliente_id))
                    {
                        $categoria = CategoriaCliente::find($pessoa->categoria_cliente_id);
                        $categoriaNome = $categoria ? $categoria->nome : '';
                    }

                $object = new stdClass();
                $object->cliente_cpf_cnpj = $cpf;
                $object->cidade_uf = $pessoa->cidade_uf;
                $object->classificacao = $categoriaNome;

                TForm::sendData(self::$formName, $object, false, false);
            }

            TTransaction::close();
        }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onSelectCpfCnpj($param = null) 
    {
        try 
        {           
            if (!empty($param['cliente_cpf_cnpj']))
            {
                TTransaction::open('minicrm');

                $pessoa = Pessoa::where('cpf_cnpj', '=', $param['cliente_cpf_cnpj'])->first();

                if ($pessoa)
                {

                    if (!empty($pessoa->categoria_cliente_id))
                    {
                        $categoria = CategoriaCliente::find($pessoa->categoria_cliente_id);
                        $categoriaNome = $categoria ? $categoria->nome : '';
                    }

                    $object = new stdClass();
                    $object->cliente_id = $pessoa->id;
                    $object->cidade_uf = $pessoa->cidade_uf;
                    $object->classificacao = $categoriaNome;

                    TForm::sendData(self::$formName, $object, false, false);
                }

                TTransaction::close();
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onCaseEmail($param = null) 
    {
        try 
        {
            if($param['tipo_atividade_id'] == 5){
                TScript::create("$(\"[name='buttonsendemail']\").show()");
            }
            else {
                TScript::create("$(\"[name='buttonsendemail']\").hide()"); 
            }  

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public  function onChamaLocEmail($param = null) 
    {
        try 
        {
            TTransaction::open('minicrm');            

            $lat = $param['geo_latitude'] ?? null;
            $long = $param['geo_longitude'] ?? null;

            if (empty($lat) || empty($long)) {
                throw new Exception('Não foi possível capturar coordenadas!');
            }

            $this->form->validate();            

            TTransaction::close();

            self::onGetAdressFromCoord($lat, $long, 'email');

        }
        catch (Exception $e) 
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSaveFalso($param = null) 
    {
        try
        {
            TTransaction::open('minicrm');            

            $lat = $param['geo_latitude'] ?? null;
            $long = $param['geo_longitude'] ?? null;

            if (empty($lat) || empty($long)) {
                throw new Exception('Não foi possível capturar coordenadas!');
            }

            $this->form->validate();                    
            $data = $this->form->getData();  

            if (empty($data->observacao)) {
                throw new Exception('O campo Observação é obrigatório!');
            }

            if (($data->tipo_atividade_id == 9 || $data->tipo_atividade_id == 5) && empty($data->conteudo_arquivo)) {
                throw new Exception('O campo Anexar arquivo é obrigatório!');
            }       

            TTransaction::close();

            self::onGetAdressFromCoord($lat, $long, 'save');
        }
        catch (Exception $e)
        {        
            TTransaction::rollback(); // undo all pending operations

            $data = $this->form->getData();
            $this->form->setData($data);
            new TMessage('error', $e->getMessage());
        }
    }

    public function onFormClear($param = null) 
    {
        try 
        {
            $this->form->clear(true);

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onVoltar($param = null) 
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

    public function onShow($param = null)
    {               

        if($param['tipo_atividade_id'] == 5){
                TScript::create("$(\"[name='buttonsendemail']\").show()");
        }
        else {
            TScript::create("$(\"[name='buttonsendemail']\").hide()"); 
        }  

    } 

    public static function onNo()
    {
        return;
    }

    public function onEdit( $param )//</ini>
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('minicrm'); // open a transaction

                $object = new Interacao($key); // instantiates the Active Record //</blockLine>

                $object->cliente_cpf_cnpj = $object->cliente->cpf_cnpj;
                $object->cliente_categoria_cliente_nome = $object->cliente->categoria_cliente->nome;

                $this->form->setData($object); // fill the form //</blockLine>

                if ($object->etapa_interacao_id == 6 || $object->etapa_interacao_id == 7) {
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
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onSaveVerdadeiro($param = null) 
    {
        try
        {
            $this->form->validate();

            TTransaction::open('minicrm');                           

            $cidade_nome = null;
            $estado_nome = null;

            $data = $this->form->getData();

            $lat = $data->geo_latitude ?? null;
            $long = $data->geo_longitude ?? null;
            $end = $data->geo_endereco ?? null;

            $pe = PessoaEndereco::where('pessoa_id', '=', $data->cliente_id)->where('principal', '=', 'S')->first();
            if (!empty($pe)) {
                $cidade = Cidade::where('id', '=', $pe->cidade_id)->first();
                if (!empty($cidade)) {
                    $cidade_nome = $cidade->nome;
                    $estado = Estado::where('id', '=', $cidade->estado_id)->first();
                    if (!empty($estado)) {
                        $estado_nome = $estado->nome;                        
                    }
                }
            }            

            if (empty($data->observacao)) {
                throw new Exception('O campo Observação é obrigatório!');
            }                
            if (($data->tipo_atividade_id == 9 || $data->tipo_atividade_id == 5) && empty($data->conteudo_arquivo)) {
                throw new Exception('O campo Anexar arquivo é obrigatório!');
            }
            if (!$end || !$lat || !$long) {
                throw new Exception('Não foi possível capturar localização!');
            }

            $object = new Interacao();

            $object->cidade = $cidade_nome;
            $object->estado = $estado_nome;

            $object->tipo_interacao_id = TipoInteracao::SIMPLES;
            $object->cliente_id = $data->cliente_id;
            $object->cliente_nome = $data->cliente_nome;
            $object->vendedor_id = $data->representante_id;
            $object->etapa_interacao_id = EtapaInteracao::FINALIZADA;
            $object->data_inicio = date('Y-m-d H:i:00');
            $object->data_fechamento = date('Y-m-d H:i:05');
            $object->mes = date('m');
            $object->ano = date('Y');
            $object->store();

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = 5;
            $interacaoHistoricoEtapa->interacao_id = $object->id;
            $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:00');
            $interacaoHistoricoEtapa->store();

            $atividade = new InteracaoAtividade();
            $atividade->tipo_atividade_id = $data->tipo_atividade_id;
            $atividade->interacao_id = $object->id;
            $atividade->estado_atividade_id = EstadoAtividade::CONCLUIDO;
            $atividade->horario_inicial = date('Y-m-d H:i:00');
            $atividade->horario_final = date('Y-m-d H:i:00');
            $atividade->observacao = $data->observacao ?? null;
            $atividade->dt_atividade = date('Y-m-d H:i:00');
            $atividade->store();

            $interacaoHistoricoAtividade = new InteracaoHistoricoAtividade;
            $interacaoHistoricoAtividade->interacao_id = $object->id;
            $interacaoHistoricoAtividade->dt_atividade = date('Y-m-d H:i:30');
            $interacaoHistoricoAtividade->observacao = $atividade->observacao;
            $interacaoHistoricoAtividade->horario_inicial = $atividade->horario_inicial;
            $interacaoHistoricoAtividade->horario_final = $atividade->horario_final;
            $interacaoHistoricoAtividade->tipo_atividade_id = $atividade->tipo_atividade_id;
            $interacaoHistoricoAtividade->estado_atividade_id = EstadoAtividade::CONCLUIDO;
            $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::CRIADO;
            $interacaoHistoricoAtividade->interacao_atividade_id = $atividade->id;
            $interacaoHistoricoAtividade->store();

            if (!empty($data->conteudo_arquivo)) {

                $interacaoArquivo = new InteracaoArquivo();
                $interacaoArquivo->interacao_id = $object->id;
                $interacaoArquivo->conteudo_arquivo = $data->conteudo_arquivo ?? null;
                $interacaoArquivo->dt_arquivo = date('Y-m-d H:i:s');
                $interacaoArquivo->interacao_atividade = $atividade->id;
                $interacaoArquivo->store();            

                $conteudo_arquivo_dir = 'anexos';

                $this->saveFile($interacaoArquivo, $data, 'conteudo_arquivo', $conteudo_arquivo_dir);

                $interacaoHistoricoArquivo = new InteracaoHistoricoArquivo;
                $interacaoHistoricoArquivo->interacao_id = $object->id;
                $interacaoHistoricoArquivo->dt_arquivo = date('Y-m-d H:i:45');
                $interacaoHistoricoArquivo->descricao = $interacaoArquivo->conteudo_arquivo;
                $interacaoHistoricoArquivo->movimentacao_id = Movimentacao::CRIADO;
                $interacaoHistoricoArquivo->interacao_arquivo_id = $interacaoArquivo->id;
                $interacaoHistoricoArquivo->store();                           
            }

            $localizacao = new InteracaoLocalizacao;
            $localizacao->interacao_id = $object->id;
            $localizacao->interacao_atividade = $atividade->id ?? null;
            $localizacao->descricao = $end;
            $localizacao->latitude = $lat;
            $localizacao->longitude = $long;
            $localizacao->dt_localizacao = date('Y-m-d H:i:55');
            $localizacao->store();

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = EtapaInteracao::FINALIZADA;
            $interacaoHistoricoEtapa->interacao_id = $object->id;
            $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:59');
            $interacaoHistoricoEtapa->store();            

            TTransaction::close();

            TApplication::loadPage('InteracaoFormView', null, ['key'=>$object->id]);

        }
        catch (Exception $e)
        {                       
            TTransaction::rollback();

            $data = $this->form->getData();
            $this->form->setData($data);
            new TMessage('error', $e->getMessage());
        }
    }

public function onSendEmailSimples($param = null) 
{
    try 
    {
        $this->form->validate();

        $cidade_nome = null;
        $estado_nome = null;

        TTransaction::open('minicrm');

        $data = $this->form->getData();

        $dadosInteracao = (array) $data;

        $lat  = $data->geo_latitude ?? ($param['geo_latitude'] ?? null);
        $long = $data->geo_longitude ?? ($param['geo_longitude'] ?? null);
        $end  = $data->geo_endereco ?? ($param['geo_endereco'] ?? null);

        $dadosInteracao['geo_latitude'] = $lat;
        $dadosInteracao['geo_longitude'] = $long;
        $dadosInteracao['geo_endereco'] = $end;

        $cliente_id = $dadosInteracao['cliente_id'] ?? null;

        if (empty($cliente_id)) {
            throw new Exception('Cliente não encontrado para abrir envio de e-mail.');
        }

        $pe = PessoaEndereco::where('pessoa_id', '=', $cliente_id)
            ->where('principal', '=', 'S')
            ->first();

        if (!empty($pe)) {
            $cidade = Cidade::where('id', '=', $pe->cidade_id)->first();

            if (!empty($cidade)) {
                $cidade_nome = $cidade->nome;

                $estado = Estado::where('id', '=', $cidade->estado_id)->first();

                if (!empty($estado)) {
                    $estado_nome = $estado->nome;                        
                }
            }
        }

        TTransaction::close();    

        TWindow::closeWindow(parent::getId());

        TApplication::loadPage('InteracaoEmailFormSimples', null, [
            'cliente_id' => $cliente_id,
            'cidade_nome' => $cidade_nome,
            'estado_nome' => $estado_nome,
            'geo_latitude' => $lat,
            'geo_longitude' => $long,
            'geo_endereco' => $end,
            'dados_interacao' => base64_encode(json_encode($dadosInteracao))
        ]);

        return;
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }  

    public static function onGetAdressFromCoord($lat, $long, $request)
    {
        $lat  = str_replace(',', '.', $lat);
        $long = str_replace(',', '.', $long);   
        $request = json_encode($request);     

        echo "
            <script>
                (function() {
                    const lat = {$lat};
                    const lng = {$long};
                    const req = {$request};

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

                        if (req == 'save') {
                            const btnSalvarReal = document.getElementById('btn_onsaveverdadeiro');

                            if (!btnSalvarReal) {
                                alert('Botão de salvamento não encontrado.');
                                return;
                            }

                            btnSalvarReal.click();                        
                        }
                        else if (req == 'email') {
                            const btnEmail = document.getElementById('btn_email');

                            if (!btnEmail) {
                                alert('Botão de e-mail não encontrado.');
                                return;
                            }

                            btnEmail.click();
                        }
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

