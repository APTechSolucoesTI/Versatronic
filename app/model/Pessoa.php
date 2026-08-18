<?php

class Pessoa extends TRecord
{
    const TABLENAME  = 'pessoa';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private TipoPessoa $tipo_pessoa;
    private CategoriaCliente $categoria_cliente;
    private SystemUsers $system_user;
    private Nacionalidade $nacionalidade;

    use SystemChangeLogTrait;
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('tipo_pessoa_id');
        parent::addAttribute('categoria_cliente_id');
        parent::addAttribute('system_user_id');
        parent::addAttribute('origem');
        parent::addAttribute('razao_social');
        parent::addAttribute('nome_fantasia');
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('rg_ie');
        parent::addAttribute('nacionalidade_id');
        parent::addAttribute('fone');
        parent::addAttribute('email');
        parent::addAttribute('obs');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('ativo');
        parent::addAttribute('data_alteracao_totvs');
        parent::addAttribute('bloqueado');
    
    }

    /**
     * Method set_tipo_pessoa
     * Sample of usage: $var->tipo_pessoa = $object;
     * @param $object Instance of TipoPessoa
     */
    public function set_tipo_pessoa(TipoPessoa $object)
    {
        $this->tipo_pessoa = $object;
        $this->tipo_pessoa_id = $object->id;
    }

    /**
     * Method get_tipo_pessoa
     * Sample of usage: $var->tipo_pessoa->attribute;
     * @returns TipoPessoa instance
     */
    public function get_tipo_pessoa()
    {
    
        // loads the associated object
        if (empty($this->tipo_pessoa))
            $this->tipo_pessoa = new TipoPessoa($this->tipo_pessoa_id);
    
        // returns the associated object
        return $this->tipo_pessoa;
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
    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_user(SystemUsers $object)
    {
        $this->system_user = $object;
        $this->system_user_id = $object->id;
    }

    /**
     * Method get_system_user
     * Sample of usage: $var->system_user->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_user()
    {
    
        // loads the associated object
        if (empty($this->system_user))
            $this->system_user = new SystemUsers($this->system_user_id);
    
        // returns the associated object
        return $this->system_user;
    }
    /**
     * Method set_nacionalidade
     * Sample of usage: $var->nacionalidade = $object;
     * @param $object Instance of Nacionalidade
     */
    public function set_nacionalidade(Nacionalidade $object)
    {
        $this->nacionalidade = $object;
        $this->nacionalidade_id = $object->id;
    }

    /**
     * Method get_nacionalidade
     * Sample of usage: $var->nacionalidade->attribute;
     * @returns Nacionalidade instance
     */
    public function get_nacionalidade()
    {
    
        // loads the associated object
        if (empty($this->nacionalidade))
            $this->nacionalidade = new Nacionalidade($this->nacionalidade_id);
    
        // returns the associated object
        return $this->nacionalidade;
    }

    /**
     * Method getInteracaos
     */
    public function getInteracaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('cliente_id', '=', $this->id));
        return Interacao::getObjects( $criteria );
    }
    /**
     * Method getComissaoRepresExcecaos
     */
    public function getComissaoRepresExcecaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return ComissaoRepresExcecao::getObjects( $criteria );
    }
    /**
     * Method getPessoaContatos
     */
    public function getPessoaContatos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaContato::getObjects( $criteria );
    }
    /**
     * Method getPessoaEnderecos
     */
    public function getPessoaEnderecos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaEndereco::getObjects( $criteria );
    }
    /**
     * Method getPessoaGrupos
     */
    public function getPessoaGrupos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return PessoaGrupo::getObjects( $criteria );
    }
    /**
     * Method getProdutos
     */
    public function getProdutos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('fornecedor_id', '=', $this->id));
        return Produto::getObjects( $criteria );
    }
    /**
     * Method getComplementos
     */
    public function getComplementos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return Complemento::getObjects( $criteria );
    }
    /**
     * Method getRepresentanteDivergentes
     */
    public function getRepresentanteDivergentes()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('pessoa_id', '=', $this->id));
        return RepresentanteDivergente::getObjects( $criteria );
    }

    public function set_interacao_tipo_interacao_to_string($interacao_tipo_interacao_to_string)
    {
        if(is_array($interacao_tipo_interacao_to_string))
        {
            $values = TipoInteracao::where('id', 'in', $interacao_tipo_interacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_tipo_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_tipo_interacao_to_string = $interacao_tipo_interacao_to_string;
        }

        $this->vdata['interacao_tipo_interacao_to_string'] = $this->interacao_tipo_interacao_to_string;
    }

    public function get_interacao_tipo_interacao_to_string()
    {
        if(!empty($this->interacao_tipo_interacao_to_string))
        {
            return $this->interacao_tipo_interacao_to_string;
        }
    
        $values = Interacao::where('cliente_id', '=', $this->id)->getIndexedArray('tipo_interacao_id','{tipo_interacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_cliente_to_string($interacao_cliente_to_string)
    {
        if(is_array($interacao_cliente_to_string))
        {
            $values = Pessoa::where('id', 'in', $interacao_cliente_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->interacao_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_cliente_to_string = $interacao_cliente_to_string;
        }

        $this->vdata['interacao_cliente_to_string'] = $this->interacao_cliente_to_string;
    }

    public function get_interacao_cliente_to_string()
    {
        if(!empty($this->interacao_cliente_to_string))
        {
            return $this->interacao_cliente_to_string;
        }
    
        $values = Interacao::where('cliente_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->razao_social}');
        return implode(', ', $values);
    }

    public function set_interacao_vendedor_to_string($interacao_vendedor_to_string)
    {
        if(is_array($interacao_vendedor_to_string))
        {
            $values = Representante::where('id', 'in', $interacao_vendedor_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->interacao_vendedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_vendedor_to_string = $interacao_vendedor_to_string;
        }

        $this->vdata['interacao_vendedor_to_string'] = $this->interacao_vendedor_to_string;
    }

    public function get_interacao_vendedor_to_string()
    {
        if(!empty($this->interacao_vendedor_to_string))
        {
            return $this->interacao_vendedor_to_string;
        }
    
        $values = Interacao::where('cliente_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
        return implode(', ', $values);
    }

    public function set_interacao_origem_contato_to_string($interacao_origem_contato_to_string)
    {
        if(is_array($interacao_origem_contato_to_string))
        {
            $values = OrigemContato::where('id', 'in', $interacao_origem_contato_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_origem_contato_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_origem_contato_to_string = $interacao_origem_contato_to_string;
        }

        $this->vdata['interacao_origem_contato_to_string'] = $this->interacao_origem_contato_to_string;
    }

    public function get_interacao_origem_contato_to_string()
    {
        if(!empty($this->interacao_origem_contato_to_string))
        {
            return $this->interacao_origem_contato_to_string;
        }
    
        $values = Interacao::where('cliente_id', '=', $this->id)->getIndexedArray('origem_contato_id','{origem_contato->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_etapa_interacao_to_string($interacao_etapa_interacao_to_string)
    {
        if(is_array($interacao_etapa_interacao_to_string))
        {
            $values = EtapaInteracao::where('id', 'in', $interacao_etapa_interacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_etapa_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_etapa_interacao_to_string = $interacao_etapa_interacao_to_string;
        }

        $this->vdata['interacao_etapa_interacao_to_string'] = $this->interacao_etapa_interacao_to_string;
    }

    public function get_interacao_etapa_interacao_to_string()
    {
        if(!empty($this->interacao_etapa_interacao_to_string))
        {
            return $this->interacao_etapa_interacao_to_string;
        }
    
        $values = Interacao::where('cliente_id', '=', $this->id)->getIndexedArray('etapa_interacao_id','{etapa_interacao->nome}');
        return implode(', ', $values);
    }

    public function set_comissao_repres_excecao_pessoa_to_string($comissao_repres_excecao_pessoa_to_string)
    {
        if(is_array($comissao_repres_excecao_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $comissao_repres_excecao_pessoa_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->comissao_repres_excecao_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->comissao_repres_excecao_pessoa_to_string = $comissao_repres_excecao_pessoa_to_string;
        }

        $this->vdata['comissao_repres_excecao_pessoa_to_string'] = $this->comissao_repres_excecao_pessoa_to_string;
    }

    public function get_comissao_repres_excecao_pessoa_to_string()
    {
        if(!empty($this->comissao_repres_excecao_pessoa_to_string))
        {
            return $this->comissao_repres_excecao_pessoa_to_string;
        }
    
        $values = ComissaoRepresExcecao::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
        return implode(', ', $values);
    }

    public function set_comissao_repres_excecao_representante_to_string($comissao_repres_excecao_representante_to_string)
    {
        if(is_array($comissao_repres_excecao_representante_to_string))
        {
            $values = Representante::where('id', 'in', $comissao_repres_excecao_representante_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->comissao_repres_excecao_representante_to_string = implode(', ', $values);
        }
        else
        {
            $this->comissao_repres_excecao_representante_to_string = $comissao_repres_excecao_representante_to_string;
        }

        $this->vdata['comissao_repres_excecao_representante_to_string'] = $this->comissao_repres_excecao_representante_to_string;
    }

    public function get_comissao_repres_excecao_representante_to_string()
    {
        if(!empty($this->comissao_repres_excecao_representante_to_string))
        {
            return $this->comissao_repres_excecao_representante_to_string;
        }
    
        $values = ComissaoRepresExcecao::where('pessoa_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
        return implode(', ', $values);
    }

    public function set_pessoa_contato_pessoa_to_string($pessoa_contato_pessoa_to_string)
    {
        if(is_array($pessoa_contato_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_contato_pessoa_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->pessoa_contato_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_contato_pessoa_to_string = $pessoa_contato_pessoa_to_string;
        }

        $this->vdata['pessoa_contato_pessoa_to_string'] = $this->pessoa_contato_pessoa_to_string;
    }

    public function get_pessoa_contato_pessoa_to_string()
    {
        if(!empty($this->pessoa_contato_pessoa_to_string))
        {
            return $this->pessoa_contato_pessoa_to_string;
        }
    
        $values = PessoaContato::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
        return implode(', ', $values);
    }

    public function set_pessoa_endereco_pessoa_to_string($pessoa_endereco_pessoa_to_string)
    {
        if(is_array($pessoa_endereco_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_endereco_pessoa_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->pessoa_endereco_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_endereco_pessoa_to_string = $pessoa_endereco_pessoa_to_string;
        }

        $this->vdata['pessoa_endereco_pessoa_to_string'] = $this->pessoa_endereco_pessoa_to_string;
    }

    public function get_pessoa_endereco_pessoa_to_string()
    {
        if(!empty($this->pessoa_endereco_pessoa_to_string))
        {
            return $this->pessoa_endereco_pessoa_to_string;
        }
    
        $values = PessoaEndereco::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
        return implode(', ', $values);
    }

    public function set_pessoa_endereco_cidade_to_string($pessoa_endereco_cidade_to_string)
    {
        if(is_array($pessoa_endereco_cidade_to_string))
        {
            $values = Cidade::where('id', 'in', $pessoa_endereco_cidade_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_endereco_cidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_endereco_cidade_to_string = $pessoa_endereco_cidade_to_string;
        }

        $this->vdata['pessoa_endereco_cidade_to_string'] = $this->pessoa_endereco_cidade_to_string;
    }

    public function get_pessoa_endereco_cidade_to_string()
    {
        if(!empty($this->pessoa_endereco_cidade_to_string))
        {
            return $this->pessoa_endereco_cidade_to_string;
        }
    
        $values = PessoaEndereco::where('pessoa_id', '=', $this->id)->getIndexedArray('cidade_id','{cidade->nome}');
        return implode(', ', $values);
    }

    public function set_pessoa_grupo_pessoa_to_string($pessoa_grupo_pessoa_to_string)
    {
        if(is_array($pessoa_grupo_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $pessoa_grupo_pessoa_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->pessoa_grupo_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_grupo_pessoa_to_string = $pessoa_grupo_pessoa_to_string;
        }

        $this->vdata['pessoa_grupo_pessoa_to_string'] = $this->pessoa_grupo_pessoa_to_string;
    }

    public function get_pessoa_grupo_pessoa_to_string()
    {
        if(!empty($this->pessoa_grupo_pessoa_to_string))
        {
            return $this->pessoa_grupo_pessoa_to_string;
        }
    
        $values = PessoaGrupo::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
        return implode(', ', $values);
    }

    public function set_pessoa_grupo_grupo_to_string($pessoa_grupo_grupo_to_string)
    {
        if(is_array($pessoa_grupo_grupo_to_string))
        {
            $values = Grupo::where('id', 'in', $pessoa_grupo_grupo_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_grupo_grupo_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_grupo_grupo_to_string = $pessoa_grupo_grupo_to_string;
        }

        $this->vdata['pessoa_grupo_grupo_to_string'] = $this->pessoa_grupo_grupo_to_string;
    }

    public function get_pessoa_grupo_grupo_to_string()
    {
        if(!empty($this->pessoa_grupo_grupo_to_string))
        {
            return $this->pessoa_grupo_grupo_to_string;
        }
    
        $values = PessoaGrupo::where('pessoa_id', '=', $this->id)->getIndexedArray('grupo_id','{grupo->nome}');
        return implode(', ', $values);
    }

    public function set_produto_tipo_produto_to_string($produto_tipo_produto_to_string)
    {
        if(is_array($produto_tipo_produto_to_string))
        {
            $values = TipoProduto::where('id', 'in', $produto_tipo_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_tipo_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_tipo_produto_to_string = $produto_tipo_produto_to_string;
        }

        $this->vdata['produto_tipo_produto_to_string'] = $this->produto_tipo_produto_to_string;
    }

    public function get_produto_tipo_produto_to_string()
    {
        if(!empty($this->produto_tipo_produto_to_string))
        {
            return $this->produto_tipo_produto_to_string;
        }
    
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('tipo_produto_id','{tipo_produto->nome}');
        return implode(', ', $values);
    }

    public function set_produto_familia_produto_to_string($produto_familia_produto_to_string)
    {
        if(is_array($produto_familia_produto_to_string))
        {
            $values = FamiliaProduto::where('id', 'in', $produto_familia_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_familia_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_familia_produto_to_string = $produto_familia_produto_to_string;
        }

        $this->vdata['produto_familia_produto_to_string'] = $this->produto_familia_produto_to_string;
    }

    public function get_produto_familia_produto_to_string()
    {
        if(!empty($this->produto_familia_produto_to_string))
        {
            return $this->produto_familia_produto_to_string;
        }
    
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('familia_produto_id','{familia_produto->nome}');
        return implode(', ', $values);
    }

    public function set_produto_fornecedor_to_string($produto_fornecedor_to_string)
    {
        if(is_array($produto_fornecedor_to_string))
        {
            $values = Pessoa::where('id', 'in', $produto_fornecedor_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->produto_fornecedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_fornecedor_to_string = $produto_fornecedor_to_string;
        }

        $this->vdata['produto_fornecedor_to_string'] = $this->produto_fornecedor_to_string;
    }

    public function get_produto_fornecedor_to_string()
    {
        if(!empty($this->produto_fornecedor_to_string))
        {
            return $this->produto_fornecedor_to_string;
        }
    
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('fornecedor_id','{fornecedor->razao_social}');
        return implode(', ', $values);
    }

    public function set_produto_unidade_medida_to_string($produto_unidade_medida_to_string)
    {
        if(is_array($produto_unidade_medida_to_string))
        {
            $values = UnidadeMedida::where('id', 'in', $produto_unidade_medida_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_unidade_medida_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_unidade_medida_to_string = $produto_unidade_medida_to_string;
        }

        $this->vdata['produto_unidade_medida_to_string'] = $this->produto_unidade_medida_to_string;
    }

    public function get_produto_unidade_medida_to_string()
    {
        if(!empty($this->produto_unidade_medida_to_string))
        {
            return $this->produto_unidade_medida_to_string;
        }
    
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('unidade_medida_id','{unidade_medida->nome}');
        return implode(', ', $values);
    }

    public function set_produto_fabricante_to_string($produto_fabricante_to_string)
    {
        if(is_array($produto_fabricante_to_string))
        {
            $values = Fabricante::where('id', 'in', $produto_fabricante_to_string)->getIndexedArray('nome', 'nome');
            $this->produto_fabricante_to_string = implode(', ', $values);
        }
        else
        {
            $this->produto_fabricante_to_string = $produto_fabricante_to_string;
        }

        $this->vdata['produto_fabricante_to_string'] = $this->produto_fabricante_to_string;
    }

    public function get_produto_fabricante_to_string()
    {
        if(!empty($this->produto_fabricante_to_string))
        {
            return $this->produto_fabricante_to_string;
        }
    
        $values = Produto::where('fornecedor_id', '=', $this->id)->getIndexedArray('fabricante_id','{fabricante->nome}');
        return implode(', ', $values);
    }

    public function set_complemento_vendedor_to_string($complemento_vendedor_to_string)
    {
        if(is_array($complemento_vendedor_to_string))
        {
            $values = Vendedor::where('id', 'in', $complemento_vendedor_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->complemento_vendedor_to_string = implode(', ', $values);
        }
        else
        {
            $this->complemento_vendedor_to_string = $complemento_vendedor_to_string;
        }

        $this->vdata['complemento_vendedor_to_string'] = $this->complemento_vendedor_to_string;
    }

    public function get_complemento_vendedor_to_string()
    {
        if(!empty($this->complemento_vendedor_to_string))
        {
            return $this->complemento_vendedor_to_string;
        }
    
        $values = Complemento::where('pessoa_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
        return implode(', ', $values);
    }

    public function set_complemento_pessoa_to_string($complemento_pessoa_to_string)
    {
        if(is_array($complemento_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $complemento_pessoa_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->complemento_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->complemento_pessoa_to_string = $complemento_pessoa_to_string;
        }

        $this->vdata['complemento_pessoa_to_string'] = $this->complemento_pessoa_to_string;
    }

    public function get_complemento_pessoa_to_string()
    {
        if(!empty($this->complemento_pessoa_to_string))
        {
            return $this->complemento_pessoa_to_string;
        }
    
        $values = Complemento::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
        return implode(', ', $values);
    }

    public function set_complemento_representante_to_string($complemento_representante_to_string)
    {
        if(is_array($complemento_representante_to_string))
        {
            $values = Representante::where('id', 'in', $complemento_representante_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->complemento_representante_to_string = implode(', ', $values);
        }
        else
        {
            $this->complemento_representante_to_string = $complemento_representante_to_string;
        }

        $this->vdata['complemento_representante_to_string'] = $this->complemento_representante_to_string;
    }

    public function get_complemento_representante_to_string()
    {
        if(!empty($this->complemento_representante_to_string))
        {
            return $this->complemento_representante_to_string;
        }
    
        $values = Complemento::where('pessoa_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
        return implode(', ', $values);
    }

    public function set_complemento_transportadora_to_string($complemento_transportadora_to_string)
    {
        if(is_array($complemento_transportadora_to_string))
        {
            $values = Transportadora::where('id', 'in', $complemento_transportadora_to_string)->getIndexedArray('id', 'id');
            $this->complemento_transportadora_to_string = implode(', ', $values);
        }
        else
        {
            $this->complemento_transportadora_to_string = $complemento_transportadora_to_string;
        }

        $this->vdata['complemento_transportadora_to_string'] = $this->complemento_transportadora_to_string;
    }

    public function get_complemento_transportadora_to_string()
    {
        if(!empty($this->complemento_transportadora_to_string))
        {
            return $this->complemento_transportadora_to_string;
        }
    
        $values = Complemento::where('pessoa_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->id}');
        return implode(', ', $values);
    }

    public function set_complemento_transportadora1_to_string($complemento_transportadora1_to_string)
    {
        if(is_array($complemento_transportadora1_to_string))
        {
            $values = Transportadora::where('id', 'in', $complemento_transportadora1_to_string)->getIndexedArray('id', 'id');
            $this->complemento_transportadora1_to_string = implode(', ', $values);
        }
        else
        {
            $this->complemento_transportadora1_to_string = $complemento_transportadora1_to_string;
        }

        $this->vdata['complemento_transportadora1_to_string'] = $this->complemento_transportadora1_to_string;
    }

    public function get_complemento_transportadora1_to_string()
    {
        if(!empty($this->complemento_transportadora1_to_string))
        {
            return $this->complemento_transportadora1_to_string;
        }
    
        $values = Complemento::where('pessoa_id', '=', $this->id)->getIndexedArray('transportadora1_id','{transportadora1->id}');
        return implode(', ', $values);
    }

    public function set_representante_divergente_pessoa_to_string($representante_divergente_pessoa_to_string)
    {
        if(is_array($representante_divergente_pessoa_to_string))
        {
            $values = Pessoa::where('id', 'in', $representante_divergente_pessoa_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->representante_divergente_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->representante_divergente_pessoa_to_string = $representante_divergente_pessoa_to_string;
        }

        $this->vdata['representante_divergente_pessoa_to_string'] = $this->representante_divergente_pessoa_to_string;
    }

    public function get_representante_divergente_pessoa_to_string()
    {
        if(!empty($this->representante_divergente_pessoa_to_string))
        {
            return $this->representante_divergente_pessoa_to_string;
        }
    
        $values = RepresentanteDivergente::where('pessoa_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
        return implode(', ', $values);
    }

    public function set_representante_divergente_rep_ap_to_string($representante_divergente_rep_ap_to_string)
    {
        if(is_array($representante_divergente_rep_ap_to_string))
        {
            $values = Representante::where('id', 'in', $representante_divergente_rep_ap_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->representante_divergente_rep_ap_to_string = implode(', ', $values);
        }
        else
        {
            $this->representante_divergente_rep_ap_to_string = $representante_divergente_rep_ap_to_string;
        }

        $this->vdata['representante_divergente_rep_ap_to_string'] = $this->representante_divergente_rep_ap_to_string;
    }

    public function get_representante_divergente_rep_ap_to_string()
    {
        if(!empty($this->representante_divergente_rep_ap_to_string))
        {
            return $this->representante_divergente_rep_ap_to_string;
        }
    
        $values = RepresentanteDivergente::where('pessoa_id', '=', $this->id)->getIndexedArray('rep_ap_id','{rep_ap->razao_social}');
        return implode(', ', $values);
    }

    public function set_representante_divergente_rep_totvs_to_string($representante_divergente_rep_totvs_to_string)
    {
        if(is_array($representante_divergente_rep_totvs_to_string))
        {
            $values = Representante::where('id', 'in', $representante_divergente_rep_totvs_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->representante_divergente_rep_totvs_to_string = implode(', ', $values);
        }
        else
        {
            $this->representante_divergente_rep_totvs_to_string = $representante_divergente_rep_totvs_to_string;
        }

        $this->vdata['representante_divergente_rep_totvs_to_string'] = $this->representante_divergente_rep_totvs_to_string;
    }

    public function get_representante_divergente_rep_totvs_to_string()
    {
        if(!empty($this->representante_divergente_rep_totvs_to_string))
        {
            return $this->representante_divergente_rep_totvs_to_string;
        }
    
        $values = RepresentanteDivergente::where('pessoa_id', '=', $this->id)->getIndexedArray('rep_totvs_id','{rep_totvs->razao_social}');
        return implode(', ', $values);
    }

    public function get_cidade_uf(){
        $endereco = PessoaEndereco::where('pessoa_id','=',$this->id)->where('principal','=','S')->first();
        if($endereco){
            return $endereco->cidade->nome."/".$endereco->cidade->estado->sigla;
        }
        return null;
    }

}

