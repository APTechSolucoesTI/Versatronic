<?php

class ViewInteracaoTimelineTimeLine extends TPage
{
    private static $database = 'minicrm';
    private static $activeRecord = 'ViewInteracaoTimeline';
    private static $primaryKey = 'chave';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param = null )
    {
        try
        {
            parent::__construct();

            TTransaction::open(self::$database);

            if(!empty($param['target_container']))
            {
                $this->adianti_target_container = $param['target_container'];
            }

            $this->timeline = new TTimeline;
            $this->timeline->setItemDatabase(self::$database);
            $this->timelineCriteria = new TCriteria;

            $filterVar = TSession::getValue('interacao_id');
            $this->timelineCriteria->add(new TFilter('interacao_id', '=', $filterVar));

            $limit = 0;

            $this->timelineCriteria->setProperty('limit', $limit);
            $this->timelineCriteria->setProperty('order', 'dt_historico desc');

/*

            $objects = ViewInteracaoTimeline::getObjects($this->timelineCriteria);

            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $id = $object->chave;
                    $title = "{titulo}";
                    $htmlTemplate = " {descricao} ";
                    $date = $object->dt_historico;
                    $icon = 'fa:arrow-left bg-green';
                    $position = 'left';

*/
                $objects = ViewInteracaoTimeline::getObjects($this->timelineCriteria);

                if ($objects)
                {
                    $usuariosQueVeemTudo = [1, 16, 13, 20, 4, 11, 12, 17, 10];
                    $usuarioLogado = TSession::getValue('userid');

                    foreach ($objects as $object)
                    {
                        $id = $object->chave;
                        $title = "{titulo}";

                        if ($object->tipo == 'atividade')
                        {
                            $attHist = InteracaoHistoricoAtividade::find($id);

                            if (!empty($attHist))
                            {
                                $att = InteracaoAtividade::find($attHist->interacao_atividade_id);

                                if (!empty($att) && $attHist->movimentacao_id !== 3)
                                {
                                    if (in_array($usuarioLogado, $usuariosQueVeemTudo))
                                    {
                                        $action = new TAction(['InteracaoAtividadeRevisaoForm', 'onEdit']);
                                    }
                                    else
                                    {
                                        $action = new TAction(['InteracaoAtividadeCalendarForm', 'onEdit']);
                                    }

                                    $action->setParameter('key', $attHist->interacao_atividade_id);
                                    $action->setParameter('target_container', 'adianti_window');

                                    $url = addslashes($action->serialize());

                                    $title = new TElement('a');
                                    $title->href = '#';
                                    $title->onclick = "__adianti_load_page('{$url}'); return false;";
                                    $title->style = 'color:#000; font-weight:bold; text-decoration:none; cursor:pointer;';
                                    $title->add($object->titulo ?? 'Atividade');
                                }
                            }
                        }
                        else if ($object->tipo == 'arquivo')
                        {
                            $arqHist = InteracaoHistoricoArquivo::find($id);

                            if (!empty($arqHist)) {

                                $file = $arqHist->descricao;

                                if (!empty($file)) {
                                    $file = ltrim($file, '/');

                                    $fileUrl = implode('/', array_map('rawurlencode', explode('/', $file)));

                                    $url = 'download.php?file=' . $fileUrl;
                                    $urlJs = json_encode($url);

                                    $title = new TElement('a');                                        
                                    $title->href = '#';
                                    $title->onclick = "window.open({$urlJs}, '_blank'); return false;";
                                    $title->style = 'color:#000; font-weight:bold; text-decoration:none; cursor:pointer;';
                                    $title->add($object->titulo ?? 'Arquivo');
                                }                                
                            }
                        }

                        $htmlTemplate = " {descricao} ";
                        $date = $object->dt_historico;
                        $icon = 'fa:arrow-left bg-green';
                        $position = 'left';
                    $this->timeline->addItem($id, $title, $htmlTemplate, $date, $icon, $position, $object);

                }
            }

            $this->timeline->setUseBothSides();
            $this->timeline->setTimeDisplayMask('dd/mm/yyyy H:i:s');
            $this->timeline->setFinalIcon( 'fas:flag-checkered #ffffff #de1414' );

            $container = new TVBox;

            $container->style = 'width: 100%';
            $container->class = 'form-container';
            if(empty($param['target_container']))
            {    
                $container->add(TBreadCrumb::create(["CRM","Interação Timeline"]));
            }
            $container->add($this->timeline);

            TTransaction::close();

            parent::add($container);
        }
        catch(Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    public function onShow($param = null)
    {

    } 

}

