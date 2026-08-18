<?php

class ComissaoRepres extends TRecord
{
    const TABLENAME  = 'comissao_repres';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Representante $representante;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('representante_id');
        parent::addAttribute('tipo_comissao');
        parent::addAttribute('valor');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_representante
     * Sample of usage: $var->representante = $object;
     * @param $object Instance of Representante
     */
    public function set_representante(Representante $object)
    {
        $this->representante = $object;
        $this->representante_id = $object->id;
    }

    /**
     * Method get_representante
     * Sample of usage: $var->representante->attribute;
     * @returns Representante instance
     */
    public function get_representante()
    {
    
        // loads the associated object
        if (empty($this->representante))
            $this->representante = new Representante($this->representante_id);
    
        // returns the associated object
        return $this->representante;
    }

    
}

