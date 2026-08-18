<?php

class CentroCusto extends TRecord
{
    const TABLENAME  = 'centro_custo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const DELETEDAT  = 'deleted_at';
    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codcusto');
        parent::addAttribute('nome');
        parent::addAttribute('ativo');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
            
    }

    /**
     * Method getCentroCustoNotas
     */
    public function getCentroCustoNotas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('centro_custo_id', '=', $this->id));
        return CentroCustoNota::getObjects( $criteria );
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
    
        $values = CentroCustoNota::where('centro_custo_id', '=', $this->id)->getIndexedArray('nota_baixada_id','{nota_baixada->numero}');
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
    
        $values = CentroCustoNota::where('centro_custo_id', '=', $this->id)->getIndexedArray('centro_custo_id','{centro_custo->nome}');
        return implode(', ', $values);
    }

    /**
     * Method onBeforeDelete
     */
    public function onBeforeDelete()
    {
            

        if(CentroCustoNota::where('centro_custo_id', '=', $this->id)->first())
        {
            throw new Exception("Não é possível deletar este registro pois ele está sendo utilizado em outra parte do sistema");
        }
    
    }

    
}

