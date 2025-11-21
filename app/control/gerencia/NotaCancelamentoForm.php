<?php

class NotaCancelamentoForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_NotaCancelamentoForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();
        parent::setSize(0.50, null);
        parent::setTitle("Motivo do cancelamento");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Motivo do cancelamento");


        $motivo = new TText('motivo');
        $id = new THidden('id');

        $motivo->addValidation("Motivo", new TRequiredValidator()); 

        $id->setValue($param['key'] ?? null);
        $id->setSize(200);
        $motivo->setSize('100%', 70);


        $row1 = $this->form->addFields([new TLabel("Motivo:", '#F44336', '14px', null, '100%'),$motivo,$id]);
        $row1->layout = [' col-sm-12'];

        // create the form actions
        $btn_oncancelar = $this->form->addAction("Cancelar Nota", new TAction([$this, 'onCancelar']), 'fas:times-circle #ffffff');
        $this->btn_oncancelar = $btn_oncancelar;
        $btn_oncancelar->addStyleClass('btn-danger'); 

        parent::add($this->form);

    }

    public function onCancelar($param = null) 
    {
        try
        {
            if($param['id']){
                TTransaction::open('minicrm');
                $nota = NotaBaixada::find($param['id']);
                TTransaction::close();

                if($nota){
                    $erros = array();

                    $numero      = $nota->numero;
                    $coligada_id = $nota->coligada_id;

                    $resultado = SigissWebService::cancelarNf($numero, $coligada_id, $param['motivo']);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obtemXmlRps($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obtemXmlNf($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    $resultado = SigissWebService::obterPdf($numero, $coligada_id);
                    if ($resultado['status'] === 'error') {
                        $erros[] = $resultado['mensagem'];
                    }

                    if (count($erros) > 0) {
                        throw new Exception("Erro ao cancelar nota {$numero}: ".implode(', ',$erros));
                    } else {
                        TToast::show("success", "Nota {$numero} cancelada com sucesso!", "topRight", "fas:check-circle");
                    }
                }
            }

            TApplication::loadPage('NotaBaixadaList', 'onShow');

        }
        catch (Exception $e)
        {
            TApplication::loadPage('NotaBaixadaList', 'onShow');
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

    } 

}

