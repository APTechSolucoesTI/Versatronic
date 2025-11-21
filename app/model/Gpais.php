<?php

class Gpais extends TRecord
{
    const TABLENAME  = 'GPAIS';
    const PRIMARYKEY = 'IDPAIS';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODPAIS');
        parent::addAttribute('DESCRICAO');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
            
    }

    
}

