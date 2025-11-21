<?php

class InteracaoHistoricoEtapa extends TRecord
{
    const TABLENAME  = 'interacao_historico_etapa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private Interacao $interacao;
    private EtapaInteracao $etapa_interacao;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('interacao_id');
        parent::addAttribute('etapa_interacao_id');
        parent::addAttribute('dt_etapa');
            
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
    /**
     * Method set_etapa_interacao
     * Sample of usage: $var->etapa_interacao = $object;
     * @param $object Instance of EtapaInteracao
     */
    public function set_etapa_interacao(EtapaInteracao $object)
    {
        $this->etapa_interacao = $object;
        $this->etapa_interacao_id = $object->id;
    }

    /**
     * Method get_etapa_interacao
     * Sample of usage: $var->etapa_interacao->attribute;
     * @returns EtapaInteracao instance
     */
    public function get_etapa_interacao()
    {
    
        // loads the associated object
        if (empty($this->etapa_interacao))
            $this->etapa_interacao = new EtapaInteracao($this->etapa_interacao_id);
    
        // returns the associated object
        return $this->etapa_interacao;
    }

    
}

