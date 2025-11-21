<?php

class RepresentanteFormView extends TPage
{
    protected $form; // form
    private static $database = 'minicrm';
    private static $activeRecord = 'RepresentanteTotvs';
    private static $primaryKey = 'id';
    private static $formName = 'formView_RepresentanteTotvs';

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

        $representante_totvs = new RepresentanteTotvs($param['key']);
        // define the form title
        $this->form->setFormTitle("Consulta de representante");

        $transformed_representante_totvs_ativo = call_user_func(function($value, $object, $row)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        }, $representante_totvs->ativo, $representante_totvs, null);

        $label2 = new TLabel("Codigo:", '', '12px', 'B', '100%');
        $text2 = new TTextDisplay($representante_totvs->codigo, '', '12px', '');
        $label19 = new TLabel("Ativo:", '', '12px', 'B', '100%');
        $text19 = new TTextDisplay($transformed_representante_totvs_ativo, '', '12px', '');
        $label3 = new TLabel("Razão Social:", '', '12px', 'B', '100%');
        $text3 = new TTextDisplay($representante_totvs->razao_social, '', '12px', '');
        $label4 = new TLabel("Fantasia:", '', '12px', 'B', '100%');
        $text4 = new TTextDisplay($representante_totvs->fantasia, '', '12px', '');
        $label5 = new TLabel(new TImage('far:id-card #000000')."Documento:", '', '12px', 'B', '100%');
        $text5 = new TTextDisplay($representante_totvs->cpf_cnpj, '', '12px', '');
        $label21 = new TLabel(new TImage('fas:phone-alt #000000')."Celular:", '', '12px', 'B', '100%');
        $text21 = new TTextDisplay($representante_totvs->celular, '', '12px', '');
        $label20 = new TLabel(new TImage('far:envelope #000000')."Email:", '', '12px', 'B', '100%');
        $text20 = new TTextDisplay($representante_totvs->email, '', '12px', '');


        $row1 = $this->form->addFields([$label2,$text2],[$label19,$text19]);
        $row1->layout = [' col-sm-4',' col-sm-4'];

        $row2 = $this->form->addFields([$label3,$text3],[$label4,$text4],[$label5,$text5]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([$label21,$text21],[$label20,$text20]);
        $row3->layout = ['col-sm-6','col-sm-6'];

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

        $style = new TStyle('right-panel > .container-part[page-name=RepresentanteFormView]');
        $style->width = '60% !important';   
        $style->show(true);

    }

    public function onShow($param = null)
    {     

    }

}

