<?php

class ClienteFormView extends TPage
{
    protected $form; // form
    private static $database = 'minicrm';
    private static $activeRecord = 'Pessoa';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Pessoa';

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

        $pessoa = new Pessoa($param['key']);
        // define the form title
        $this->form->setFormTitle("Consulta de Pessoa");

        $label8 = new TLabel("Nome:", '', '12px', 'B', '100%');
        $text5 = new TTextDisplay($pessoa->razao_social, '', '12px', '');
        $label10 = new TLabel("Tipo de pessoa:", '', '12px', 'B', '100%');
        $text2 = new TTextDisplay($pessoa->tipo_pessoa->nome, '', '12px', '');
        $label12 = new TLabel("Categoria:", '', '12px', 'B', '100%');
        $text3 = new TTextDisplay($pessoa->categoria_cliente->nome, '', '12px', '');
        $label14 = new TLabel(new TImage('far:id-card #000000')."Documento:", '', '12px', 'B', '100%');
        $text6 = new TTextDisplay($pessoa->cpf_cnpj, '', '12px', '');
        $label16 = new TLabel(new TImage('far:envelope #000000')."Email:", '', '12px', 'B', '100%');
        $text9 = new TTextDisplay($pessoa->email, '', '12px', '');
        $label18 = new TLabel(new TImage('fas:phone-alt #000000')."Fone:", '', '12px', 'B', '100%');
        $text8 = new TTextDisplay($pessoa->fone, '', '12px', '');
        $label20 = new TLabel(new TImage('fas:comment-alt #000000')."Obs:", '', '12px', 'B', '100%');
        $text7 = new TTextDisplay($pessoa->obs, '', '12px', '');
        $label22 = new TLabel("Grupos:", '', '12px', 'B', '100%');
        $grupos = new TTextDisplay($pessoa->pessoa_grupo_grupo_to_string, '', '12px', '');
        $label24 = new TLabel("Representante:", '', '12px', '', '100%');
        $text24 = new TTextDisplay($pessoa->complemento_representante_to_string, '', '12px', '');
        $label25 = new TLabel("Transportadora:", '', '12px', '', '100%');
        $text26 = new TTextDisplay($pessoa->complemento_transportadora_to_string, '', '12px', '');
        $label27 = new TLabel("Transportadora 2:", '', '12px', '', '100%');
        $text15 = new TTextDisplay($pessoa->complemento_transportadora1_to_string, '', '12px', '');
        $action2 = new TActionLink("Adicionar contato", new TAction(['PessoaContatoForm', 'onShow'], ['pessoa_id'=> $pessoa->id]), '', '12px', '', 'fas:plus #4CAF50');

        $text6->enableToggleVisibility(false);

        $action2->class = 'btn btn-default';

        $row1 = $this->form->addFields([$label8,$text5],[$label10,$text2],[$label12,$text3]);
        $row1->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row2 = $this->form->addFields([$label14,$text6],[$label16,$text9],[$label18,$text8]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([$label20,$text7],[$label22,$grupos]);
        $row3->layout = [' col-sm-6',' col-sm-6'];

        $row4 = $this->form->addFields([$label24,$text24],[$label25,$text26],[$label27,$text15]);
        $row4->layout = ['col-sm-3',' col-sm-3',' col-sm-3'];

        $tab_622940daf9f3b = new BootstrapFormBuilder('tab_622940daf9f3b');
        $this->tab_622940daf9f3b = $tab_622940daf9f3b;
        $tab_622940daf9f3b->setProperty('style', 'border:none; box-shadow:none;');

        $tab_622940daf9f3b->appendPage("Endereços");

        $tab_622940daf9f3b->addFields([new THidden('current_tab_tab_622940daf9f3b')]);
        $tab_622940daf9f3b->setTabFunction("$('[name=current_tab_tab_622940daf9f3b]').val($(this).attr('data-current_page'));");

        $this->pessoa_endereco_pessoa_id_list = new TQuickGrid;
        $this->pessoa_endereco_pessoa_id_list->style = 'width:100%';
        $this->pessoa_endereco_pessoa_id_list->disableDefaultClick();

        $column_nome = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Nome", 'nome', 'left');
        $column_cidade_nome = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Cidade", 'cidade->nome', 'left');
        $column_cep = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Cep", 'cep', 'left');
        $column_rua = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Rua", 'rua', 'left');
        $column_numero = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Numero", 'numero', 'left');
        $column_bairro = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Bairro", 'bairro', 'left');
        $column_complemento = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Complemento", 'complemento', 'left');
        $column_principal_transformed = $this->pessoa_endereco_pessoa_id_list->addQuickColumn("Principal", 'principal', 'left');

        $column_principal_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if($value === true || $value == 't' || $value === 1 || $value == '1' || $value == 's' || $value == 'S' || $value == 'T')
            {
                return 'Sim';
            }
            elseif($value === false || $value == 'f' || $value === 0 || $value == '0' || $value == 'n' || $value == 'N' || $value == 'F')   
            {
                return 'Não';
            }

            return $value;

        });

        $this->pessoa_endereco_pessoa_id_list->createModel();

        $criteria_pessoa_endereco_pessoa_id = new TCriteria();
        $criteria_pessoa_endereco_pessoa_id->add(new TFilter('pessoa_id', '=', $pessoa->id));

        $criteria_pessoa_endereco_pessoa_id->setProperty('order', 'id desc');

        $pessoa_endereco_pessoa_id_items = PessoaEndereco::getObjects($criteria_pessoa_endereco_pessoa_id);

        $this->pessoa_endereco_pessoa_id_list->addItems($pessoa_endereco_pessoa_id_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->pessoa_endereco_pessoa_id_list));

        $tab_622940daf9f3b->addContent([$panel]);

        $tab_622940daf9f3b->appendPage("Contatos");
        $row5 = $tab_622940daf9f3b->addFields([$action2],[],[]);
        $row5->layout = ['col-sm-3','col-sm-3','col-sm-6'];

        $this->pessoa_contato_pessoa_id_list = new TQuickGrid;
        $this->pessoa_contato_pessoa_id_list->style = 'width:100%';
        $this->pessoa_contato_pessoa_id_list->disableDefaultClick();

        $action_onEdit = new TDataGridAction(array('PessoaContatoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('fas:edit #000000');
        $action_onEdit->setField('id');

        $action_onEdit->setParameter('key', '{id}');
        $this->pessoa_contato_pessoa_id_list->addAction($action_onEdit);

        $column_nome1 = $this->pessoa_contato_pessoa_id_list->addQuickColumn("Nome", 'nome', 'left');
        $column_email = $this->pessoa_contato_pessoa_id_list->addQuickColumn("Email", 'email', 'left');
        $column_telefone = $this->pessoa_contato_pessoa_id_list->addQuickColumn("Telefone", 'telefone', 'left');
        $column_obs = $this->pessoa_contato_pessoa_id_list->addQuickColumn("Obs", 'obs', 'left');

        $this->pessoa_contato_pessoa_id_list->createModel();

        $criteria_pessoa_contato_pessoa_id = new TCriteria();
        $criteria_pessoa_contato_pessoa_id->add(new TFilter('pessoa_id', '=', $pessoa->id));

        $criteria_pessoa_contato_pessoa_id->setProperty('order', 'id desc');

        $pessoa_contato_pessoa_id_items = PessoaContato::getObjects($criteria_pessoa_contato_pessoa_id);

        $this->pessoa_contato_pessoa_id_list->addItems($pessoa_contato_pessoa_id_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->pessoa_contato_pessoa_id_list));

        $tab_622940daf9f3b->addContent([$panel]);

        $tab_622940daf9f3b->appendPage("Histórico");

        $this->interacao_cliente_id_list = new TQuickGrid;
        $this->interacao_cliente_id_list->style = 'width:100%';
        $this->interacao_cliente_id_list->disableDefaultClick();

        $action_onShow = new TDataGridAction(array('InteracaoFormView', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("");
        $action_onShow->setImage('fas:folder-open #000000');
        $action_onShow->setField('id');

        $action_onShow->setParameter('key', '{id}');
        $this->interacao_cliente_id_list->addAction($action_onShow);

        $column_vendedor_razao_social = $this->interacao_cliente_id_list->addQuickColumn("Representante", 'vendedor->razao_social', 'left');
        $column_data_inicio_transformed = $this->interacao_cliente_id_list->addQuickColumn("Data de início", 'data_inicio', 'left');
        $column_data_fechamento_transformed = $this->interacao_cliente_id_list->addQuickColumn("Data de fechamento", 'data_fechamento', 'left');
        $column_data_fechamento_esperada_transformed = $this->interacao_cliente_id_list->addQuickColumn("Data esperada de fechamento", 'data_fechamento_esperada', 'left');
        $column_etapa_interacao_nome_transformed = $this->interacao_cliente_id_list->addQuickColumn("Etapa", 'etapa_interacao->nome', 'left');

        $column_data_inicio_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
          if (!empty($value)) {
                $date = new DateTime($value); 
                return $date->format('d/m/Y');
            }

            return null;

        });

        $column_data_fechamento_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
          if (!empty($value)) {
                $date = new DateTime($value); 
                return $date->format('d/m/Y');
            }

            return null;

        });

        $column_data_fechamento_esperada_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
          if (!empty($value)) {
                $date = new DateTime($value); 
                return $date->format('d/m/Y');
            }

            return null;

        });

        $column_etapa_interacao_nome_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if(!empty($object->etapa_interacao_id))
            {
                return "<span class='label ' style='width: 100%; max-width: 200px; background-color:{$object->etapa_interacao->cor}'>{$object->etapa_interacao->nome}</span>";
            }

        });

        $this->interacao_cliente_id_list->createModel();

        $criteria_interacao_cliente_id = new TCriteria();
        $criteria_interacao_cliente_id->add(new TFilter('cliente_id', '=', $pessoa->id));

        $criteria_interacao_cliente_id->setProperty('order', 'id desc');

        $interacao_cliente_id_items = Interacao::getObjects($criteria_interacao_cliente_id);

        $this->interacao_cliente_id_list->addItems($interacao_cliente_id_items);

        $panel = new TElement('div');
        $panel->class = 'formView-detail';
        $panel->add(new BootstrapDatagridWrapper($this->interacao_cliente_id_list));

        $tab_622940daf9f3b->addContent([$panel]);
        $row6 = $this->form->addFields([$tab_622940daf9f3b]);
        $row6->layout = [' col-sm-12'];

        if(!empty($param['current_tab']))
        {
            $this->form->setCurrentPage($param['current_tab']);
        }

        if(!empty($param['current_tab_tab_622940daf9f3b']))
        {
            $this->tab_622940daf9f3b->setCurrentPage($param['current_tab_tab_622940daf9f3b']);
        }

        parent::setTargetContainer('adianti_right_panel');

        $btnClose = new TButton('closeCurtain');
        $btnClose->class = 'btn btn-sm btn-default';
        $btnClose->style = 'margin-right:10px;';
        $btnClose->onClick = "Template.closeRightPanel();";
        $btnClose->setLabel("Fechar");
        $btnClose->setImage('fas:times');

        $this->form->addHeaderWidget($btnClose);

        TTransaction::close();
        parent::add($this->form);

        $style = new TStyle('right-panel > .container-part[page-name=ClienteFormView]');
        $style->width = '60% !important';   
        $style->show(true);

    }

    public function onShow($param = null)
    {     

            TSession::setValue('origem', 'ClienteList');
            TSession::setValue('method', 'onShow');
            TSession::setValue('key', $param['key']);
    }

}

