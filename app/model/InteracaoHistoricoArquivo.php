<?php

class InteracaoHistoricoArquivo extends TRecord
{
    const TABLENAME  = 'interacao_historico_arquivo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private Interacao $interacao;
    private Movimentacao $movimentacao;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('interacao_id');
        parent::addAttribute('dt_arquivo');
        parent::addAttribute('movimentacao_id');
        parent::addAttribute('descricao');
        parent::addAttribute('interacao_arquivo_id');
            
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
     * Method set_movimentacao
     * Sample of usage: $var->movimentacao = $object;
     * @param $object Instance of Movimentacao
     */
    public function set_movimentacao(Movimentacao $object)
    {
        $this->movimentacao = $object;
        $this->movimentacao_id = $object->id;
    }

    /**
     * Method get_movimentacao
     * Sample of usage: $var->movimentacao->attribute;
     * @returns Movimentacao instance
     */
    public function get_movimentacao()
    {
    
        // loads the associated object
        if (empty($this->movimentacao))
            $this->movimentacao = new Movimentacao($this->movimentacao_id);
    
        // returns the associated object
        return $this->movimentacao;
    }

    
}

