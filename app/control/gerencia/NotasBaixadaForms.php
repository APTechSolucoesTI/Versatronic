<?php

class NotasBaixadaForms extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_NotasBaixadaForms';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();
        parent::setSize(0.50, null);
        parent::setTitle("Buscar intervalo de NFSe");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Buscar intervalo de NFSe");

        $criteria_coligada_id = new TCriteria();

        $coligada_id = new TDBCombo('coligada_id', 'minicrm', 'Coligada', 'id', '{nome}','nome asc' , $criteria_coligada_id );
        $numero_inicial = new TEntry('numero_inicial');
        $numero_final = new TEntry('numero_final');


        $coligada_id->enableSearch();
        $coligada_id->setSize('100%');
        $numero_final->setSize('100%');
        $numero_inicial->setSize('100%');


        $row1 = $this->form->addFields([new TLabel("Coligada:", '#F44336', '14px', null, '100%'),$coligada_id],[new TLabel("Número inicial:", '#F44336', '14px', null, '100%'),$numero_inicial],[new TLabel("Número final:", '#F44336', '14px', null, '100%'),$numero_final]);
        $row1->layout = [' col-sm-4',' col-sm-4',' col-sm-4'];

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
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array

            $erros = array();
            $erros_service = array();
            $count = 0;
            $numero = $data->numero_inicial;
            $erro_encontrado = false; // Flag para controlar se houve erro
            TTransaction::open('minicrm');
            $coligada = Coligada::find($data->coligada_id);
            TTransaction::close();

            while ($numero <= (int) $data->numero_final && !$erro_encontrado) {

                // Executa o serviço de busca da nota
                $resultado = SigissWebService::buscarNota($numero, $data->coligada_id);

                // Verifica se houve erro no resultado
                if (is_array($resultado) && $resultado['status'] === 'error') {
                    $erros_service[] = "Erro na nota {$numero}: " . $resultado['mensagem'];
                    $erro_encontrado = true; // Para o loop
                    break;
                }

                $count++;
                $numero++;
            }

            // Tratamento após o loop
            if ($erro_encontrado) {
                // Exibe mensagens de erro e para a execução
                $mensagem_erro = "Processamento interrompido devido a erro(s):<br/>";
                if (!empty($erros)) {
                    $mensagem_erro .= implode("<br/>", $erros) . "<br/>";
                }
                if (!empty($erros_service)) {
                    $mensagem_erro .= implode("<br/>", $erros_service);
                }

                throw new Exception($mensagem_erro);
            } else {
                // Processamento concluído com sucesso
                TToast::show("success", "Processamento concluído. {$count} notas processadas.", "topRight", "fas:check-circle");
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

