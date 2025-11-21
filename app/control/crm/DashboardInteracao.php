<?php

class DashboardInteracao extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_DashboardInteracao';

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
        $this->form->setFormTitle("Dashboard");

        $criteria_vendedor_id = new TCriteria();
        $criteria_total_em_interacao = new TCriteria();
        $criteria_total_finalizado = new TCriteria();
        $criteria_interacoes_por_etapa = new TCriteria();
        $criteria_origens_de_contato = new TCriteria();
        $criteria_vendedor = new TCriteria();
        $criteria_clientes_uf = new TCriteria();
        $criteria_total_finalizado_por_mes = new TCriteria();
        $criteria_cidade_uf = new TCriteria();

        $filterVar = [EtapaInteracao::CANCELADA, EtapaInteracao::FINALIZADA];
        $criteria_total_em_interacao->add(new TFilter('interacao.etapa_interacao_id', 'not in', $filterVar)); 
        $filterVar = EtapaInteracao::FINALIZADA;
        $criteria_total_finalizado->add(new TFilter('interacao.etapa_interacao_id', '=', $filterVar)); 

        $criteria_clientes_uf->setProperty('order', 'representante asc, uf asc, cidade asc, cliente_razao_social asc');

        $mes = new TCombo('mes');
        $ano = new TCombo('ano');
        $vendedor_id = new TDBCombo('vendedor_id', 'minicrm', 'Representante', 'id', '{razao_social}','razao_social asc' , $criteria_vendedor_id );
        $button_buscar = new TButton('button_buscar');
        $total_em_interacao = new BIndicator('total_em_interacao');
        $total_finalizado = new BIndicator('total_finalizado');
        $interacoes_por_etapa = new BPieChart('interacoes_por_etapa');
        $origens_de_contato = new BPieChart('origens_de_contato');
        $vendedor = new BPieChart('vendedor');
        $clientes_uf = new BTableChart('clientes_uf');
        $total_finalizado_por_mes = new BBarChart('total_finalizado_por_mes');
        $cidade_uf = new BBarChart('cidade_uf');


        $button_buscar->setAction(new TAction(['DashboardInteracao', 'onShow']), "Buscar");
        $button_buscar->addStyleClass('btn-primary');
        $button_buscar->setImage('fas:search #FFFFFF');
        $ano->addItems(TempoService::getAnos());
        $mes->addItems(TempoService::getMeses());

        $mes->setValue($param["mes"] ?? date('m'));
        $ano->setValue($param["ano"] ?? date('Y'));

        $mes->setSize('100%');
        $ano->setSize('100%');
        $vendedor_id->setSize('100%');

        $mes->enableSearch();
        $ano->enableSearch();
        $vendedor_id->enableSearch();

        $total_em_interacao->setDatabase('minicrm');
        $total_em_interacao->setFieldValue("interacao.id");
        $total_em_interacao->setModel('Interacao');
        $total_em_interacao->setTotal('count');
        $total_em_interacao->setColors('#0984E3', '#ffffff', '#74B9FF', '#ffffff');
        $total_em_interacao->setTitle("interações abertas", '#ffffff', '20', '');
        $criteria_total_em_interacao->add(new TFilter('interacao.deleted_at', 'is', NULL));
        $total_em_interacao->setCriteria($criteria_total_em_interacao);
        $total_em_interacao->setIcon(new TImage('fas:shopping-basket #ffffff'));
        $total_em_interacao->setValueSize("20");
        $total_em_interacao->setValueColor("#ffffff", 'B');
        $total_em_interacao->setSize('100%', 95);
        $total_em_interacao->setLayout('horizontal', 'left');

        $total_finalizado->setDatabase('minicrm');
        $total_finalizado->setFieldValue("interacao.id");
        $total_finalizado->setModel('Interacao');
        $total_finalizado->setTotal('count');
        $total_finalizado->setColors('#10AC84', '#FFFFFF', '#1DD1A1', '#FFFFFF');
        $total_finalizado->setTitle("interações finalizadas", '#FFFFFF', '20', '');
        $criteria_total_finalizado->add(new TFilter('interacao.deleted_at', 'is', NULL));
        $total_finalizado->setCriteria($criteria_total_finalizado);
        $total_finalizado->setIcon(new TImage('fas:shopping-basket #FFFFFF'));
        $total_finalizado->setValueSize("20");
        $total_finalizado->setValueColor("#FFFFFF", 'B');
        $total_finalizado->setSize('100%', 95);
        $total_finalizado->setLayout('horizontal', 'left');

        $interacoes_por_etapa->setDatabase('minicrm');
        $interacoes_por_etapa->setFieldValue("interacao.id");
        $interacoes_por_etapa->setFieldGroup("etapa_interacao.nome");
        $interacoes_por_etapa->setModel('Interacao');
        $interacoes_por_etapa->setTitle("Interações por Etapa");
        $interacoes_por_etapa->setJoins([
             'etapa_interacao' => ['interacao.etapa_interacao_id', 'etapa_interacao.id']
        ]);
        $interacoes_por_etapa->setTotal('count');
        $interacoes_por_etapa->showLegend(true);
        $interacoes_por_etapa->enableOrderByValue('asc');
        $criteria_interacoes_por_etapa->add(new TFilter('interacao.deleted_at', 'is', NULL));
        $interacoes_por_etapa->setCriteria($criteria_interacoes_por_etapa);
        $interacoes_por_etapa->setSize('100%', 250);
        $interacoes_por_etapa->disableZoom();

        $origens_de_contato->setDatabase('minicrm');
        $origens_de_contato->setFieldValue("interacao.id");
        $origens_de_contato->setFieldGroup("origem_contato.nome");
        $origens_de_contato->setModel('Interacao');
        $origens_de_contato->setTitle("Interações por origem de Contato");
        $origens_de_contato->setJoins([
             'origem_contato' => ['interacao.origem_contato_id', 'origem_contato.id']
        ]);
        $origens_de_contato->setTotal('count');
        $origens_de_contato->showLegend(true);
        $origens_de_contato->enableOrderByValue('asc');
        $criteria_origens_de_contato->add(new TFilter('interacao.deleted_at', 'is', NULL));
        $origens_de_contato->setCriteria($criteria_origens_de_contato);
        $origens_de_contato->setSize('100%', 250);
        $origens_de_contato->disableZoom();

        $vendedor->setDatabase('minicrm');
        $vendedor->setFieldValue("interacao.id");
        $vendedor->setFieldGroup("representante.razao_social");
        $vendedor->setModel('Interacao');
        $vendedor->setTitle("Interações por Representante");
        $vendedor->setJoins([
             'representante' => ['interacao.vendedor_id', 'representante.id']
        ]);
        $vendedor->setTotal('count');
        $vendedor->showLegend(true);
        $vendedor->enableOrderByValue('asc');
        $criteria_vendedor->add(new TFilter('interacao.deleted_at', 'is', NULL));
        $vendedor->setCriteria($criteria_vendedor);
        $vendedor->setSize('100%', 250);
        $vendedor->disableZoom();

        $clientes_uf_column_cliente_razao_social = new BTableColumnChart('cliente_razao_social', "Cliente", 'left','40%');
        $clientes_uf_column_cidade = new BTableColumnChart('cidade', "Cidade", 'left','20%','asc');
        $clientes_uf_column_uf = new BTableColumnChart('uf', "UF", 'left','10%');
        $clientes_uf_column_representante = new BTableColumnChart('representante', "Representante", 'left','30%');

        $clientes_uf->setDatabase('minicrm');
        $clientes_uf->setModel('ViewClienteCidade');
        $clientes_uf->setTitle("");
        $clientes_uf->setSize('100%', 300);
        $clientes_uf->setColumns([$clientes_uf_column_cliente_razao_social,$clientes_uf_column_cidade,$clientes_uf_column_uf,$clientes_uf_column_representante]);
        $clientes_uf->setCriteria($criteria_clientes_uf);

        $clientes_uf->setRowColorOdd('#F9F9F9');
        $clientes_uf->setRowColorEven('#FFFFFF');
        $clientes_uf->setFontRowColorOdd('#333333');
        $clientes_uf->setFontRowColorEven('#333333');
        $clientes_uf->setBorderColor('#DDDDDD');
        $clientes_uf->setTableHeaderColor('#FFFFFF');
        $clientes_uf->setTableHeaderFontColor('#333333');
        $clientes_uf->setTableFooterColor('#FFFFFF');
        $clientes_uf->setTableFooterFontColor('#333333');

        $total_finalizado_por_mes->setDatabase('minicrm');
        $total_finalizado_por_mes->setFieldValue("interacao.id");
        $total_finalizado_por_mes->setFieldGroup(["interacao.mes"]);
        $total_finalizado_por_mes->setModel('Interacao');
        $total_finalizado_por_mes->setTitle("Quantidade total de interações por mês");
        $total_finalizado_por_mes->setTransformerLegend(function($value, $row, $data)
            {

                $value = str_pad($value, 2, "0", STR_PAD_LEFT);
                $meses = TempoService::getMeses();

                return $meses[$value] ?? '';

            });
        $total_finalizado_por_mes->setLayout('vertical');
        $total_finalizado_por_mes->setTotal('count');
        $total_finalizado_por_mes->showLegend(true);
        $criteria_total_finalizado_por_mes->add(new TFilter('interacao.deleted_at', 'is', NULL));
        $total_finalizado_por_mes->setCriteria($criteria_total_finalizado_por_mes);
        $total_finalizado_por_mes->setLabelValue("Quantidade total");
        $total_finalizado_por_mes->setSize('100%', 300);
        $total_finalizado_por_mes->disableZoom();

        $cidade_uf->setDatabase('minicrm');
        $cidade_uf->setFieldValue("view_cliente_cidade.interacao_id");
        $cidade_uf->setFieldGroup(["view_cliente_cidade.cidade_uf"]);
        $cidade_uf->setModel('ViewClienteCidade');
        $cidade_uf->setTitle("Interação por Cidade/UF");
        $cidade_uf->setLayout('vertical');
        $cidade_uf->setTotal('count');
        $cidade_uf->showLegend(true);
        $cidade_uf->enableOrderByValue('asc');
        $cidade_uf->setCriteria($criteria_cidade_uf);
        $cidade_uf->setSize('100%', 300);
        $cidade_uf->disableZoom();

        $row1 = $this->form->addFields([new TLabel("Mês:", null, '14px', null, '100%'),$mes],[new TLabel("Ano:", null, '14px', null, '100%'),$ano],[new TLabel("Representante:", null, '14px', null, '100%'),$vendedor_id],[new TLabel(" ", null, '14px', null, '100%'),$button_buscar]);
        $row1->layout = ['col-sm-3','col-sm-3','col-sm-3','col-sm-3'];

        $row2 = $this->form->addFields([$total_em_interacao],[$total_finalizado]);
        $row2->layout = [' col-sm-6','col-sm-6'];

        $row3 = $this->form->addFields([$interacoes_por_etapa],[$origens_de_contato],[$vendedor]);
        $row3->layout = ['col-sm-4','col-sm-4',' col-sm-4'];

        $row4 = $this->form->addFields([$clientes_uf],[$total_finalizado_por_mes]);
        $row4->layout = [' col-sm-6',' col-sm-6'];

        $row5 = $this->form->addFields([$cidade_uf]);
        $row5->layout = [' col-sm-12'];

        TTransaction::open('minicrm');
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
            $vendedor_id->setValue($representante->id);
            $vendedor_id->setEditable(false);
        }
        TTransaction::close();

        if(!isset($param['mes']) && $mes->getValue())
        {
            $_POST['mes'] = $mes->getValue();
        }
        if(!isset($param['ano']) && $ano->getValue())
        {
            $_POST['ano'] = $ano->getValue();
        }

        $searchData = $this->form->getData();
        $this->form->setData($searchData);

        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_total_em_interacao->add(new TFilter('interacao.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_em_interacao->add(new TFilter('interacao.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_total_em_interacao->add(new TFilter('interacao.vendedor_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_total_finalizado->add(new TFilter('interacao.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_finalizado->add(new TFilter('interacao.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_total_finalizado->add(new TFilter('interacao.vendedor_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_interacoes_por_etapa->add(new TFilter('interacao.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_interacoes_por_etapa->add(new TFilter('interacao.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_interacoes_por_etapa->add(new TFilter('interacao.vendedor_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_origens_de_contato->add(new TFilter('interacao.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_origens_de_contato->add(new TFilter('interacao.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_origens_de_contato->add(new TFilter('interacao.vendedor_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_vendedor->add(new TFilter('interacao.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_vendedor->add(new TFilter('interacao.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_vendedor->add(new TFilter('interacao.vendedor_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_clientes_uf->add(new TFilter('view_cliente_cidade.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_clientes_uf->add(new TFilter('view_cliente_cidade.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_clientes_uf->add(new TFilter('view_cliente_cidade.representante_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_total_finalizado_por_mes->add(new TFilter('interacao.vendedor_id', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_total_finalizado_por_mes->add(new TFilter('interacao.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->ano;
        if($filterVar)
        {
            $criteria_cidade_uf->add(new TFilter('view_cliente_cidade.ano', '=', $filterVar)); 
        }
        $filterVar = $searchData->mes;
        if($filterVar)
        {
            $criteria_cidade_uf->add(new TFilter('view_cliente_cidade.mes', '=', $filterVar)); 
        }
        $filterVar = $searchData->vendedor_id;
        if($filterVar)
        {
            $criteria_cidade_uf->add(new TFilter('view_cliente_cidade.representante_id', '=', $filterVar)); 
        }

        TTransaction::open('minicrm');
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
            $vendedor_id->setValue($representante->id);
            $vendedor_id->setEditable(false);

            $filter = new TFilter('interacao.vendedor_id','in',"(SELECT id FROM representante WHERE system_user_id = " . TSession::getValue('userid') . ")");

            $criteria_total_em_interacao->add($filter);
            $criteria_total_finalizado->add($filter);
            $criteria_interacoes_por_etapa->add($filter);
            $criteria_origens_de_contato->add($filter);
            $criteria_vendedor->add($filter);
            $criteria_total_finalizado_por_mes->add($filter);

            $filter = new TFilter('view_cliente_cidade.representante_id', '=', "(SELECT id FROM representante WHERE system_user_id = " . TSession::getValue('userid') . ")"); 

            $criteria_clientes_uf->add($filter);
            $criteria_cidade_uf->add($filter);
        }
        TTransaction::close();

        BChart::generate($total_em_interacao, $total_finalizado, $interacoes_por_etapa, $origens_de_contato, $vendedor, $clientes_uf, $total_finalizado_por_mes, $cidade_uf);

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Dashboard"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onShow($param = null)
    {               

    } 

}

