<?php

class TransportadoraFormView extends TPage
{
    protected $form; // form
    private static $database = 'minicrm';
    private static $activeRecord = 'Transportadora';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Transportadora';

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

        $transportadora = new Transportadora($param['key']);
        // define the form title
        $this->form->setFormTitle("Consulta de transportadora");

        $transformed_transportadora_ativo = call_user_func(function($value, $object, $row)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        }, $transportadora->ativo, $transportadora, null);

        $label1 = new TLabel("Id:", '', '12px', '', '100%');
        $text1 = new TTextDisplay($transportadora->id, '', '12px', '');
        $label5 = new TLabel("Código:", '', '12px', '', '100%');
        $text5 = new TTextDisplay($transportadora->codtra, '', '12px', '');
        $label23 = new TLabel("Ativo:", '', '12px', '', '100%');
        $text23 = new TTextDisplay($transformed_transportadora_ativo, '', '12px', '');
        $label6 = new TLabel("Razão Social:", '', '12px', '', '100%');
        $text6 = new TTextDisplay($transportadora->nome, '', '12px', '');
        $label20 = new TLabel("Fantasia:", '', '12px', '', '100%');
        $text20 = new TTextDisplay($transportadora->nomefantasia, '', '12px', '');
        $label13 = new TLabel("Documento:", '', '12px', '', '100%');
        $text13 = new TTextDisplay($transportadora->cgc, '', '12px', '');
        $label22 = new TLabel("Inscrição municipal:", '', '12px', '', '100%');
        $text22 = new TTextDisplay($transportadora->inscrmunicipal, '', '12px', '');
        $label14 = new TLabel("Inscrição estadual:", '', '12px', '', '100%');
        $text14 = new TTextDisplay($transportadora->inscrestadual, '', '12px', '');
        $label21 = new TLabel("CEI:", '', '12px', '', '100%');
        $text21 = new TTextDisplay($transportadora->cei, '', '12px', '');
        $label24 = new TLabel("Email:", '', '12px', '', '100%');
        $text24 = new TTextDisplay($transportadora->email, '', '12px', '');
        $label12 = new TLabel("Cep:", '', '12px', '', '100%');
        $text12 = new TTextDisplay($transportadora->cep, '', '12px', '');
        $label7 = new TLabel("Rua:", '', '12px', '', '100%');
        $text7 = new TTextDisplay($transportadora->rua, '', '12px', '');
        $label8 = new TLabel("Número:", '', '12px', '', '100%');
        $text8 = new TTextDisplay($transportadora->numero, '', '12px', '');
        $label9 = new TLabel("Complemento:", '', '12px', '', '100%');
        $text9 = new TTextDisplay($transportadora->complemento, '', '12px', '');
        $label10 = new TLabel("Bairro:", '', '12px', '', '100%');
        $text10 = new TTextDisplay($transportadora->bairro, '', '12px', '');
        $label11 = new TLabel("Cidade:", '', '12px', '', '100%');
        $text11 = new TTextDisplay($transportadora->cidade->nome, '', '12px', '');
        $label15 = new TLabel("Contato:", '', '12px', '', '100%');
        $text15 = new TTextDisplay($transportadora->contato, '', '12px', '');
        $label16 = new TLabel("Telefone:", '', '12px', '', '100%');
        $text16 = new TTextDisplay($transportadora->telefone, '', '12px', '');
        $label17 = new TLabel("Telex:", '', '12px', '', '100%');
        $text17 = new TTextDisplay($transportadora->telex, '', '12px', '');
        $label18 = new TLabel("Fax:", '', '12px', '', '100%');
        $text18 = new TTextDisplay($transportadora->fax, '', '12px', '');
        $label19 = new TLabel("Livre:", '', '12px', '', '100%');
        $text19 = new TTextDisplay($transportadora->livre, '', '12px', '');

        $row1 = $this->form->addFields([$label1,$text1],[$label5,$text5],[$label23,$text23]);
        $row1->layout = ['col-sm-6',' col-sm-3',' col-sm-3'];

        $row2 = $this->form->addFields([$label6,$text6],[$label20,$text20]);
        $row2->layout = ['col-sm-6',' col-sm-6'];

        $row3 = $this->form->addFields([$label13,$text13],[$label22,$text22],[$label14,$text14]);
        $row3->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];

        $row4 = $this->form->addFields([$label21,$text21],[$label24,$text24]);
        $row4->layout = ['col-sm-6','col-sm-6'];

        $row5 = $this->form->addContent([new TFormSeparator("", '#333', '18', '#eee')]);
        $row6 = $this->form->addFields([$label12,$text12],[$label7,$text7]);
        $row6->layout = [' col-sm-3',' col-sm-9'];

        $row7 = $this->form->addFields([$label8,$text8],[$label9,$text9],[$label10,$text10],[$label11,$text11]);
        $row7->layout = [' col-sm-3',' col-sm-3',' col-sm-3',' col-sm-3'];

        $row8 = $this->form->addContent([new TFormSeparator("", '#333', '18', '#eee')]);
        $row9 = $this->form->addFields([$label15,$text15],[$label16,$text16],[$label17,$text17],[$label18,$text18]);
        $row9->layout = ['col-sm-3','col-sm-3',' col-sm-3',' col-sm-3'];

        $row10 = $this->form->addFields([$label19,$text19]);
        $row10->layout = [' col-sm-12'];

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

    }

    public function onShow($param = null)
    {     

    }

}

