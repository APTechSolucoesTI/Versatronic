<?php

class NotaBaixada extends TRecord
{
    const TABLENAME  = 'nota_baixada';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private NotaStatus $nota_status;
    private Coligada $coligada;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('coligada_id');
        parent::addAttribute('nota_status_id');
        parent::addAttribute('numero');
        parent::addAttribute('numero_nf');
        parent::addAttribute('serie_nf');
        parent::addAttribute('data_emissao');
        parent::addAttribute('data_emissao_os');
        parent::addAttribute('razao_social');
        parent::addAttribute('documento');
        parent::addAttribute('valor_total');
        parent::addAttribute('enviado_email');
        parent::addAttribute('totvs_xml');
        parent::addAttribute('rps_xml');
        parent::addAttribute('nfs_xml');
        parent::addAttribute('obs');
        parent::addAttribute('nfs_pdf');
        parent::addAttribute('tem_comissao');
        parent::addAttribute('comissao');
            
    }

    /**
     * Method set_nota_status
     * Sample of usage: $var->nota_status = $object;
     * @param $object Instance of NotaStatus
     */
    public function set_nota_status(NotaStatus $object)
    {
        $this->nota_status = $object;
        $this->nota_status_id = $object->id;
    }

    /**
     * Method get_nota_status
     * Sample of usage: $var->nota_status->attribute;
     * @returns NotaStatus instance
     */
    public function get_nota_status()
    {
    
        // loads the associated object
        if (empty($this->nota_status))
            $this->nota_status = new NotaStatus($this->nota_status_id);
    
        // returns the associated object
        return $this->nota_status;
    }
    /**
     * Method set_coligada
     * Sample of usage: $var->coligada = $object;
     * @param $object Instance of Coligada
     */
    public function set_coligada(Coligada $object)
    {
        $this->coligada = $object;
        $this->coligada_id = $object->id;
    }

    /**
     * Method get_coligada
     * Sample of usage: $var->coligada->attribute;
     * @returns Coligada instance
     */
    public function get_coligada()
    {
    
        // loads the associated object
        if (empty($this->coligada))
            $this->coligada = new Coligada($this->coligada_id);
    
        // returns the associated object
        return $this->coligada;
    }

    /**
     * Method getCentroCustoNotas
     */
    public function getCentroCustoNotas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('nota_baixada_id', '=', $this->id));
        return CentroCustoNota::getObjects( $criteria );
    }
    /**
     * Method getControleNotas
     */
    public function getControleNotas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('nota_baixada_id', '=', $this->id));
        return ControleNota::getObjects( $criteria );
    }

    public function set_centro_custo_nota_nota_baixada_to_string($centro_custo_nota_nota_baixada_to_string)
    {
        if(is_array($centro_custo_nota_nota_baixada_to_string))
        {
            $values = NotaBaixada::where('id', 'in', $centro_custo_nota_nota_baixada_to_string)->getIndexedArray('numero', 'numero');
            $this->centro_custo_nota_nota_baixada_to_string = implode(', ', $values);
        }
        else
        {
            $this->centro_custo_nota_nota_baixada_to_string = $centro_custo_nota_nota_baixada_to_string;
        }

        $this->vdata['centro_custo_nota_nota_baixada_to_string'] = $this->centro_custo_nota_nota_baixada_to_string;
    }

    public function get_centro_custo_nota_nota_baixada_to_string()
    {
        if(!empty($this->centro_custo_nota_nota_baixada_to_string))
        {
            return $this->centro_custo_nota_nota_baixada_to_string;
        }
    
        $values = CentroCustoNota::where('nota_baixada_id', '=', $this->id)->getIndexedArray('nota_baixada_id','{nota_baixada->numero}');
        return implode(', ', $values);
    }

    public function set_centro_custo_nota_centro_custo_to_string($centro_custo_nota_centro_custo_to_string)
    {
        if(is_array($centro_custo_nota_centro_custo_to_string))
        {
            $values = CentroCusto::where('id', 'in', $centro_custo_nota_centro_custo_to_string)->getIndexedArray('nome', 'nome');
            $this->centro_custo_nota_centro_custo_to_string = implode(', ', $values);
        }
        else
        {
            $this->centro_custo_nota_centro_custo_to_string = $centro_custo_nota_centro_custo_to_string;
        }

        $this->vdata['centro_custo_nota_centro_custo_to_string'] = $this->centro_custo_nota_centro_custo_to_string;
    }

    public function get_centro_custo_nota_centro_custo_to_string()
    {
        if(!empty($this->centro_custo_nota_centro_custo_to_string))
        {
            return $this->centro_custo_nota_centro_custo_to_string;
        }
    
        $values = CentroCustoNota::where('nota_baixada_id', '=', $this->id)->getIndexedArray('centro_custo_id','{centro_custo->nome}');
        return implode(', ', $values);
    }

    public function set_controle_nota_nota_baixada_to_string($controle_nota_nota_baixada_to_string)
    {
        if(is_array($controle_nota_nota_baixada_to_string))
        {
            $values = NotaBaixada::where('id', 'in', $controle_nota_nota_baixada_to_string)->getIndexedArray('numero', 'numero');
            $this->controle_nota_nota_baixada_to_string = implode(', ', $values);
        }
        else
        {
            $this->controle_nota_nota_baixada_to_string = $controle_nota_nota_baixada_to_string;
        }

        $this->vdata['controle_nota_nota_baixada_to_string'] = $this->controle_nota_nota_baixada_to_string;
    }

    public function get_controle_nota_nota_baixada_to_string()
    {
        if(!empty($this->controle_nota_nota_baixada_to_string))
        {
            return $this->controle_nota_nota_baixada_to_string;
        }
    
        $values = ControleNota::where('nota_baixada_id', '=', $this->id)->getIndexedArray('nota_baixada_id','{nota_baixada->numero}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(CentroCustoNota::where('nota_baixada_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(ControleNota::where('nota_baixada_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

