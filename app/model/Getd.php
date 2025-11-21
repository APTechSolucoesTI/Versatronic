<?php

class Getd extends TRecord
{
    const TABLENAME  = 'GETD';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODETD');
        parent::addAttribute('NOME');
        parent::addAttribute('NACIONAL');
        parent::addAttribute('CODIGOSINIEF');
        parent::addAttribute('IDPAIS');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
            
    }

    
}

