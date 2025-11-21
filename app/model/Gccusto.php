<?php

class Gccusto extends TRecord
{
    const TABLENAME  = 'GCCUSTO';
    const PRIMARYKEY = 'ID';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('USE');
        parent::addAttribute('CODCCUSTO');
        parent::addAttribute('NOME');
        parent::addAttribute('CODCOLCONTAGER');
        parent::addAttribute('CODCONTAGER');
        parent::addAttribute('CODCOLCONTA');
        parent::addAttribute('CODCONTA');
        parent::addAttribute('CODREDUZIDO');
        parent::addAttribute('CAMPOLIVRE');
        parent::addAttribute('ATIVO');
        parent::addAttribute('PERMITELANC');
        parent::addAttribute('CODCLASSIFICA');
        parent::addAttribute('ENVIASPED');
        parent::addAttribute('DATAINCLUSAO');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
        parent::addAttribute('RESPONSAVEL');
            
    }

    
}

