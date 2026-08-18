<?php

class Representante extends TRecord
{
    const TABLENAME  = 'representante';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private SystemUsers $system_user;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('system_user_id');
        parent::addAttribute('razao_social');
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('inscrestadual');
        parent::addAttribute('telefone');
        parent::addAttribute('ativo');
        parent::addAttribute('email');
        parent::addAttribute('cor');
            
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
        $criteria->add(new TFilter('representante_id', '=', $this->id));
        return Complemento::getObjects( $criteria );
    }
    /**
     * Method getInteracaos
     */
    public function getInteracaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('vendedor_id', '=', $this->id));
        return Interacao::getObjects( $criteria );
    }
    /**
     * Method getComissaoRepress
     */
    public function getComissaoRepress()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('representante_id', '=', $this->id));
        return ComissaoRepres::getObjects( $criteria );
    }
    /**
     * Method getComissaoRepresExcecaos
     */
    public function getComissaoRepresExcecaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('representante_id', '=', $this->id));
        return ComissaoRepresExcecao::getObjects( $criteria );
    }
    /**
     * Method getRepresentanteDivergentes
     */
    public function getRepresentanteDivergentesByRepAps()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('rep_ap_id', '=', $this->id));
        return RepresentanteDivergente::getObjects( $criteria );
    }
    /**
     * Method getRepresentanteDivergentes
     */
    public function getRepresentanteDivergentesByRepTotvss()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('rep_totvs_id', '=', $this->id));
        return RepresentanteDivergente::getObjects( $criteria );
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
    
        $values = Complemento::where('representante_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
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
    
        $values = Complemento::where('representante_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
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
    
        $values = Complemento::where('representante_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
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
    
        $values = Complemento::where('representante_id', '=', $this->id)->getIndexedArray('transportadora_id','{transportadora->id}');
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
    
        $values = Complemento::where('representante_id', '=', $this->id)->getIndexedArray('transportadora1_id','{transportadora1->id}');
        return implode(', ', $values);
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
    
        $values = Interacao::where('vendedor_id', '=', $this->id)->getIndexedArray('tipo_interacao_id','{tipo_interacao->nome}');
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
    
        $values = Interacao::where('vendedor_id', '=', $this->id)->getIndexedArray('cliente_id','{cliente->razao_social}');
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
    
        $values = Interacao::where('vendedor_id', '=', $this->id)->getIndexedArray('vendedor_id','{vendedor->razao_social}');
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
    
        $values = Interacao::where('vendedor_id', '=', $this->id)->getIndexedArray('origem_contato_id','{origem_contato->nome}');
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
    
        $values = Interacao::where('vendedor_id', '=', $this->id)->getIndexedArray('etapa_interacao_id','{etapa_interacao->nome}');
        return implode(', ', $values);
    }

    public function set_comissao_repres_representante_to_string($comissao_repres_representante_to_string)
    {
        if(is_array($comissao_repres_representante_to_string))
        {
            $values = Representante::where('id', 'in', $comissao_repres_representante_to_string)->getIndexedArray('razao_social', 'razao_social');
            $this->comissao_repres_representante_to_string = implode(', ', $values);
        }
        else
        {
            $this->comissao_repres_representante_to_string = $comissao_repres_representante_to_string;
        }

        $this->vdata['comissao_repres_representante_to_string'] = $this->comissao_repres_representante_to_string;
    }

    public function get_comissao_repres_representante_to_string()
    {
        if(!empty($this->comissao_repres_representante_to_string))
        {
            return $this->comissao_repres_representante_to_string;
        }
    
        $values = ComissaoRepres::where('representante_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
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
    
        $values = ComissaoRepresExcecao::where('representante_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
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
    
        $values = ComissaoRepresExcecao::where('representante_id', '=', $this->id)->getIndexedArray('representante_id','{representante->razao_social}');
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
    
        $values = RepresentanteDivergente::where('rep_totvs_id', '=', $this->id)->getIndexedArray('pessoa_id','{pessoa->razao_social}');
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
    
        $values = RepresentanteDivergente::where('rep_totvs_id', '=', $this->id)->getIndexedArray('rep_ap_id','{rep_ap->razao_social}');
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
    
        $values = RepresentanteDivergente::where('rep_totvs_id', '=', $this->id)->getIndexedArray('rep_totvs_id','{rep_totvs->razao_social}');
        return implode(', ', $values);
    }

    
}

