<?php

class Produto extends TRecord
{
    const TABLENAME  = 'produto';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private TipoProduto $tipo_produto;
    private FamiliaProduto $familia_produto;
    private Fabricante $fabricante;
    private UnidadeMedida $unidade_medida;
    private Pessoa $fornecedor;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_produto_id');
        parent::addAttribute('familia_produto_id');
        parent::addAttribute('fornecedor_id');
        parent::addAttribute('unidade_medida_id');
        parent::addAttribute('fabricante_id');
        parent::addAttribute('nome');
        parent::addAttribute('cod_barras');
        parent::addAttribute('preco_venda');
        parent::addAttribute('preco_custo');
        parent::addAttribute('peso_liquido');
        parent::addAttribute('peso_bruto');
        parent::addAttribute('largura');
        parent::addAttribute('altura');
        parent::addAttribute('volume');
        parent::addAttribute('estoque_minimo');
        parent::addAttribute('qtde_estoque');
        parent::addAttribute('estoque_maximo');
        parent::addAttribute('obs');
        parent::addAttribute('ativo');
        parent::addAttribute('foto');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method set_tipo_produto
     * Sample of usage: $var->tipo_produto = $object;
     * @param $object Instance of TipoProduto
     */
    public function set_tipo_produto(TipoProduto $object)
    {
        $this->tipo_produto = $object;
        $this->tipo_produto_id = $object->id;
    }

    /**
     * Method get_tipo_produto
     * Sample of usage: $var->tipo_produto->attribute;
     * @returns TipoProduto instance
     */
    public function get_tipo_produto()
    {
    
        // loads the associated object
        if (empty($this->tipo_produto))
            $this->tipo_produto = new TipoProduto($this->tipo_produto_id);
    
        // returns the associated object
        return $this->tipo_produto;
    }
    /**
     * Method set_familia_produto
     * Sample of usage: $var->familia_produto = $object;
     * @param $object Instance of FamiliaProduto
     */
    public function set_familia_produto(FamiliaProduto $object)
    {
        $this->familia_produto = $object;
        $this->familia_produto_id = $object->id;
    }

    /**
     * Method get_familia_produto
     * Sample of usage: $var->familia_produto->attribute;
     * @returns FamiliaProduto instance
     */
    public function get_familia_produto()
    {
    
        // loads the associated object
        if (empty($this->familia_produto))
            $this->familia_produto = new FamiliaProduto($this->familia_produto_id);
    
        // returns the associated object
        return $this->familia_produto;
    }
    /**
     * Method set_fabricante
     * Sample of usage: $var->fabricante = $object;
     * @param $object Instance of Fabricante
     */
    public function set_fabricante(Fabricante $object)
    {
        $this->fabricante = $object;
        $this->fabricante_id = $object->id;
    }

    /**
     * Method get_fabricante
     * Sample of usage: $var->fabricante->attribute;
     * @returns Fabricante instance
     */
    public function get_fabricante()
    {
    
        // loads the associated object
        if (empty($this->fabricante))
            $this->fabricante = new Fabricante($this->fabricante_id);
    
        // returns the associated object
        return $this->fabricante;
    }
    /**
     * Method set_unidade_medida
     * Sample of usage: $var->unidade_medida = $object;
     * @param $object Instance of UnidadeMedida
     */
    public function set_unidade_medida(UnidadeMedida $object)
    {
        $this->unidade_medida = $object;
        $this->unidade_medida_id = $object->id;
    }

    /**
     * Method get_unidade_medida
     * Sample of usage: $var->unidade_medida->attribute;
     * @returns UnidadeMedida instance
     */
    public function get_unidade_medida()
    {
    
        // loads the associated object
        if (empty($this->unidade_medida))
            $this->unidade_medida = new UnidadeMedida($this->unidade_medida_id);
    
        // returns the associated object
        return $this->unidade_medida;
    }
    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_fornecedor(Pessoa $object)
    {
        $this->fornecedor = $object;
        $this->fornecedor_id = $object->id;
    }

    /**
     * Method get_fornecedor
     * Sample of usage: $var->fornecedor->attribute;
     * @returns Pessoa instance
     */
    public function get_fornecedor()
    {
    
        // loads the associated object
        if (empty($this->fornecedor))
            $this->fornecedor = new Pessoa($this->fornecedor_id);
    
        // returns the associated object
        return $this->fornecedor;
    }

    /**
     * Method getInteracaoItems
     */
    public function getInteracaoItems()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('produto_id', '=', $this->id));
        return InteracaoItem::getObjects( $criteria );
    }

    public function set_interacao_item_produto_to_string($interacao_item_produto_to_string)
    {
        if(is_array($interacao_item_produto_to_string))
        {
            $values = Produto::where('id', 'in', $interacao_item_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_item_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_item_produto_to_string = $interacao_item_produto_to_string;
        }

        $this->vdata['interacao_item_produto_to_string'] = $this->interacao_item_produto_to_string;
    }

    public function get_interacao_item_produto_to_string()
    {
        if(!empty($this->interacao_item_produto_to_string))
        {
            return $this->interacao_item_produto_to_string;
        }
    
        $values = InteracaoItem::where('produto_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_item_interacao_to_string($interacao_item_interacao_to_string)
    {
        if(is_array($interacao_item_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_item_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_item_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_item_interacao_to_string = $interacao_item_interacao_to_string;
        }

        $this->vdata['interacao_item_interacao_to_string'] = $this->interacao_item_interacao_to_string;
    }

    public function get_interacao_item_interacao_to_string()
    {
        if(!empty($this->interacao_item_interacao_to_string))
        {
            return $this->interacao_item_interacao_to_string;
        }
    
        $values = InteracaoItem::where('produto_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    
}

