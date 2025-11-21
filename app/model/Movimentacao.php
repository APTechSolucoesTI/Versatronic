<?php

class Movimentacao extends TRecord
{
    const TABLENAME  = 'movimentacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CRIADO = '1';
    const ALTERADO = '2';
    const EXCLUIDO = '3';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
            
    }

    /**
     * Method getInteracaoHistoricoArquivos
     */
    public function getInteracaoHistoricoArquivos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('movimentacao_id', '=', $this->id));
        return InteracaoHistoricoArquivo::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoObservacaos
     */
    public function getInteracaoHistoricoObservacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('movimentacao_id', '=', $this->id));
        return InteracaoHistoricoObservacao::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoAtividades
     */
    public function getInteracaoHistoricoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('movimentacao_id', '=', $this->id));
        return InteracaoHistoricoAtividade::getObjects( $criteria );
    }

    public function set_interacao_historico_arquivo_interacao_to_string($interacao_historico_arquivo_interacao_to_string)
    {
        if(is_array($interacao_historico_arquivo_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_arquivo_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_arquivo_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_arquivo_interacao_to_string = $interacao_historico_arquivo_interacao_to_string;
        }

        $this->vdata['interacao_historico_arquivo_interacao_to_string'] = $this->interacao_historico_arquivo_interacao_to_string;
    }

    public function get_interacao_historico_arquivo_interacao_to_string()
    {
        if(!empty($this->interacao_historico_arquivo_interacao_to_string))
        {
            return $this->interacao_historico_arquivo_interacao_to_string;
        }
    
        $values = InteracaoHistoricoArquivo::where('movimentacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_arquivo_movimentacao_to_string($interacao_historico_arquivo_movimentacao_to_string)
    {
        if(is_array($interacao_historico_arquivo_movimentacao_to_string))
        {
            $values = Movimentacao::where('id', 'in', $interacao_historico_arquivo_movimentacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_arquivo_movimentacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_arquivo_movimentacao_to_string = $interacao_historico_arquivo_movimentacao_to_string;
        }

        $this->vdata['interacao_historico_arquivo_movimentacao_to_string'] = $this->interacao_historico_arquivo_movimentacao_to_string;
    }

    public function get_interacao_historico_arquivo_movimentacao_to_string()
    {
        if(!empty($this->interacao_historico_arquivo_movimentacao_to_string))
        {
            return $this->interacao_historico_arquivo_movimentacao_to_string;
        }
    
        $values = InteracaoHistoricoArquivo::where('movimentacao_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_observacao_interacao_to_string($interacao_historico_observacao_interacao_to_string)
    {
        if(is_array($interacao_historico_observacao_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_observacao_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_observacao_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_observacao_interacao_to_string = $interacao_historico_observacao_interacao_to_string;
        }

        $this->vdata['interacao_historico_observacao_interacao_to_string'] = $this->interacao_historico_observacao_interacao_to_string;
    }

    public function get_interacao_historico_observacao_interacao_to_string()
    {
        if(!empty($this->interacao_historico_observacao_interacao_to_string))
        {
            return $this->interacao_historico_observacao_interacao_to_string;
        }
    
        $values = InteracaoHistoricoObservacao::where('movimentacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_observacao_movimentacao_to_string($interacao_historico_observacao_movimentacao_to_string)
    {
        if(is_array($interacao_historico_observacao_movimentacao_to_string))
        {
            $values = Movimentacao::where('id', 'in', $interacao_historico_observacao_movimentacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_observacao_movimentacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_observacao_movimentacao_to_string = $interacao_historico_observacao_movimentacao_to_string;
        }

        $this->vdata['interacao_historico_observacao_movimentacao_to_string'] = $this->interacao_historico_observacao_movimentacao_to_string;
    }

    public function get_interacao_historico_observacao_movimentacao_to_string()
    {
        if(!empty($this->interacao_historico_observacao_movimentacao_to_string))
        {
            return $this->interacao_historico_observacao_movimentacao_to_string;
        }
    
        $values = InteracaoHistoricoObservacao::where('movimentacao_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
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
    
        $values = InteracaoHistoricoAtividade::where('movimentacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
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
    
        $values = InteracaoHistoricoAtividade::where('movimentacao_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
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
    
        $values = InteracaoHistoricoAtividade::where('movimentacao_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
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
    
        $values = InteracaoHistoricoAtividade::where('movimentacao_id', '=', $this->id)->getIndexedArray('estado_atividade_id','{estado_atividade->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(InteracaoHistoricoArquivo::where('movimentacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(InteracaoHistoricoObservacao::where('movimentacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(InteracaoHistoricoAtividade::where('movimentacao_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

