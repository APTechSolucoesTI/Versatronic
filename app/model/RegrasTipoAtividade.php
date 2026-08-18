<?php

class RegrasTipoAtividade extends TRecord
{
    const TABLENAME  = 'regras_tipo_atividade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo');
        parent::addAttribute('nome');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method getPrazoAtividades
     */
    public function getPrazoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('regras_tipo_atividade_id', '=', $this->id));
        return PrazoAtividade::getObjects( $criteria );
    }
    /**
     * Method getTipoAtividades
     */
    public function getTipoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('regras_tipo_atividade_id', '=', $this->id));
        return TipoAtividade::getObjects( $criteria );
    }

    public function set_prazo_atividade_regras_tipo_atividade_to_string($prazo_atividade_regras_tipo_atividade_to_string)
    {
        if(is_array($prazo_atividade_regras_tipo_atividade_to_string))
        {
            $values = RegrasTipoAtividade::where('id', 'in', $prazo_atividade_regras_tipo_atividade_to_string)->getIndexedArray('id', 'id');
            $this->prazo_atividade_regras_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->prazo_atividade_regras_tipo_atividade_to_string = $prazo_atividade_regras_tipo_atividade_to_string;
        }

        $this->vdata['prazo_atividade_regras_tipo_atividade_to_string'] = $this->prazo_atividade_regras_tipo_atividade_to_string;
    }

    public function get_prazo_atividade_regras_tipo_atividade_to_string()
    {
        if(!empty($this->prazo_atividade_regras_tipo_atividade_to_string))
        {
            return $this->prazo_atividade_regras_tipo_atividade_to_string;
        }
    
        $values = PrazoAtividade::where('regras_tipo_atividade_id', '=', $this->id)->getIndexedArray('regras_tipo_atividade_id','{regras_tipo_atividade->id}');
        return implode(', ', $values);
    }

    public function set_prazo_atividade_categoria_cliente_to_string($prazo_atividade_categoria_cliente_to_string)
    {
        if(is_array($prazo_atividade_categoria_cliente_to_string))
        {
            $values = CategoriaCliente::where('id', 'in', $prazo_atividade_categoria_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->prazo_atividade_categoria_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->prazo_atividade_categoria_cliente_to_string = $prazo_atividade_categoria_cliente_to_string;
        }

        $this->vdata['prazo_atividade_categoria_cliente_to_string'] = $this->prazo_atividade_categoria_cliente_to_string;
    }

    public function get_prazo_atividade_categoria_cliente_to_string()
    {
        if(!empty($this->prazo_atividade_categoria_cliente_to_string))
        {
            return $this->prazo_atividade_categoria_cliente_to_string;
        }
    
        $values = PrazoAtividade::where('regras_tipo_atividade_id', '=', $this->id)->getIndexedArray('categoria_cliente_id','{categoria_cliente->nome}');
        return implode(', ', $values);
    }

    public function set_tipo_atividade_regras_tipo_atividade_to_string($tipo_atividade_regras_tipo_atividade_to_string)
    {
        if(is_array($tipo_atividade_regras_tipo_atividade_to_string))
        {
            $values = RegrasTipoAtividade::where('id', 'in', $tipo_atividade_regras_tipo_atividade_to_string)->getIndexedArray('id', 'id');
            $this->tipo_atividade_regras_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->tipo_atividade_regras_tipo_atividade_to_string = $tipo_atividade_regras_tipo_atividade_to_string;
        }

        $this->vdata['tipo_atividade_regras_tipo_atividade_to_string'] = $this->tipo_atividade_regras_tipo_atividade_to_string;
    }

    public function get_tipo_atividade_regras_tipo_atividade_to_string()
    {
        if(!empty($this->tipo_atividade_regras_tipo_atividade_to_string))
        {
            return $this->tipo_atividade_regras_tipo_atividade_to_string;
        }
    
        $values = TipoAtividade::where('regras_tipo_atividade_id', '=', $this->id)->getIndexedArray('regras_tipo_atividade_id','{regras_tipo_atividade->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(PrazoAtividade::where('regras_tipo_atividade_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(TipoAtividade::where('regras_tipo_atividade_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

