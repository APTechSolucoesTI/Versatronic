<?php

class InteracaoAtividadeRevisao extends TRecord
{
    const TABLENAME  = 'interacao_atividade_revisao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATEDAT  = 'created_at';

    private InteracaoAtividade $interacao_atividade;
    private SystemUsers $system_users;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_users_id');
        parent::addAttribute('interacao_atividade_id');
        parent::addAttribute('observacao');
        parent::addAttribute('created_at');
            
    }

    /**
     * Method set_interacao_atividade
     * Sample of usage: $var->interacao_atividade = $object;
     * @param $object Instance of InteracaoAtividade
     */
    public function set_interacao_atividade(InteracaoAtividade $object)
    {
        $this->interacao_atividade = $object;
        $this->interacao_atividade_id = $object->id;
    }

    /**
     * Method get_interacao_atividade
     * Sample of usage: $var->interacao_atividade->attribute;
     * @returns InteracaoAtividade instance
     */
    public function get_interacao_atividade()
    {
    
        // loads the associated object
        if (empty($this->interacao_atividade))
            $this->interacao_atividade = new InteracaoAtividade($this->interacao_atividade_id);
    
        // returns the associated object
        return $this->interacao_atividade;
    }
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_users(SystemUsers $object)
    {
        $this->system_users = $object;
        $this->system_users_id = $object->id;
    }

    /**
     * Method get_system_users
     * Sample of usage: $var->system_users->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_users()
    {
    
        // loads the associated object
        if (empty($this->system_users))
            $this->system_users = new SystemUsers($this->system_users_id);
    
        // returns the associated object
        return $this->system_users;
    }

    
}

