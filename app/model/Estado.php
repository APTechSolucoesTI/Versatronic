<?php

class Estado extends TRecord
{
    const TABLENAME  = 'estado';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private Pais $pais;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pais_id');
        parent::addAttribute('nome');
        parent::addAttribute('sigla');
        parent::addAttribute('codigo_ibge');
            
    }

    /**
     * Method set_pais
     * Sample of usage: $var->pais = $object;
     * @param $object Instance of Pais
     */
    public function set_pais(Pais $object)
    {
        $this->pais = $object;
        $this->pais_id = $object->id;
    }

    /**
     * Method get_pais
     * Sample of usage: $var->pais->attribute;
     * @returns Pais instance
     */
    public function get_pais()
    {
    
        // loads the associated object
        if (empty($this->pais))
            $this->pais = new Pais($this->pais_id);
    
        // returns the associated object
        return $this->pais;
    }

    /**
     * Method getCidades
     */
    public function getCidades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('estado_id', '=', $this->id));
        return Cidade::getObjects( $criteria );
    }

    public function set_cidade_estado_to_string($cidade_estado_to_string)
    {
        if(is_array($cidade_estado_to_string))
        {
            $values = Estado::where('id', 'in', $cidade_estado_to_string)->getIndexedArray('nome', 'nome');
            $this->cidade_estado_to_string = implode(', ', $values);
        }
        else
        {
            $this->cidade_estado_to_string = $cidade_estado_to_string;
        }

        $this->vdata['cidade_estado_to_string'] = $this->cidade_estado_to_string;
    }

    public function get_cidade_estado_to_string()
    {
        if(!empty($this->cidade_estado_to_string))
        {
            return $this->cidade_estado_to_string;
        }
    
        $values = Cidade::where('estado_id', '=', $this->id)->getIndexedArray('estado_id','{estado->nome}');
        return implode(', ', $values);
    }

    
}

