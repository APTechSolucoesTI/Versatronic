<?php

class Pais extends TRecord
{
    const TABLENAME  = 'pais';
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
        parent::addAttribute('codigo');
        parent::addAttribute('nome');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method getEstados
     */
    public function getEstados()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pais_id', '=', $this->id));
        return Estado::getObjects( $criteria );
    }
    /**
     * Method getNacionalidades
     */
    public function getNacionalidades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pais_id', '=', $this->id));
        return Nacionalidade::getObjects( $criteria );
    }

    public function set_estado_pais_to_string($estado_pais_to_string)
    {
        if(is_array($estado_pais_to_string))
        {
            $values = Pais::where('id', 'in', $estado_pais_to_string)->getIndexedArray('nome', 'nome');
            $this->estado_pais_to_string = implode(', ', $values);
        }
        else
        {
            $this->estado_pais_to_string = $estado_pais_to_string;
        }

        $this->vdata['estado_pais_to_string'] = $this->estado_pais_to_string;
    }

    public function get_estado_pais_to_string()
    {
        if(!empty($this->estado_pais_to_string))
        {
            return $this->estado_pais_to_string;
        }
    
        $values = Estado::where('pais_id', '=', $this->id)->getIndexedArray('pais_id','{pais->nome}');
        return implode(', ', $values);
    }

    public function set_nacionalidade_pais_to_string($nacionalidade_pais_to_string)
    {
        if(is_array($nacionalidade_pais_to_string))
        {
            $values = Pais::where('id', 'in', $nacionalidade_pais_to_string)->getIndexedArray('nome', 'nome');
            $this->nacionalidade_pais_to_string = implode(', ', $values);
        }
        else
        {
            $this->nacionalidade_pais_to_string = $nacionalidade_pais_to_string;
        }

        $this->vdata['nacionalidade_pais_to_string'] = $this->nacionalidade_pais_to_string;
    }

    public function get_nacionalidade_pais_to_string()
    {
        if(!empty($this->nacionalidade_pais_to_string))
        {
            return $this->nacionalidade_pais_to_string;
        }
    
        $values = Nacionalidade::where('pais_id', '=', $this->id)->getIndexedArray('pais_id','{pais->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Estado::where('pais_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Nacionalidade::where('pais_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

