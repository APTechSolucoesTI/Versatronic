<?php

class InteracaoSimplesForm extends TWindow
{
    protected $form;
    private $formFields = [];
    private static $database = '';
    private static $activeRecord = '';
    private static $primaryKey = '';
    private static $formName = 'form_InteracaoSimplesForm';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null)
    {
        parent::__construct();
        parent::setSize(0.60, null);
        parent::setTitle("Cadastro de interação simples");
        parent::setProperty('class', 'window_modal');

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de interação simples");

        $criteria_representante_id = new TCriteria();
        $criteria_cliente_id = new TCriteria();
        $criteria_tipo_atividade_id = new TCriteria();

        $filterVar = Grupo::CLIENTE;
        $criteria_cliente_id->add(new TFilter('id', 'in', "(SELECT pessoa_id FROM pessoa_grupo WHERE grupo_id = '{$filterVar}')")); 
        $filterVar = TipoInteracao::SIMPLES;
        $criteria_tipo_atividade_id->add(new TFilter('id', 'in', "(SELECT tipo_atividade_id FROM tipo_atividade_interacao WHERE tipo_interacao_id = '{$filterVar}')")); 

        $representante_id = new TDBCombo('representante_id', 'minicrm', 'Representante', 'id', '{razao_social}','razao_social asc' , $criteria_representante_id );
        $cliente_id = new TDBUniqueSearch('cliente_id', 'minicrm', 'Pessoa', 'id', 'razao_social','razao_social asc' , $criteria_cliente_id );
        $cidade_uf = new TEntry('cidade_uf');
        $cliente_nome = new TEntry('cliente_nome');
        $diversos = new TCheckButton('diversos');
        $tipo_atividade_id = new TDBCombo('tipo_atividade_id', 'minicrm', 'TipoAtividade', 'id', '{nome}','nome asc' , $criteria_tipo_atividade_id );
        $descricao = new TEntry('descricao');
        $observacao = new TText('observacao');

        $cliente_id->setChangeAction(new TAction([$this,'onSelect']));
        $diversos->setChangeAction(new TAction([$this,'onSelectDiversos']));

        $cliente_id->addValidation("Cliente", new TRequiredValidator()); 

        $cliente_id->setMinLength(2);
        $cliente_id->setMask('{razao_social}');
        $cliente_id->setFilterColumns(["razao_social"]);
        $cidade_uf->setEditable(false);
        $diversos->setValue('N');
        $diversos->setUseSwitch(true, 'blue');
        $diversos->setIndexValue("S");
        $diversos->setInactiveIndexValue("N");
        $representante_id->enableSearch();
        $tipo_atividade_id->enableSearch();

        $cidade_uf->setSize('35%');
        $descricao->setSize('100%');
        $cliente_nome->setSize('100%');
        $observacao->setSize('100%', 250);
        $representante_id->setSize('100%');
        $tipo_atividade_id->setSize('100%');
        $cliente_id->setSize('calc(65% - 6px)');


        $row1 = $this->form->addFields([new TLabel("Representante:", '#FF0000', '14px', null, '100%'),$representante_id]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Cliente:", '#FF0000', '14px', null, '100%'),$cliente_id,$cidade_uf,$cliente_nome],[new TLabel("Diversos:", null, '14px', null, '100%'),$diversos]);
        $row2->layout = [' col-sm-9',' col-sm-3'];

        $row3 = $this->form->addFields([new TLabel("Tipo de atividade:", '#FF0000', '14px', null, '100%'),$tipo_atividade_id]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Descrição:", null, '14px', null, '100%'),$descricao]);
        $row4->layout = [' col-sm-12'];

        $row5 = $this->form->addFields([new TLabel("Histórico:", null, '14px', null, '100%'),$observacao]);
        $row5->layout = [' col-sm-12'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fas:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        TTransaction::open('minicrm');
        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->first();
        if($representante){
            $representante_id->setValue($representante->id);
            $representante_id->setEditable(false);
        }
        TTransaction::close();
        TScript::create("$(\"[name='cliente_nome']\").closest('.fb-inline-field-container').hide()");

        parent::add($this->form);

    }

    public static function onSelect($param = null) 
    {
        try 
        {
            if($param['cliente_id'])
            {
                TTransaction::open('minicrm');
                $pessoa = Pessoa::find($param['cliente_id']);

                $object = new stdClass();
                $object->cidade_uf = $pessoa->cidade_uf;

                TForm::sendData(self::$formName, $object);
                TTransaction::close();
            }

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function onSelectDiversos($param = null) 
    {
        try 
        {
            if($param['diversos']){

                $object = new stdClass();

                if($param['diversos'] == 'S'){
                    $object->cliente_id = null;
                    TScript::create("$(\"[name='cliente_id']\").closest('.fb-inline-field-container').hide()");
                    TScript::create("$(\"[name='cidade_uf']\").closest('.fb-inline-field-container').hide()");
                    TScript::create("$(\"[name='cliente_nome']\").closest('.fb-inline-field-container').show()");
                }elseif($param['diversos'] == 'N'){
                    $object->cliente_nome = null;
                    TScript::create("$(\"[name='cliente_id']\").closest('.fb-inline-field-container').show()");
                    TScript::create("$(\"[name='cliente_nome']\").closest('.fb-inline-field-container').hide()");
                }

                TForm::sendData(self::$formName, $object);
            }

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
            $exception = array();
            if($param['tipo_atividade_id'] == null){
                $exception[] = "O campo tipo de interação é obrigatório.";
            }
            if($param['cliente_id'] == null && $param['cliente_nome'] == null){
                $exception[] = "O campo cliente é obrigatório.";
            }
            if($param['representante_id'] == null){
                $exception[] = "O campo representante é obrigatório.";
            }

            if(count($exception) > 0){
                throw new Exception(implode('<br/>', $exception));
            }

            TTransaction::open('minicrm');
            $object = new Interacao();
            $object->tipo_interacao_id = TipoInteracao::SIMPLES;
            $object->cliente_id = $param['cliente_id'];
            $object->cliente_nome = $param['cliente_nome'];
            $object->vendedor_id = $param['representante_id'];
            $object->etapa_interacao_id = EtapaInteracao::FINALIZADA;
            $object->data_inicio = date('Y-m-d H:i:00');
            $object->data_fechamento = date('Y-m-d H:i:05');
            $object->mes = date('m');
            $object->ano = date('Y');
            $object->store();

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = 5;
            $interacaoHistoricoEtapa->interacao_id = $object->id;
            $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:00');
            $interacaoHistoricoEtapa->store();

            $atividade = new InteracaoAtividade();
            $atividade->tipo_atividade_id = $param['tipo_atividade_id'];
            $atividade->interacao_id = $object->id;
            $atividade->estado_atividade_id = EstadoAtividade::CONCLUIDO;
            $atividade->descricao = $param['descricao'] ?? null;
            $atividade->horario_inicial = date('Y-m-d H:i:00');
            $atividade->horario_final = date('Y-m-d H:i:00');
            $atividade->observacao = $param['observacao'] ?? null;
            $atividade->dt_atividade = date('Y-m-d H:i:00');
            $atividade->store();

            $interacaoHistoricoAtividade = new InteracaoHistoricoAtividade;
            $interacaoHistoricoAtividade->interacao_id = $object->id;
            $interacaoHistoricoAtividade->dt_atividade = date('Y-m-d H:i:30');
            $interacaoHistoricoAtividade->descricao = $atividade->descricao;
            $interacaoHistoricoAtividade->observacao = $atividade->observacao;
            $interacaoHistoricoAtividade->horario_inicial = $atividade->horario_inicial;
            $interacaoHistoricoAtividade->horario_final = $atividade->horario_final;
            $interacaoHistoricoAtividade->tipo_atividade_id = $atividade->tipo_atividade_id;
            $interacaoHistoricoAtividade->estado_atividade_id = EstadoAtividade::CONCLUIDO;
            $interacaoHistoricoAtividade->movimentacao_id = Movimentacao::CRIADO;
            $interacaoHistoricoAtividade->store();

            $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
            $interacaoHistoricoEtapa->etapa_interacao_id = EtapaInteracao::FINALIZADA;
            $interacaoHistoricoEtapa->interacao_id = $object->id;
            $interacaoHistoricoEtapa->dt_etapa =$atividade->horario_final;
            $interacaoHistoricoEtapa->store();

            TTransaction::close();

            TToast::show("success", "Registro de interação simples salvo.", "topRight", "fas:check-circle");

            TApplication::loadPage('InteracaoList', 'onShow');
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {               

    } 

}

