<?php

class InteracaoEmailForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_InteracaoEmailForm';

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
        //  $filterVar = (Interacao::find($param['interacao_id'])->first())->cliente_id;;
         $filterVar = new TFilter("pessoa_id","=","(SELECT cliente_id FROM interacao WHERE id = ". $param['interacao_id']. ")");
        $criteria_TDBCheckList->add($filterVar); 

        TTransaction::close();

        $interacao_id = new THidden('interacao_id');
        $TDBCheckList = new TCheckList('TDBCheckList');
        $email_template_id = new TDBCombo('email_template_id', 'minicrm', 'EmailTemplate', 'id', '{titulo}','titulo asc' , $criteria_email_template_id );
        $mensagem = new THtmlEditor('mensagem');

        $email_template_id->setChangeAction(new TAction([$this,'onChangeTemplateEmail']));

        $TDBCheckList->addValidation("Selecione um Email", new TRequiredValidator()); 
        $email_template_id->addValidation("Template de email", new TRequiredValidator()); 

        $interacao_id->setValue($param["interacao_id"] ?? "");
        $email_template_id->enableSearch();
        $interacao_id->setSize(200);
        $mensagem->setSize('100%', 160);
        $email_template_id->setSize('100%');

        $TDBCheckList->setIdColumn('id');

        $column_TDBCheckList_pessoa_razao_social = $TDBCheckList->addColumn('pessoa->razao_social', "Cliente", 'center' , '20%');
        $column_TDBCheckList_nome = $TDBCheckList->addColumn('nome', "Descrição", 'center' , '20%');
        $column_TDBCheckList_email = $TDBCheckList->addColumn('email', "Email", 'center' , '20%');
        $column_TDBCheckList_telefone = $TDBCheckList->addColumn('telefone', "Telefone", 'center' , '20%');

        $TDBCheckList->setHeight(250);
        $TDBCheckList->makeScrollable();

        $TDBCheckList->fillWith('minicrm', 'PessoaContato', 'id', 'id asc' , $criteria_TDBCheckList);


        $row1 = $this->form->addFields([new TLabel("Interações:", '#F44336', '14px', null, '100%'),$interacao_id,$TDBCheckList]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Template de email:", '#F44336', '14px', null, '100%'),$email_template_id]);
        $row2->layout = [' col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Mensagem:", '#F44336', '14px', null, '100%'),$mensagem]);
        $row3->layout = [' col-sm-12'];

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

        $style = new TStyle('right-panel > .container-part[page-name=InteracaoEmailForm]');
        $style->width = '60% !important';   
        $style->show(true);

    }

    public static function onChangeTemplateEmail($param = null) 
    {
        try 
        {

            if(!empty($param['key']))
            {
                TTransaction::open('minicrm');

                $emailTemplate = new EmailTemplate($param['key']);

                TTransaction::close();

                $obj = new stdClass();
                $obj->mensagem = $emailTemplate->mensagem;

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
            $mensagem = $data->mensagem;

            if($data->interacao_id)
            {
                TTransaction::open('minicrm');
                $emailTemplate = new EmailTemplate($data->email_template_id);

                foreach($data->TDBCheckList as $contato_id)
                {
                    $contato = new PessoaContato($contato_id);
                    $interacao = new Interacao($data->interacao_id);

                    $mensagem = str_replace('{nome}', $interacao->cliente->razao_social, $mensagem);
                    $mensagem = str_replace('{id}', $interacao->id, $mensagem);

                    $emailTemplate->titulo = str_replace('{nome}', $interacao->cliente->nome, $emailTemplate->titulo);

                    if($interacao->cliente->email)
                    {
                        MailService::send($contato->email, $emailTemplate->titulo, $mensagem,  'html');    
                    }

                }
                TTransaction::close();
            }

            $this->form->setData($data);

            new TMessage('info', 'Emails enviados!');

            // veio da listagem
            if(!$data->interacao_id)
            {
                // limpa a variavel de sessao
                TSession::setValue('InteracaoListbuilder_datagrid_check', null);

                TApplication::loadPage('InteracaoList', 'onShow');
            }

            // fecha a cortina lateral
            TScript::create("Template.closeRightPanel();");

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

}

