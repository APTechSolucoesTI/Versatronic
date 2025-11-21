<?php

class InteracaoKanbanView extends TPage
{
    private static $database = 'minicrm';
    private static $activeRecord = 'Interacao';
    private static $primaryKey = 'id';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        try
        {
            parent::__construct();

            $kanban = new TKanban;
            $kanban->setItemDatabase(self::$database);

            $limit = 20;
            $kanban->setLoadMoreAction(new TAction([$this, 'onLoadMore'], $param), $limit);

            $criteriaStage = new TCriteria();
            $criteriaItem = new TCriteria();

            $criteriaStage->setProperty('order', 'ordem asc');
            $criteriaItem->setProperty('order', 'ordem asc');

            $filterVar = "T";
            $criteriaStage->add(new TFilter('kanban', '=', $filterVar)); 

            TSession::setValue(__CLASS__.'load_filter_cliente_id', null);
            TSession::setValue(__CLASS__.'load_filter_vendedor_id', null);
            TSession::setValue(__CLASS__.'load_filter_origem_contato_id', null);

            if(!empty($param["cliente_id"] ?? ""))
            {
                TSession::setValue(__CLASS__.'load_filter_cliente_id', $param["cliente_id"] ?? "");
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_cliente_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('cliente_id', '=', $filterVar)); 
            }
            if(!empty($param["vendedor_id"] ?? ""))
            {
                TSession::setValue(__CLASS__.'load_filter_vendedor_id', $param["vendedor_id"] ?? "");
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_vendedor_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('vendedor_id', '=', $filterVar)); 
            }
            if(!empty($param["origem_contato_id"] ?? ""))
            {
                TSession::setValue(__CLASS__.'load_filter_origem_contato_id', $param["origem_contato_id"] ?? "");
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_origem_contato_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('origem_contato_id', '=', $filterVar)); 
            }

            $filterVar = [EtapaInteracao::CANCELADA, EtapaInteracao::FINALIZADA];
            $criteriaItem->add(new TFilter('etapa_interacao_id', 'not in', $filterVar)); 

             TTransaction::open(self::$database);
            $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->load();
            if($representante){
                $filter = new TFilter('id', 'in', "(SELECT id FROM interacao WHERE deleted_at is null AND vendedor_id  in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");
                $criteriaItem->add($filter);  
            }

            TTransaction::close();

            TTransaction::open(self::$database);
            $stages = EtapaInteracao::getObjects($criteriaStage);

            if($stages)
            {
                foreach ($stages as $key => $stage)
                {

                    $criteriaItemStage = clone $criteriaItem;
                    $criteriaItemStage->add(new TFilter('etapa_interacao_id', '=', $stage->id));
                    $criteriaItemStage->setProperty('limit', $limit);

                    $kanban->addStage($stage->id, "{nome}", $stage ,$stage->cor);

                    $items = Interacao::getObjects($criteriaItemStage);

                    if ($stage->id == EtapaInteracao::FINALIZADA)
                    {
                        $criteriaInteracoesFinalizadas = new TCriteria;
                        $criteriaInteracoesFinalizadas->add(new TFilter('etapa_interacao_id', '=', $stage->id));

                        TTransaction::open(self::$database);
                        $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->load();
                        if($representante){
                            $subquery = new TFilter('id', 'in', "(SELECT id FROM interacao WHERE deleted_at is null AND vendedor_id  in (SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')."))");
                            $filterVar = TSession::getValue(__CLASS__.'load_filter_cliente_id');
                            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
                            {
                                if (is_array($filterVar)) {
                                    $criteriaInteracoesFinalizadas->add(new TFilter('cliente_id', 'in', '(' . implode(',', $filterVar) . ')'));
                                } else {
                                    $criteriaInteracoesFinalizadas->add(new TFilter('cliente_id', '=', $filterVar));
                                }
                            }

                            $criteriaInteracoesFinalizadas->add($subquery);  
                        }
                        $criteriaInteracoesFinalizadas->setProperty('limit', 10);
                        $criteriaInteracoesFinalizadas->setProperty('order', 'updated_at desc, ordem asc');

                        $interacoesFinalizadas = Interacao::getObjects($criteriaInteracoesFinalizadas);
                        $items = array_merge($interacoesFinalizadas, $items);
                    }

                    if($items)
                    {
                        foreach ($items as $key => $item)
                        {

                            $item->data_inicio = call_user_func(function($value, $object, $row) 
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
                            }, $item->data_inicio, $item, null);

                            $item->data_fechamento_esperada = call_user_func(function($value, $object, $row) 
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
                            }, $item->data_fechamento_esperada, $item, null);

                            $kanban->addItem($item->id, $item->etapa_interacao_id, "Interação #{id}", "Nome: <b>{cliente->razao_social}</b><br/>
Vendedor: <b>{vendedor->razao_social}</b><br/>
Início: <b>{data_inicio}</b><br/>
Previsão de finalização: <b>{data_fechamento_esperada}</b> ", '', $item);

                        }    
                    }
                }    
            }

            $kanbanItemAction_InteracaoFormView_onShow = new TAction(['InteracaoFormView', 'onShow']);
            $kanbanItemAction_InteracaoFormView_onShow->setParameter("target_container", "adianti_div_content");

            $kanban->addItemAction("Visualizar", $kanbanItemAction_InteracaoFormView_onShow, 'fas:search-plus #00BCD4', null, true);

            //$kanban->setTemplatePath('app/resources/card.html');

            $kanban->setItemDropAction(new TAction([__CLASS__, 'onUpdateItemDrop']));
            TTransaction::close();

            $container = new TVBox;

            $container->style = 'width: 100%';
            $container->class = 'form-container';
            if(empty($param['target_container']))
            {
                $container->add(TBreadCrumb::create(["CRM","Kanban de Interação"]));
            }
            $container->add($kanban);

            parent::add($container);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Update item on drop
     */
    public static function onUpdateItemDrop($param)
    {
        try
        {
            TTransaction::open(self::$database);

            if (!empty($param['order']))
            {
                foreach ($param['order'] as $key => $id)
                {
                    $sequence = ++$key;

                    $item = new Interacao($id);
                    $item->ordem = $sequence;
                    $item->etapa_interacao_id = $param['stage_id'];

                    if(NegociacaoService::podeEditar($item->id))
                    {

                    $item->store();

                        $interacaoHistoricoEtapa = new InteracaoHistoricoEtapa;
                        $interacaoHistoricoEtapa->etapa_interacao_id = $item->etapa_interacao_id;
                        $interacaoHistoricoEtapa->interacao_id = $item->id;
                        $interacaoHistoricoEtapa->dt_etapa = date('Y-m-d H:i:s');
                        $interacaoHistoricoEtapa->store();
                    }
                    else {
                         TScript::create("window.location.reload();");
                    }
                }

                TTransaction::close();
            }
        }
        catch (Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public static function onLoadMore($param)
    {
        try
        {
            TTransaction::open(self::$database);
            $criteriaItem = new TCriteria;

            TSession::setValue(__CLASS__.'load_filter_cliente_id', null);
            TSession::setValue(__CLASS__.'load_filter_vendedor_id', null);
            TSession::setValue(__CLASS__.'load_filter_origem_contato_id', null);

            if(!empty($param["cliente_id"] ?? ""))
            {
                TSession::setValue(__CLASS__.'load_filter_cliente_id', $param["cliente_id"] ?? "");
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_cliente_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('cliente_id', '=', $filterVar)); 
            }
            if(!empty($param["vendedor_id"] ?? ""))
            {
                TSession::setValue(__CLASS__.'load_filter_vendedor_id', $param["vendedor_id"] ?? "");
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_vendedor_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('vendedor_id', '=', $filterVar)); 
            }
            if(!empty($param["origem_contato_id"] ?? ""))
            {
                TSession::setValue(__CLASS__.'load_filter_origem_contato_id', $param["origem_contato_id"] ?? "");
            }
            $filterVar = TSession::getValue(__CLASS__.'load_filter_origem_contato_id');
            if (isset($filterVar) AND ( (is_scalar($filterVar) AND $filterVar !== '') OR (is_array($filterVar) AND (!empty($filterVar)))))
            {
                $criteriaItem->add(new TFilter('origem_contato_id', '=', $filterVar)); 
            }

                $filterVar = [EtapaInteracao::CANCELADA, EtapaInteracao::FINALIZADA];
            $criteriaItem->add(new TFilter('etapa_interacao_id', 'not in', $filterVar)); 

            $criteriaItem->add(new TFilter('etapa_interacao_id', '=', $param['key'])); 
            $criteriaItem->setProperty('offset', $param['offset']);
            $criteriaItem->setProperty('limit', $param['limit']);
            $criteriaItem->setProperty('order', 'ordem asc');

            $items = Interacao::getObjects($criteriaItem);

            if ($items)
            {
                $actions = [];
                $kanbanItemAction_InteracaoFormView_onShow = new TAction(['InteracaoFormView', 'onShow']);
                $kanbanItemAction_InteracaoFormView_onShow->setParameter("target_container", "adianti_div_content");

                $actions[] = ["Visualizar", $kanbanItemAction_InteracaoFormView_onShow, 'fas:search-plus #00BCD4', null, true];

                foreach($items as $item)
                {

                    $item->data_inicio = call_user_func(function($value, $object, $row) 
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
                    }, $item->data_inicio, $item, null);

                    $item->data_fechamento_esperada = call_user_func(function($value, $object, $row) 
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
                    }, $item->data_fechamento_esperada, $item, null);

                    TKanban::createItem($item->id, $item->etapa_interacao_id, "Interação #{id}", "Nome: <b>{cliente->razao_social}</b><br/>
Vendedor: <b>{vendedor->razao_social}</b><br/>
Início: <b>{data_inicio}</b><br/>
Previsão de finalização: <b>{data_fechamento_esperada}</b> ", '', $item, null, $actions);

                }
            }

            TTransaction::close();
        }
        catch(Exception $e)
        {
            TTransaction::rollback();
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {

    } 

}

