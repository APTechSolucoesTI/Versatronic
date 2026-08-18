<?php

class InteracaoArquivoForm extends TPage
{
    protected BootstrapFormBuilder $form;
    private $formFields = [];
    private static $database = 'minicrm';
    private static $activeRecord = 'InteracaoArquivo';
    private static $primaryKey = 'id';
    private static $formName = 'form_NegociacaoArquivoForm';

    use Adianti\Base\AdiantiFileSaveTrait;

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
        $this->form->setFormTitle("Adicione um arquivo a interação");


        $id = new THidden('id');
        $interacao_id = new THidden('interacao_id');
        $conteudo_arquivo = new TFile('conteudo_arquivo');

        $conteudo_arquivo->addValidation("forneça o arquivo!", new TRequiredValidator()); 

        $interacao_id->setValue(TSession::getValue('interacao_id'));
        $conteudo_arquivo->enableFileHandling();
        $id->setSize(200);
        $interacao_id->setSize(200);
        $conteudo_arquivo->setSize('100%');

        $row1 = $this->form->addFields([$id,$interacao_id]);
        $row1->layout = ['col-sm-6'];

        $row2 = $this->form->addFields([new TLabel("Anexar arquivo:", null, '14px', null, '100%'),$conteudo_arquivo]);
        $row2->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        $btn_onshow = $this->form->addAction("Voltar", new TAction(['InteracaoArquivoHeaderList', 'onShow']), 'fas:arrow-left #000000');
        $this->btn_onshow = $btn_onshow;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["CRM","Cadastro de arquivo de Interação"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new InteracaoArquivo(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            if (empty($data->interacao_id)) {
                throw new Exception('Interação não informada para salvar o arquivo.');
            }

            $conteudo_arquivo_dir = 'anexos/interacao_' . (int) $data->interacao_id;

            if (!is_dir($conteudo_arquivo_dir)) {
                if (!mkdir($conteudo_arquivo_dir, 0777, true)) {
                    throw new Exception('Não foi possível criar a pasta do arquivo: ' . $conteudo_arquivo_dir);
                }
            }

            if (!is_writable($conteudo_arquivo_dir)) {
                throw new Exception('A pasta do arquivo não tem permissão de escrita: ' . $conteudo_arquivo_dir);
            }

            if(!$data->id)
            {
                $object->dt_arquivo = date('Y-m-d H:i:s');
            }

            $object->store(); // save the object 
            $this->saveFile($object, $data, 'conteudo_arquivo', $conteudo_arquivo_dir);

            $loadPageParam = [];

            if(!empty($param['target_container']))
            {
                $loadPageParam['target_container'] = $param['target_container'];
            }

            $interacaoHistoricoArquivo = new InteracaoHistoricoArquivo;
            $interacaoHistoricoArquivo->interacao_id = $object->interacao_id;
            $interacaoHistoricoArquivo->dt_arquivo = date('Y-m-d H:i:s');
            $interacaoHistoricoArquivo->descricao = $object->conteudo_arquivo;
            $interacaoHistoricoArquivo->movimentacao_id = Movimentacao::CRIADO;
            $interacaoHistoricoArquivo->interacao_arquivo_id = $object->id;
            $interacaoHistoricoArquivo->store();
            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            $paramTimeline = [

                'key' => $object->interacao_id
            ];
            TApplication::loadPage(
                'ViewInteracaoTimelineTimeLine',
                'onShow',
                [
                    'target_container' => 'container_timeline'
                ]
            );

            TToast::show('success', "Registro salvo", 'topRight', 'far:check-circle');
            TApplication::loadPage('InteracaoArquivoHeaderList', 'onShow', $loadPageParam); 

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

                $object = new InteracaoArquivo($key); // instantiates the Active Record 

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

