<?php

class TipoAtividade extends TRecord
{
    const TABLENAME  = 'tipo_atividade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private RegrasTipoAtividade $regras_tipo_atividade;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('cor');
        parent::addAttribute('icone');
        parent::addAttribute('regras_tipo_atividade_id');
    
    }

    /**
     * Method set_regras_tipo_atividade
     * Sample of usage: $var->regras_tipo_atividade = $object;
     * @param $object Instance of RegrasTipoAtividade
     */
    public function set_regras_tipo_atividade(RegrasTipoAtividade $object)
    {
        $this->regras_tipo_atividade = $object;
        $this->regras_tipo_atividade_id = $object->id;
    }

    /**
     * Method get_regras_tipo_atividade
     * Sample of usage: $var->regras_tipo_atividade->attribute;
     * @returns RegrasTipoAtividade instance
     */
    public function get_regras_tipo_atividade()
    {
    
        // loads the associated object
        if (empty($this->regras_tipo_atividade))
            $this->regras_tipo_atividade = new RegrasTipoAtividade($this->regras_tipo_atividade_id);
    
        // returns the associated object
        return $this->regras_tipo_atividade;
    }

    /**
     * Method getInteracaoAtividades
     */
    public function getInteracaoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_atividade_id', '=', $this->id));
        return InteracaoAtividade::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoAtividades
     */
    public function getInteracaoHistoricoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_atividade_id', '=', $this->id));
        return InteracaoHistoricoAtividade::getObjects( $criteria );
    }
    /**
     * Method getTipoAtividadeInteracaos
     */
    public function getTipoAtividadeInteracaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_atividade_id', '=', $this->id));
        return TipoAtividadeInteracao::getObjects( $criteria );
    }

    public function set_interacao_atividade_tipo_atividade_to_string($interacao_atividade_tipo_atividade_to_string)
    {
        if(is_array($interacao_atividade_tipo_atividade_to_string))
        {
            $values = TipoAtividade::where('id', 'in', $interacao_atividade_tipo_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_atividade_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_atividade_tipo_atividade_to_string = $interacao_atividade_tipo_atividade_to_string;
        }

        $this->vdata['interacao_atividade_tipo_atividade_to_string'] = $this->interacao_atividade_tipo_atividade_to_string;
    }

    public function get_interacao_atividade_tipo_atividade_to_string()
    {
        if(!empty($this->interacao_atividade_tipo_atividade_to_string))
        {
            return $this->interacao_atividade_tipo_atividade_to_string;
        }
    
        $values = InteracaoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_atividade_interacao_to_string($interacao_atividade_interacao_to_string)
    {
        if(is_array($interacao_atividade_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_atividade_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_atividade_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_atividade_interacao_to_string = $interacao_atividade_interacao_to_string;
        }

        $this->vdata['interacao_atividade_interacao_to_string'] = $this->interacao_atividade_interacao_to_string;
    }

    public function get_interacao_atividade_interacao_to_string()
    {
        if(!empty($this->interacao_atividade_interacao_to_string))
        {
            return $this->interacao_atividade_interacao_to_string;
        }
    
        $values = InteracaoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_atividade_estado_atividade_to_string($interacao_atividade_estado_atividade_to_string)
    {
        if(is_array($interacao_atividade_estado_atividade_to_string))
        {
            $values = EstadoAtividade::where('id', 'in', $interacao_atividade_estado_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_atividade_estado_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_atividade_estado_atividade_to_string = $interacao_atividade_estado_atividade_to_string;
        }

        $this->vdata['interacao_atividade_estado_atividade_to_string'] = $this->interacao_atividade_estado_atividade_to_string;
    }

    public function get_interacao_atividade_estado_atividade_to_string()
    {
        if(!empty($this->interacao_atividade_estado_atividade_to_string))
        {
            return $this->interacao_atividade_estado_atividade_to_string;
        }
    
        $values = InteracaoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('estado_atividade_id','{estado_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_interacao_to_string($interacao_historico_atividade_interacao_to_string)
    {
        if(is_array($interacao_historico_atividade_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_atividade_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_atividade_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_interacao_to_string = $interacao_historico_atividade_interacao_to_string;
        }

        $this->vdata['interacao_historico_atividade_interacao_to_string'] = $this->interacao_historico_atividade_interacao_to_string;
    }

    public function get_interacao_historico_atividade_interacao_to_string()
    {
        if(!empty($this->interacao_historico_atividade_interacao_to_string))
        {
            return $this->interacao_historico_atividade_interacao_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_movimentacao_to_string($interacao_historico_atividade_movimentacao_to_string)
    {
        if(is_array($interacao_historico_atividade_movimentacao_to_string))
        {
            $values = Movimentacao::where('id', 'in', $interacao_historico_atividade_movimentacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_atividade_movimentacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_movimentacao_to_string = $interacao_historico_atividade_movimentacao_to_string;
        }

        $this->vdata['interacao_historico_atividade_movimentacao_to_string'] = $this->interacao_historico_atividade_movimentacao_to_string;
    }

    public function get_interacao_historico_atividade_movimentacao_to_string()
    {
        if(!empty($this->interacao_historico_atividade_movimentacao_to_string))
        {
            return $this->interacao_historico_atividade_movimentacao_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_tipo_atividade_to_string($interacao_historico_atividade_tipo_atividade_to_string)
    {
        if(is_array($interacao_historico_atividade_tipo_atividade_to_string))
        {
            $values = TipoAtividade::where('id', 'in', $interacao_historico_atividade_tipo_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_atividade_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_tipo_atividade_to_string = $interacao_historico_atividade_tipo_atividade_to_string;
        }

        $this->vdata['interacao_historico_atividade_tipo_atividade_to_string'] = $this->interacao_historico_atividade_tipo_atividade_to_string;
    }

    public function get_interacao_historico_atividade_tipo_atividade_to_string()
    {
        if(!empty($this->interacao_historico_atividade_tipo_atividade_to_string))
        {
            return $this->interacao_historico_atividade_tipo_atividade_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_estado_atividade_to_string($interacao_historico_atividade_estado_atividade_to_string)
    {
        if(is_array($interacao_historico_atividade_estado_atividade_to_string))
        {
            $values = EstadoAtividade::where('id', 'in', $interacao_historico_atividade_estado_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_atividade_estado_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_estado_atividade_to_string = $interacao_historico_atividade_estado_atividade_to_string;
        }

        $this->vdata['interacao_historico_atividade_estado_atividade_to_string'] = $this->interacao_historico_atividade_estado_atividade_to_string;
    }

    public function get_interacao_historico_atividade_estado_atividade_to_string()
    {
        if(!empty($this->interacao_historico_atividade_estado_atividade_to_string))
        {
            return $this->interacao_historico_atividade_estado_atividade_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('estado_atividade_id','{estado_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_tipo_atividade_interacao_tipo_atividade_to_string($tipo_atividade_interacao_tipo_atividade_to_string)
    {
        if(is_array($tipo_atividade_interacao_tipo_atividade_to_string))
        {
            $values = TipoAtividade::where('id', 'in', $tipo_atividade_interacao_tipo_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->tipo_atividade_interacao_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->tipo_atividade_interacao_tipo_atividade_to_string = $tipo_atividade_interacao_tipo_atividade_to_string;
        }

        $this->vdata['tipo_atividade_interacao_tipo_atividade_to_string'] = $this->tipo_atividade_interacao_tipo_atividade_to_string;
    }

    public function get_tipo_atividade_interacao_tipo_atividade_to_string()
    {
        if(!empty($this->tipo_atividade_interacao_tipo_atividade_to_string))
        {
            return $this->tipo_atividade_interacao_tipo_atividade_to_string;
        }
    
        $values = TipoAtividadeInteracao::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_tipo_atividade_interacao_tipo_interacao_to_string($tipo_atividade_interacao_tipo_interacao_to_string)
    {
        if(is_array($tipo_atividade_interacao_tipo_interacao_to_string))
        {
            $values = TipoInteracao::where('id', 'in', $tipo_atividade_interacao_tipo_interacao_to_string)->getIndexedArray('nome', 'nome');
            $this->tipo_atividade_interacao_tipo_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->tipo_atividade_interacao_tipo_interacao_to_string = $tipo_atividade_interacao_tipo_interacao_to_string;
        }

        $this->vdata['tipo_atividade_interacao_tipo_interacao_to_string'] = $this->tipo_atividade_interacao_tipo_interacao_to_string;
    }

    public function get_tipo_atividade_interacao_tipo_interacao_to_string()
    {
        if(!empty($this->tipo_atividade_interacao_tipo_interacao_to_string))
        {
            return $this->tipo_atividade_interacao_tipo_interacao_to_string;
        }
    
        $values = TipoAtividadeInteracao::where('tipo_atividade_id', '=', $this->id)->getIndexedArray('tipo_interacao_id','{tipo_interacao->nome}');
        return implode(', ', $values);
    }

    public function get_icone_formatado()
    {
        if($this->icone)
        {
            return "<i class='{$this->icone}'> </i>";
        }
    }

}

