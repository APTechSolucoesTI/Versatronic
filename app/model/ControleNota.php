<?php

class ControleNota extends TRecord
{
    const TABLENAME  = 'controle_nota';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATED_BY_USER_ID  = 'created_by';

    const CREATEDAT  = 'created_at';

    private NotaBaixada $nota_baixada;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nota_baixada_id');
        parent::addAttribute('obs');
        parent::addAttribute('created_at');
        parent::addAttribute('created_by');
            
    }

    /**
     * Method set_nota_baixada
     * Sample of usage: $var->nota_baixada = $object;
     * @param $object Instance of NotaBaixada
     */
    public function set_nota_baixada(NotaBaixada $object)
    {
        $this->nota_baixada = $object;
        $this->nota_baixada_id = $object->id;
    }

    /**
     * Method get_nota_baixada
     * Sample of usage: $var->nota_baixada->attribute;
     * @returns NotaBaixada instance
     */
    public function get_nota_baixada()
    {
    
        // loads the associated object
        if (empty($this->nota_baixada))
            $this->nota_baixada = new NotaBaixada($this->nota_baixada_id);
    
        // returns the associated object
        return $this->nota_baixada;
    }

    
}

