<?php

class InteracaoItem extends TRecord
{
    const TABLENAME  = 'interacao_item';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATEDAT  = 'dt_item';

    private Interacao $interacao;
    private Produto $produto;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('produto_id');
        parent::addAttribute('interacao_id');
        parent::addAttribute('quantidade');
        parent::addAttribute('valor');
        parent::addAttribute('valor_total');
        parent::addAttribute('dt_item');
        parent::addAttribute('deleted_at');
            
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
     * Method set_produto
     * Sample of usage: $var->produto = $object;
     * @param $object Instance of Produto
     */
    public function set_produto(Produto $object)
    {
        $this->produto = $object;
        $this->produto_id = $object->id;
    }

    /**
     * Method get_produto
     * Sample of usage: $var->produto->attribute;
     * @returns Produto instance
     */
    public function get_produto()
    {
    
        // loads the associated object
        if (empty($this->produto))
            $this->produto = new Produto($this->produto_id);
    
        // returns the associated object
        return $this->produto;
    }

    
}

