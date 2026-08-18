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

        TScript::create("
            (function() {
                function pintarAgendaPorTipo() {

                    $('.tfullcalendar .fc-event-dot, .tfullcalendar .fc-list-event-dot').each(function() {
                        this.style.setProperty('display', 'none', 'important');
                    });

                    $('.tfullcalendar .cor_tipo_evento').each(function() {
                        var marcador = $(this);
                        var cor = marcador.attr('data-cor-tipo');

                        if (!cor) {
                            return;
                        }

                        var linhaLista = marcador.closest('tr.fc-list-event');
                        var eventoAgenda = marcador.closest('.fc-event');

                        if (linhaLista.length) {
                            linhaLista.find('.fc-list-event-dot, .fc-event-dot').each(function() {
                                this.style.setProperty('display', 'none', 'important');
                            });

                            linhaLista.find('td.fc-list-event-graphic').each(function() {
                                this.style.setProperty('display', 'none', 'important');
                                this.style.setProperty('width', '0', 'important');
                                this.style.setProperty('padding', '0', 'important');
                            });

                            linhaLista.find('td').each(function() {
                                this.style.setProperty('background-color', cor, 'important');
                                this.style.setProperty('color', '#ffffff', 'important');
                            });

                            linhaLista.find('a, div, span, b, i').not('.estado_atividade').each(function() {
                                this.style.setProperty('color', '#ffffff', 'important');
                            });

                            linhaLista.off('mouseenter.corTipo mouseleave.corTipo').on('mouseenter.corTipo mouseleave.corTipo', function() {
                                $(this).find('td').each(function() {
                                    this.style.setProperty('background-color', cor, 'important');
                                    this.style.setProperty('color', '#ffffff', 'important');
                                });

                                $(this).find('a, div, span, b, i').not('.estado_atividade').each(function() {
                                    this.style.setProperty('color', '#ffffff', 'important');
                                });

                                $(this).find('.fc-list-event-dot, .fc-event-dot').each(function() {
                                    this.style.setProperty('display', 'none', 'important');
                                });

                                $(this).find('td.fc-list-event-graphic').each(function() {
                                    this.style.setProperty('display', 'none', 'important');
                                    this.style.setProperty('width', '0', 'important');
                                    this.style.setProperty('padding', '0', 'important');
                                });
                            });
                        }

                        if (eventoAgenda.length) {
                            eventoAgenda.each(function() {
                                this.style.setProperty('background-color', cor, 'important');
                                this.style.setProperty('border-color', cor, 'important');
                                this.style.setProperty('color', '#ffffff', 'important');
                            });

                            eventoAgenda.find('.fc-event-main, .fc-event-title, .fc-event-time, a, div, span, b, i').not('.estado_atividade').each(function() {
                                this.style.setProperty('color', '#ffffff', 'important');
                            });

                            eventoAgenda.find('.fc-event-dot, .fc-list-event-dot').each(function() {
                                this.style.setProperty('display', 'none', 'important');
                            });
                        }

                        $('.tfullcalendar .estado_atividade').each(function() {
                            var corEstado = $(this).css('background-color');

                            this.style.setProperty('background-color', corEstado, 'important');
                            this.style.setProperty('border-color', corEstado, 'important');
                        });
                    });
                }

                function iniciarPinturaAgendaPorTipo() {
                    pintarAgendaPorTipo();

                    var alvo = document.querySelector('.tfullcalendar');

                    if (alvo && !alvo.dataset.observandoCorTipo) {
                        alvo.dataset.observandoCorTipo = '1';

                        var observer = new MutationObserver(function() {
                            setTimeout(pintarAgendaPorTipo, 100);
                        });

                        observer.observe(alvo, {
                            childList: true,
                            subtree: true
                        });
                    }
                }

                setTimeout(iniciarPinturaAgendaPorTipo, 300);
                setTimeout(iniciarPinturaAgendaPorTipo, 800);
                setTimeout(iniciarPinturaAgendaPorTipo, 1500);
                setTimeout(iniciarPinturaAgendaPorTipo, 2500);

                $(document).off('click.corTipoAgenda').on('click.corTipoAgenda', '.fc-button, .fc-button-primary', function() {
                    setTimeout(pintarAgendaPorTipo, 300);
                    setTimeout(pintarAgendaPorTipo, 800);
                    setTimeout(pintarAgendaPorTipo, 1500);
                });
            })();
        ");

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

/*

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

*/                             
            $events = InteracaoAtividade::getObjects($criteria);

            if ($events)
            {
                foreach ($events as $event)
                {
                    $event_array = $event->toArray();
                    $event_array['start'] = str_replace( ' ', 'T', $event_array['horario_inicial']);
                    $event_array['end'] = str_replace( ' ', 'T', $event_array['horario_final']);
                    $event_array['id'] = $event->id;

                    $corTipo = trim($event->render("{tipo_atividade->cor}"));

                    if (empty($corTipo)) {
                        $corTipo = '#3a87ad';
                    }

                    $event_array['color'] = $corTipo;
                    $event_array['textColor'] = '#ffffff';

                    $event_array['title'] = TFullCalendar::renderPopover($event->render("
                        <div class='cor_tipo_evento' data-cor-tipo='{$corTipo}' style='display: flex; flex-direction: revert; justify-content: flex-start; align-items: center; gap: 5px; color: #ffffff; font-weight: bold;'>
                            <span title=' {estado_atividade->nome} ' class='estado_atividade' style='background-color: {estado_atividade->cor}; border-color: {estado_atividade->cor};'></span>
                            {tipo_atividade->icone_formatado}  -  {tipo_atividade->nome} 
                        </div>
                    "), $event->render("Observações "), $event->render("
                        <b>{descricao} </b> <br> 
                        {observacao}   <br >
                    "));

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

