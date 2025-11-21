<?php

class Vendedor extends TRecord
{
    const TABLENAME  = 'vendedor';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private SystemUsers $system_user;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_user_id');
        parent::addAttribute('codigo');
        parent::addAttribute('razao_social');
        parent::addAttribute('fantasia');
        parent::addAttribute('cpf');
        parent::addAttribute('cargo');
        parent::addAttribute('telefone');
        parent::addAttribute('codfilial');
        parent::addAttribute('codloc');
        parent::addAttribute('vendecompra');
        parent::addAttribute('codusuario');
        parent::addAttribute('senha');
        parent::addAttribute('ativo');
        parent::addAttribute('pfvendedor');
        parent::addAttribute('pfcaixa');
        parent::addAttribute('pfsupervisor');
        parent::addAttribute('pfgerente');
        parent::addAttribute('descmaximo');
        parent::addAttribute('cor');
        parent::addAttribute('codcoligada');
            
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
     * Method getComplementos
     */
    public function getComplementos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('vendedor_id', '=', $this->id));
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
    
        $values = Complemento::where('vendedor_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
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
    
        $values = Complemento::where('vendedor_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
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
    
        $values = Complemento::where('vendedor_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
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
    
        $values = Complemento::where('vendedor_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->id}');
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
    
        $values = Complemento::where('vendedor_id', '=', $this->id)->getIndexedArray('transportadora1_id','{transportadora1->id}');
        return implode(', ', $values);
    }

    
}

