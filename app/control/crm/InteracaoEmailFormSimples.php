<?php

class InteracaoEmailFormSimples extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_InteracaoEmailFormSimples';

    use Adianti\Base\AdiantiFileSaveTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Envio de email Interação");

        $criteria_TDBCheckList = new TCriteria();
        $criteria_email_template_id = new TCriteria();

        TTransaction::open('minicrm');
        if (!empty($param['cliente_id'])) {
            $cliente_id = $param['cliente_id'];
            $filterVar = new TFilter("pessoa_id","=",$cliente_id);
            $criteria_TDBCheckList->add($filterVar);          
        }
        TTransaction::close();

        $interacao_id = new THidden('interacao_id');
        $TDBCheckList = new TCheckList('TDBCheckList');
        $com_copia = new TEntry('com_copia');
        $email_template_id = new TDBCombo('email_template_id', 'minicrm', 'EmailTemplate', 'id', '{titulo}','titulo asc' , $criteria_email_template_id );
        $assunto = new TEntry('assunto');
        $mensagem = new THtmlEditor('mensagem');
        $conteudo_arquivo = new TMultiFile('conteudo_arquivo');

        $email_template_id->setChangeAction(new TAction([$this,'onChangeTemplateEmail']));

        $TDBCheckList->addValidation("Selecione um Email", new TRequiredValidator()); 
        $assunto->addValidation("Assunto", new TRequiredValidator()); 
        $mensagem->addValidation("Corpo do E-mail", new TRequiredValidator()); 

        $interacao_id->setValue($param["interacao_id"] ?? "");
        $com_copia->enableToggleVisibility(false);
        $email_template_id->enableSearch();
        $conteudo_arquivo->enableFileHandling();
        $conteudo_arquivo->setLimitUploadSize(1000);
        $conteudo_arquivo->setAllowedExtensions(["pdf","doc","docs","xls","xlsx","jpg","jpeg","png","zip","rar"]);
        $conteudo_arquivo->enableImageGallery('0', NULL);
        $assunto->setSize('100%');
        $com_copia->setSize('60%');
        $interacao_id->setSize(200);
        $mensagem->setSize('100%', 160);
        $email_template_id->setSize('60%');
        $conteudo_arquivo->setSize('100%');

        $TDBCheckList->setIdColumn('id');

        $column_TDBCheckList_pessoa_razao_social = $TDBCheckList->addColumn('pessoa->razao_social', "Cliente", 'center' , '20%');
        $column_TDBCheckList_nome = $TDBCheckList->addColumn('nome', "Descrição", 'center' , '20%');
        $column_TDBCheckList_email = $TDBCheckList->addColumn('email', "Email", 'center' , '20%');
        $column_TDBCheckList_telefone = $TDBCheckList->addColumn('telefone', "Telefone", 'center' , '20%');

        $TDBCheckList->setHeight(250);
        $TDBCheckList->makeScrollable();

        $TDBCheckList->fillWith('minicrm', 'PessoaContato', 'id', 'id asc' , $criteria_TDBCheckList);

        $dados_interacao = new THidden('dados_interacao');
        $cidade_temp = new THidden('cidade_temp');
        $estado_temp = new THidden('estado_temp');
        $geo_latitude = new THidden('geo_latitude');
        $geo_longitude = new THidden('geo_longitude');
        $geo_endereco = new THidden('geo_endereco');

        $dados_interacao->setValue($param['dados_interacao'] ?? null);
        $cidade_temp->setValue($param['cidade_nome'] ?? null);
        $estado_temp->setValue($param['estado_nome'] ?? null);
        $geo_latitude->setValue($param['geo_latitude'] ?? null);
        $geo_longitude->setValue($param['geo_longitude'] ?? null);
        $geo_endereco->setValue($param['geo_endereco'] ?? null);

        $row1 = $this->form->addFields([new TLabel("Interações:", '#F44336', '14px', null, '100%'),$interacao_id,$TDBCheckList]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Cc (Com Cópia):", null, '14px', null, '100%'),$com_copia]);
        $row2->layout = ['col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Template de email:", '#2E2E2E', '14px', null, '100%'),$email_template_id]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Assunto:", '#F44336', '14px', null, '100%'),$assunto]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([new TLabel("Corpo do E-mail:", '#F44336', '14px', null, '100%'),$mensagem]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->form->addFields([new TLabel("Anexar arquivo:", '#000000', '14px', null, '100%'),$conteudo_arquivo]);
        $row6->layout = [' col-sm-12'];

        $this->form->addFields([$dados_interacao], [$cidade_temp], [$estado_temp], [$geo_latitude], [$geo_longitude], [$geo_endereco]);

        // create the form actions
        $btn_onenviaremail = $this->form->addAction("Enviar", new TAction([$this, 'onEnviarEmail']), 'fas:rocket #ffffff');
        $this->btn_onenviaremail = $btn_onenviaremail;
        $btn_onenviaremail->addStyleClass('btn-primary'); 

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

    public static function onChangeTemplateEmail($param = null) 
    {
        try 
        {

            if (!empty($param['key']))
            {
                TTransaction::open('minicrm');

                $emailTemplate = new EmailTemplate($param['key']);

                TTransaction::close();

                $obj = new stdClass();
                $obj->mensagem = $emailTemplate->mensagem;
                $obj->assunto  = $emailTemplate->titulo;

                TForm::sendData(self::$formName, $obj);

            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onEnviarEmail($param = null) 
    {
        try
        {
            $this->form->validate();
            $data = $this->form->getData();
            $this->form->setData($data);        

            $cidade = $data->cidade_temp ?? null;
            $estado = $data->estado_temp ?? null;

            $dados = [];

            if (!empty($data->dados_interacao)) {
                $json = base64_decode($data->dados_interacao);
                $dados = json_decode($json, true);
            }

            if (empty($dados) || !is_array($dados)) {
                throw new Exception('Dados da interação inválidos ou corrompidos.');
            }

            $email = TSession::getValue("usermail");
            TTransaction::open('minicrm');

            $configuracao =  ConfiguracaoEmail::where('mail_from', '=', $email)->first();

            if (empty($configuracao->id))
            {
                throw new Exception('E-mail de envio inválido!');
            }

            $hora_menos = date('Y-m-d H:i:s', strtotime('-1 minute'));
            $hora_mais = date('Y-m-d H:i:s', strtotime('+1 minute'));            

            $validaOnSave = Interacao::where('cliente_id', '=', $dados['cliente_id'])
                                    ->where('vendedor_id', '=', $dados['representante_id'])
                                    ->where('tipo_interacao_id', '=', 2)
                                    ->where('created_at', '>=', $hora_menos)
                                    ->where('created_at', '<=', $hora_mais)
                                    ->first();

            if (!empty($validaOnSave)) {
                TTransaction::close();  
                return;
            }

            $interacao = new Interacao();

            $interacao->cidade = $cidade ?? null;
            $interacao->estado = $estado ?? null;

            $interacao->tipo_interacao_id = TipoInteracao::SIMPLES;
            $interacao->cliente_id = $dados['cliente_id'];
            $interacao->cliente_nome = $dados['cliente_nome'];
            $interacao->vendedor_id = $dados['representante_id'];
            $interacao->etapa_interacao_id = 5;
            $interacao->data_inicio = date('Y-m-d H:i:00');
            $interacao->data_fechamento = date('Y-m-d H:i:00');
            $interacao->mes = date('m');
            $interacao->ano = date('Y');
            $interacao->store();

            TSession::setValue('temp_interacao_dados', null);
            TSession::setValue('cidade_temp', null);
            TSession::setValue('estado_temp', null);

            $data->interacao_id = $interacao->id;

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = 5;
            $interacaoHistoricoEtapa->interacao_id = $interacao->id;
            $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:00');
            $interacaoHistoricoEtapa->store();

            $tipo = $interacao->tipo_interacao_id;

            if (empty($interacao->id))
            {
                throw new Exception('Interação não encontrada.');
            }

            $tos = [];

            if (!empty($data->TDBCheckList))
            {
                foreach ((array) $data->TDBCheckList as $contato_id)
                {
                    $contato = new PessoaContato($contato_id);

                    if (!empty($contato->email))
                    {
                        $tos[] = trim($contato->email);
                    }
                }
            }

            $tos = array_values(array_unique($tos));

            if (empty($tos))
            {
                throw new Exception('Nenhum contato selecionado possui email cadastrado.');
            }

            $destinatarios = implode(', ', $tos);

            $clienteNome = $interacao->cliente->razao_social ?: $interacao->cliente->nome;

            TTransaction::close();

            $assunto = self::aplicarMarcadores($data->assunto, [
                '{nome}' => $clienteNome,
                '{id}'   => $interacao->id
            ]);

            $mensagem = self::aplicarMarcadores($data->mensagem, [
                '{nome}' => $clienteNome,
                '{id}'   => $interacao->id
            ]);

            $ccs    = self::normalizarEmails($data->com_copia ?? null);
            $anexos = self::normalizarAnexosEmail($data->conteudo_arquivo ?? null);

            //var_dump($data->conteudo_arquivo);

            if (!empty($data->conteudo_arquivo) && empty($anexos))
            {
                throw new Exception('Os arquivos foram selecionados, mas não foram encontrados no servidor para anexar.');
            }

            $confirma = self::enviarEmailComConfiguracao(
                $tos,
                $assunto,
                $mensagem,
                $configuracao,
                'html',
                $anexos,
                $ccs
            );

            $dadosGeolocalizacao = InteracaoAtividadeCalendarForm::registrarEmailEnviadoNaAtividade(
                $data->interacao_id,
                $destinatarios,
                $data->com_copia,
                $assunto,
                $mensagem,
                $data->conteudo_arquivo ?? null
            );            

            TTransaction::open('minicrm');

            if (empty($data->geo_latitude) || empty($data->geo_longitude) || empty($data->geo_endereco)) {
                throw new Exception('Dados de geolocalização não encontrados.');
            }

            if (empty($dadosGeolocalizacao['interacao_atividade'])) {
                throw new Exception('Atividade do e-mail não encontrada para registrar localização.');
            }

            $localizacao = new InteracaoLocalizacao;
            $localizacao->interacao_id = $data->interacao_id;
            $localizacao->interacao_atividade = $dadosGeolocalizacao['interacao_atividade'];
            $localizacao->descricao = $data->geo_endereco ?? null;
            $localizacao->latitude = $data->geo_latitude ?? null;
            $localizacao->longitude = $data->geo_longitude ?? null;
            $localizacao->dt_localizacao = date('Y-m-d H:i:55');
            $localizacao->store();

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = 6;
            $interacaoHistoricoEtapa->interacao_id = $data->interacao_id;
            $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:59');
            $interacaoHistoricoEtapa->store();      
            $interacao->etapa_interacao_id = 6;
            $interacao->store();   

            TTransaction::close();

            TToast::show('info', 'Email enviado com sucesso!', 'topRight', 'far:check-circle');        

            TApplication::loadPage('InteracaoFormView', null, ['key'=>$interacao->id]);

        }
        catch (Exception $e)
        {
            $data = $this->form->getData();
            $this->form->setData($data);
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

    } 

    public static function getInteracoesId($param)
    {
        if(!empty($param['interacao_id']))
        {
            return [ $param['interacao_id'] ];
        }
        else if(TSession::getValue('InteracaoListbuilder_datagrid_check'))
        {
            return TSession::getValue('InteracaoListbuilder_datagrid_check');
        }

        return [-1];
    }

    private static function normalizarEmails($valor)
    {
        if (empty($valor))
        {
            return [];
        }

        if (is_array($valor))
        {
            $lista = $valor;
        }
        else
        {
            $lista = explode(';', str_replace(',', ';', (string) $valor));
        }

        $emails = [];

        foreach ($lista as $item)
        {
            $email = trim((string) $item);

            if ($email !== '')
            {
                $emails[] = $email;
            }
        }

        return array_values(array_unique($emails));
    }

    private static function toBool($valor)
    {
        return in_array(strtolower(trim((string) $valor)), ['1', 't', 'true', 'y', 'yes', 's', 'sim'], true);
    }

    private static function normalizarAnexos($valor, $interacaoId)
    {
        if (empty($valor))
        {
            return [];
        }

        if (empty($interacaoId))
        {
            throw new Exception('Interação não informada para salvar os anexos.');
        }

        $lista = is_array($valor) ? $valor : [$valor];

        $interacaoId  = (int) $interacaoId;
        $destinoBase  = "anexos/interacao_$interacaoId";
        $destinoFinal = $destinoBase . '/' . $interacaoId;

        if (!is_dir($destinoBase))
        {
            if (!mkdir($destinoBase, 0777, true))
            {
                throw new Exception('Não foi possível criar a pasta base de anexos: ' . $destinoBase);
            }
        }

        if (!is_dir($destinoFinal))
        {
            if (!mkdir($destinoFinal, 0777, true))
            {
                throw new Exception('Não foi possível criar a pasta da interação: ' . $destinoFinal);
            }
        }

        if (!is_writable($destinoFinal))
        {
            throw new Exception('A pasta da interação não tem permissão de escrita: ' . $destinoFinal);
        }

        $anexos = [];

        foreach ($lista as $item)
        {
            $arquivo = '';

            if (is_string($item))
            {
                $itemDecodificado = urldecode($item);
                $json = json_decode($itemDecodificado);

                if (json_last_error() === JSON_ERROR_NONE && is_object($json))
                {
                    $arquivo = $json->newFile ?? $json->fileName ?? '';
                }
                else
                {
                    $arquivo = $item;
                }
            }
            else if (is_object($item))
            {
                $arquivo = $item->newFile ?? $item->fileName ?? $item->name ?? '';
            }
            else
            {
                $arquivo = (string) $item;
            }

            $arquivo = trim($arquivo);

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
                throw new Exception('Não foi possível copiar o anexo para a pasta da interação: ' . $nomeOriginal);
            }

            $anexos[] = [$destinoArquivo, $nomeOriginal];
        }

        return $anexos;
    }

    private static function aplicarMarcadores($texto, array $marcadores)
    {
        return str_replace(
            array_keys($marcadores),
            array_values($marcadores),
            (string) $texto
        );
    }

    private static function enviarEmailComConfiguracao($tos, $subject, $body, ConfiguracaoEmail $configuracao, $bodytype = 'html', $attachs = [], $ccs = null)
    {
        $mail = new TMail;
        $mail->setFrom(trim($configuracao->mail_from), APPLICATION_NAME);
        $mail->setSubject($subject);

        foreach (self::normalizarEmails($tos) as $to)
        {
            $mail->addAddress($to);
        }

        foreach (self::normalizarEmails($ccs) as $cc)
        {
            $mail->addCC($cc);
        }

        $usarSmtp = !empty($configuracao->smtp_host) && !empty($configuracao->smtp_port);
        $mail->setUseSmtp($usarSmtp);

        if ($usarSmtp)
        {
            $mail->SetSmtpHost($configuracao->smtp_host, $configuracao->smtp_port);

            if (self::toBool($configuracao->smtp_auth))
            {
                $chaveSecreta = 'lakjsdlkasjdalksjdlakjdlk';
                $senhaDescriptografada = openssl_decrypt(
                    $configuracao->smtp_pass,
                    'AES-128-CTR',
                    $chaveSecreta,
                    0,
                    '1234567891011121'
                );

                if ($senhaDescriptografada === false)
                {
                    throw new Exception('Não foi possível descriptografar a senha do email.');
                }

                $mail->SetSmtpUser($configuracao->smtp_user, $senhaDescriptografada);
            }
        }

        if (!empty($attachs))
        {
            foreach ($attachs as $attach)
            {
                $mail->addAttach($attach[0], $attach[1] ?? null);
            }
        }

        if ($bodytype == 'text')
        {
            $mail->setTextBody($body);
        }
        else
        {
            $mail->setHtmlBody($body);
        }

        try {
        $mail->send();
        return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

     private static function normalizarAnexosEmail($valor)
    {
        if (empty($valor))
        {
            return [];
        }

        $lista = is_array($valor) ? $valor : [$valor];
        $anexos = [];

        foreach ($lista as $item)
        {
            $arquivo = '';

            if (is_string($item))
            {
                $itemDecodificado = urldecode($item);
                $json = json_decode($itemDecodificado);

                if (json_last_error() === JSON_ERROR_NONE && is_object($json))
                {
                    $arquivo = $json->newFile ?? $json->fileName ?? $json->name ?? '';
                }
                else
                {
                    $arquivo = $item;
                }
            }
            else if (is_object($item))
            {
                $arquivo = $item->newFile ?? $item->fileName ?? $item->name ?? '';
            }
            else if (is_array($item))
            {
                $arquivo = $item['newFile'] ?? $item['fileName'] ?? $item['name'] ?? '';
            }
            else
            {
                $arquivo = (string) $item;
            }

            $arquivo = trim($arquivo);

            if ($arquivo === '')
            {
                continue;
            }

            $candidatos = [
                $arquivo,
                ltrim($arquivo, '/'),
                getcwd() . '/' . ltrim($arquivo, '/'),
            ];

            foreach ($candidatos as $caminho)
            {
                if (is_file($caminho))
                {
                    $anexos[] = [$caminho, basename($arquivo)];
                    break;
                }
            }
        }

        return $anexos;
    }

}

