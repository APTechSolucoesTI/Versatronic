<?php

class Complemento extends TRecord
{
    const TABLENAME  = 'complemento';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Representante $representante;
    private Transportadora $transportadora;
    private Transportadora $transportadora1;
    private Vendedor $vendedor;
    private Pessoa $pessoa;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('vendedor_id');
        parent::addAttribute('pessoa_id');
        parent::addAttribute('representante_id');
        parent::addAttribute('transportadora_id');
        parent::addAttribute('transportadora1_id');
        parent::addAttribute('ciffob');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('codcoligada');
        parent::addAttribute('data_alteracao_totvs');
            
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
    /**
     * Method set_transportadora
     * Sample of usage: $var->transportadora = $object;
     * @param $object Instance of Transportadora
     */
    public function set_transportadora(Transportadora $object)
    {
        $this->transportadora = $object;
        $this->transportadora_id = $object->id;
    }

    /**
     * Method get_transportadora
     * Sample of usage: $var->transportadora->attribute;
     * @returns Transportadora instance
     */
    public function get_transportadora()
    {
    
        // loads the associated object
        if (empty($this->transportadora))
            $this->transportadora = new Transportadora($this->transportadora_id);
    
        // returns the associated object
        return $this->transportadora;
    }
    /**
     * Method set_transportadora
     * Sample of usage: $var->transportadora = $object;
     * @param $object Instance of Transportadora
     */
    public function set_transportadora1(Transportadora $object)
    {
        $this->transportadora1 = $object;
        $this->transportadora1_id = $object->id;
    }

    /**
     * Method get_transportadora1
     * Sample of usage: $var->transportadora1->attribute;
     * @returns Transportadora instance
     */
    public function get_transportadora1()
    {
    
        // loads the associated object
        if (empty($this->transportadora1))
            $this->transportadora1 = new Transportadora($this->transportadora1_id);
    
        // returns the associated object
        return $this->transportadora1;
    }
    /**
     * Method set_vendedor
     * Sample of usage: $var->vendedor = $object;
     * @param $object Instance of Vendedor
     */
    public function set_vendedor(Vendedor $object)
    {
        $this->vendedor = $object;
        $this->vendedor_id = $object->id;
    }

    /**
     * Method get_vendedor
     * Sample of usage: $var->vendedor->attribute;
     * @returns Vendedor instance
     */
    public function get_vendedor()
    {
    
        // loads the associated object
        if (empty($this->vendedor))
            $this->vendedor = new Vendedor($this->vendedor_id);
    
        // returns the associated object
        return $this->vendedor;
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

