<?php

class CentroCustoNota extends TRecord
{
    const TABLENAME  = 'centro_custo_nota';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private CentroCusto $centro_custo;
    private NotaBaixada $nota_baixada;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nota_baixada_id');
        parent::addAttribute('centro_custo_id');
        parent::addAttribute('valor_centro_custo');
        parent::addAttribute('comissao_centro_custo');
            
    }

    /**
     * Method set_centro_custo
     * Sample of usage: $var->centro_custo = $object;
     * @param $object Instance of CentroCusto
     */
    public function set_centro_custo(CentroCusto $object)
    {
        $this->centro_custo = $object;
        $this->centro_custo_id = $object->id;
    }

    /**
     * Method get_centro_custo
     * Sample of usage: $var->centro_custo->attribute;
     * @returns CentroCusto instance
     */
    public function get_centro_custo()
    {
    
        // loads the associated object
        if (empty($this->centro_custo))
            $this->centro_custo = new CentroCusto($this->centro_custo_id);
    
        // returns the associated object
        return $this->centro_custo;
    }
    /**
     * Method set_nota_baixada
     * Sample of usage: $var->nota_baixada = $object;
     * @param $object Instance of NotaBaixada
     */
    public function set_nota_baixada(NotaBaixada $object)
    {
        $this->nota_baixada = $object;
        $this->nota_baixada_id = $object->id;
    }

    /**
     * Method get_nota_baixada
     * Sample of usage: $var->nota_baixada->attribute;
     * @returns NotaBaixada instance
     */
    public function get_nota_baixada()
    {
    
        // loads the associated object
        if (empty($this->nota_baixada))
            $this->nota_baixada = new NotaBaixada($this->nota_baixada_id);
    
        // returns the associated object
        return $this->nota_baixada;
    }

    
}

