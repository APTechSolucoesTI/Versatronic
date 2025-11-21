<?php

class InteracaoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'Interacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoForm';

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
        $this->form->setFormTitle("Cadastro de Interações");

        $criteria_vendedor_id = new TCriteria();
        $criteria_etapa_interacao_id = new TCriteria();
        $criteria_tipo_interacao_id = new TCriteria();
        $criteria_origem_contato_id = new TCriteria();
        $criteria_cliente_id = new TCriteria();

        TTransaction::open(self::$database);
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->load();
        if($representante){
            $filter = new TFilter('id', 'in', "(SELECT pessoa_id FROM complemento WHERE deleted_at is null AND representante_id in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");
            $criteria_cliente_id->add($filter);  
        }
        TTransaction::close();

        $id = new TEntry('id');
        $vendedor_id = new TDBCombo('vendedor_id', 'minicrm', 'Representante', 'id', '{razao_social}','razao_social asc' , $criteria_vendedor_id );
        $etapa_interacao_id = new TDBCombo('etapa_interacao_id', 'minicrm', 'EtapaInteracao', 'id', '{nome}','nome asc' , $criteria_etapa_interacao_id );
        $tipo_interacao_id = new TDBCombo('tipo_interacao_id', 'minicrm', 'TipoInteracao', 'id', '{nome}','nome asc' , $criteria_tipo_interacao_id );
        $origem_contato_id = new TDBCombo('origem_contato_id', 'minicrm', 'OrigemContato', 'id', '{nome}','nome asc' , $criteria_origem_contato_id );
        $data_inicio = new TDate('data_inicio');
        $data_fechamento_esperada = new TDate('data_fechamento_esperada');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'minicrm', 'Pessoa', 'id', 'razao_social','razao_social asc' , $criteria_cliente_id );
        $cliente_cpf_cnpj = new TEntry('cliente_cpf_cnpj');
        $cidade_uf = new TEntry('cidade_uf');

        $cliente_id->setChangeAction(new TAction([$this,'onSelectClient']));

        $vendedor_id->addValidation("vendedor", new TRequiredValidator()); 
        $etapa_interacao_id->addValidation("Etapa", new TRequiredValidator()); 
        $origem_contato_id->addValidation("Origem do contato", new TRequiredValidator()); 
        $data_inicio->addValidation("Data de início", new TRequiredValidator()); 
        $cliente_id->addValidation("Cliente", new TRequiredValidator()); 

        $cliente_id->setMinLength(3);
        $data_inicio->setValue(date('d/m/Y'));
        $etapa_interacao_id->setValue(EtapaNegociacao::PROSPECTAR);

        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_fechamento_esperada->setDatabaseMask('yyyy-mm-dd');

        $id->setEditable(false);
        $cidade_uf->setEditable(false);
        $cliente_cpf_cnpj->setEditable(false);

        $data_inicio->setMask('dd/mm/yyyy');
        $cliente_id->setMask('{razao_social}');
        $data_fechamento_esperada->setMask('dd/mm/yyyy');

        $vendedor_id->enableSearch();
        $tipo_interacao_id->enableSearch();
        $origem_contato_id->enableSearch();
        $etapa_interacao_id->enableSearch();

        $id->setSize('100%');
        $cidade_uf->setSize('100%');
        $cliente_id->setSize('100%');
        $vendedor_id->setSize('100%');
        $data_inicio->setSize('100%');
        $cliente_cpf_cnpj->setSize('100%');
        $tipo_interacao_id->setSize('100%');
        $origem_contato_id->setSize('100%');
        $etapa_interacao_id->setSize('100%');
        $data_fechamento_esperada->setSize('100%');

        $row1 = $this->form->addContent([new TFormSeparator("Informações Gerais", '#333', '14', '#eee')]);
        $row2 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Representante:", '#ff0000', '14px', null, '100%'),$vendedor_id],[new TLabel("Etapa:", '#ff0000', '14px', null, '100%'),$etapa_interacao_id],[new TLabel("Tipo de interação:", '#FF0000', '14px', null, '100%'),$tipo_interacao_id]);
        $row2->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row3 = $this->form->addFields([new TLabel("Origem do contato:", '#ff0000', '14px', null, '100%'),$origem_contato_id],[new TLabel("Data de início:", '#ff0000', '14px', null, '100%'),$data_inicio],[new TLabel("Data esperada de fechamento:", null, '14px', null, '100%'),$data_fechamento_esperada]);
        $row3->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addContent([new TFormSeparator("Informações do Cliente", '#333', '14', '#eee')]);
        $row5 = $this->form->addFields([new TLabel("Cliente:", '#ff0000', '14px', null, '100%'),$cliente_id],[new TLabel("Documento:", null, '14px', null, '100%'),$cliente_cpf_cnpj],[new TLabel("Cidade/UF:", null, '14px', null, '100%'),$cidade_uf]);
        $row5->layout = ['col-sm-4',' col-sm-4',' col-sm-4'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['InteracaoList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=InteracaoForm]');
        $style->width = '70% !important';   
        $style->show(true);

    }

    public static function onSelectClient($param = null) 
    {
        try 
        {
            if($param['cliente_id'])
            {
                TTransaction::open(self::$database);
                $pessoa = Pessoa::find($param['cliente_id']);

                $object = new stdClass();
                $object->cliente_cpf_cnpj = $pessoa->cpf_cnpj;
                $object->cidade_uf = $pessoa->cidade_uf;

                TForm::sendData(self::$formName, $object);
                TTransaction::close();
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Interacao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if($data->id && !NegociacaoService::podeEditar($data->id))
            {
                throw new Exception('Não é possível editar esse registro!');
            }

            $object->mes = date('m');
            $object->ano = date('Y');

            $object->store(); // save the object 

            if(!$data->id)
            {
                $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
                $interacaoHistoricoEtapa->etapa_interacao_id = $object->etapa_interacao_id;
                $interacaoHistoricoEtapa->interacao_id = $object->id;
                $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:s');
                $interacaoHistoricoEtapa->store();
            }

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            if(!empty($object->id))
            {
                $loadPageParam["key"] = $object->id;
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('InteracaoFormView', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();"); 

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

                $object = new Interacao($key); // instantiates the Active Record 

                                $object->cliente_cpf_cnpj = $object->cliente->cpf_cnpj;

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

        TTransaction::open(self::$database);
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
            $object = new stdClass();
            $object->vendedor_id = $representante->id;
            TDBCombo::disableField(self::$formName, 'vendedor_id');
            $this->form->getField('vendedor_id')->setEditable(FALSE);
            TForm::sendData(self::$formName, $object);
        }
        TTransaction::close();
    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

