<?php

class Coligada extends TRecord
{
    const TABLENAME  = 'coligada';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const VERSATRONIC = 1;
    const CNC = 2;
    const REFORMA = 3;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cnpj');
        parent::addAttribute('nome');
        parent::addAttribute('senha');
    
    }

    /**
     * Method getNotaBaixadaTestes
     */
    public function getNotaBaixadaTestes()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('coligada_id', '=', $this->id));
        return NotaBaixadaTeste::getObjects( $criteria );
    }
    /**
     * Method getNotaBaixadas
     */
    public function getNotaBaixadas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('coligada_id', '=', $this->id));
        return NotaBaixada::getObjects( $criteria );
    }

    public function set_nota_baixada_teste_nota_status_to_string($nota_baixada_teste_nota_status_to_string)
    {
        if(is_array($nota_baixada_teste_nota_status_to_string))
        {
            $values = NotaStatus::where('id', 'in', $nota_baixada_teste_nota_status_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_baixada_teste_nota_status_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_baixada_teste_nota_status_to_string = $nota_baixada_teste_nota_status_to_string;
        }

        $this->vdata['nota_baixada_teste_nota_status_to_string'] = $this->nota_baixada_teste_nota_status_to_string;
    }

    public function get_nota_baixada_teste_nota_status_to_string()
    {
        if(!empty($this->nota_baixada_teste_nota_status_to_string))
        {
            return $this->nota_baixada_teste_nota_status_to_string;
        }
    
        $values = NotaBaixadaTeste::where('coligada_id', '=', $this->id)->getIndexedArray('nota_status_id','{nota_status->nome}');
        return implode(', ', $values);
    }

    public function set_nota_baixada_teste_coligada_to_string($nota_baixada_teste_coligada_to_string)
    {
        if(is_array($nota_baixada_teste_coligada_to_string))
        {
            $values = Coligada::where('id', 'in', $nota_baixada_teste_coligada_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_baixada_teste_coligada_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_baixada_teste_coligada_to_string = $nota_baixada_teste_coligada_to_string;
        }

        $this->vdata['nota_baixada_teste_coligada_to_string'] = $this->nota_baixada_teste_coligada_to_string;
    }

    public function get_nota_baixada_teste_coligada_to_string()
    {
        if(!empty($this->nota_baixada_teste_coligada_to_string))
        {
            return $this->nota_baixada_teste_coligada_to_string;
        }
    
        $values = NotaBaixadaTeste::where('coligada_id', '=', $this->id)->getIndexedArray('coligada_id','{coligada->nome}');
        return implode(', ', $values);
    }

    public function set_nota_baixada_coligada_to_string($nota_baixada_coligada_to_string)
    {
        if(is_array($nota_baixada_coligada_to_string))
        {
            $values = Coligada::where('id', 'in', $nota_baixada_coligada_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_baixada_coligada_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_baixada_coligada_to_string = $nota_baixada_coligada_to_string;
        }

        $this->vdata['nota_baixada_coligada_to_string'] = $this->nota_baixada_coligada_to_string;
    }

    public function get_nota_baixada_coligada_to_string()
    {
        if(!empty($this->nota_baixada_coligada_to_string))
        {
            return $this->nota_baixada_coligada_to_string;
        }
    
        $values = NotaBaixada::where('coligada_id', '=', $this->id)->getIndexedArray('coligada_id','{coligada->nome}');
        return implode(', ', $values);
    }

    public function set_nota_baixada_nota_status_to_string($nota_baixada_nota_status_to_string)
    {
        if(is_array($nota_baixada_nota_status_to_string))
        {
            $values = NotaStatus::where('id', 'in', $nota_baixada_nota_status_to_string)->getIndexedArray('nome', 'nome');
            $this->nota_baixada_nota_status_to_string = implode(', ', $values);
        }
        else
        {
            $this->nota_baixada_nota_status_to_string = $nota_baixada_nota_status_to_string;
        }

        $this->vdata['nota_baixada_nota_status_to_string'] = $this->nota_baixada_nota_status_to_string;
    }

    public function get_nota_baixada_nota_status_to_string()
    {
        if(!empty($this->nota_baixada_nota_status_to_string))
        {
            return $this->nota_baixada_nota_status_to_string;
        }
    
        $values = NotaBaixada::where('coligada_id', '=', $this->id)->getIndexedArray('nota_status_id','{nota_status->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
    

        if(NotaBaixadaTeste::where('coligada_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
        if(NotaBaixada::where('coligada_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

}

