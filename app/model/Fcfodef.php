<?php

class Fcfodef extends TRecord
{
    const TABLENAME  = 'FCFODEF';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODCOLIGADA');
        parent::addAttribute('CODCFO');
        parent::addAttribute('CODTB1FLX');
        parent::addAttribute('CODTB2FLX');
        parent::addAttribute('CODTB3FLX');
        parent::addAttribute('CODTB4FLX');
        parent::addAttribute('CODTB5FLX');
        parent::addAttribute('CODDEPARTAMENTO');
        parent::addAttribute('CODCCUSTO');
        parent::addAttribute('CODFILIAL');
        parent::addAttribute('CODCOLCFO');
        parent::addAttribute('CODBANCOCOBRANCA');
        parent::addAttribute('CNABCARTEIRA');
        parent::addAttribute('CODCPG');
        parent::addAttribute('CODVEN');
        parent::addAttribute('PERCENTUALDESC');
        parent::addAttribute('CODRPR');
        parent::addAttribute('CODTDO');
        parent::addAttribute('CODCOLCXA');
        parent::addAttribute('CODCXA');
        parent::addAttribute('CODTRA');
        parent::addAttribute('TIPOCONTABILLAN');
        parent::addAttribute('CODCPGVENDA');
        parent::addAttribute('CIFFOB');
        parent::addAttribute('CODTRA2');
        parent::addAttribute('DIASVENCSEMANA');
        parent::addAttribute('PERCDESCCOMPRA');
        parent::addAttribute('CODIGOINSS');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
        parent::addAttribute('IDITEMCONTABIL');
        parent::addAttribute('IDCONVENIO');
            
    }

    
}

