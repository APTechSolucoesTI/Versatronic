<?php

class InteracaoAtividade extends TRecord
{
    const TABLENAME  = 'interacao_atividade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATEDAT  = 'dt_atividade';

    private Interacao $interacao;
    private TipoAtividade $tipo_atividade;
    private EstadoAtividade $estado_atividade;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_atividade_id');
        parent::addAttribute('interacao_id');
        parent::addAttribute('estado_atividade_id');
        parent::addAttribute('descricao');
        parent::addAttribute('horario_inicial');
        parent::addAttribute('horario_final');
        parent::addAttribute('observacao');
        parent::addAttribute('dt_atividade');
            
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
     * Method set_estado_atividade
     * Sample of usage: $var->estado_atividade = $object;
     * @param $object Instance of EstadoAtividade
     */
    public function set_estado_atividade(EstadoAtividade $object)
    {
        $this->estado_atividade = $object;
        $this->estado_atividade_id = $object->id;
    }

    /**
     * Method get_estado_atividade
     * Sample of usage: $var->estado_atividade->attribute;
     * @returns EstadoAtividade instance
     */
    public function get_estado_atividade()
    {
    
        // loads the associated object
        if (empty($this->estado_atividade))
            $this->estado_atividade = new EstadoAtividade($this->estado_atividade_id);
    
        // returns the associated object
        return $this->estado_atividade;
    }

    
}

