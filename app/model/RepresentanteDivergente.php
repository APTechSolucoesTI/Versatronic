<?php

class RepresentanteDivergente extends TRecord
{
    const TABLENAME  = 'representante_divergente';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private Representante $rep_ap;
    private Representante $rep_totvs;
    private Pessoa $pessoa;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('rep_ap_id');
        parent::addAttribute('rep_totvs_id');
        parent::addAttribute('status');
            
    }

    /**
     * Method set_representante
     * Sample of usage: $var->representante = $object;
     * @param $object Instance of Representante
     */
    public function set_rep_ap(Representante $object)
    {
        $this->rep_ap = $object;
        $this->rep_ap_id = $object->id;
    }

    /**
     * Method get_rep_ap
     * Sample of usage: $var->rep_ap->attribute;
     * @returns Representante instance
     */
    public function get_rep_ap()
    {
    
        // loads the associated object
        if (empty($this->rep_ap))
            $this->rep_ap = new Representante($this->rep_ap_id);
    
        // returns the associated object
        return $this->rep_ap;
    }
    /**
     * Method set_representante
     * Sample of usage: $var->representante = $object;
     * @param $object Instance of Representante
     */
    public function set_rep_totvs(Representante $object)
    {
        $this->rep_totvs = $object;
        $this->rep_totvs_id = $object->id;
    }

    /**
     * Method get_rep_totvs
     * Sample of usage: $var->rep_totvs->attribute;
     * @returns Representante instance
     */
    public function get_rep_totvs()
    {
    
        // loads the associated object
        if (empty($this->rep_totvs))
            $this->rep_totvs = new Representante($this->rep_totvs_id);
    
        // returns the associated object
        return $this->rep_totvs;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_pessoa(Pessoa $object)
    {
        $this->pessoa = $object;
        $this->pessoa_id = $object->id;
    }

    /**
     * Method get_pessoa
     * Sample of usage: $var->pessoa->attribute;
     * @returns Pessoa instance
     */
    public function get_pessoa()
    {
    
        // loads the associated object
        if (empty($this->pessoa))
            $this->pessoa = new Pessoa($this->pessoa_id);
    
        // returns the associated object
        return $this->pessoa;
    }

    
}

