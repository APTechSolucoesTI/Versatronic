<?php

class PessoaForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'Pessoa';
    private static $primaryKey = 'id';
    private static $formName = 'form_PessoaForm';

    use BuilderMasterDetailTrait;

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
        $this->form->setFormTitle("Cadastro de Cliente");

        $criteria_tipo_pessoa_id = new TCriteria();
        $criteria_representante_id = new TCriteria();
        $criteria_categoria_cliente_id = new TCriteria();
        $criteria_pessoa_endereco_pessoa_cidade_id = new TCriteria();

        $id = new TEntry('id');
        $tipo_pessoa_id = new TDBCombo('tipo_pessoa_id', 'minicrm', 'TipoPessoa', 'id', '{nome}','nome asc' , $criteria_tipo_pessoa_id );
        $buscar_cnpj = new TButton('buscar_cnpj');
        $razao_social = new TEntry('razao_social');
        $fone = new TEntry('fone');
        $email = new TEntry('email');
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $rg_ie = new TEntry('rg_ie');
        $ativo = new TCombo('ativo');
        $representante_id = new TDBCombo('representante_id', 'minicrm', 'Representante', 'id', '{razao_social}','razao_social asc' , $criteria_representante_id );
        $categoria_cliente_id = new TDBCombo('categoria_cliente_id', 'minicrm', 'CategoriaCliente', 'id', '{nome}','nome asc' , $criteria_categoria_cliente_id );
        $pessoa_endereco_pessoa_nome = new TEntry('pessoa_endereco_pessoa_nome');
        $pessoa_endereco_pessoa_cep = new TEntry('pessoa_endereco_pessoa_cep');
        $button_buscar_pessoa_endereco_pessoa = new TButton('button_buscar_pessoa_endereco_pessoa');
        $pessoa_endereco_pessoa_principal = new TCheckButton('pessoa_endereco_pessoa_principal');
        $pessoa_endereco_pessoa_cidade_id = new TDBCombo('pessoa_endereco_pessoa_cidade_id', 'minicrm', 'Cidade', 'id', '{nome}','nome asc' , $criteria_pessoa_endereco_pessoa_cidade_id );
        $pessoa_endereco_pessoa_id = new THidden('pessoa_endereco_pessoa_id');
        $pessoa_endereco_pessoa_bairro = new TEntry('pessoa_endereco_pessoa_bairro');
        $pessoa_endereco_pessoa_rua = new TEntry('pessoa_endereco_pessoa_rua');
        $pessoa_endereco_pessoa_numero = new TEntry('pessoa_endereco_pessoa_numero');
        $pessoa_endereco_pessoa_complemento = new TEntry('pessoa_endereco_pessoa_complemento');
        $button_adicionar_pessoa_endereco_pessoa = new TButton('button_adicionar_pessoa_endereco_pessoa');

        $tipo_pessoa_id->setChangeAction(new TAction([$this,'onSelectTipo']));

        $cpf_cnpj->setExitAction(new TAction([$this,'onInsertCNPJ']));

        $tipo_pessoa_id->addValidation("Tipo de pessoa", new TRequiredValidator()); 
        $razao_social->addValidation("Razão social", new TRequiredValidator()); 
        $ativo->addValidation("Ativo não informado", new TRequiredValidator()); 
        $representante_id->addValidation("Representante não informado!", new TRequiredValidator()); 
        $categoria_cliente_id->addValidation("categoria não preenchida", new TRequiredValidator()); 
        $email->addValidation("Email invalido!", new TEmailValidator(), []); 

        $id->setEditable(false);
        $fone->setMask('(99) 99999-9999');
        $ativo->addItems(["S"=>" Sim","N"=>" Não"]);
        $pessoa_endereco_pessoa_principal->setUseSwitch(true, 'blue');
        $pessoa_endereco_pessoa_principal->setIndexValue("S");
        $pessoa_endereco_pessoa_principal->setInactiveIndexValue("N");
        $ativo->setValue('S');
        $tipo_pessoa_id->setValue('2');
        $pessoa_endereco_pessoa_principal->setValue('S');

        $button_buscar_pessoa_endereco_pessoa->setAction(new TAction([$this, 'onBuscarCEP']), "Buscar");
        $button_adicionar_pessoa_endereco_pessoa->setAction(new TAction([$this, 'onAddDetailPessoaEnderecoPessoa'],['static' => 1]), "Adicionar");
        $buscar_cnpj->setAction(new TAction(['BuscarCNPJForm', 'onShow'],['campo' => '["razao_social", "fone" ,"cpf_cnpj", "pessoa_endereco_pessoa_cep" ,"pessoa_endereco_pessoa_cidade_id", "pessoa_endereco_pessoa_id", "pessoa_endereco_pessoa_bairro", "pessoa_endereco_pessoa_rua" ,"pessoa_endereco_pessoa_numero" ,"pessoa_endereco_pessoa_complemento"]',"form" => self::$formName,"page" => "PessoaForm"]), "Buscar");

        $buscar_cnpj->addStyleClass('btn-default');
        $button_buscar_pessoa_endereco_pessoa->addStyleClass('btn-default');
        $button_adicionar_pessoa_endereco_pessoa->addStyleClass('btn-default');

        $buscar_cnpj->setImage('fas:search #000000');
        $button_buscar_pessoa_endereco_pessoa->setImage('fas:search #000000');
        $button_adicionar_pessoa_endereco_pessoa->setImage('fas:plus #2ecc71');

        $ativo->enableSearch();
        $tipo_pessoa_id->enableSearch();
        $representante_id->enableSearch();
        $categoria_cliente_id->enableSearch();
        $pessoa_endereco_pessoa_cidade_id->enableSearch();

        $fone->setMaxLength(255);
        $rg_ie->setMaxLength(30);
        $email->setMaxLength(255);
        $cpf_cnpj->setMaxLength(20);
        $razao_social->setMaxLength(500);
        $pessoa_endereco_pessoa_cep->setMaxLength(10);
        $pessoa_endereco_pessoa_rua->setMaxLength(500);
        $pessoa_endereco_pessoa_nome->setMaxLength(255);
        $pessoa_endereco_pessoa_numero->setMaxLength(20);
        $pessoa_endereco_pessoa_bairro->setMaxLength(500);
        $pessoa_endereco_pessoa_complemento->setMaxLength(500);

        $id->setSize('100%');
        $fone->setSize('100%');
        $email->setSize('100%');
        $rg_ie->setSize('100%');
        $ativo->setSize('100%');
        $cpf_cnpj->setSize('100%');
        $razao_social->setSize('100%');
        $representante_id->setSize('100%');
        $categoria_cliente_id->setSize('100%');
        $pessoa_endereco_pessoa_id->setSize(200);
        $pessoa_endereco_pessoa_rua->setSize('100%');
        $pessoa_endereco_pessoa_nome->setSize('100%');
        $tipo_pessoa_id->setSize('calc(100% - 100px)');
        $pessoa_endereco_pessoa_bairro->setSize('100%');
        $pessoa_endereco_pessoa_numero->setSize('100%');
        $pessoa_endereco_pessoa_cidade_id->setSize('100%');
        $pessoa_endereco_pessoa_complemento->setSize('100%');
        $pessoa_endereco_pessoa_cep->setSize('calc(100% - 100px)');

        $button_adicionar_pessoa_endereco_pessoa->id = '66e45588668c9';

        $tab_66e84664306d9 = new BootstrapFormBuilder('tab_66e84664306d9');
        $this->tab_66e84664306d9 = $tab_66e84664306d9;
        $tab_66e84664306d9->setProperty('style', 'border:none; box-shadow:none;');

        $tab_66e84664306d9->appendPage("Dados Gerais");

        $tab_66e84664306d9->addFields([new THidden('current_tab_tab_66e84664306d9')]);
        $tab_66e84664306d9->setTabFunction("$('[name=current_tab_tab_66e84664306d9]').val($(this).attr('data-current_page'));");

        $row1 = $tab_66e84664306d9->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id]);
        $row1->layout = [' col-sm-3'];

        $row2 = $tab_66e84664306d9->addFields([new TLabel("Tipo de pessoa:", '#ff0000', '14px', null, '100%'),$tipo_pessoa_id,$buscar_cnpj],[new TLabel("Razão social:", '#ff0000', '14px', null, '100%'),$razao_social]);
        $row2->layout = [' col-sm-4',' col-sm-8'];

        $row3 = $tab_66e84664306d9->addFields([new TLabel("Telefone:", null, '14px', null, '100%'),$fone],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
        $row3->layout = [' col-sm-6',' col-sm-6'];

        $row4 = $tab_66e84664306d9->addContent([new TFormSeparator("", '#333', '18', '#eee')]);
        $row5 = $tab_66e84664306d9->addFields([new TLabel("CNPJ:", '#FF0000', '14px', null, '100%'),$cpf_cnpj],[new TLabel("Inscrição Estadual:", null, '14px', null, '100%'),$rg_ie],[new TLabel("Ativo:", null, '14px', null, '100%'),$ativo]);
        $row5->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row6 = $tab_66e84664306d9->addFields([new TLabel("Representante: ", '#FF0000', '14px', null, '100%'),$representante_id],[new TLabel("Categoria:", '#FF0000', '14px', null, '100%'),$categoria_cliente_id]);
        $row6->layout = [' col-sm-6',' col-sm-6'];

        $tab_66e84664306d9->appendPage("Endereço");

        $this->detailFormPessoaEnderecoPessoa = new BootstrapFormBuilder('detailFormPessoaEnderecoPessoa');
        $this->detailFormPessoaEnderecoPessoa->setProperty('style', 'border:none; box-shadow:none; width:100%;');

        $this->detailFormPessoaEnderecoPessoa->setProperty('class', 'form-horizontal builder-detail-form');

        $row7 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Nome:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_nome],[new TLabel("Cep:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_cep,$button_buscar_pessoa_endereco_pessoa],[new TLabel("Principal:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_principal]);
        $row7->layout = [' col-sm-6',' col-sm-4','col-sm-2'];

        $row8 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Cidade:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_cidade_id,$pessoa_endereco_pessoa_id],[new TLabel("Bairro:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_bairro]);
        $row8->layout = [' col-sm-6',' col-sm-6'];

        $row9 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Rua:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_rua],[new TLabel("Numero:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_numero]);
        $row9->layout = ['col-sm-6','col-sm-6'];

        $row10 = $this->detailFormPessoaEnderecoPessoa->addFields([new TLabel("Complemento:", null, '14px', null, '100%'),$pessoa_endereco_pessoa_complemento]);
        $row10->layout = [' col-sm-12'];

        $row11 = $this->detailFormPessoaEnderecoPessoa->addFields([$button_adicionar_pessoa_endereco_pessoa]);
        $row11->layout = [' col-sm-12'];

        $row12 = $this->detailFormPessoaEnderecoPessoa->addFields([new THidden('pessoa_endereco_pessoa__row__id')]);
        $this->pessoa_endereco_pessoa_criteria = new TCriteria();

        $this->pessoa_endereco_pessoa_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->pessoa_endereco_pessoa_list->generateHiddenFields();
        $this->pessoa_endereco_pessoa_list->setId('pessoa_endereco_pessoa_list');

        $this->pessoa_endereco_pessoa_list->style = 'width:100%';
        $this->pessoa_endereco_pessoa_list->class .= ' table-bordered';

        $column_pessoa_endereco_pessoa_nome = new TDataGridColumn('nome', "Nome", 'left');
        $column_pessoa_endereco_pessoa_cidade_nome = new TDataGridColumn('cidade->nome', "Cidade", 'left');
        $column_pessoa_endereco_pessoa_cep = new TDataGridColumn('cep', "Cep", 'left');
        $column_pessoa_endereco_pessoa_rua = new TDataGridColumn('rua', "Rua", 'left');
        $column_pessoa_endereco_pessoa_numero = new TDataGridColumn('numero', "Numero", 'left');
        $column_pessoa_endereco_pessoa_bairro = new TDataGridColumn('bairro', "Bairro", 'left');
        $column_pessoa_endereco_pessoa_complemento = new TDataGridColumn('complemento', "Complemento", 'left');
        $column_pessoa_endereco_pessoa_principal = new TDataGridColumn('principal', "Principal", 'left');

        $column_pessoa_endereco_pessoa__row__data = new TDataGridColumn('__row__data', '', 'center');
        $column_pessoa_endereco_pessoa__row__data->setVisibility(false);

        $action_onEditDetailPessoaEndereco = new TDataGridAction(array('PessoaForm', 'onEditDetailPessoaEndereco'));
        $action_onEditDetailPessoaEndereco->setUseButton(false);
        $action_onEditDetailPessoaEndereco->setButtonClass('btn btn-default btn-sm');
        $action_onEditDetailPessoaEndereco->setLabel("Editar");
        $action_onEditDetailPessoaEndereco->setImage('far:edit #478fca');
        $action_onEditDetailPessoaEndereco->setFields(['__row__id', '__row__data']);

        $this->pessoa_endereco_pessoa_list->addAction($action_onEditDetailPessoaEndereco);
        $action_onDeleteDetailPessoaEndereco = new TDataGridAction(array('PessoaForm', 'onDeleteDetailPessoaEndereco'));
        $action_onDeleteDetailPessoaEndereco->setUseButton(false);
        $action_onDeleteDetailPessoaEndereco->setButtonClass('btn btn-default btn-sm');
        $action_onDeleteDetailPessoaEndereco->setLabel("Excluir");
        $action_onDeleteDetailPessoaEndereco->setImage('fas:trash-alt #dd5a43');
        $action_onDeleteDetailPessoaEndereco->setFields(['__row__id', '__row__data']);

        $this->pessoa_endereco_pessoa_list->addAction($action_onDeleteDetailPessoaEndereco);

        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_nome);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_cidade_nome);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_cep);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_rua);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_numero);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_bairro);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_complemento);
        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa_principal);

        $this->pessoa_endereco_pessoa_list->addColumn($column_pessoa_endereco_pessoa__row__data);

        $this->pessoa_endereco_pessoa_list->createModel();
        $tableResponsiveDiv = new TElement('div');
        $tableResponsiveDiv->class = 'table-responsive';
        $tableResponsiveDiv->add($this->pessoa_endereco_pessoa_list);
        $this->detailFormPessoaEnderecoPessoa->addContent([$tableResponsiveDiv]);
        $row13 = $tab_66e84664306d9->addFields([$this->detailFormPessoaEnderecoPessoa]);
        $row13->layout = ['col-sm-12'];

        $row14 = $this->form->addFields([$tab_66e84664306d9]);
        $row14->layout = ['col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=PessoaForm]');
        $style->width = '60% !important';   
        $style->show(true);

    }

    public static function onInsertCNPJ($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);
            $cpf_cnpj = preg_replace("/[^0-9.]/", "",$param['cpf_cnpj']);
            $pessoa = Pessoa::where('cpf_cnpj','=',$cpf_cnpj)->first();
            if($pessoa){
                TToast::show("warning", "Documento já cadastrado.", "topRight", "");
                $data = new stdClass();
                $data->cpf_cnpj = null;
                TForm::sendData(self::$formName, $data);
            }

            TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onSelectTipo($param = null) 
    {
        try 
        {
            if($param['tipo_pessoa_id'] === '1')
            {

                TScript::create("$('label:contains(\"CNPJ:\")').html('CPF:')");
                TEntry::changeMask(self::$formName, 'cpf_cnpj', '999.999.999-99');

                TScript::create("$('label:contains(\"Inscrição Estadual:\")').html('RG:')");

                TButton::disableField(self::$formName, 'buscar_cnpj');

            }
            else  if($param['tipo_pessoa_id'] === '2')
            {
                TScript::create("$('label:contains(\"CPF:\")').html('CNPJ:')");
                TEntry::changeMask(self::$formName, 'cpf_cnpj', '99.999.999/9999-99');

                TScript::create("$('label:contains(\"RG:\")').html('Inscrição Estadual:')");
                TButton::enableField(self::$formName, 'buscar_cnpj');
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public  function onBuscarCEP($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);
            $dadosCEP = CEPService::get($param['pessoa_endereco_pessoa_cep']);
            TTransaction::close();

            if($dadosCEP)
            {
                $data = new stdClass;
                $data->pessoa_endereco_pessoa_cidade_id = $dadosCEP->cidade_id;
                $data->pessoa_endereco_pessoa_bairro = $dadosCEP->bairro;
                $data->pessoa_endereco_pessoa_rua = $dadosCEP->rua;    
                TForm::sendData(self::$formName, $data);
            }
            else
            {
                throw new Exception('CEP não encontrado');
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public  function onAddDetailPessoaEnderecoPessoa($param = null) 
    {
        try
        {
            $data = $this->form->getData();

            $errors = [];
            $requiredFields = [];
            $requiredFields[] = ['label'=>"Endereço principal não informado", 'name'=>"pessoa_endereco_pessoa_principal", 'class'=>'TRequiredValidator', 'value'=>[]];
            foreach($requiredFields as $requiredField)
            {
                try
                {
                    (new $requiredField['class'])->validate($requiredField['label'], $data->{$requiredField['name']}, $requiredField['value']);
                }
                catch(Exception $e)
                {
                    $errors[] = $e->getMessage() . '.';
                }
             }
             if(count($errors) > 0)
             {
                 throw new Exception(implode('<br>', $errors));
             }

            $__row__id = !empty($data->pessoa_endereco_pessoa__row__id) ? $data->pessoa_endereco_pessoa__row__id : 'b'.uniqid();

            TTransaction::open(self::$database);

            $grid_data = new PessoaEndereco();
            $grid_data->__row__id = $__row__id;
            $grid_data->nome = $data->pessoa_endereco_pessoa_nome;
            $grid_data->cep = $data->pessoa_endereco_pessoa_cep;
            $grid_data->principal = $data->pessoa_endereco_pessoa_principal;
            $grid_data->cidade_id = $data->pessoa_endereco_pessoa_cidade_id;
            $grid_data->id = $data->pessoa_endereco_pessoa_id;
            $grid_data->bairro = $data->pessoa_endereco_pessoa_bairro;
            $grid_data->rua = $data->pessoa_endereco_pessoa_rua;
            $grid_data->numero = $data->pessoa_endereco_pessoa_numero;
            $grid_data->complemento = $data->pessoa_endereco_pessoa_complemento;

            $__row__data = array_merge($grid_data->toArray(), (array)$grid_data->getVirtualData());
            $__row__data['__row__id'] = $__row__id;
            $__row__data['__display__']['nome'] =  $param['pessoa_endereco_pessoa_nome'] ?? null;
            $__row__data['__display__']['cep'] =  $param['pessoa_endereco_pessoa_cep'] ?? null;
            $__row__data['__display__']['principal'] =  $param['pessoa_endereco_pessoa_principal'] ?? null;
            $__row__data['__display__']['cidade_id'] =  $param['pessoa_endereco_pessoa_cidade_id'] ?? null;
            $__row__data['__display__']['id'] =  $param['pessoa_endereco_pessoa_id'] ?? null;
            $__row__data['__display__']['bairro'] =  $param['pessoa_endereco_pessoa_bairro'] ?? null;
            $__row__data['__display__']['rua'] =  $param['pessoa_endereco_pessoa_rua'] ?? null;
            $__row__data['__display__']['numero'] =  $param['pessoa_endereco_pessoa_numero'] ?? null;
            $__row__data['__display__']['complemento'] =  $param['pessoa_endereco_pessoa_complemento'] ?? null;

            $grid_data->__row__data = base64_encode(serialize((object)$__row__data));
            $row = $this->pessoa_endereco_pessoa_list->addItem($grid_data);
            $row->id = $grid_data->__row__id;

            TDataGrid::replaceRowById('pessoa_endereco_pessoa_list', $grid_data->__row__id, $row);

            TTransaction::close();

            $data = new stdClass;
            $data->pessoa_endereco_pessoa_nome = '';
            $data->pessoa_endereco_pessoa_cep = '';
            $data->pessoa_endereco_pessoa_principal = 'S';
            $data->pessoa_endereco_pessoa_cidade_id = '';
            $data->pessoa_endereco_pessoa_id = '';
            $data->pessoa_endereco_pessoa_bairro = '';
            $data->pessoa_endereco_pessoa_rua = '';
            $data->pessoa_endereco_pessoa_numero = '';
            $data->pessoa_endereco_pessoa_complemento = '';
            $data->pessoa_endereco_pessoa__row__id = '';

            TForm::sendData(self::$formName, $data);
            TScript::create("
               var element = $('#66e45588668c9');
               if(typeof element.attr('add') != 'undefined')
               {
                   element.html(base64_decode(element.attr('add')));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }

    public static function onEditDetailPessoaEndereco($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));
            $__row__data->__display__ = is_array($__row__data->__display__) ? (object) $__row__data->__display__ : $__row__data->__display__;
            $fireEvents = true;
            $aggregate = false;

            $data = new stdClass;
            $data->pessoa_endereco_pessoa_nome = $__row__data->__display__->nome ?? null;
            $data->pessoa_endereco_pessoa_cep = $__row__data->__display__->cep ?? null;
            $data->pessoa_endereco_pessoa_principal = $__row__data->__display__->principal ?? null;
            $data->pessoa_endereco_pessoa_cidade_id = $__row__data->__display__->cidade_id ?? null;
            $data->pessoa_endereco_pessoa_id = $__row__data->__display__->id ?? null;
            $data->pessoa_endereco_pessoa_bairro = $__row__data->__display__->bairro ?? null;
            $data->pessoa_endereco_pessoa_rua = $__row__data->__display__->rua ?? null;
            $data->pessoa_endereco_pessoa_numero = $__row__data->__display__->numero ?? null;
            $data->pessoa_endereco_pessoa_complemento = $__row__data->__display__->complemento ?? null;
            $data->pessoa_endereco_pessoa__row__id = $__row__data->__row__id;

            TForm::sendData(self::$formName, $data, $aggregate, $fireEvents);
            TScript::create("
               var element = $('#66e45588668c9');
               if(!element.attr('add')){
                   element.attr('add', base64_encode(element.html()));
               }
               element.html(\"<span><i class='far fa-edit' style='color:#478fca;padding-right:4px;'></i>Editar</span>\");
               if(!element.attr('edit')){
                   element.attr('edit', base64_encode(element.html()));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public static function onDeleteDetailPessoaEndereco($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));

            $data = new stdClass;
            $data->pessoa_endereco_pessoa_nome = '';
            $data->pessoa_endereco_pessoa_cep = '';
            $data->pessoa_endereco_pessoa_principal = '';
            $data->pessoa_endereco_pessoa_cidade_id = '';
            $data->pessoa_endereco_pessoa_id = '';
            $data->pessoa_endereco_pessoa_bairro = '';
            $data->pessoa_endereco_pessoa_rua = '';
            $data->pessoa_endereco_pessoa_numero = '';
            $data->pessoa_endereco_pessoa_complemento = '';
            $data->pessoa_endereco_pessoa__row__id = '';

            TForm::sendData(self::$formName, $data);

            TDataGrid::removeRowById('pessoa_endereco_pessoa_list', $__row__data->__row__id);
            TScript::create("
               var element = $('#66e45588668c9');
               if(typeof element.attr('add') != 'undefined')
               {
                   element.html(base64_decode(element.attr('add')));
               }
            ");

        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Pessoa(); // create an empty object 

            $data = $this->form->getData(); // get form data as array

            $cpf_cnpj = preg_replace("/[^0-9.]/", "",$data->cpf_cnpj);

            $validate = Pessoa::where('cpf_cnpj','=',  $cpf_cnpj)->first();

            if($validate){
                throw new Exception('Documento já cadastrado.');
            }else{
                $object->fromArray( (array) $data); // load the object with data

                $pessoa = Pessoa::where('cpf_cnpj','=',$param['cpf_cnpj'])->first();
                if($pessoa){
                    throw new Exception('Documento já cadastrado.');
                }

                $object->origem = 'AP';

            $object->store(); // save the object 

            $grupo = new PessoaGrupo();
            $grupo->grupo_id = Grupo::CLIENTE;
            $grupo->pessoa_id =  $object->id;
            $grupo->store();

            $complemento = new Complemento();
            $complemento->pessoa_id =  $object->id;
            $complemento->representante_id = $data->representante_id;
            $complemento->store();

            TForm::sendData(self::$formName, (object)['id' => $object->id]);

            $pessoa_endereco_pessoa_items = $this->storeMasterDetailItems('PessoaEndereco', 'pessoa_id', 'pessoa_endereco_pessoa', $object, $param['pessoa_endereco_pessoa_list___row__data'] ?? [], $this->form, $this->pessoa_endereco_pessoa_list, function($masterObject, $detailObject){ 

                //code here

            }, $this->pessoa_endereco_pessoa_criteria); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data  */  
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

             TApplication::loadPage('ClienteList', 'onShow');
                        TScript::create("Template.closeRightPanel();"); 

            }
        }
        catch (Exception $e) // in case of exception
        {

            var_dump($e->getMessage() . '</br>' . $e->getLine());
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

                $object = new Pessoa($key); // instantiates the Active Record 

                $pessoa_endereco_pessoa_items = $this->loadMasterDetailItems('PessoaEndereco', 'pessoa_id', 'pessoa_endereco_pessoa', $object, $this->form, $this->pessoa_endereco_pessoa_list, $this->pessoa_endereco_pessoa_criteria, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }); 

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

