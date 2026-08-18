<?php

class ViewComissaoRepres extends TRecord
{
    const TABLENAME  = 'view_comissao_repres';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('coligada_id');
        parent::addAttribute('data_emissao');
        parent::addAttribute('data_emissao_os');
        parent::addAttribute('codigo_cliente');
        parent::addAttribute('fantasia');
        parent::addAttribute('categoria_cliente');
        parent::addAttribute('numero_nota');
        parent::addAttribute('valor');
        parent::addAttribute('tem_comissao');
        parent::addAttribute('comissao');
        parent::addAttribute('representante');
            
    }

    
}

