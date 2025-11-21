<?php

class BuscaAgendaGlobalAtividadeForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_BuscaAgendaGlobalAtividadeForm';

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
        $this->form->setFormTitle("Agenda Global");

        $criteria_cliente_id = new TCriteria();
        $criteria_vendedor_id = new TCriteria();
        $criteria_tipo_atividade_id = new TCriteria();

        $filterVar = Grupo::CLIENTE;
        $criteria_cliente_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_id = '{$filterVar}')")); 

        $cliente_id = new TDBUniqueSearch('cliente_id', 'minicrm', 'Pessoa', 'id', 'razao_social','razao_social asc' , $criteria_cliente_id );
        $vendedor_id = new TDBUniqueSearch('vendedor_id', 'minicrm', 'Representante', 'id', 'razao_social','razao_social asc' , $criteria_vendedor_id );
        $tipo_atividade_id = new TDBCombo('tipo_atividade_id', 'minicrm', 'TipoAtividade', 'id', '{icone_formatado} {nome}','nome asc' , $criteria_tipo_atividade_id );
        $button_buscar = new TButton('button_buscar');
        $calendario = new BPageContainer();


        $cliente_id->setFilterColumns(["razao_social"]);
        $tipo_atividade_id->enableSearch();
        $button_buscar->addStyleClass('btn-primary');
        $button_buscar->setImage('fas:search #F9F5F5');
        $calendario->setId('b6351d611c87df');
        $cliente_id->setMinLength(2);
        $vendedor_id->setMinLength(3);

        $cliente_id->setMask('{razao_social}');
        $vendedor_id->setMask('{razao_social}');

        $button_buscar->setAction(new TAction(['BuscaAgendaGlobalAtividadeForm', 'onShow']), "Buscar");
        $calendario->setAction(new TAction(['InteracaoAtividadeGlobalCalendarFormView', 'onReload'], $param));

        $cliente_id->setValue($param["cliente_id"] ?? "");
        $vendedor_id->setValue($param["vendedor_id"] ?? "");
        $tipo_atividade_id->setValue($param["tipo_atividade_id"] ?? "");

        $cliente_id->setSize('100%');
        $calendario->setSize('100%');
        $vendedor_id->setSize('100%');
        $tipo_atividade_id->setSize('100%');

        $loadingContainer = new TElement('div');
        $loadingContainer->style = 'text-align:center; padding:50px';

        $icon = new TElement('i');
        $icon->class = 'fas fa-spinner fa-spin fa-3x';

        $loadingContainer->add($icon);
        $loadingContainer->add('<br>Carregando');

        $calendario->add($loadingContainer);

        $this->calendario = $calendario;

        $row1 = $this->form->addFields([new TLabel("Cliente:", null, '14px', null, '100%'),$cliente_id],[new TLabel("Representante:", null, '14px', null),$vendedor_id],[new TLabel("Tipo atividade:", null, '14px', null, '100%'),$tipo_atividade_id],[new TLabel(" ", null, '14px', null, '100%'),$button_buscar]);
        $row1->layout = [' col-sm-3',' col-sm-3',' col-sm-3','col-sm-2'];

        $row2 = $this->form->addFields([$calendario]);
        $row2->layout = [' col-sm-12'];

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Agenda Global"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onShow($param = null)
    {               

        TTransaction::open('minicrm');
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){

            $object = new stdClass();
            $object->vendedor_id = $representante->id;

            TForm::sendData(self::$formName, $object);
            $this->form->getField('vendedor_id')->setEditable(FALSE);
        }

        TTransaction::close();
    } 

}

