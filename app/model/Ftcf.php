<?php

class Ftcf extends TRecord
{
    const TABLENAME  = 'FTCF';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODCOLIGADA');
        parent::addAttribute('CODTCF');
        parent::addAttribute('DESCRICAO');
        parent::addAttribute('CAMPOLIVRE');
        parent::addAttribute('CODSEGM');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
            
    }

    
}

