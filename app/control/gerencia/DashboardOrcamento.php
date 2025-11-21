<?php

class DashboardOrcamento extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_DashboardOrcamento';

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

        $criteria_comercio_total = new TCriteria();
        $criteria_verde_comercio = new TCriteria();
        $criteria_amarelo_comercio = new TCriteria();
        $criteria_vermelho_comercio = new TCriteria();
        $criteria_tabela_comercio = new TCriteria();
        $criteria_cnc_total = new TCriteria();
        $criteria_verde_cnc = new TCriteria();
        $criteria_amarelo_cnc = new TCriteria();
        $criteria_vermelho_cnc = new TCriteria();
        $criteria_tabela_cnc = new TCriteria();

        $filterVar = "Verde";
        $criteria_verde_comercio->add(new TFilter('orcamentos_comercio.status', '=', $filterVar)); 
        $filterVar = "Amarelo";
        $criteria_amarelo_comercio->add(new TFilter('orcamentos_comercio.status', '=', $filterVar)); 
        $filterVar = "Vermelho";
        $criteria_vermelho_comercio->add(new TFilter('orcamentos_comercio.status', '=', $filterVar)); 
        $filterVar = "Verde";
        $criteria_verde_cnc->add(new TFilter('orcamentos_cnc.status', '=', $filterVar)); 
        $filterVar = "Amarelo";
        $criteria_amarelo_cnc->add(new TFilter('orcamentos_cnc.status', '=', $filterVar)); 
        $filterVar = "Vermelho";
        $criteria_vermelho_cnc->add(new TFilter('orcamentos_cnc.status', '=', $filterVar)); 

        $comercio_total = new BIndicator('comercio_total');
        $verde_comercio = new BIndicator('verde_comercio');
        $amarelo_comercio = new BIndicator('amarelo_comercio');
        $vermelho_comercio = new BIndicator('vermelho_comercio');
        $tabela_comercio = new BTableChart('tabela_comercio');
        $cnc_total = new BIndicator('cnc_total');
        $verde_cnc = new BIndicator('verde_cnc');
        $amarelo_cnc = new BIndicator('amarelo_cnc');
        $vermelho_cnc = new BIndicator('vermelho_cnc');
        $tabela_cnc = new BTableChart('tabela_cnc');


        $comercio_total->setDatabase('corporerm');
        $comercio_total->setFieldValue("orcamentos_comercio.orcamento");
        $comercio_total->setModel('OrcamentosComercio');
        $comercio_total->setTotal('count');
        $comercio_total->setColors('#03A9F4', '#000000', '#03A9F4', '#000000');
        $comercio_total->setTitle("versatronic eletrônica industrial", '#000000', '12', '');
        $comercio_total->setCriteria($criteria_comercio_total);
        $comercio_total->setValueSize("12");
        $comercio_total->setValueColor("#000000", 'B');
        $comercio_total->setSize('100%', 50);
        $comercio_total->setLayout('horizontal', 'center');

        $verde_comercio->setDatabase('corporerm');
        $verde_comercio->setFieldValue("orcamentos_comercio.orcamento");
        $verde_comercio->setModel('OrcamentosComercio');
        $verde_comercio->setTotal('count');
        $verde_comercio->setColors('#00D80B', '#000000', '#00D80B', '#000000');
        $verde_comercio->setTitle("0 a 2 dias", '#000000', '12', '');
        $verde_comercio->setCriteria($criteria_verde_comercio);
        $verde_comercio->setValueSize("12");
        $verde_comercio->setValueColor("#000000", 'B');
        $verde_comercio->setSize('100%', 50);
        $verde_comercio->setLayout('horizontal', 'center');

        $amarelo_comercio->setDatabase('corporerm');
        $amarelo_comercio->setFieldValue("orcamentos_comercio.orcamento");
        $amarelo_comercio->setModel('OrcamentosComercio');
        $amarelo_comercio->setTotal('count');
        $amarelo_comercio->setColors('#FFE821', '#000000', '#FFE821', '#000000');
        $amarelo_comercio->setTitle("3 a 4 dias", '#000000', '12', '');
        $amarelo_comercio->setCriteria($criteria_amarelo_comercio);
        $amarelo_comercio->setValueSize("12");
        $amarelo_comercio->setValueColor("#000000", 'B');
        $amarelo_comercio->setSize('100%', 50);
        $amarelo_comercio->setLayout('horizontal', 'center');

        $vermelho_comercio->setDatabase('corporerm');
        $vermelho_comercio->setFieldValue("orcamentos_comercio.orcamento");
        $vermelho_comercio->setModel('OrcamentosComercio');
        $vermelho_comercio->setTotal('count');
        $vermelho_comercio->setColors('#FF1410', '#000000', '#FF1410', '#000000');
        $vermelho_comercio->setTitle("mais de 5 dias", '#000000', '12', '');
        $vermelho_comercio->setCriteria($criteria_vermelho_comercio);
        $vermelho_comercio->setValueSize("12");
        $vermelho_comercio->setValueColor("#000000", 'B');
        $vermelho_comercio->setSize('100%', 50);
        $vermelho_comercio->setLayout('horizontal', 'center');

        $tabela_comercio_column_dias = new BTableColumnChart('dias', "Dias", 'left','10%','desc');
        $tabela_comercio_column_objeto = new BTableColumnChart('objeto', "Objeto de Manutenção", 'left','35%');
        $tabela_comercio_column_cliente = new BTableColumnChart('cliente', "Cliente", 'center','15%');
        $tabela_comercio_column_orcamento = new BTableColumnChart('orcamento', "Orçamento", 'center','10%');
        $tabela_comercio_column_os = new BTableColumnChart('os', "OS", 'right','10%');
        $tabela_comercio_column_tecnico = new BTableColumnChart('tecnico', "Técnico", 'right','20%');
        $tabela_comercio_column_dias->setTransformer(function($value, $object, $row)
        {
                if ($value >= 5) {
                    $cor_status = 'ff1410';
                } elseif ($value > 2) {
                    $cor_status = 'e0e349';
                } else {
                    $cor_status = '00d506';
                }
                return "<i class='fas fa-circle' style='color: #{$cor_status}'></i> {$value}";
        });

        $tabela_comercio->setDatabase('corporerm');
        $tabela_comercio->setModel('OrcamentosComercio');
        $tabela_comercio->setTitle("Comércio");
        $tabela_comercio->setSize('100%', 500);
        $tabela_comercio->setColumns([$tabela_comercio_column_dias,$tabela_comercio_column_objeto,$tabela_comercio_column_cliente,$tabela_comercio_column_orcamento,$tabela_comercio_column_os,$tabela_comercio_column_tecnico]);
        $tabela_comercio->setCriteria($criteria_tabela_comercio);

        $tabela_comercio->setRowColorOdd('#F9F9F9');
        $tabela_comercio->setRowColorEven('#FFFFFF');
        $tabela_comercio->setFontRowColorOdd('#333333');
        $tabela_comercio->setFontRowColorEven('#333333');
        $tabela_comercio->setBorderColor('#DDDDDD');
        $tabela_comercio->setTableHeaderColor('#FFFFFF');
        $tabela_comercio->setTableHeaderFontColor('#333333');
        $tabela_comercio->setTableFooterColor('#FFFFFF');
        $tabela_comercio->setTableFooterFontColor('#333333');

        $cnc_total->setDatabase('corporerm');
        $cnc_total->setFieldValue("orcamentos_cnc.orcamento");
        $cnc_total->setModel('OrcamentosCnc');
        $cnc_total->setTotal('count');
        $cnc_total->setColors('#03A9F4', '#000000', '#03A9F4', '#000000');
        $cnc_total->setTitle("versatronic cnc", '#000000', '12', '');
        $cnc_total->setCriteria($criteria_cnc_total);
        $cnc_total->setValueSize("12");
        $cnc_total->setValueColor("#000000", 'B');
        $cnc_total->setSize('100%', 50);
        $cnc_total->setLayout('horizontal', 'center');

        $verde_cnc->setDatabase('corporerm');
        $verde_cnc->setFieldValue("orcamentos_cnc.orcamento");
        $verde_cnc->setModel('OrcamentosCnc');
        $verde_cnc->setTotal('count');
        $verde_cnc->setColors('#00D80B', '#000000', '#00D80B', '#000000');
        $verde_cnc->setTitle("0 a 2 dias", '#000000', '12', '');
        $verde_cnc->setCriteria($criteria_verde_cnc);
        $verde_cnc->setValueSize("12");
        $verde_cnc->setValueColor("#000000", 'B');
        $verde_cnc->setSize('100%', 50);
        $verde_cnc->setLayout('horizontal', 'center');

        $amarelo_cnc->setDatabase('corporerm');
        $amarelo_cnc->setFieldValue("orcamentos_cnc.orcamento");
        $amarelo_cnc->setModel('OrcamentosCnc');
        $amarelo_cnc->setTotal('count');
        $amarelo_cnc->setColors('#FFE821', '#000000', '#FFE821', '#000000');
        $amarelo_cnc->setTitle("3 a 4 dias", '#000000', '12', '');
        $amarelo_cnc->setCriteria($criteria_amarelo_cnc);
        $amarelo_cnc->setValueSize("12");
        $amarelo_cnc->setValueColor("#000000", 'B');
        $amarelo_cnc->setSize('100%', 50);
        $amarelo_cnc->setLayout('horizontal', 'center');

        $vermelho_cnc->setDatabase('corporerm');
        $vermelho_cnc->setFieldValue("orcamentos_cnc.orcamento");
        $vermelho_cnc->setModel('OrcamentosCnc');
        $vermelho_cnc->setTotal('count');
        $vermelho_cnc->setColors('#FF1410', '#000000', '#FF1410', '#000000');
        $vermelho_cnc->setTitle("mais de 5 dias", '#000000', '12', '');
        $vermelho_cnc->setCriteria($criteria_vermelho_cnc);
        $vermelho_cnc->setValueSize("12");
        $vermelho_cnc->setValueColor("#000000", 'B');
        $vermelho_cnc->setSize('100%', 50);
        $vermelho_cnc->setLayout('horizontal', 'center');

        $tabela_cnc_column_dias = new BTableColumnChart('dias', "Dias", 'left','10%','desc');
        $tabela_cnc_column_objeto = new BTableColumnChart('objeto', "Objeto de Manutenção", 'left','35%');
        $tabela_cnc_column_cliente = new BTableColumnChart('cliente', "Cliente", 'center','15%');
        $tabela_cnc_column_orcamento = new BTableColumnChart('orcamento', "Orçamento", 'center','10%');
        $tabela_cnc_column_os = new BTableColumnChart('os', "OS", 'right','10%');
        $tabela_cnc_column_tecnico = new BTableColumnChart('tecnico', "Técnico", 'right','20%');
        $tabela_cnc_column_dias->setTransformer(function($value, $object, $row)
        {
                if ($value >= 5) {
                    $cor_status = 'ff1410';
                } elseif ($value > 2) {
                    $cor_status = 'e0e349';
                } else {
                    $cor_status = '00d506';
                }
                return "<i class='fas fa-circle' style='color: #{$cor_status}'></i> {$value}";
        });

        $tabela_cnc->setDatabase('corporerm');
        $tabela_cnc->setModel('OrcamentosCnc');
        $tabela_cnc->setTitle("CNC");
        $tabela_cnc->setSize('100%', 500);
        $tabela_cnc->setColumns([$tabela_cnc_column_dias,$tabela_cnc_column_objeto,$tabela_cnc_column_cliente,$tabela_cnc_column_orcamento,$tabela_cnc_column_os,$tabela_cnc_column_tecnico]);
        $tabela_cnc->setCriteria($criteria_tabela_cnc);

        $tabela_cnc->setRowColorOdd('#F9F9F9');
        $tabela_cnc->setRowColorEven('#FFFFFF');
        $tabela_cnc->setFontRowColorOdd('#333333');
        $tabela_cnc->setFontRowColorEven('#333333');
        $tabela_cnc->setBorderColor('#DDDDDD');
        $tabela_cnc->setTableHeaderColor('#FFFFFF');
        $tabela_cnc->setTableHeaderFontColor('#333333');
        $tabela_cnc->setTableFooterColor('#FFFFFF');
        $tabela_cnc->setTableFooterFontColor('#333333');

        $bcontainer_679ca9ecbf51d = new BootstrapFormBuilder('bcontainer_679ca9ecbf51d');
        $this->bcontainer_679ca9ecbf51d = $bcontainer_679ca9ecbf51d;
        $bcontainer_679ca9ecbf51d->setProperty('style', 'border:none; box-shadow:none;');
        $row1 = $bcontainer_679ca9ecbf51d->addFields([$comercio_total]);
        $row1->layout = [' col-sm-12'];

        $row2 = $bcontainer_679ca9ecbf51d->addFields([$verde_comercio],[$amarelo_comercio],[$vermelho_comercio]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $bcontainer_679ca9ecbf51d->addFields([$tabela_comercio]);
        $row3->layout = [' col-sm-12'];

        $bcontainer_679ca9f2bf51f = new BootstrapFormBuilder('bcontainer_679ca9f2bf51f');
        $this->bcontainer_679ca9f2bf51f = $bcontainer_679ca9f2bf51f;
        $bcontainer_679ca9f2bf51f->setProperty('style', 'border:none; box-shadow:none;');
        $row4 = $bcontainer_679ca9f2bf51f->addFields([$cnc_total]);
        $row4->layout = [' col-sm-12'];

        $row5 = $bcontainer_679ca9f2bf51f->addFields([$verde_cnc],[$amarelo_cnc],[$vermelho_cnc]);
        $row5->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row6 = $bcontainer_679ca9f2bf51f->addFields([$tabela_cnc]);
        $row6->layout = [' col-sm-12'];

        $row7 = $this->form->addFields([$bcontainer_679ca9ecbf51d],[$bcontainer_679ca9f2bf51f]);
        $row7->layout = ['col-sm-6',' col-sm-6'];

        $searchData = $this->form->getData();
        $this->form->setData($searchData);

        BChart::generate($comercio_total, $verde_comercio, $amarelo_comercio, $vermelho_comercio, $tabela_comercio, $cnc_total, $verde_cnc, $amarelo_cnc, $vermelho_cnc, $tabela_cnc);

        // create the form actions

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gerencia","Dashboard"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onShow($param = null)
    {               

    } 

}

