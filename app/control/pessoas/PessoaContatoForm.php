<?php

class PessoaContatoForm extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'PessoaContato';
    private static $primaryKey = 'id';
    private static $formName = 'form_PessoaContatoForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::setTitle("Cadastro de contato");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de contato");


        $id = new TEntry('id');
        $pessoa_id = new THidden('pessoa_id');
        $nome = new TEntry('nome');
        $telefone = new TEntry('telefone');
        $email = new TEntry('email');
        $obs = new TText('obs');


        $id->setEditable(false);
        $pessoa_id->setValue($param['pessoa_id'] ?? null);
        $nome->setTip("Casa, Trabalho, Celular");
        $telefone->setMask('(99) 99999-9999');
        $nome->setMaxLength(255);
        $email->setMaxLength(255);
        $telefone->setMaxLength(255);

        $id->setSize(100);
        $nome->setSize('100%');
        $email->setSize('100%');
        $pessoa_id->setSize(200);
        $obs->setSize('100%', 70);
        $telefone->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id,$pessoa_id]);
        $row1->layout = [' col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Descrição:", null, '14px', null, '100%'),$nome]);
        $row2->layout = [' col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Telefone:", null, '14px', null, '100%'),$telefone],[new TLabel("Email:", null, '14px', null, '100%'),$email]);
        $row3->layout = ['col-sm-6','col-sm-6'];

        $row4 = $this->form->addFields([new TLabel("Observação:", null, '14px', null, '100%'),$obs]);
        $row4->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new PessoaContato(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

            $pessoa = Pessoa::find($data->pessoa_id);

            TTransaction::close();

            TTransaction::open('corporerm');

            if(!isset($data->id) || empty($data->id) || $data->id == null){

                $nro_contato = 0;

                $conn = TTransaction::get();
                $result = $conn->query("SELECT TOP 1 IDCONTATO FROM FCFOCONTATO WHERE CODCFO like '$pessoa->codigo' ORDER BY IDCONTATO DESC");
                $resultados = $result->fetchAll(PDO::FETCH_CLASS, "stdClass");
                foreach ($resultados as $resultado) {
                    $nro_contato = $resultado->IDCONTATO ?? 0;
                }

                $nro_contato = $nro_contato+1;

                $sql = "INSERT INTO FCFOCONTATO (CODCOLIGADA,CODCFO,IDCONTATO,NOME,EMAIL,TELEFONE,OBSERVACAO)
                                    VALUES (0,'$pessoa->codigo',$nro_contato,'$object->nome','$object->email','$object->telefone','$object->obs')";
                $conn->query($sql);
            }else{
                Fcfocontato::where('CODCFO', 'like', $pessoa->codigo)
                            ->set('NOME', $object->nome)
                            ->set('EMAIL', $object->email)
                            ->set('TELEFONE', $object->telefone)
                            ->set('OBSERVACAO', $object->obs)
                            ->update();
            }
            TTransaction::close();

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            if(!empty($object->pessoa_id))
            {
                $loadPageParam["key"] = $object->pessoa_id;
            }

            $data->id = $object->id; 

            $this->form->setData($data); // fill form data

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('ClienteFormView', 'onShow', $loadPageParam); 

            TWindow::closeWindow(parent::getId());

        }
        catch (Exception $e) // in case of exception
        {

            //new TMessage('error', $e->getMessage()); // shows the exception error message
            echo "Erro ao processar transações - Contato <hr/>" . $e->getMessage();
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

                $object = new PessoaContato($key); // instantiates the Active Record 

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

