<?php

class OrcamentosComercio extends TRecord
{
    const TABLENAME  = 'orcamentos_comercio';
    const PRIMARYKEY = 'row_num';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('coligada');
        parent::addAttribute('orcamento');
        parent::addAttribute('data_emissao');
        parent::addAttribute('cod_cliente');
        parent::addAttribute('cliente');
        parent::addAttribute('dias');
        parent::addAttribute('id_objeto');
        parent::addAttribute('objeto');
        parent::addAttribute('centro_custo');
        parent::addAttribute('os');
        parent::addAttribute('tecnico');
        parent::addAttribute('status');
        parent::addAttribute('cor_status');
        parent::addAttribute('intervalo');
        parent::addAttribute('qtde');
            
    }

    
}

