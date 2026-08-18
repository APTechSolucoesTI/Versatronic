<?php

class InteracaoLocalizacao extends TRecord
{
    const TABLENAME  = 'interacao_localizacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATEDAT  = 'created_at';

    private Interacao $interacao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('interacao_id');
        parent::addAttribute('descricao');
        parent::addAttribute('latitude');
        parent::addAttribute('longitude');
        parent::addAttribute('dt_localizacao');
        parent::addAttribute('created_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('interacao_atividade');
    
    }

    /**
     * Method set_interacao
     * Sample of usage: $var->interacao = $object;
     * @param $object Instance of Interacao
     */
    public function set_interacao(Interacao $object)
    {
        $this->interacao = $object;
        $this->interacao_id = $object->id;
    }

    /**
     * Method get_interacao
     * Sample of usage: $var->interacao->attribute;
     * @returns Interacao instance
     */
    public function get_interacao()
    {
    
        // loads the associated object
        if (empty($this->interacao))
            $this->interacao = new Interacao($this->interacao_id);
    
        // returns the associated object
        return $this->interacao;
    }

}

