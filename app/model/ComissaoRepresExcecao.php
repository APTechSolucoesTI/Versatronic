<?php

class ComissaoRepresExcecao extends TRecord
{
    const TABLENAME  = 'comissao_repres_excecao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Representante $representante;
    private Pessoa $pessoa;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('representante_id');
        parent::addAttribute('valor');
        parent::addAttribute('ativo');
        parent::addAttribute('tipo_comissao');
        parent::addAttribute('deleted_at');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
            
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

