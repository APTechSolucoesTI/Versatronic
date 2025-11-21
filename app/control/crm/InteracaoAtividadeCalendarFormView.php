<?php
/**
 * InteracaoAtividadeCalendarForm Form
 * @author  <your name here>
 */
class InteracaoAtividadeCalendarFormView extends TPage
{
    private $fc;

    /**
     * Page constructor
     */
    public function __construct($param = null)
    {
        parent::__construct();

        $this->fc = new TFullCalendar(date('Y-m-d'), 'month');
        $this->fc->enableDays([1,2,3,4,5]);
        $this->fc->setReloadAction(new TAction(array($this, 'getEvents'), $param));
        $this->fc->setDayClickAction(new TAction(array('InteracaoAtividadeCalendarForm', 'onStartEdit')));
        $this->fc->setEventClickAction(new TAction(array('InteracaoAtividadeCalendarForm', 'onEdit')));
        $this->fc->setCurrentView('agendaWeek');
        $this->fc->setTimeRange('07:00', '19:00');
        $this->fc->setOption('slotTime', "00:30:00");
        $this->fc->setOption('slotDuration', "00:30:00");
        $this->fc->setOption('slotLabelInterval', 30);

        parent::add( $this->fc );
    }

    /**
     * Output events as an json
     */
    public static function getEvents($param=NULL)
    {
        $return = array();
        try
        {
            TTransaction::open('minicrm');

            $criteria = new TCriteria(); 

            $criteria->add(new TFilter('horario_inicial', '<=', substr($param['end'], 0, 10).' 23:59:59'));
            $criteria->add(new TFilter('horario_final', '>=', substr($param['start'], 0, 10).' 00:00:00'));

            $filterVar = TSession::getValue('interacao_id');
            $criteria->add(new TFilter('interacao_id', '=', $filterVar)); 

            $events = InteracaoAtividade::getObjects($criteria);

            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['horario_inicial']);
                    $event_array['end'] = str_replace( ' ', 'T', $event_array['horario_final']);
                    $event_array['id'] = $event->id;
                    $event_array['color'] = $event->render("{tipo_atividade->cor}");

                    $event_array['title'] = TFullCalendar::renderPopover($event->render("<div style='display: flex; flex-direction: revert; justify-content: flex-start; align-items: center; gap: 5px;'>
    <span title=' {estado_atividade->nome} ' class='estado_atividade' style='background-color: {estado_atividade->cor} '></span>
    {tipo_atividade->icone_formatado}  -  {tipo_atividade->nome} 
</div>"),$event->render("Observações "), $event->render("
<b>{descricao} </b> <br> 
{observacao}   <br >"));

                    $return[] = $event_array;
                }
            }
            TTransaction::close();
            echo json_encode($return);
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }

    /**
     * Reconfigure the callendar
     */
    public function onReload($param = null)
    {
        if (isset($param['view']))
        {
            $this->fc->setCurrentView($param['view']);
        }

        if (isset($param['date']))
        {
            $this->fc->setCurrentDate($param['date']);
        }
    }

}

