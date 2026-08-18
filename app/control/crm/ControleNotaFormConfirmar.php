<?php

class ControleNotaFormConfirmar extends TWindow
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'ControleNota';
    private static $primaryKey = 'id';
    private static $formName = 'form_ControleNotaFormConfirmar';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::setTitle("Confirmar Comissão");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Confirmar Comissão");

        $criteria_created_by = new TCriteria();

        $id = new THidden('id');
        $nota_baixada_id = new THidden('nota_baixada_id');
        $obs = new THtmlEditor('obs');
        $created_by = new TDBCombo('created_by', 'minicrm', 'SystemUsers', 'id', '{name}','name asc' , $criteria_created_by );
        $created_at = new TDateTime('created_at');

        $obs->addValidation("Justificativa", new TRequiredValidator()); 

        $nota_baixada_id->setValue($param["key"] ?? "");
        $created_by->enableSearch();
        $created_at->setMask('dd/mm/yyyy hh:ii');
        $created_at->setDatabaseMask('yyyy-mm-dd hh:ii');
        $created_by->setEditable(false);
        $created_at->setEditable(false);

        $id->setSize(200);
        $created_at->setSize(320);
        $obs->setSize('100%', 300);
        $created_by->setSize('100%');
        $nota_baixada_id->setSize(200);

        $row1 = $this->form->addFields([$id,$nota_baixada_id],[]);
        $row1->layout = ['col-sm-6','col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Justifique a confirmação da comissão:", '#8BC34A', '14px', null, '100%'),$obs]);
        $row2->layout = [' col-12 col-sm-12 col-lg-12 col-xl-12 col-md-12'];

        $row3 = $this->form->addFields([new TLabel("Criado Por:", null, '14px', null, '100%'),$created_by],[new TLabel("Criado em:", null, '14px', null, '100%'),$created_at]);
        $row3->layout = [' col-sm-3',' col-sm-3'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Confirmar Comissão", new TAction([$this, 'onSave']), 'fas:check #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-success'); 

        parent::add($this->form);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new ControleNota(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $notaId = (int) ($data->nota_baixada_id ?? 0);

            $justificativa = trim(
                html_entity_decode(
                    strip_tags((string) ($data->obs ?? '')),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                )
            );

            if ($notaId <= 0)
            {
                throw new Exception('Nota baixada não informada.');
            }

            if ($justificativa === '')
            {
                throw new Exception('Informe a justificativa da confirmação.');
            }

            $usuarioId = (int) TSession::getValue('userid');
            $dataHora  = date('Y-m-d H:i:s');

            $data->created_by = $usuarioId;
            $data->created_at = $dataHora;

            $object->nota_baixada_id = $notaId;
            $object->created_by      = $usuarioId;
            $object->created_at      = $dataHora;

            $conn = TTransaction::get();

            $stmtNota = $conn->prepare("
                SELECT
                    id,
                    tem_comissao
                FROM nota_baixada
                WHERE id = :nota_id
                FOR UPDATE
            ");

            $stmtNota->execute([
                ':nota_id' => $notaId
            ]);

            $nota = $stmtNota->fetch(PDO::FETCH_ASSOC);

            if (!$nota)
            {
                throw new Exception('A nota baixada informada não foi encontrada.');
            }

            if ($nota['tem_comissao'] === 'C')
            {
                throw new Exception('Não é possível confirmar uma comissão cancelada.');
            }

            if ($nota['tem_comissao'] === 'P')
            {
                throw new Exception('A comissão desta nota já foi confirmada.');
            }

            if ($nota['tem_comissao'] !== 'S')
            {
                throw new Exception('Esta nota não possui comissão disponível para confirmação.');
            }

            $object->store(); // save the object 

            $stmtConfirmarNota = $conn->prepare("
                UPDATE nota_baixada
                SET tem_comissao = 'P'
                WHERE id = :nota_id
            ");

            $stmtConfirmarNota->execute([
                ':nota_id' => $notaId
            ]);

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            new TMessage('info', "Registro salvo", $messageAction); 

                TWindow::closeWindow(parent::getId()); 

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

                $object = new ControleNota($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                if (($param['modo'] ?? null) === 'visualizar')
                {
                    $campoObs = $this->form->getField('obs');

                    if ($campoObs)
                    {
                        $campoObs->setEditable(false);
                    }

                    $campoCreatedBy = $this->form->getField('created_by');

                    if ($campoCreatedBy)
                    {
                        $campoCreatedBy->setEditable(false);
                    }

                    $campoCreatedAt = $this->form->getField('created_at');

                    if ($campoCreatedAt)
                    {
                        $campoCreatedAt->setEditable(false);
                    }

                    $this->btn_onsave->setProperty(
                        'style',
                        'display: none !important;'
                    );

                    $this->form->setFormTitle(
                        'Visualização da Confirmação de Comissão'
                    );

                    parent::setTitle(
                        'Visualização da Confirmação de Comissão'
                    );
                }

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

