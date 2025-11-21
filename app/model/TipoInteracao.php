<?php

class TipoInteracao extends TRecord
{
    const TABLENAME  = 'tipo_interacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const COMPLETA = '1';
    const SIMPLES = '2';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
            
    }

    /**
     * Method getTipoAtividadeInteracaos
     */
    public function getTipoAtividadeInteracaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_interacao_id', '=', $this->id));
        return TipoAtividadeInteracao::getObjects( $criteria );
    }
    /**
     * Method getInteracaos
     */
    public function getInteracaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('tipo_interacao_id', '=', $this->id));
        return Interacao::getObjects( $criteria );
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
    
        $values = TipoAtividadeInteracao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
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
    
        $values = TipoAtividadeInteracao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('tipo_interacao_id','{tipo_interacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_tipo_interacao_to_string($interacao_tipo_interacao_to_string)
    {
        if(is_array($interacao_tipo_interacao_to_string))
        {
            $values = TipoInteracao::where('id', 'in', $interacao_tipo_interacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_tipo_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_tipo_interacao_to_string = $interacao_tipo_interacao_to_string;
        }

        $this->vdata['interacao_tipo_interacao_to_string'] = $this->interacao_tipo_interacao_to_string;
    }

    public function get_interacao_tipo_interacao_to_string()
    {
        if(!empty($this->interacao_tipo_interacao_to_string))
        {
            return $this->interacao_tipo_interacao_to_string;
        }
    
        $values = Interacao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('tipo_interacao_id','{tipo_interacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_cliente_to_string($interacao_cliente_to_string)
    {
        if(is_array($interacao_cliente_to_string))
        {
            $values = Pessoa::where('id', 'in', $interacao_cliente_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->interacao_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_cliente_to_string = $interacao_cliente_to_string;
        }

        $this->vdata['interacao_cliente_to_string'] = $this->interacao_cliente_to_string;
    }

    public function get_interacao_cliente_to_string()
    {
        if(!empty($this->interacao_cliente_to_string))
        {
            return $this->interacao_cliente_to_string;
        }
    
        $values = Interacao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->razao_social}');
        return implode(', ', $values);
    }

    public function set_interacao_vendedor_to_string($interacao_vendedor_to_string)
    {
        if(is_array($interacao_vendedor_to_string))
        {
            $values = Representante::where('id', 'in', $interacao_vendedor_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->interacao_vendedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_vendedor_to_string = $interacao_vendedor_to_string;
        }

        $this->vdata['interacao_vendedor_to_string'] = $this->interacao_vendedor_to_string;
    }

    public function get_interacao_vendedor_to_string()
    {
        if(!empty($this->interacao_vendedor_to_string))
        {
            return $this->interacao_vendedor_to_string;
        }
    
        $values = Interacao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
        return implode(', ', $values);
    }

    public function set_interacao_origem_contato_to_string($interacao_origem_contato_to_string)
    {
        if(is_array($interacao_origem_contato_to_string))
        {
            $values = OrigemContato::where('id', 'in', $interacao_origem_contato_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_origem_contato_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_origem_contato_to_string = $interacao_origem_contato_to_string;
        }

        $this->vdata['interacao_origem_contato_to_string'] = $this->interacao_origem_contato_to_string;
    }

    public function get_interacao_origem_contato_to_string()
    {
        if(!empty($this->interacao_origem_contato_to_string))
        {
            return $this->interacao_origem_contato_to_string;
        }
    
        $values = Interacao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('origem_contato_id','{origem_contato->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_etapa_interacao_to_string($interacao_etapa_interacao_to_string)
    {
        if(is_array($interacao_etapa_interacao_to_string))
        {
            $values = EtapaInteracao::where('id', 'in', $interacao_etapa_interacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_etapa_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_etapa_interacao_to_string = $interacao_etapa_interacao_to_string;
        }

        $this->vdata['interacao_etapa_interacao_to_string'] = $this->interacao_etapa_interacao_to_string;
    }

    public function get_interacao_etapa_interacao_to_string()
    {
        if(!empty($this->interacao_etapa_interacao_to_string))
        {
            return $this->interacao_etapa_interacao_to_string;
        }
    
        $values = Interacao::where('tipo_interacao_id', '=', $this->id)->getIndexedArray('etapa_interacao_id','{etapa_interacao->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(TipoAtividadeInteracao::where('tipo_interacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Interacao::where('tipo_interacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

