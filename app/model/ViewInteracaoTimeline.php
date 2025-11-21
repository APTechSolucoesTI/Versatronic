<?php

class ViewInteracaoTimeline extends TRecord
{
    const TABLENAME  = 'view_interacao_timeline';
    const PRIMARYKEY = 'chave';
    const IDPOLICY   =  'max'; // {max, serial}

    private $interacao_historico_etapa;
    private $interacao_historico_atividade;
    private $interacao_historico_observacao;
    private $interacao_historico_arquivo;
    private $interacao_localizacao;
                                                            

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('interacao_id');
        parent::addAttribute('dt_historico');
        parent::addAttribute('tipo');
    
    }

    public function get_interacao_historico_etapa()
    {
        if (! $this->interacao_historico_etapa)
        {
            $this->interacao_historico_etapa = InteracaoHistoricoEtapa::find($this->chave);
        }
    
        return $this->interacao_historico_etapa;
    }

    public function get_interacao_historico_atividade()
    {
        if (! $this->interacao_historico_atividade)
        {
            $this->interacao_historico_atividade = InteracaoHistoricoAtividade::find($this->chave);
        }
    
        return $this->interacao_historico_atividade;
    }

    public function get_interacao_historico_observacao()
    {
        if (! $this->interacao_historico_observacao)
        {
            $this->interacao_historico_observacao = InteracaoHistoricoObservacao::find($this->chave);
        }
    
        return $this->interacao_historico_observacao;
    }

    public function get_interacao_historico_arquivo()
    {
        if (! $this->interacao_historico_arquivo)
        {
            $this->interacao_historico_arquivo = InteracaoHistoricoArquivo::find($this->chave);
        }
    
        return $this->interacao_historico_arquivo;
    }

    public function get_interacao_localizacao()
    {
        if (! $this->interacao_localizacao)
        {
            $this->interacao_localizacao = InteracaoLocalizacao::find($this->chave);
        }
    
        return $this->interacao_localizacao;
    }

    public function get_titulo()
    {
        if ($this->tipo == 'observacao')
        {
            return "<i style='margin-right: 5px;' class='far fa-bookmark'></i>Observação {$this->get_interacao_historico_observacao()->movimentacao->nome}";
        }
        else if ($this->tipo == 'arquivo')
        {
            return "<i style='margin-right: 5px;' class='fas fa-archive'></i>Arquivo {$this->get_interacao_historico_arquivo()->movimentacao->nome}";
        }
        else if ($this->tipo == 'etapa')
        {
            return "<span style='margin-right: 5px; width: 30px; height: 15px; display: inline-block; border-radius: 3px; border: 1px solid #555; background: {$this->get_interacao_historico_etapa()->etapa_interacao->cor}'></span>{$this->get_interacao_historico_etapa()->etapa_interacao->nome}";
        }
        else if ($this->tipo == 'atividade')
        {
            return "<i class='{$this->get_interacao_historico_atividade()->tipo_atividade->icone}' style='color: {$this->get_interacao_historico_atividade()->tipo_atividade->cor}; margin-right: 5px; '></i>{$this->get_interacao_historico_atividade()->tipo_atividade->nome} {$this->get_interacao_historico_atividade()->movimentacao->nome}";
        }
        else if ($this->tipo == 'localizacao')
        {
            return "<i style='margin-right: 5px;' class='fas fa-globe-americas'></i>Localização";
        }
    }

    public function get_descricao()
{
    $div = "";

    if ($this->tipo == 'observacao') {
        $nota = $this->get_interacao_historico_observacao();
    
        if ($nota) {
            $div = "{$nota->descricao}";
        } else {
            $div = "Observação não encontrada.";
        }
    } elseif ($this->tipo == 'arquivo') {
        $arquivo = $this->get_interacao_historico_arquivo();

        if ($arquivo) {
          $div = $arquivo->descricao . " " . $arquivo->movimentacao->nome;
        } else {
            $div = "Arquivo não encontrado.";
        }
    } elseif ($this->tipo == 'etapa') {
        $div = "Interação movimentada";
    } elseif ($this->tipo == 'atividade') {
        $atividade = $this->get_interacao_historico_atividade();
    
        if ($atividade) {
            $descricao = $atividade->descricao;
            $observacao = $atividade->observacao;

            $ini = date('d/m/Y H:i', strtotime($atividade->horario_inicial));
            $fim = date('d/m/Y H:i', strtotime($atividade->horario_final));
            $estado = $atividade->estado_atividade->nome;

            $div = "<b>Estado: </b>{$estado}<br/><b>Descrição: </b>{$descricao}<br/><b>Início: </b>{$ini}<br/><b>Fim: </b>{$fim}<br/>{$observacao}<br/>";
        } else {
            $div = "Atividade não encontrada.";
        }
    } elseif ($this->tipo == 'localizacao') {
        $localizacao = $this->get_interacao_localizacao();
        $div = "<a href='https://www.google.com/maps?q={$localizacao->latitude},{$localizacao->longitude}' target='_blank'>$localizacao->descricao</a>";
    }

    return $div;
}

    public function get_informacao_documento()
    {
        $titulo = "";
        $descricao = "";

        //OBSERVAÇÃO
        if ($this->tipo == 'observacao') {
            $nota = $this->get_interacao_historico_observacao();
        
            if ($nota) {
                $titulo = "Observação {$nota->movimentacao->nome}";
                $descricao = "{$nota->descricao}";
            } else {
                $titulo = "Observação não encontrada.";
            }

        //ARQUIVO
        } elseif ($this->tipo == 'arquivo') {
            $arquivo = $this->get_interacao_historico_arquivo();
    
            if ($arquivo) {
                $titulo = "Arquivo {$arquivo->movimentacao->nome}";
                $descricao = $arquivo->descricao . " " . $arquivo->movimentacao->nome;
            } else {
                $titulo = "Arquivo não encontrado.";
            }

        //ETAPA
        } elseif ($this->tipo == 'etapa') {
            $etapa = $this->get_interacao_historico_etapa();
            $titulo = "{$etapa->etapa_interacao->nome}";
            $descricao = "Interação movimentada";

        //ATIVIDADE
        } elseif ($this->tipo == 'atividade') {
            $atividade = $this->get_interacao_historico_atividade();
        
            if ($atividade) {
                $titulo = "{$atividade->tipo_atividade->nome} {$atividade->movimentacao->nome}";

                $descricao = "<b>Estado: </b>{$atividade->estado_atividade->nome}<br/><b>Descrição: </b>{$atividade->descricao}<br/>
                              <b>Início: </b>" . date('d/m/Y H:i', strtotime($atividade->horario_inicial)) . "<br/>
                              <b>Fim: </b>" . date('d/m/Y H:i', strtotime($atividade->horario_final)) . "<br/>
                              {$atividade->observacao}<br/>";
            } else {
                $titulo = "Atividade não encontrada.";
            }

        //LOCALIZACAO
        } elseif ($this->tipo == 'localizacao') {
            $localizacao = $this->get_interacao_localizacao();
            $titulo = "Localização";
            $descricao = "<a href='https://www.google.com/maps?q={$localizacao->latitude},{$localizacao->longitude}' target='_blank'>{$localizacao->descricao}</a>";
        }

        return  "<div style='margin-left: 5px;'>
                    <b>".$titulo."</b><br/>
                    <span style='margin-left: 5px;'>".$descricao."</span>
                </div>";
    }
                                                            
}

