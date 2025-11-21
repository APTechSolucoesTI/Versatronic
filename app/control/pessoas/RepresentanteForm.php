<?php

class RepresentanteForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'Representante';
    private static $primaryKey = 'id';
    private static $formName = 'form_RepresentanteForm';

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

        TTransaction::open(self::$database);
        $conn = TTransaction::get();

        // Execute a subconsulta para obter os IDs desejados
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

        // Recarregar o TCombo com os valores obtidos
        TCombo::reload(self::$formName, 'representante', $representantes, true);
        // -----

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

        $representante->setChangeAction(new TAction([$this,'onGet']));

        $codigo->addValidation("Codigo", new TRequiredValidator()); 
        $razao_social->addValidation("Razão Social", new TRequiredValidator()); 

        $ativo->addItems(["S"=>" Sim","N"=>" Não"]);
        $id->setEditable(false);
        $codigo->setEditable(false);

        $ativo->enableSearch();
        $representante->enableSearch();
        $system_user_id->enableSearch();

        $email->setMaxLength(40);
        $codigo->setMaxLength(15);
        $telefone->setMaxLength(20);
        $cpf_cnpj->setMaxLength(20);
        $inscrestadual->setMaxLength(20);

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


        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id],[new TLabel("Codigo:", null, '14px', null, '100%'),$codigo],[new TLabel("Representante Totvs:", null, '14px', null),$representante]);
        $row1->layout = ['col-sm-3','col-sm-3',' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Razão Social:", '#ff0000', '14px', null, '100%'),$razao_social],[new TLabel("Email:", null, '14px', null, '100%'),$email],[new TLabel("Telefone:", null, '14px', null, '100%'),$telefone]);
        $row2->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

        $row3 = $this->form->addFields([new TLabel("CNPJ:", null, '14px', null, '100%'),$cpf_cnpj],[new TLabel("inscrição estadual:", null, '14px', null, '100%'),$inscrestadual]);
        $row3->layout = [' col-sm-4',' col-sm-4'];

        $row4 = $this->form->addFields([new TLabel("Usuario do Sistema:", null, '14px', null, '100%'),$system_user_id],[new TLabel("Ativo:", null, '14px', null, '100%'),$ativo],[new TLabel("Cor:", null, '14px', null, '100%'),$cor]);
        $row4->layout = [' col-sm-6',' col-sm-3',' col-sm-3'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
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

                TForm::sendData(self::$formName, $object);

            }
            TTransaction::close();

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

            $object = new Representante(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('RepresentanteList', 'onShow', $loadPageParam); 

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

                $object = new Representante($key); // instantiates the Active Record 

                TScript::create("$('label:contains(\"Representante Totvs:\")').hide();");
                TScript::create("$(\"[name='representante']\").closest('.fb-inline-field-container').hide()");
                TEntry::disableField(self::$formName, 'razao_social');
                TEntry::disableField(self::$formName, 'email');
                TEntry::disableField(self::$formName, 'telefone');
                TEntry::disableField(self::$formName, 'inscrestadual');
                TEntry::disableField(self::$formName, 'cpf_cnpj');

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

