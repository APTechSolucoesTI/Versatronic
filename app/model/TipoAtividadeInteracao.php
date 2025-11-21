<?php

class TipoAtividadeInteracao extends TRecord
{
    const TABLENAME  = 'tipo_atividade_interacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private TipoAtividade $tipo_atividade;
    private TipoInteracao $tipo_interacao;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_atividade_id');
        parent::addAttribute('tipo_interacao_id');
            
    }

    /**
     * Method set_tipo_atividade
     * Sample of usage: $var->tipo_atividade = $object;
     * @param $object Instance of TipoAtividade
     */
    public function set_tipo_atividade(TipoAtividade $object)
    {
        $this->tipo_atividade = $object;
        $this->tipo_atividade_id = $object->id;
    }

    /**
     * Method get_tipo_atividade
     * Sample of usage: $var->tipo_atividade->attribute;
     * @returns TipoAtividade instance
     */
    public function get_tipo_atividade()
    {
    
        // loads the associated object
        if (empty($this->tipo_atividade))
            $this->tipo_atividade = new TipoAtividade($this->tipo_atividade_id);
    
        // returns the associated object
        return $this->tipo_atividade;
    }
    /**
     * Method set_tipo_interacao
     * Sample of usage: $var->tipo_interacao = $object;
     * @param $object Instance of TipoInteracao
     */
    public function set_tipo_interacao(TipoInteracao $object)
    {
        $this->tipo_interacao = $object;
        $this->tipo_interacao_id = $object->id;
    }

    /**
     * Method get_tipo_interacao
     * Sample of usage: $var->tipo_interacao->attribute;
     * @returns TipoInteracao instance
     */
    public function get_tipo_interacao()
    {
    
        // loads the associated object
        if (empty($this->tipo_interacao))
            $this->tipo_interacao = new TipoInteracao($this->tipo_interacao_id);
    
        // returns the associated object
        return $this->tipo_interacao;
    }

    
}

