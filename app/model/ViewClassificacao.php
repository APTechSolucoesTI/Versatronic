<?php

class ViewClassificacao extends TRecord
{
    const TABLENAME  = 'view_classificacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('categoria');
        parent::addAttribute('codigo_cliente');
        parent::addAttribute('nome_cliente');
        parent::addAttribute('cidade');
        parent::addAttribute('uf');
        parent::addAttribute('tipo1');
        parent::addAttribute('tipo2');
        parent::addAttribute('representante');
        parent::addAttribute('s_id');
        parent::addAttribute('dentro_prazo_tipo1');
        parent::addAttribute('dentro_prazo_tipo2');
        parent::addAttribute('dentro_prazo_ambos');
            
    }

    
}

