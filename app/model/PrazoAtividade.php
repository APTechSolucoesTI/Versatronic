<?php

class PrazoAtividade extends TRecord
{
    const TABLENAME  = 'prazo_atividade';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private RegrasTipoAtividade $regras_tipo_atividade;
    private CategoriaCliente $categoria_cliente;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('regras_tipo_atividade_id');
        parent::addAttribute('categoria_cliente_id');
        parent::addAttribute('ambos');
        parent::addAttribute('dias');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_regras_tipo_atividade
     * Sample of usage: $var->regras_tipo_atividade = $object;
     * @param $object Instance of RegrasTipoAtividade
     */
    public function set_regras_tipo_atividade(RegrasTipoAtividade $object)
    {
        $this->regras_tipo_atividade = $object;
        $this->regras_tipo_atividade_id = $object->id;
    }

    /**
     * Method get_regras_tipo_atividade
     * Sample of usage: $var->regras_tipo_atividade->attribute;
     * @returns RegrasTipoAtividade instance
     */
    public function get_regras_tipo_atividade()
    {
    
        // loads the associated object
        if (empty($this->regras_tipo_atividade))
            $this->regras_tipo_atividade = new RegrasTipoAtividade($this->regras_tipo_atividade_id);
    
        // returns the associated object
        return $this->regras_tipo_atividade;
    }
    /**
     * Method set_categoria_cliente
     * Sample of usage: $var->categoria_cliente = $object;
     * @param $object Instance of CategoriaCliente
     */
    public function set_categoria_cliente(CategoriaCliente $object)
    {
        $this->categoria_cliente = $object;
        $this->categoria_cliente_id = $object->id;
    }

    /**
     * Method get_categoria_cliente
     * Sample of usage: $var->categoria_cliente->attribute;
     * @returns CategoriaCliente instance
     */
    public function get_categoria_cliente()
    {
    
        // loads the associated object
        if (empty($this->categoria_cliente))
            $this->categoria_cliente = new CategoriaCliente($this->categoria_cliente_id);
    
        // returns the associated object
        return $this->categoria_cliente;
    }

    
}

