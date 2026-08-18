<?php

class CategoriaCliente extends TRecord
{
    const TABLENAME  = 'categoria_cliente';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('nome');
            
    }

    /**
     * Method getPessoas
     */
    public function getPessoas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('categoria_cliente_id', '=', $this->id));
        return Pessoa::getObjects( $criteria );
    }
    /**
     * Method getPrazoAtividades
     */
    public function getPrazoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('categoria_cliente_id', '=', $this->id));
        return PrazoAtividade::getObjects( $criteria );
    }

    public function set_pessoa_tipo_pessoa_to_string($pessoa_tipo_pessoa_to_string)
    {
        if(is_array($pessoa_tipo_pessoa_to_string))
        {
            $values = TipoPessoa::where('id', 'in', $pessoa_tipo_pessoa_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_tipo_pessoa_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_tipo_pessoa_to_string = $pessoa_tipo_pessoa_to_string;
        }

        $this->vdata['pessoa_tipo_pessoa_to_string'] = $this->pessoa_tipo_pessoa_to_string;
    }

    public function get_pessoa_tipo_pessoa_to_string()
    {
        if(!empty($this->pessoa_tipo_pessoa_to_string))
        {
            return $this->pessoa_tipo_pessoa_to_string;
        }
    
        $values = Pessoa::where('categoria_cliente_id', '=', $this->id)->getIndexedArray('tipo_pessoa_id','{tipo_pessoa->nome}');
        return implode(', ', $values);
    }

    public function set_pessoa_categoria_cliente_to_string($pessoa_categoria_cliente_to_string)
    {
        if(is_array($pessoa_categoria_cliente_to_string))
        {
            $values = CategoriaCliente::where('id', 'in', $pessoa_categoria_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->pessoa_categoria_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_categoria_cliente_to_string = $pessoa_categoria_cliente_to_string;
        }

        $this->vdata['pessoa_categoria_cliente_to_string'] = $this->pessoa_categoria_cliente_to_string;
    }

    public function get_pessoa_categoria_cliente_to_string()
    {
        if(!empty($this->pessoa_categoria_cliente_to_string))
        {
            return $this->pessoa_categoria_cliente_to_string;
        }
    
        $values = Pessoa::where('categoria_cliente_id', '=', $this->id)->getIndexedArray('categoria_cliente_id','{categoria_cliente->nome}');
        return implode(', ', $values);
    }

    public function set_pessoa_system_user_to_string($pessoa_system_user_to_string)
    {
        if(is_array($pessoa_system_user_to_string))
        {
            $values = SystemUsers::where('id', 'in', $pessoa_system_user_to_string)->getIndexedArray('name', 'name');
            $this->pessoa_system_user_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_system_user_to_string = $pessoa_system_user_to_string;
        }

        $this->vdata['pessoa_system_user_to_string'] = $this->pessoa_system_user_to_string;
    }

    public function get_pessoa_system_user_to_string()
    {
        if(!empty($this->pessoa_system_user_to_string))
        {
            return $this->pessoa_system_user_to_string;
        }
    
        $values = Pessoa::where('categoria_cliente_id', '=', $this->id)->getIndexedArray('system_user_id','{system_user->name}');
        return implode(', ', $values);
    }

    public function set_pessoa_nacionalidade_to_string($pessoa_nacionalidade_to_string)
    {
        if(is_array($pessoa_nacionalidade_to_string))
        {
            $values = Nacionalidade::where('id', 'in', $pessoa_nacionalidade_to_string)->getIndexedArray('descricao', 'descricao');
            $this->pessoa_nacionalidade_to_string = implode(', ', $values);
        }
        else
        {
            $this->pessoa_nacionalidade_to_string = $pessoa_nacionalidade_to_string;
        }

        $this->vdata['pessoa_nacionalidade_to_string'] = $this->pessoa_nacionalidade_to_string;
    }

    public function get_pessoa_nacionalidade_to_string()
    {
        if(!empty($this->pessoa_nacionalidade_to_string))
        {
            return $this->pessoa_nacionalidade_to_string;
        }
    
        $values = Pessoa::where('categoria_cliente_id', '=', $this->id)->getIndexedArray('nacionalidade_id','{nacionalidade->descricao}');
        return implode(', ', $values);
    }

    public function set_prazo_atividade_regras_tipo_atividade_to_string($prazo_atividade_regras_tipo_atividade_to_string)
    {
        if(is_array($prazo_atividade_regras_tipo_atividade_to_string))
        {
            $values = RegrasTipoAtividade::where('id', 'in', $prazo_atividade_regras_tipo_atividade_to_string)->getIndexedArray('id', 'id');
            $this->prazo_atividade_regras_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->prazo_atividade_regras_tipo_atividade_to_string = $prazo_atividade_regras_tipo_atividade_to_string;
        }

        $this->vdata['prazo_atividade_regras_tipo_atividade_to_string'] = $this->prazo_atividade_regras_tipo_atividade_to_string;
    }

    public function get_prazo_atividade_regras_tipo_atividade_to_string()
    {
        if(!empty($this->prazo_atividade_regras_tipo_atividade_to_string))
        {
            return $this->prazo_atividade_regras_tipo_atividade_to_string;
        }
    
        $values = PrazoAtividade::where('categoria_cliente_id', '=', $this->id)->getIndexedArray('regras_tipo_atividade_id','{regras_tipo_atividade->id}');
        return implode(', ', $values);
    }

    public function set_prazo_atividade_categoria_cliente_to_string($prazo_atividade_categoria_cliente_to_string)
    {
        if(is_array($prazo_atividade_categoria_cliente_to_string))
        {
            $values = CategoriaCliente::where('id', 'in', $prazo_atividade_categoria_cliente_to_string)->getIndexedArray('nome', 'nome');
            $this->prazo_atividade_categoria_cliente_to_string = implode(', ', $values);
        }
        else
        {
            $this->prazo_atividade_categoria_cliente_to_string = $prazo_atividade_categoria_cliente_to_string;
        }

        $this->vdata['prazo_atividade_categoria_cliente_to_string'] = $this->prazo_atividade_categoria_cliente_to_string;
    }

    public function get_prazo_atividade_categoria_cliente_to_string()
    {
        if(!empty($this->prazo_atividade_categoria_cliente_to_string))
        {
            return $this->prazo_atividade_categoria_cliente_to_string;
        }
    
        $values = PrazoAtividade::where('categoria_cliente_id', '=', $this->id)->getIndexedArray('categoria_cliente_id','{categoria_cliente->nome}');
        return implode(', ', $values);
    }

    
}

