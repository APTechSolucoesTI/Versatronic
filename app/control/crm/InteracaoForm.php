<?php

class InteracaoForm extends TWindow
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
        parent::setSize(0.70, null);
        parent::setTitle("Cadastro de Interações");
        parent::setProperty('class', 'window_modal');

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
        $criteria_origem_contato_id = new TCriteria();
        $criteria_cliente_id = new TCriteria();
        $criteria_cliente_cpf_cnpj = new TCriteria();

        $filterVar = TSession::getValue("userid");
        $criteria_vendedor_id->add(new TFilter('system_user_id', '=', $filterVar)); 
        $filterVar = Grupo::CLIENTE;
        $criteria_cliente_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_id = '{$filterVar}')")); 

        TTransaction::open(self::$database);

        $userid = TSession::getValue('userid');
        $usuariosQueVeemTudo = [1, 16, 13, 20, 4];

        if (!in_array($userid, $usuariosQueVeemTudo))
        {
            $representante = Representante::where('system_user_id', '=', $userid)->first();

            if ($representante)
            {
                $repId = (int) $representante->id;

                $filter = new TFilter(
                    'id',
                    'in',
                    "(SELECT pessoa_id
                        FROM complemento
                    WHERE deleted_at IS NULL
                        AND (
                                representante_id = {$repId}
                            OR representante_id = 0
                        )
                    )"
                );

                $criteria_cliente_id->add($filter);
            }
            else
            {
                $filter = new TFilter(
                    'id',
                    'in',
                    "(SELECT pessoa_id
                        FROM complemento
                    WHERE deleted_at IS NULL
                        AND representante_id = 0
                    )"
                );

                $criteria_cliente_id->add($filter);
            }
        }

        TTransaction::close();

        $id = new TEntry('id');
        $vendedor_id = new TDBCombo('vendedor_id', 'minicrm', 'Representante', 'id', '{razao_social}','razao_social asc' , $criteria_vendedor_id );
        $etapa_interacao_id = new TDBCombo('etapa_interacao_id', 'minicrm', 'EtapaInteracao', 'id', '{nome}','nome asc' , $criteria_etapa_interacao_id );
        $origem_contato_id = new TDBCombo('origem_contato_id', 'minicrm', 'OrigemContato', 'id', '{nome}','nome asc' , $criteria_origem_contato_id );
        $data_inicio = new TDate('data_inicio');
        $data_fechamento_esperada = new TDate('data_fechamento_esperada');
        $cliente_id = new TDBUniqueSearch('cliente_id', 'minicrm', 'Pessoa', 'id', 'nome_fantasia','nome_fantasia asc' , $criteria_cliente_id );
        $cliente_cpf_cnpj = new TDBCombo('cliente_cpf_cnpj', 'minicrm', 'Pessoa', 'cpf_cnpj', '{cpf_cnpj}','cpf_cnpj asc' , $criteria_cliente_cpf_cnpj );
        $cidade_uf = new TEntry('cidade_uf');
        $cliente_categoria_cliente_nome = new TEntry('cliente_categoria_cliente_nome');

        $cliente_id->setChangeAction(new TAction([$this,'onSelectClient']));
        $cliente_cpf_cnpj->setChangeAction(new TAction([$this,'onSelectCpfCnpj']));

        $vendedor_id->addValidation("Representante", new TRequiredValidator()); 
        $etapa_interacao_id->addValidation("Etapa", new TRequiredValidator()); 
        $origem_contato_id->addValidation("Origem do contato", new TRequiredValidator()); 
        $data_inicio->addValidation("Data de início", new TRequiredValidator()); 
        $cliente_id->addValidation("Cliente", new TRequiredValidator()); 
        $cliente_cpf_cnpj->addValidation("Documento", new TRequiredValidator()); 

        $cliente_id->setMinLength(2);
        $cliente_id->setFilterColumns(["nome_fantasia"]);
        $etapa_interacao_id->setValue('5');
        $data_inicio->setValue(date('d/m/Y'));

        $data_inicio->setDatabaseMask('yyyy-mm-dd');
        $data_fechamento_esperada->setDatabaseMask('yyyy-mm-dd');

        $data_inicio->setMask('dd/mm/yyyy');
        $cliente_id->setMask('{nome_fantasia}');
        $data_fechamento_esperada->setMask('dd/mm/yyyy');

        $id->setEditable(false);
        $cidade_uf->setEditable(false);
        $vendedor_id->setEditable(false);
        $cliente_categoria_cliente_nome->setEditable(false);

        $vendedor_id->enableSearch();
        $cliente_cpf_cnpj->enableSearch();
        $origem_contato_id->enableSearch();
        $etapa_interacao_id->enableSearch();

        $id->setSize('100%');
        $cidade_uf->setSize('100%');
        $cliente_id->setSize('100%');
        $vendedor_id->setSize('100%');
        $data_inicio->setSize('100%');
        $cliente_cpf_cnpj->setSize('100%');
        $origem_contato_id->setSize('100%');
        $etapa_interacao_id->setSize('100%');
        $data_fechamento_esperada->setSize('100%');
        $cliente_categoria_cliente_nome->setSize('33%');

        $row1 = $this->form->addContent([new TFormSeparator("Informações Gerais", '#333', '14', '#eee')]);
        $row2 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Representante:", '#ff0000', '14px', null, '100%'),$vendedor_id],[new TLabel("Etapa:", '#ff0000', '14px', null, '100%'),$etapa_interacao_id]);
        $row2->layout = [' col-sm-2',' col-sm-5',' col-sm-5'];

        $row3 = $this->form->addFields([new TLabel("Origem do contato:", '#ff0000', '14px', null, '100%'),$origem_contato_id],[new TLabel("Data de início:", '#ff0000', '14px', null, '100%'),$data_inicio],[new TLabel("Data esperada de fechamento:", null, '14px', null, '100%'),$data_fechamento_esperada]);
        $row3->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addContent([new TFormSeparator("Informações do Cliente", '#333', '14', '#eee')]);
        $row5 = $this->form->addFields([new TLabel("Cliente:", '#ff0000', '14px', null, '100%'),$cliente_id],[new TLabel("CPF/CNPJ:", '#FF0000', '14px', null, '100%'),$cliente_cpf_cnpj],[new TLabel("Cidade/UF:", null, '14px', null, '100%'),$cidade_uf],[new TLabel("Classificação:", null, '14px', null, '100%'),$cliente_categoria_cliente_nome]);
        $row5->layout = ['col-sm-4','col-sm-4','col-sm-4',' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['InteracaoList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        parent::add($this->form);

    }

    public static function onSelectClient($param = null) 
    {
        try 
        {
           if (!empty($param['cliente_id']))
            {
                TTransaction::open(self::$database);
                $pessoa = Pessoa::find($param['cliente_id']);

                if (!empty($pessoa))
                {
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
                    $object->cliente_categoria_cliente_nome = $categoriaNome;

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
                TTransaction::open(self::$database);

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
                    $object->cliente_categoria_cliente_nome = $categoriaNome;

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

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $cidade_nome = null;
            $estado_nome = null;

            $object = new Interacao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $pe = PessoaEndereco::where('pessoa_id', '=', $data->cliente_id)->where('principal', '=', 'S')->first();
            if (!empty($pe)) {
                $cidade = Cidade::find($pe->cidade_id);
                if (!empty($cidade)) {
                    $cidade_nome = $cidade->nome;
                    $estado = Estado::find($cidade->estado_id);
                    if (!empty($estado)) {
                        $estado_nome = $estado->nome;                        
                    }
                }
            }

            if($data->id && !NegociacaoService::podeEditar($data->id))
            {
                throw new Exception('Não é possível editar esse registro!');
            }

            $object->cidade = $cidade_nome;
            $object->estado = $estado_nome;
            $object->mes = date('m');
            $object->ano = date('Y');

            $object->tipo_interacao_id = TipoInteracao::COMPLETA;
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
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Interacao($key); // instantiates the Active Record 

                                $object->cliente_cpf_cnpj = $object->cliente->cpf_cnpj;
                $object->cliente_categoria_cliente_nome = $object->cliente->categoria_cliente->nome;

                $this->form->setData($object); // fill the form 

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

