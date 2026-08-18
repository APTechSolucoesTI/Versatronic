<?php

class RepresentanteForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'Representante';
    private static $primaryKey = 'id';
    private static $formName = 'form_RepresentanteForm';

    use BuilderMasterDetailTrait;
    use BuilderMasterDetailFieldListTrait;

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
        $this->form->setFormTitle("Cadastro de representante");

        $criteria_system_user_id = new TCriteria();
        $criteria_comissao_repres_excecao_representante_pessoa_id = new TCriteria();

        TTransaction::open(self::$database);
        $conn = TTransaction::get();

        $result = $conn->query("
            WITH RepresentanteCTE AS (
                SELECT
                    id,
                    codigo,
                    razao_social,
                    ROW_NUMBER() OVER (PARTITION BY codigo ORDER BY razao_social ASC) AS row_num
                FROM
                    representante_totvs
            ) 
            SELECT 
                id,
                razao_social
            FROM 
                RepresentanteCTE
            WHERE 
                row_num = 1
            ORDER BY
                codigo;
        ");

        $representantes = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $representantes[$row['id']] = $row['razao_social'];
        }

        TCombo::reload(self::$formName, 'representante', $representantes, true);

        // filtro dos clientes na aba exceção
        $repId = null;

        if (!empty($param['key'])) {
            $repId = (int) $param['key'];
        }

        if ($repId) {
            $filterClientesRepresentante = new TFilter(
                'id',
                'in',
                "(SELECT pessoa_id
                    FROM complemento
                WHERE deleted_at IS NULL
                    AND (
                            representante_id = {$repId}
                        OR representante_id = 0
                        OR representante_id IS NULL
                        )
                )"
            );
        } else {
            $filterClientesRepresentante = new TFilter(
                'id',
                'in',
                "(SELECT pessoa_id
                    FROM complemento
                WHERE deleted_at IS NULL
                    AND (
                            representante_id = 0
                        OR representante_id IS NULL
                        )
                )"
            );
        }

        $criteria_comissao_repres_excecao_representante_pessoa_id->add($filterClientesRepresentante);

        TTransaction::close();

        $id = new TEntry('id');
        $codigo = new TEntry('codigo');
        $representante = new TCombo('representante');
        $razao_social = new TEntry('razao_social');
        $email = new TEntry('email');
        $telefone = new TEntry('telefone');
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $inscrestadual = new TEntry('inscrestadual');
        $system_user_id = new TDBCombo('system_user_id', 'minicrm', 'SystemUsers', 'id', '{name}','name asc' , $criteria_system_user_id );
        $ativo = new TCombo('ativo');
        $cor = new TColor('cor');
        $representante_comm = new TEntry('representante_comm');
        $comissao_repres_representante_tipo_comissao = new TCombo('comissao_repres_representante_tipo_comissao');
        $comissao_repres_representante_valor = new TNumeric('comissao_repres_representante_valor', '2', ',', '.' );
        $comissao_repres_representante_id = new THidden('comissao_repres_representante_id');
        $button_adicionar_comissao_repres_representante = new TButton('button_adicionar_comissao_repres_representante');
        $comissao_repres_excecao_representante_id = new THidden('comissao_repres_excecao_representante_id[]');
        $comissao_repres_excecao_representante___row__id = new THidden('comissao_repres_excecao_representante___row__id[]');
        $comissao_repres_excecao_representante___row__data = new THidden('comissao_repres_excecao_representante___row__data[]');
        $comissao_repres_excecao_representante_pessoa_id = new TDBCombo('comissao_repres_excecao_representante_pessoa_id[]', 'minicrm', 'Pessoa', 'id', '{nome_fantasia}  - {cpf_cnpj}','nome_fantasia asc' , $criteria_comissao_repres_excecao_representante_pessoa_id );
        $comissao_repres_excecao_representante_tipo_comissao = new TCombo('comissao_repres_excecao_representante_tipo_comissao[]');
        $comissao_repres_excecao_representante_valor = new TNumeric('comissao_repres_excecao_representante_valor[]', '2', ',', '.' );
        $comissao_repres_excecao_representante_ativo = new TCombo('comissao_repres_excecao_representante_ativo[]');
        $this->fieldList_6a425e9093a9b = new TFieldList();

        $this->fieldList_6a425e9093a9b->addField(null, $comissao_repres_excecao_representante_id, []);
        $this->fieldList_6a425e9093a9b->addField(null, $comissao_repres_excecao_representante___row__id, ['uniqid' => true]);
        $this->fieldList_6a425e9093a9b->addField(null, $comissao_repres_excecao_representante___row__data, []);
        $this->fieldList_6a425e9093a9b->addField(new TLabel("Cliente", null, '14px', null), $comissao_repres_excecao_representante_pessoa_id, ['width' => '44%']);
        $this->fieldList_6a425e9093a9b->addField(new TLabel("Tipo de Comissão", null, '14px', null), $comissao_repres_excecao_representante_tipo_comissao, ['width' => '25%']);
        $this->fieldList_6a425e9093a9b->addField(new TLabel("Comissão", null, '14px', null), $comissao_repres_excecao_representante_valor, ['width' => '23%']);
        $this->fieldList_6a425e9093a9b->addField(new TLabel("Ativo", null, '14px', null), $comissao_repres_excecao_representante_ativo, ['width' => '30%']);

        $this->fieldList_6a425e9093a9b->width = '100%';
        $this->fieldList_6a425e9093a9b->setFieldPrefix('comissao_repres_excecao_representante');
        $this->fieldList_6a425e9093a9b->name = 'fieldList_6a425e9093a9b';

        $this->criteria_fieldList_6a425e9093a9b = new TCriteria();
        $this->default_item_fieldList_6a425e9093a9b = new stdClass();

        $this->form->addField($comissao_repres_excecao_representante_id);
        $this->form->addField($comissao_repres_excecao_representante___row__id);
        $this->form->addField($comissao_repres_excecao_representante___row__data);
        $this->form->addField($comissao_repres_excecao_representante_pessoa_id);
        $this->form->addField($comissao_repres_excecao_representante_tipo_comissao);
        $this->form->addField($comissao_repres_excecao_representante_valor);
        $this->form->addField($comissao_repres_excecao_representante_ativo);

        $this->fieldList_6a425e9093a9b->setRemoveAction(null, 'fas:times #dd5a43', "Excluír");

        $representante->setChangeAction(new TAction([$this,'onGet']));

        $codigo->addValidation("Codigo", new TRequiredValidator()); 
        $razao_social->addValidation("Razão Social", new TRequiredValidator()); 
        $comissao_repres_excecao_representante_pessoa_id->addValidation("Pessoa id", new TRequiredListValidator()); 

        $button_adicionar_comissao_repres_representante->setAction(new TAction([$this, 'onAddDetailComissaoRepresRepresentante'],['static' => 1]), "Adicionar");
        $button_adicionar_comissao_repres_representante->addStyleClass('btn-default');
        $button_adicionar_comissao_repres_representante->setImage('fas:plus #2ecc71');
        $id->setEditable(false);
        $codigo->setEditable(false);
        $representante_comm->setEditable(false);

        $ativo->addItems(["S"=>" Sim","N"=>" Não"]);
        $comissao_repres_excecao_representante_ativo->addItems(["S"=>"Sim","N"=>"Não"]);
        $comissao_repres_representante_tipo_comissao->addItems(["P"=>"Percentual","R"=>"Valor Monetário"]);
        $comissao_repres_excecao_representante_tipo_comissao->addItems(["P"=>"Percentual","R"=>"Valor Monetário"]);

        $email->setMaxLength(40);
        $codigo->setMaxLength(15);
        $telefone->setMaxLength(20);
        $cpf_cnpj->setMaxLength(20);
        $inscrestadual->setMaxLength(20);

        $ativo->enableSearch();
        $representante->enableSearch();
        $system_user_id->enableSearch();
        $comissao_repres_representante_tipo_comissao->enableSearch();
        $comissao_repres_excecao_representante_ativo->enableSearch();
        $comissao_repres_excecao_representante_pessoa_id->enableSearch();
        $comissao_repres_excecao_representante_tipo_comissao->enableSearch();

        $id->setSize('100%');
        $cor->setSize('100%');
        $email->setSize('100%');
        $ativo->setSize('100%');
        $codigo->setSize('100%');
        $telefone->setSize('100%');
        $cpf_cnpj->setSize('100%');
        $razao_social->setSize('100%');
        $representante->setSize('100%');
        $inscrestadual->setSize('100%');
        $system_user_id->setSize('100%');
        $representante_comm->setSize('100%');
        $comissao_repres_representante_id->setSize(200);
        $comissao_repres_representante_valor->setSize('100%');
        $comissao_repres_representante_tipo_comissao->setSize('100%');
        $comissao_repres_excecao_representante_valor->setSize('100%');
        $comissao_repres_excecao_representante_ativo->setSize('100%');
        $comissao_repres_excecao_representante_pessoa_id->setSize('100%');
        $comissao_repres_excecao_representante_tipo_comissao->setSize('100%');

        $button_adicionar_comissao_repres_representante->id = '6a4256a85fa47';

        try
        {
            $reflection = new ReflectionObject($comissao_repres_excecao_representante_pessoa_id);

            while ($reflection && !$reflection->hasProperty('validations'))
            {
                $reflection = $reflection->getParentClass();
            }

            if ($reflection && $reflection->hasProperty('validations'))
            {
                $property = $reflection->getProperty('validations');
                $property->setAccessible(true);
                $property->setValue($comissao_repres_excecao_representante_pessoa_id, []);
            }

            $comissao_repres_excecao_representante_pessoa_id->setProperty('required', false);
        }
        catch (Exception $e)
        {
        }

        $this->form->appendPage("Representante");

        $this->form->addFields([new THidden('current_tab')]);
        $this->form->setTabFunction("$('[name=current_tab]').val($(this).attr('data-current_page'));");

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Codigo:", null, '14px', null, '100%'),$codigo],[new TLabel("Representante Totvs:", null, '14px', null),$representante]);
        $row1->layout = ['col-sm-3','col-sm-3',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Razão Social:", '#ff0000', '14px', null, '100%'),$razao_social],[new TLabel("Email:", null, '14px', null, '100%'),$email],[new TLabel("Telefone:", null, '14px', null, '100%'),$telefone]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([new TLabel("CNPJ:", null, '14px', null, '100%'),$cpf_cnpj],[new TLabel("inscrição estadual:", null, '14px', null, '100%'),$inscrestadual]);
        $row3->layout = [' col-sm-4',' col-sm-4'];

        $row4 = $this->form->addFields([new TLabel("Usuario do Sistema:", null, '14px', null, '100%'),$system_user_id],[new TLabel("Ativo:", null, '14px', null, '100%'),$ativo],[new TLabel("Cor:", null, '14px', null, '100%'),$cor]);
        $row4->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];

        $this->form->appendPage("Comissão");

        $this->detailFormComissaoRepresRepresentante = new BootstrapFormBuilder('detailFormComissaoRepresRepresentante');
        $this->detailFormComissaoRepresRepresentante->setProperty('style', 'border:none; box-shadow:none; width:100%;');

        $this->detailFormComissaoRepresRepresentante->setProperty('class', 'form-horizontal builder-detail-form');

        $row5 = $this->detailFormComissaoRepresRepresentante->addFields([new TFormSeparator("Comissão do Representante", '#333', '18', '#eee')]);
        $row5->layout = [' col-sm-12'];

        $row6 = $this->detailFormComissaoRepresRepresentante->addFields([new TLabel("Representante", null, '14px', null),$representante_comm]);
        $row6->layout = ['col-sm-6'];

        $row7 = $this->detailFormComissaoRepresRepresentante->addFields([new TLabel("Tipo de Comissão", null, '14px', null),$comissao_repres_representante_tipo_comissao],[new TLabel("Comissão", null, '14px', null, '100%'),$comissao_repres_representante_valor,$comissao_repres_representante_id]);
        $row7->layout = ['col-sm-6','col-sm-6'];

        $row8 = $this->detailFormComissaoRepresRepresentante->addFields([$button_adicionar_comissao_repres_representante]);
        $row8->layout = [' col-sm-12'];

        $row9 = $this->detailFormComissaoRepresRepresentante->addFields([new THidden('comissao_repres_representante__row__id')]);
        $this->comissao_repres_representante_criteria = new TCriteria();

        $this->comissao_repres_representante_list = new BootstrapDatagridWrapper(new TDataGrid);
        $this->comissao_repres_representante_list->generateHiddenFields();
        $this->comissao_repres_representante_list->setId('comissao_repres_representante_list');

        $this->comissao_repres_representante_list->disableDefaultClick();
        $this->comissao_repres_representante_list->style = 'width:100%';
        $this->comissao_repres_representante_list->class .= ' table-bordered';

        $column_comissao_repres_representante_representante_razao_social = new TDataGridColumn('representante->razao_social', "Representante", 'left');
        $column_comissao_repres_representante_valor = new TDataGridColumn('valor', "Comissão", 'left');

        $column_comissao_repres_representante__row__data = new TDataGridColumn('__row__data', '', 'center');
        $column_comissao_repres_representante__row__data->setVisibility(false);

        $action_onEditDetailComissaoRepres = new TDataGridAction(array('RepresentanteForm', 'onEditDetailComissaoRepres'));
        $action_onEditDetailComissaoRepres->setUseButton(false);
        $action_onEditDetailComissaoRepres->setButtonClass('btn btn-default btn-sm');
        $action_onEditDetailComissaoRepres->setLabel("Editar");
        $action_onEditDetailComissaoRepres->setImage('far:edit #478fca');
        $action_onEditDetailComissaoRepres->setFields(['__row__id', '__row__data']);

        $this->comissao_repres_representante_list->addAction($action_onEditDetailComissaoRepres);
        $action_onDeleteDetailComissaoRepres = new TDataGridAction(array('RepresentanteForm', 'onDeleteDetailComissaoRepres'));
        $action_onDeleteDetailComissaoRepres->setUseButton(false);
        $action_onDeleteDetailComissaoRepres->setButtonClass('btn btn-default btn-sm');
        $action_onDeleteDetailComissaoRepres->setLabel("Excluir");
        $action_onDeleteDetailComissaoRepres->setImage('fas:trash-alt #dd5a43');
        $action_onDeleteDetailComissaoRepres->setFields(['__row__id', '__row__data']);

        $this->comissao_repres_representante_list->addAction($action_onDeleteDetailComissaoRepres);

        $this->comissao_repres_representante_list->addColumn($column_comissao_repres_representante_representante_razao_social);
        $this->comissao_repres_representante_list->addColumn($column_comissao_repres_representante_valor);

        $this->comissao_repres_representante_list->addColumn($column_comissao_repres_representante__row__data);

        $this->comissao_repres_representante_list->createModel();
        $tableResponsiveDiv = new TElement('div');
        $tableResponsiveDiv->class = 'table-responsive';
        $tableResponsiveDiv->add($this->comissao_repres_representante_list);
        $this->detailFormComissaoRepresRepresentante->addContent([$tableResponsiveDiv]);
        $row10 = $this->form->addFields([$this->detailFormComissaoRepresRepresentante]);
        $row10->layout = [' col-sm-12'];

        $row11 = $this->form->addContent([new TFormSeparator("Exceção", '#333', '18', '#eee')]);
        $row12 = $this->form->addFields([$this->fieldList_6a425e9093a9b]);
        $row12->layout = ['col-sm-12'];

        TScript::create(<<<'JS'
        (function () {

            function normalizarTipo(tipo) {
                tipo = (tipo || 'P').toString().trim().toUpperCase();
                return tipo === 'R' ? 'R' : 'P';
            }

            function limparSimbolo(valor) {
                return (valor || '')
                    .toString()
                    .replace(/R\$/gi, '')
                    .replace(/%/g, '')
                    .trim();
            }

            function limparCampo($campo) {
                if (!$campo.length) {
                    return;
                }

                $campo.val(limparSimbolo($campo.val()));
            }

            function aplicarCampo($campo, tipo) {
                if (!$campo.length) {
                    return;
                }

                tipo = normalizarTipo(tipo);

                var valor = limparSimbolo($campo.val());

                if (valor === '') {
                    $campo.val('');
                    return;
                }

                if (tipo === 'R') {
                    $campo.val('R$ ' + valor);
                } else {
                    $campo.val(valor + ' %');
                }
            }

            function aplicarPadrao() {
                var $tipo = $('[name="comissao_repres_representante_tipo_comissao"]');
                var $valor = $('[name="comissao_repres_representante_valor"]');

                aplicarCampo($valor, $tipo.val());
            }

            function aplicarExcecoes() {
                var $tipos = $('[name="comissao_repres_excecao_representante_tipo_comissao[]"]');
                var $valores = $('[name="comissao_repres_excecao_representante_valor[]"]');

                $valores.each(function (index) {
                    aplicarCampo($(this), $tipos.eq(index).val());
                });
            }

            function aplicarTodos() {
                aplicarPadrao();
                aplicarExcecoes();
            }

            function limparTodos() {
                limparCampo($('[name="comissao_repres_representante_valor"]'));

                $('[name="comissao_repres_excecao_representante_valor[]"]').each(function () {
                    limparCampo($(this));
                });
            }

            window.aplicarMascaraComissaoRepresentante = aplicarTodos;
            window.limparMascaraComissaoRepresentante = limparTodos;

            $(document).off('focus.comissaoValor');
            $(document).on('focus.comissaoValor',
                '[name="comissao_repres_representante_valor"], [name="comissao_repres_excecao_representante_valor[]"]',
                function () {
                    limparCampo($(this));
                }
            );

            $(document).off('blur.comissaoValor');
            $(document).on('blur.comissaoValor',
                '[name="comissao_repres_representante_valor"], [name="comissao_repres_excecao_representante_valor[]"]',
                function () {
                    var $campo = $(this);

                    if ($campo.attr('name') === 'comissao_repres_representante_valor') {
                        var tipo = $('[name="comissao_repres_representante_tipo_comissao"]').val();
                        aplicarCampo($campo, tipo);
                    } else {
                        var index = $('[name="comissao_repres_excecao_representante_valor[]"]').index($campo);
                        var tipo = $('[name="comissao_repres_excecao_representante_tipo_comissao[]"]').eq(index).val();
                        aplicarCampo($campo, tipo);
                    }
                }
            );

            $(document).off('change.comissaoTipo');
            $(document).on('change.comissaoTipo',
                '[name="comissao_repres_representante_tipo_comissao"], [name="comissao_repres_excecao_representante_tipo_comissao[]"]',
                function () {
                    limparTodos();
                    aplicarTodos();
                }
            );

            // Antes de clicar em adicionar/salvar/editar, tira R$ e % para o PHP receber número limpo
            $(document).off('mousedown.comissaoLimpar');
            $(document).on('mousedown.comissaoLimpar', 'form[name="form_RepresentanteForm"] button, form[name="form_RepresentanteForm"] a', function () {
                limparTodos();

                setTimeout(aplicarTodos, 700);
            });

            setTimeout(aplicarTodos, 300);
            setTimeout(aplicarTodos, 900);
            setTimeout(aplicarTodos, 1500);

        })();
        JS);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave'],['static' => 1]), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['RepresentanteList', 'onShow']), 'fas:arrow-left #000000');
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

        $style = new TStyle('right-panel > .container-part[page-name=RepresentanteForm]');
        $style->width = '60% !important';   
        $style->show(true);

    }

    public static function onGet($param = null) 
    {
        try 
        {
            TTransaction::open(self::$database);
            if($param['key'])
            {
                $representanteTotvs = RepresentanteTotvs::where('id','=',$param['key'])->first();

                $object = new stdClass();
                $object->codigo = $representanteTotvs->codigo;
                $object->razao_social = $representanteTotvs->razao_social;
                $object->email = $representanteTotvs->email;
                $object->telefone = $representanteTotvs->telefone;
                $object->inscrestadual = $representanteTotvs->inscrestadual;
                $object->cpf_cnpj = $representanteTotvs->cpf_cnpj;
                $object->representante_comm = $representanteTotvs->razao_social;

                TForm::sendData(self::$formName, $object);

            }
            TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public  function onAddDetailComissaoRepresRepresentante($param = null) 
    {
        try
        {
            $data = $this->form->getData();

/*

            $__row__id = !empty($data->comissao_repres_representante__row__id) ? $data->comissao_repres_representante__row__id : 'b'.uniqid();

            TTransaction::open(self::$database);

            $grid_data = new ComissaoRepres();
            $grid_data->__row__id = $__row__id;
            $grid_data->representante_comm = $data->representante_comm;
            $grid_data->tipo_comissao = $data->comissao_repres_representante_tipo_comissao;
            $grid_data->valor = $data->comissao_repres_representante_valor;
            $grid_data->id = $data->comissao_repres_representante_id;

            $__row__data = array_merge($grid_data->toArray(), (array)$grid_data->getVirtualData());
            $__row__data['__row__id'] = $__row__id;
            $__row__data['__display__']['representante_comm'] =  $param['representante_comm'] ?? null;
            $__row__data['__display__']['tipo_comissao'] =  $param['comissao_repres_representante_tipo_comissao'] ?? null;
            $__row__data['__display__']['valor'] =  $param['comissao_repres_representante_valor'] ?? null;
            $__row__data['__display__']['id'] =  $param['comissao_repres_representante_id'] ?? null;

            $grid_data->__row__data = base64_encode(serialize((object)$__row__data));
            $row = $this->comissao_repres_representante_list->addItem($grid_data);
            $row->id = $grid_data->__row__id;

            TDataGrid::replaceRowById('comissao_repres_representante_list', $grid_data->__row__id, $row);

            TTransaction::close();

*/

            $__row__id = !empty($data->comissao_repres_representante__row__id) ? $data->comissao_repres_representante__row__id : 'b'.uniqid();

            TTransaction::open(self::$database);

            $grid_data = new ComissaoRepres();
            $grid_data->__row__id = $__row__id;

            $grid_data->representante_comm = $data->representante_comm;

            $representante_grid = new Representante;
            $representante_grid->razao_social = $data->representante_comm;

            $grid_data->representante = $representante_grid;

            $grid_data->valor_comissao = $data->comissao_repres_representante_tipo_comissao ?? 'P';
            $grid_data->valor_comissao = strtoupper(trim((string) $grid_data->valor_comissao));
            $grid_data->valor_comissao = ($grid_data->valor_comissao === 'R') ? 'R' : 'P';

            $grid_data->valor = $data->comissao_repres_representante_valor;
            $grid_data->id = $data->comissao_repres_representante_id;

            $__row__data = array_merge($grid_data->toArray(), (array)$grid_data->getVirtualData());
            $__row__data['__row__id'] = $__row__id;
            $__row__data['__display__']['representante_comm'] =  $param['representante_comm'] ?? null;
            $__row__data['__display__']['valor_comissao'] =  $grid_data->valor_comissao;
            $__row__data['__display__']['valor'] =  $param['comissao_repres_representante_valor'] ?? null;
            $__row__data['__display__']['id'] =  $param['comissao_repres_representante_id'] ?? null;

            $grid_data->__row__data = base64_encode(serialize((object)$__row__data));
            $row = $this->comissao_repres_representante_list->addItem($grid_data);
            $row->id = $grid_data->__row__id;

            TDataGrid::replaceRowById('comissao_repres_representante_list', $grid_data->__row__id, $row);

            TTransaction::close();

/*

            $data = new stdClass;
            $data->representante_comm = '';
            $data->comissao_repres_representante_tipo_comissao = '';
            $data->comissao_repres_representante_valor = '';
            $data->comissao_repres_representante_id = '';
            $data->comissao_repres_representante__row__id = '';

*/
            $formData = $this->form->getData();
            $data = new stdClass;
            $data->representante_comm = $formData->representante_comm ?? null;
            $data->comissao_repres_representante_valor = '';
            $data->comissao_repres_representante_id = '';
            $data->comissao_repres_representante__row__id = '';

            TForm::sendData(self::$formName, $data);
            TScript::create("
               var element = $('#6a4256a85fa47');
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

    public static function onEditDetailComissaoRepres($param = null) 
    {
        try
        {

            $__row__data = unserialize(base64_decode($param['__row__data']));
            $__row__data->__display__ = is_array($__row__data->__display__) ? (object) $__row__data->__display__ : $__row__data->__display__;
            $fireEvents = true;
            $aggregate = false;

/*

            $data = new stdClass;
            $data->representante_comm = $__row__data->__display__->representante_comm ?? null;
            $data->comissao_repres_representante_tipo_comissao = $__row__data->__display__->tipo_comissao ?? null;
            $data->comissao_repres_representante_valor = $__row__data->__display__->valor ?? null;
            $data->comissao_repres_representante_id = $__row__data->__display__->id ?? null;
            $data->comissao_repres_representante__row__id = $__row__data->__row__id;

*/      

            $data = new stdClass;
            $data->representante_comm = $__row__data->__display__->representante_comm ?? null;
            $data->comissao_repres_representante_tipo_comissao = $__row__data->__display__->valor_comissao ?? 'P';
            $data->comissao_repres_representante_valor = $__row__data->__display__->valor ?? null;
            $data->comissao_repres_representante_id = $__row__data->__display__->id ?? null;
            $data->comissao_repres_representante__row__id = $__row__data->__row__id;

            TForm::sendData(self::$formName, $data, $aggregate, $fireEvents);
            TScript::create("
               var element = $('#6a4256a85fa47');
               if(!element.attr('add')){
                   element.attr('add', base64_encode(element.html()));
               }
               element.html(\"<span><i class='far fa-edit' style='color:#478fca;padding-right:4px;'></i>Editar</span>\");
               if(!element.attr('edit')){
                   element.attr('edit', base64_encode(element.html()));
               }
            ");

            TScript::create("
                setTimeout(function() {
                    if (window.aplicarMascaraComissaoRepresentante) {
                        window.aplicarMascaraComissaoRepresentante();
                    }
                }, 300);
            ");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public static function onDeleteDetailComissaoRepres($param = null) 
    {
        try
        {

            /*

            $__row__data = unserialize(base64_decode($param['__row__data']));

            $data = new stdClass;
            $data->representante_comm = '';
            $data->comissao_repres_representante_tipo_comissao = '';
            $data->comissao_repres_representante_valor = '';
            $data->comissao_repres_representante_id = '';
            $data->comissao_repres_representante__row__id = '';

            */

            $__row__data = unserialize(base64_decode($param['__row__data']));

            $data = new stdClass;
            $formData = TSession::getValue(self::$formName . '_data');

            $data->representante_comm = $formData->representante_comm ?? null;

            $data->comissao_repres_representante_valor = '';
            $data->comissao_repres_representante_id = '';
            $data->comissao_repres_representante__row__id = '';

            TForm::sendData(self::$formName, $data);

            TDataGrid::removeRowById('comissao_repres_representante_list', $__row__data->__row__id);
            TScript::create("
               var element = $('#6a4256a85fa47');
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

            $object = new Representante(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $camposExcecao = [
                'comissao_repres_excecao_representante_id',
                'comissao_repres_excecao_representante___row__id',
                'comissao_repres_excecao_representante___row__data',
                'comissao_repres_excecao_representante_pessoa_id',
                'comissao_repres_excecao_representante_tipo_comissao',
                'comissao_repres_excecao_representante_valor',
                'comissao_repres_excecao_representante_ativo',
            ];

            $pessoasExcecao = $param['comissao_repres_excecao_representante_pessoa_id'] ?? [];

            if (!is_array($pessoasExcecao))
            {
                $pessoasExcecao = [];
            }

            $indicesValidosExcecao = [];

            foreach ($pessoasExcecao as $indice => $pessoaId)
            {
                if ($pessoaId !== null && trim((string) $pessoaId) !== '')
                {
                    $indicesValidosExcecao[] = $indice;
                }
            }

            foreach ($camposExcecao as $campo)
            {
                $valores = $param[$campo] ?? ($_POST[$campo] ?? []);

                if (is_array($valores))
                {
                    $novo = [];

                    foreach ($indicesValidosExcecao as $indice)
                    {
                        $novo[] = $valores[$indice] ?? null;
                    }

                    $param[$campo] = $novo;
                    $_POST[$campo] = $novo;
                    $data->{$campo} = $novo;
                }
            }

            $this->form->setData($data);

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $comissao_repres_excecao_representante_items = $this->storeItems('ComissaoRepresExcecao', 'representante_id', $object, $this->fieldList_6a425e9093a9b, function($masterObject, $detailObject){ 

                $tipoComissao = $detailObject->tipo_comissao ?? $detailObject->valor_comissao ?? 'P';
                $tipoComissao = strtoupper(trim((string) $tipoComissao));

                $detailObject->valor_comissao = ($tipoComissao === 'R') ? 'R' : 'P';

                if (isset($detailObject->tipo_comissao)) {
                    unset($detailObject->tipo_comissao);
                }

            }, $this->criteria_fieldList_6a425e9093a9b); 

            $comissao_repres_representante_items = $this->storeMasterDetailItems('ComissaoRepres', 'representante_id', 'comissao_repres_representante', $object, $param['comissao_repres_representante_list___row__data'] ?? [], $this->form, $this->comissao_repres_representante_list, function($masterObject, $detailObject){ 

                $tipoComissao = $detailObject->valor_comissao ?? $detailObject->tipo_comissao ?? 'P';
                $tipoComissao = strtoupper(trim((string) $tipoComissao));

                $detailObject->valor_comissao = ($tipoComissao === 'R') ? 'R' : 'P';

                if (isset($detailObject->tipo_comissao)) {
                    unset($detailObject->tipo_comissao);
                }

            }, $this->comissao_repres_representante_criteria); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('RepresentanteList', 'onShow', $loadPageParam); 

                        TScript::create("Template.closeRightPanel();");
            TForm::sendData(self::$formName, (object)['id' => $object->id]);

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

                $object = new Representante($key); // instantiates the Active Record 

                $comissaoAtual = ComissaoRepres::where('representante_id', '=', $object->id)->first();

                $object->comissao_repres_representante_valor_comissao = 'P';

                if ($comissaoAtual && !empty($comissaoAtual->valor_comissao)) {
                    $tipoComissao = strtoupper(trim((string) $comissaoAtual->valor_comissao));
                    $object->comissao_repres_representante_valor_comissao = ($tipoComissao === 'R') ? 'R' : 'P';
                }
                $this->fieldList_6a425e9093a9b_items = $this->loadItems('ComissaoRepresExcecao', 'representante_id', $object, $this->fieldList_6a425e9093a9b, function($masterObject, $detailObject, $objectItems){ 

                    $tipoComissao = $detailObject->valor_comissao ?? 'P';
                    $tipoComissao = strtoupper(trim((string) $tipoComissao));

                    $detailObject->valor_comissao = ($tipoComissao === 'R') ? 'R' : 'P';

                    if (is_object($objectItems)) {
                        $objectItems->tipo_comissao = $detailObject->valor_comissao;
                    }

                }, $this->criteria_fieldList_6a425e9093a9b); 

                $comissao_repres_representante_items = $this->loadMasterDetailItems('ComissaoRepres', 'representante_id', 'comissao_repres_representante', $object, $this->form, $this->comissao_repres_representante_list, $this->comissao_repres_representante_criteria, function($masterObject, $detailObject, $objectItems){ 

                    //code here

                }); 

                TScript::create("$('label:contains(\"Representante Totvs:\")').hide();");
                TScript::create("$(\"[name='representante']\").closest('.fb-inline-field-container').hide()");
                TEntry::disableField(self::$formName, 'razao_social');
                TEntry::disableField(self::$formName, 'email');
                TEntry::disableField(self::$formName, 'telefone');
                TEntry::disableField(self::$formName, 'inscrestadual');
                TEntry::disableField(self::$formName, 'cpf_cnpj');
                $object->representante_comm = $object->razao_social;

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

        $this->fieldList_6a425e9093a9b->addHeader();
        $this->fieldList_6a425e9093a9b->addDetail($this->default_item_fieldList_6a425e9093a9b);

        $this->fieldList_6a425e9093a9b->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    }

    public function onShow($param = null)
    {
        $this->fieldList_6a425e9093a9b->addHeader();
        $this->fieldList_6a425e9093a9b->addDetail($this->default_item_fieldList_6a425e9093a9b);

        $this->fieldList_6a425e9093a9b->addCloneAction(null, 'fas:plus #69aa46', "Clonar");

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

