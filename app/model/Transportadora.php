<?php

class Transportadora extends TRecord
{
    const TABLENAME  = 'transportadora';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Cidade $cidade;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('codtra');
        parent::addAttribute('nome');
        parent::addAttribute('rua');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade_id');
        parent::addAttribute('cep');
        parent::addAttribute('cgc');
        parent::addAttribute('inscrestadual');
        parent::addAttribute('contato');
        parent::addAttribute('telefone');
        parent::addAttribute('telex');
        parent::addAttribute('fax');
        parent::addAttribute('livre');
        parent::addAttribute('nomefantasia');
        parent::addAttribute('cei');
        parent::addAttribute('inscrmunicipal');
        parent::addAttribute('ativo');
        parent::addAttribute('email');
            
    }

    /**
     * Method set_cidade
     * Sample of usage: $var->cidade = $object;
     * @param $object Instance of Cidade
     */
    public function set_cidade(Cidade $object)
    {
        $this->cidade = $object;
        $this->cidade_id = $object->id;
    }

    /**
     * Method get_cidade
     * Sample of usage: $var->cidade->attribute;
     * @returns Cidade instance
     */
    public function get_cidade()
    {
    
        // loads the associated object
        if (empty($this->cidade))
            $this->cidade = new Cidade($this->cidade_id);
    
        // returns the associated object
        return $this->cidade;
    }

    /**
     * Method getComplementos
     */
    public function getComplementosByTransportadoras()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('transportadora_id', '=', $this->id));
        return Complemento::getObjects( $criteria );
    }
    /**
     * Method getComplementos
     */
    public function getComplementosByTransportadora1s()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('transportadora1_id', '=', $this->id));
        return Complemento::getObjects( $criteria );
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
    
        $values = Complemento::where('transportadora1_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
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
    
        $values = Complemento::where('transportadora1_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
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
    
        $values = Complemento::where('transportadora1_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
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
    
        $values = Complemento::where('transportadora1_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->id}');
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
    
        $values = Complemento::where('transportadora1_id', '=', $this->id)->getIndexedArray('transportadora1_id','{transportadora1->id}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(Complemento::where('transportadora_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(Complemento::where('transportadora1_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

