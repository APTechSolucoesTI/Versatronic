<?php

class ViewComissaoRepresHeaderList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'ViewComissaoRepres';
    private static $primaryKey = 'id';
    private static $formName = 'formList_ViewComissaoRepres';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

    use BuilderDatagridTrait;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();
        // creates the form

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        $this->limit = 0;

        $criteria_coligada_id = new TCriteria();
        $criteria_representante = new TCriteria();
        $criteria_fantasia = new TCriteria();
        $criteria_categoria_cliente = new TCriteria();

        $coligada_id = new TDBCombo('coligada_id', 'minicrm', 'Coligada', 'id', '{nome}','nome asc' , $criteria_coligada_id );
        $representante = new TDBCombo('representante', 'minicrm', 'Representante', 'razao_social', '{razao_social}','razao_social asc' , $criteria_representante );
        $numero_nota = new TEntry('numero_nota');
        $data_emissao = new BDateRange('data_emissao', 'data_emissao_fim');
        $data_emissao_os = new BDateRange('data_emissao_os', 'data_emissao_os_fim');
        $codigo_cliente = new TEntry('codigo_cliente');
        $fantasia = new TDBCombo('fantasia', 'minicrm', 'Pessoa', 'nome_fantasia', '{nome_fantasia}','nome_fantasia asc' , $criteria_fantasia );
        $categoria_cliente = new TDBCombo('categoria_cliente', 'minicrm', 'CategoriaCliente', 'nome', '{nome}','nome asc' , $criteria_categoria_cliente );
        $valor = new TEntry('valor');
        $tem_comissao = new TCombo('tem_comissao');
        $comissao = new TEntry('comissao');

        $numero_nota->exitOnEnter();
        $codigo_cliente->exitOnEnter();
        $valor->exitOnEnter();
        $comissao->exitOnEnter();

        $numero_nota->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_emissao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $data_emissao_os->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $codigo_cliente->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $valor->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $comissao->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $coligada_id->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $representante->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $fantasia->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $categoria_cliente->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));
        $tem_comissao->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1', 'target_container' => $param['target_container'] ?? null]));

        $tem_comissao->addItems(["S"=>"Sim","N"=>"Não"]);
        $data_emissao->setMask('dd/mm/yyyy');
        $data_emissao_os->setMask('dd/mm/yyyy');

        $data_emissao->setDatabaseMask('yyyy-mm-dd');
        $data_emissao_os->setDatabaseMask('yyyy-mm-dd');

        $fantasia->enableSearch();
        $coligada_id->enableSearch();
        $tem_comissao->enableSearch();
        $representante->enableSearch();
        $categoria_cliente->enableSearch();

        $valor->setSize('100%');
        $fantasia->setSize('100%');
        $comissao->setSize('100%');
        $data_emissao->setSize(220);
        $coligada_id->setSize('100%');
        $numero_nota->setSize('100%');
        $data_emissao_os->setSize(220);
        $tem_comissao->setSize('100%');
        $representante->setSize('100%');
        $codigo_cliente->setSize('100%');
        $categoria_cliente->setSize('100%');


        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->enableUserProperties('fa fa-cog', 'btn btn-default', new TAction([$this, 'setDatagridProperties']));
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm(self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->disableDefaultClick();
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_coligada_id_transformed = new TDataGridColumn('coligada_id', "Coligada", 'left');
        $column_representante = new TDataGridColumn('representante', "Representante", 'left');
        $column_numero_nota = new TDataGridColumn('numero_nota', "Número NF", 'left' , '6%');
        $column_data_emissao_transformed = new TDataGridColumn('data_emissao', "Data Emissão", 'left');
        $column_data_emissao_os_transformed = new TDataGridColumn('data_emissao_os', "Data da O.S", 'left');
        $column_codigo_cliente = new TDataGridColumn('codigo_cliente', "Código", 'left' , '6%');
        $column_fantasia = new TDataGridColumn('fantasia', "Cliente", 'left' , '20%');
        $column_categoria_cliente = new TDataGridColumn('categoria_cliente', "Categoria", 'left');
        $column_valor_transformed = new TDataGridColumn('valor', "Valor", 'left' , '8%');
        $column_tem_comissao_transformed = new TDataGridColumn('tem_comissao', "Tem Comissão?", 'left' , '8%');
        $column_comissao_transformed = new TDataGridColumn('comissao', "Comissao", 'left' , '8%');

        $column_valor_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_comissao_transformed->setTotalFunction( function($values) { 
            return array_sum((array) $values); 
        }); 

        $column_coligada_id_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            $value = Coligada::find($object->coligada_id);

            return $value->nome;

        });

        $column_data_emissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_data_emissao_os_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_valor_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });

        $column_tem_comissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            $valor = strtoupper(trim((string) $value));

            if ($valor === 'S')
            {
                $texto = 'SIM';
                $cor   = '#198754';
            }
            elseif ($valor === 'C')
            {
                $texto = 'CANCELADA';
                $cor   = '#DC3545';
            }
            elseif ($valor === 'P')
            {
                $texto = 'CONFIRMADA';
                $cor   = '#0D6EFD';
            }
            else
            {
                $texto = 'NÃO';
                $cor   = '#DC3545';
            }

            $label = new TElement('span');

            $label->style = "
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 90px;
                height: 25px;
                padding: 5px 8px;
                box-sizing: border-box;
                border-radius: 6px;
                background-color: {$cor}
                ;
                color: #FFFFFF;
                font-size: 11px;
                font-weight: 700;
                text-align: center;
                line-height: 1;
                letter-spacing: 0.3px;
                text-transform: uppercase;
                box-shadow: 0 1px 2px rgba(0,0,0,0.12);
            ";

            $label->add($texto);

            /*
             * Cancelada ou Confirmada:
             * permite clicar para visualizar a justificativa.
             */
            if ($valor === 'C' || $valor === 'P')
            {
                $controle = ControleNota::where(
                    'nota_baixada_id',
                    '=',
                    $object->id
                )
                ->orderBy('id', 'desc')
                ->first();

                if ($controle)
                {
                    $label->style .= "
                        cursor: pointer;
                    ";

                    /*
                     * Define qual formulário abrir.
                     */
                    if ($valor === 'C')
                    {
                        $classeForm = 'ControleNotaForm';
                        $tituloLink = 'Visualizar justificativa do cancelamento';
                    }
                    else
                    {
                        $classeForm = 'ControleNotaFormConfirmar';
                        $tituloLink = 'Visualizar justificativa da confirmação';
                    }

                    $link = new TElement('a');

                    $link->href =
                        "index.php?class={$classeForm}" .
                        "&method=onEdit" .
                        "&key={$controle->id}" .
                        "&modo=visualizar";

                    $link->generator = 'adianti';
                    $link->title = $tituloLink;

                    $link->style = "
                        display: inline-block;
                        text-decoration: none;
                    ";

                    $link->add($label);

                    return $link;
                }
            }

            return $label;

        });

        $column_comissao_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!$value)
            {
                $value = 0;
            }

            if(is_numeric($value))
            {
                return "R$ " . number_format($value, 2, ",", ".");
            }
            else
            {
                return $value;
            }
        });        

        $order_data_emissao_transformed = new TAction(array($this, 'onReload'));
        $order_data_emissao_transformed->setParameter('order', 'data_emissao');
        $column_data_emissao_transformed->setAction($order_data_emissao_transformed);

        $column_tem_comissao_transformed->disableHtmlConversion();

        $this->datagrid->addColumn($column_coligada_id_transformed);
        $this->datagrid->addColumn($column_representante);
        $this->datagrid->addColumn($column_numero_nota);
        $this->datagrid->addColumn($column_data_emissao_transformed);
        $this->datagrid->addColumn($column_data_emissao_os_transformed);
        $this->datagrid->addColumn($column_codigo_cliente);
        $this->datagrid->addColumn($column_fantasia);
        $this->datagrid->addColumn($column_categoria_cliente);
        $this->datagrid->addColumn($column_valor_transformed);
        $this->datagrid->addColumn($column_tem_comissao_transformed);
        $this->datagrid->addColumn($column_comissao_transformed);

        $action_onShow = new TDataGridAction(array('ControleNotaForm', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Cancelar Comissão");
        $action_onShow->setImage('fas:ban #F44336');
        $action_onShow->setField(self::$primaryKey);
        $action_onShow->setDisplayCondition('ViewComissaoRepresHeaderList::canCancelar');
        $action_onShow->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onShow);

        $action_ControleNotaFormConfirmar_onShow = new TDataGridAction(array('ControleNotaFormConfirmar', 'onShow'));
        $action_ControleNotaFormConfirmar_onShow->setUseButton(false);
        $action_ControleNotaFormConfirmar_onShow->setButtonClass('btn btn-default btn-sm');
        $action_ControleNotaFormConfirmar_onShow->setLabel("");
        $action_ControleNotaFormConfirmar_onShow->setImage('fas:check-circle #4CAF50');
        $action_ControleNotaFormConfirmar_onShow->setField(self::$primaryKey);

        $action_ControleNotaFormConfirmar_onShow->setParameter('key', '{id}');

        $this->datagrid->addAction($action_ControleNotaFormConfirmar_onShow);

        $action_onVisualizarInteracao = new TDataGridAction(array('ViewComissaoRepresHeaderList', 'onVisualizarInteracao'));
        $action_onVisualizarInteracao->setUseButton(false);
        $action_onVisualizarInteracao->setButtonClass('btn btn-default btn-sm');
        $action_onVisualizarInteracao->setLabel("Visualizar Interações");
        $action_onVisualizarInteracao->setImage('fas:search-plus #000000');
        $action_onVisualizarInteracao->setField(self::$primaryKey);

        $action_onVisualizarInteracao->setParameter('codigoc', '{codigo_cliente}');

        $this->datagrid->addAction($action_onVisualizarInteracao);

        $this->applyDatagridProperties();

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        if(!$action_onShow->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_ControleNotaFormConfirmar_onShow->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_onVisualizarInteracao->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_coligada_id = TElement::tag('td', $coligada_id);
        $tr->add($td_coligada_id);
        $td_representante = TElement::tag('td', $representante);
        $tr->add($td_representante);
        $td_numero_nota = TElement::tag('td', $numero_nota);
        $tr->add($td_numero_nota);
        $td_data_emissao = TElement::tag('td', $data_emissao);
        $tr->add($td_data_emissao);
        $td_data_emissao_os = TElement::tag('td', $data_emissao_os);
        $tr->add($td_data_emissao_os);
        $td_codigo_cliente = TElement::tag('td', $codigo_cliente);
        $tr->add($td_codigo_cliente);
        $td_fantasia = TElement::tag('td', $fantasia);
        $tr->add($td_fantasia);
        $td_categoria_cliente = TElement::tag('td', $categoria_cliente);
        $tr->add($td_categoria_cliente);
        $td_valor = TElement::tag('td', $valor);
        $tr->add($td_valor);
        $td_tem_comissao = TElement::tag('td', $tem_comissao);
        $tr->add($td_tem_comissao);
        $td_comissao = TElement::tag('td', $comissao);
        $tr->add($td_comissao);
        $tr->add(TElement::tag('td', ''));

        $this->datagrid_form->addField($coligada_id);
        $this->datagrid_form->addField($representante);
        $this->datagrid_form->addField($numero_nota);
        $this->datagrid_form->addField($data_emissao);
        $this->datagrid_form->addField($data_emissao_os);
        $this->datagrid_form->addField($codigo_cliente);
        $this->datagrid_form->addField($fantasia);
        $this->datagrid_form->addField($categoria_cliente);
        $this->datagrid_form->addField($valor);
        $this->datagrid_form->addField($tem_comissao);
        $this->datagrid_form->addField($comissao);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $this->datagrid->disableDefaultClick(); 

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $panel->getHeader()->style = ' display:none !important; ';
        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($headerActions);
        $panel->add($this->datagrid_form);

        $button_gerar_analitico = new TButton('button_button_gerar_analitico');
        $button_gerar_analitico->setAction(new TAction(['ViewComissaoRepresHeaderList', 'onGerarAnalitico']), "Gerar Analítico");
        $button_gerar_analitico->addStyleClass('btn-default');
        $button_gerar_analitico->setImage('fas:file-excel #4CAF50');

        $this->datagrid_form->addField($button_gerar_analitico);

        $head_right_actions->add($button_gerar_analitico);

        $this->datagrid_form->add($this->datagrid);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Gerencia","Comissão de Representante"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public static function canCancelar($object)
    {
        try 
        {
            if($object->tem_comissao == 'S')
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onVisualizarInteracao($param = null) 
    {
        $transactionOpen = false;

            try 
            {
                $codigoCliente = trim((string) ($param['codigoc'] ?? ''));

                if ($codigoCliente === '')
                {
                    throw new Exception('Código do cliente não informado.');
                }

                TTransaction::open('minicrm');
                $transactionOpen = true;

                /*
                * Localiza a pessoa pelo mesmo código que aparece
                * na ViewComissaoRepres.
                */
                $cliente = Pessoa::where('codigo', '=', $codigoCliente)->first();

                if (!$cliente)
                {
                    throw new Exception(
                        "Cliente {$codigoCliente} não encontrado no CRM."
                    );
                }

                /*
                * Todas as interações desse cliente.
                */
                $interacoes = Interacao::where('cliente_id', '=', $cliente->id)
                    ->orderBy('id', 'desc')
                    ->load();

                $nomeCliente =
                    $cliente->nome_fantasia
                    ?: $cliente->razao_social
                    ?: $codigoCliente;

                /*
                * Resolve nome de registros relacionados sem quebrar caso
                * determinado model/campo não exista.
                */
                $resolverNome = function ($classe, $id, $fallback = '-') 
                {
                    if (empty($id) || !class_exists($classe))
                    {
                        return $fallback;
                    }

                    $registro = $classe::find($id);

                    if (!$registro)
                    {
                        return $fallback;
                    }

                    foreach ([
                        'nome',
                        'descricao',
                        'razao_social',
                        'nome_fantasia'
                    ] as $campo)
                    {
                        if (!empty($registro->$campo))
                        {
                            return $registro->$campo;
                        }
                    }

                    return $fallback;
                };

                $cards = '';

                if ($interacoes)
                {
                    foreach ($interacoes as $interacao)
                    {
                        /*
                        * Tipo da interação
                        */
                        $tipoFallback = !empty($interacao->tipo_interacao_id)
                            ? 'Tipo #' . $interacao->tipo_interacao_id
                            : '-';

                        $tipo = $resolverNome(
                            'TipoInteracao',
                            $interacao->tipo_interacao_id ?? null,
                            $tipoFallback
                        );

                        /*
                        * Etapa atual
                        */
                        $etapaFallback = !empty($interacao->etapa_interacao_id)
                            ? 'Etapa #' . $interacao->etapa_interacao_id
                            : '-';

                        $etapa = $resolverNome(
                            'EtapaInteracao',
                            $interacao->etapa_interacao_id ?? null,
                            $etapaFallback
                        );

                        /*
                        * No seu sistema vendedor_id é usado nas interações
                        * junto com representante.
                        */
                        $vendedor = $resolverNome(
                            'Representante',
                            $interacao->vendedor_id ?? null,
                            '-'
                        );

                        /*
                        * Atividades da interação.
                        */
                        $qtdAtividades = InteracaoAtividade::where(
                            'interacao_id',
                            '=',
                            $interacao->id
                        )->count();

                        $ultimaAtividade = InteracaoAtividade::where(
                            'interacao_id',
                            '=',
                            $interacao->id
                        )
                        ->orderBy('id', 'desc')
                        ->first();

                        /*
                        * Histórico de etapas também ajuda a determinar
                        * a última movimentação.
                        */
                        $ultimoHistorico = InteracaoHistoricoEtapa::where(
                            'interacao_id',
                            '=',
                            $interacao->id
                        )
                        ->orderBy('dt_etapa', 'desc')
                        ->first();

                        $datas = [];

                        if ($ultimaAtividade)
                        {
                            if (!empty($ultimaAtividade->horario_final))
                            {
                                $datas[] = $ultimaAtividade->horario_final;
                            }
                            elseif (!empty($ultimaAtividade->horario_inicial))
                            {
                                $datas[] = $ultimaAtividade->horario_inicial;
                            }
                        }

                        if ($ultimoHistorico && !empty($ultimoHistorico->dt_etapa))
                        {
                            $datas[] = $ultimoHistorico->dt_etapa;
                        }

                        $ultimaMovimentacao = '-';

                        if ($datas)
                        {
                            $maiorTimestamp = 0;
                            $ultimaData = null;

                            foreach ($datas as $data)
                            {
                                $timestamp = strtotime($data);

                                if ($timestamp && $timestamp > $maiorTimestamp)
                                {
                                    $maiorTimestamp = $timestamp;
                                    $ultimaData = $data;
                                }
                            }

                            if ($ultimaData)
                            {
                                $ultimaMovimentacao = date(
                                    'd/m/Y H:i',
                                    strtotime($ultimaData)
                                );
                            }
                        }

                        /*
                        * Escapa tudo que vai para HTML.
                        */
                        $idHtml = (int) $interacao->id;

                        $tipoHtml = htmlspecialchars(
                            (string) $tipo,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $etapaHtml = htmlspecialchars(
                            (string) $etapa,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $vendedorHtml = htmlspecialchars(
                            (string) $vendedor,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        $ultimaHtml = htmlspecialchars(
                            (string) $ultimaMovimentacao,
                            ENT_QUOTES,
                            'UTF-8'
                        );

                        /*
                        * IMPORTANTE:
                        * não usamos generator="adianti" aqui justamente
                        * porque queremos abrir em uma NOVA GUIA.
                        */
                        $urlInteracao =
                            "index.php?class=InteracaoFormView" .
                            "&method=onShow" .
                            "&key={$idHtml}";

                        $cards .= "
                            <div class='interacao-history-card'>

                                <div class='interacao-history-card-top'>

                                    <div>
                                        <div class='interacao-history-id'>
                                            <i class='fas fa-comments'></i>
                                            Interação #{$idHtml}
                                        </div>

                                        <div class='interacao-history-type'>
                                            {$tipoHtml}
                                        </div>
                                    </div>

                                    <div class='interacao-history-stage'>
                                        {$etapaHtml}
                                    </div>

                                </div>

                                <div class='interacao-history-info'>

                                    <div class='interacao-history-info-item'>
                                        <span>Última movimentação</span>
                                        <strong>
                                            <i class='far fa-clock'></i>
                                            {$ultimaHtml}
                                        </strong>
                                    </div>

                                    <div class='interacao-history-info-item'>
                                        <span>Atividades</span>
                                        <strong>
                                            <i class='fas fa-tasks'></i>
                                            {$qtdAtividades}
                                        </strong>
                                    </div>

                                    <div class='interacao-history-info-item interacao-history-info-full'>
                                        <span>Responsável</span>
                                        <strong>
                                            <i class='fas fa-user'></i>
                                            {$vendedorHtml}
                                        </strong>
                                    </div>

                                </div>

                                <div class='interacao-history-actions'>
                                    <a
                                        href='{$urlInteracao}'
                                        target='_blank'
                                        rel='noopener noreferrer'
                                        class='btn btn-primary btn-sm'
                                    >
                                        <i class='fas fa-external-link-alt'></i>
                                        Abrir interação
                                    </a>
                                </div>

                            </div>
                        ";
                    }
                }
                else
                {
                    $cards = "
                        <div class='interacao-history-empty'>
                            <i class='far fa-comments'></i>

                            <strong>Nenhuma interação encontrada</strong>

                            <span>
                                Este cliente ainda não possui interações cadastradas.
                            </span>
                        </div>
                    ";
                }

                $nomeClienteHtml = htmlspecialchars(
                    (string) $nomeCliente,
                    ENT_QUOTES,
                    'UTF-8'
                );

                $codigoClienteHtml = htmlspecialchars(
                    (string) $codigoCliente,
                    ENT_QUOTES,
                    'UTF-8'
                );

                $totalInteracoes = count($interacoes ?: []);

                TTransaction::close();
                $transactionOpen = false;

                /*
                * Conteúdo do painel.
                */
                $container = new TElement('div');
                $container->class = 'interacao-history-container';

                $container->add("
                    <style>

                        .interacao-history-container {
                            padding: 0;
                            background: #f5f6f8;
                            min-height: 100vh;
                        }

                        .interacao-history-header {
                            background: #fff;
                            padding: 18px 20px;
                            border-bottom: 1px solid #e5e7eb;
                            position: sticky;
                            top: 0;
                            z-index: 20;
                        }

                        .interacao-history-header-top {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            gap: 15px;
                        }

                        .interacao-history-title {
                            font-size: 18px;
                            font-weight: 700;
                            color: #1f2937;
                            margin: 0;
                        }

                        .interacao-history-subtitle {
                            margin-top: 5px;
                            color: #6b7280;
                            font-size: 13px;
                        }

                        .interacao-history-count {
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            background: #eef2ff;
                            color: #4338ca;
                            border-radius: 20px;
                            padding: 4px 10px;
                            font-size: 12px;
                            font-weight: 700;
                            margin-left: 6px;
                        }

                        .interacao-history-list {
                            padding: 16px;
                        }

                        .interacao-history-card {
                            background: #fff;
                            border: 1px solid #e5e7eb;
                            border-radius: 10px;
                            padding: 16px;
                            margin-bottom: 12px;
                            box-shadow: 0 1px 3px rgba(0,0,0,.04);
                            transition: all .15s ease;
                        }

                        .interacao-history-card:hover {
                            border-color: #c7d2fe;
                            box-shadow: 0 4px 12px rgba(0,0,0,.08);
                        }

                        .interacao-history-card-top {
                            display: flex;
                            align-items: flex-start;
                            justify-content: space-between;
                            gap: 12px;
                            margin-bottom: 14px;
                        }

                        .interacao-history-id {
                            font-size: 15px;
                            font-weight: 700;
                            color: #111827;
                        }

                        .interacao-history-id i {
                            color: #4f46e5;
                            margin-right: 5px;
                        }

                        .interacao-history-type {
                            color: #6b7280;
                            font-size: 12px;
                            margin-top: 3px;
                        }

                        .interacao-history-stage {
                            background: #ecfdf5;
                            color: #047857;
                            border: 1px solid #a7f3d0;
                            border-radius: 20px;
                            padding: 4px 9px;
                            font-size: 11px;
                            font-weight: 700;
                            text-align: center;
                        }

                        .interacao-history-info {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 12px;
                            padding: 12px;
                            background: #f9fafb;
                            border-radius: 8px;
                        }

                        .interacao-history-info-item {
                            display: flex;
                            flex-direction: column;
                            gap: 3px;
                        }

                        .interacao-history-info-item span {
                            color: #9ca3af;
                            font-size: 10px;
                            font-weight: 600;
                            text-transform: uppercase;
                            letter-spacing: .4px;
                        }

                        .interacao-history-info-item strong {
                            color: #374151;
                            font-size: 12px;
                            font-weight: 600;
                        }

                        .interacao-history-info-item strong i {
                            margin-right: 4px;
                            color: #6b7280;
                        }

                        .interacao-history-info-full {
                            grid-column: 1 / -1;
                        }

                        .interacao-history-actions {
                            display: flex;
                            justify-content: flex-end;
                            margin-top: 13px;
                        }

                        .interacao-history-actions a {
                            text-decoration: none !important;
                        }

                        .interacao-history-empty {
                            background: #fff;
                            border: 1px dashed #d1d5db;
                            border-radius: 10px;
                            padding: 45px 20px;
                            text-align: center;
                            color: #6b7280;
                        }

                        .interacao-history-empty i {
                            display: block;
                            font-size: 36px;
                            margin-bottom: 12px;
                            color: #9ca3af;
                        }

                        .interacao-history-empty strong {
                            display: block;
                            color: #374151;
                            font-size: 15px;
                            margin-bottom: 5px;
                        }

                        .interacao-history-empty span {
                            font-size: 12px;
                        }

                    </style>

                    <div class='interacao-history-header'>

                        <div class='interacao-history-header-top'>

                            <div>
                                <div class='interacao-history-title'>
                                    Histórico de Interações
                                </div>

                                <div class='interacao-history-subtitle'>
                                    {$nomeClienteHtml}
                                    · Código {$codigoClienteHtml}

                                    <span class='interacao-history-count'>
                                        {$totalInteracoes}
                                    </span>
                                </div>
                            </div>

                            <button
                                type='button'
                                class='btn btn-default btn-sm'
                                onclick='Template.closeRightPanel();'
                                title='Fechar'
                            >
                                <i class='fas fa-times'></i>
                            </button>

                        </div>

                    </div>

                    <div class='interacao-history-list'>
                        {$cards}
                    </div>
                ");

                /*
                * Abre no painel lateral direito.
                */
                $page = new TPage();

                $page->setTargetContainer('adianti_right_panel');

                $page->setProperty(
                    'page-name',
                    'ViewComissaoRepresInteracoes'
                );

                $page->setProperty(
                    'page_name',
                    'ViewComissaoRepresInteracoes'
                );

                $page->adianti_target_container = 'adianti_right_panel';
                $page->target_container = 'adianti_right_panel';

                $page->add($container);

                $page->setIsWrapped(true);

                $page->show();

                /*
                * Largura do painel.
                */
                $style = new TStyle(
                    'right-panel > .container-part[page-name=ViewComissaoRepresInteracoes]'
                );

                $style->width = '48% !important';
                $style->show(true);

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onGerarAnalitico($param = null) 
    {
        try 
        {
            TScript::create("
                window.open(
                    'https://metabase.aptechinfo.com.br:94/public/dashboard/e72b25f7-ac46-437c-b11a-da5f03ea94a0',
                    '_blank'
                );
            ");
            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        // get the search form data
        $data = $this->datagrid_form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->coligada_id) AND ( (is_scalar($data->coligada_id) AND $data->coligada_id !== '') OR (is_array($data->coligada_id) AND (!empty($data->coligada_id)) )) )
        {

            $filters[] = new TFilter('coligada_id', '=', $data->coligada_id);// create the filter 
        }

        if (isset($data->representante) AND ( (is_scalar($data->representante) AND $data->representante !== '') OR (is_array($data->representante) AND (!empty($data->representante)) )) )
        {

            $filters[] = new TFilter('representante', '=', $data->representante);// create the filter 
        }

        if (isset($data->numero_nota) AND ( (is_scalar($data->numero_nota) AND $data->numero_nota !== '') OR (is_array($data->numero_nota) AND (!empty($data->numero_nota)) )) )
        {

            $filters[] = new TFilter('numero_nota', '=', $data->numero_nota);// create the filter 
        }

        if (isset($data->data_emissao_fim) AND ( (is_scalar($data->data_emissao_fim) AND $data->data_emissao_fim !== '') OR (is_array($data->data_emissao_fim) AND (!empty($data->data_emissao_fim)) )) )
        {

            $filters[] = new TFilter('data_emissao', '<=', $data->data_emissao_fim);// create the filter 
        }

        if (isset($data->data_emissao) AND ( (is_scalar($data->data_emissao) AND $data->data_emissao !== '') OR (is_array($data->data_emissao) AND (!empty($data->data_emissao)) )) )
        {

            $filters[] = new TFilter('data_emissao', '>=', $data->data_emissao);// create the filter 
        }

        if (isset($data->data_emissao_os_fim) AND ( (is_scalar($data->data_emissao_os_fim) AND $data->data_emissao_os_fim !== '') OR (is_array($data->data_emissao_os_fim) AND (!empty($data->data_emissao_os_fim)) )) )
        {

            $filters[] = new TFilter('data_emissao_os', '<=', $data->data_emissao_os_fim);// create the filter 
        }

        if (isset($data->data_emissao_os) AND ( (is_scalar($data->data_emissao_os) AND $data->data_emissao_os !== '') OR (is_array($data->data_emissao_os) AND (!empty($data->data_emissao_os)) )) )
        {

            $filters[] = new TFilter('data_emissao_os', '>=', $data->data_emissao_os);// create the filter 
        }

        if (isset($data->codigo_cliente) AND ( (is_scalar($data->codigo_cliente) AND $data->codigo_cliente !== '') OR (is_array($data->codigo_cliente) AND (!empty($data->codigo_cliente)) )) )
        {

            $filters[] = new TFilter('codigo_cliente', '=', $data->codigo_cliente);// create the filter 
        }

        if (isset($data->fantasia) AND ( (is_scalar($data->fantasia) AND $data->fantasia !== '') OR (is_array($data->fantasia) AND (!empty($data->fantasia)) )) )
        {

            $filters[] = new TFilter('fantasia', 'ilike', "%{$data->fantasia}%");// create the filter 
        }

        if (isset($data->categoria_cliente) AND ( (is_scalar($data->categoria_cliente) AND $data->categoria_cliente !== '') OR (is_array($data->categoria_cliente) AND (!empty($data->categoria_cliente)) )) )
        {

            $filters[] = new TFilter('categoria_cliente', '=', $data->categoria_cliente);// create the filter 
        }

        if (isset($data->valor) AND ( (is_scalar($data->valor) AND $data->valor !== '') OR (is_array($data->valor) AND (!empty($data->valor)) )) )
        {

            $filters[] = new TFilter('valor', '=', $data->valor);// create the filter 
        }

        if (isset($data->tem_comissao) AND ( (is_scalar($data->tem_comissao) AND $data->tem_comissao !== '') OR (is_array($data->tem_comissao) AND (!empty($data->tem_comissao)) )) )
        {

            $filters[] = new TFilter('tem_comissao', '=', $data->tem_comissao);// create the filter 
        }

        if (isset($data->comissao) AND ( (is_scalar($data->comissao) AND $data->comissao !== '') OR (is_array($data->comissao) AND (!empty($data->comissao)) )) )
        {

            $filters[] = new TFilter('comissao', '=', $data->comissao);// create the filter 
        }

        // fill the form with data again
        $this->datagrid_form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            $onReloadParam = ['offset' => 0, 'first_page' => 1, 'target_container' => $param['target_container'] ?? null];
            AdiantiCoreApplication::loadPage($class, 'onReload', $onReloadParam);
            TScript::create('$(".select2").prev().select2("close");');
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minicrm'
            TTransaction::open(self::$database);

            // creates a repository for ViewComissaoRepres
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }
            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            $this->datagrid->initPopoverHeaderFilters();

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new ViewComissaoRepres($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

