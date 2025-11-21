<?php

class ViewClienteCidade extends TRecord
{
    const TABLENAME  = 'view_cliente_cidade';
    const PRIMARYKEY = 'interacao_id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('representante_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('tipo_pessoa_id');
        parent::addAttribute('cliente_codigo');
        parent::addAttribute('cliente_razao_social');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('cidade_uf');
        parent::addAttribute('representante');
            
    }

    
}

