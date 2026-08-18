<?php

class BuscarCNPJForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_BuscarCNPJForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();
        parent::setSize(0.30, null);
        parent::setTitle("Buscar CNPJ");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Buscar CNPJ");


        $cnpj = new TEntry('cnpj');
        $button_buscar = new TButton('button_buscar');
        $campo = new THidden('campo');
        $form = new THidden('form');


        $cnpj->setMask('##.###.###/####-##');
        $button_buscar->setAction(new TAction([$this, 'onBuscarCNPJ']), "Buscar");
        $button_buscar->addStyleClass('btn-default');
        $button_buscar->setImage('fas:search #000000');
        $form->setSize(200);
        $campo->setSize(200);
        $cnpj->setSize('calc(100% - 100px)');


        $form->setValue($param['form']);
        $campo->setValue($param['campo']);

        $cnpj->autofocus = 'autofocus';
        $row1 = $this->form->addFields([new TLabel("CNPJ:", null, '14px', null, '100%'),$cnpj,$button_buscar,$campo,$form]);
        $row1->layout = [' col-sm-12'];

        // create the form actions


        parent::add($this->form);

    }

    public static function onBuscarCNPJ($param = null) 
    {
        try 
        {
            if (!isset($param['cnpj']) || empty($param['cnpj'])) {
                throw new Exception('CNPJ não informado');
            }

            TTransaction::open('minicrm');
            $dados = CNPJService::get($param['cnpj']);
            $dadosFull = CNPJService::getFull($param['cnpj']);

            if(!$dados)
            {
                throw new Exception('CNPJ não encontrado');
            }

            if(!$dadosFull)
            {
                throw new Exception('CNPJ Full não encontrado');
            }
            TCombo::reload(self::$formName, 'pessoa_endereco_pessoa_cidade_estado_id', Estado::getIndexedArray('id', 'nome'), true);

            TTransaction::close();

            $object = new stdClass();

            $object->razao_social = $dados->razao_social;
            $object->nome_fantasia = $dados->razao_social;
            $object->fone = $dados->ddd_telefone_1 ?? NULL;
            $object->email = $dadosFull->estabelecimento->email ?? NULL;

            $object->cpf_cnpj = $dados->cnpj;
            $object->rg_ie = $dadosFull->estabelecimento->inscricoes_estaduais[0]->inscricao_estadual ?? NULL;

            // dados relacionados ao endereço
            $object->pessoa_endereco_pessoa_cep = $dados->cep;
            $object->pessoa_endereco_pessoa_rua = $dados->logradouro;
            $object->pessoa_endereco_pessoa_bairro = $dados->bairro;
            $object->pessoa_endereco_pessoa_numero = $dados->numero;
            $object->pessoa_endereco_pessoa_complemento = $dados->complemento;
            $object->pessoa_endereco_pessoa_cidade_estado_id = $dados->estado_id ?? null;
            $object->pessoa_endereco_pessoa_cidade_id = $dados->cidade_id ?? null;

            if (!empty($param['form']))
            {
                TScript::create("$(\"[page_name='BuscarCNPJForm']\").remove()");
                TToast::show("show", "Sucesso ao buscar!", "topRight", "fas:check-circle");
                return TForm::sendData($param['form'], $object);
            }else{
                throw new Exception('Não foi possível carregar os dados, tente novamente!');
            }

        }
        catch (Exception $e) 
        {
            TTransaction::rollback(); 
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onShow($param = null)
    {               

    } 

}

