<?php

class Tven extends TRecord
{
    const TABLENAME  = 'TVEN';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODCOLIGADA');
        parent::addAttribute('CODVEN');
        parent::addAttribute('NOME');
        parent::addAttribute('CARGO');
        parent::addAttribute('CODFILIAL');
        parent::addAttribute('CODLOC');
        parent::addAttribute('COMISSAO1');
        parent::addAttribute('COMISSAO2');
        parent::addAttribute('COMISSAO3');
        parent::addAttribute('CODPESSOA');
        parent::addAttribute('VENDECOMPRA');
        parent::addAttribute('CODUSUARIO');
        parent::addAttribute('SENHA');
        parent::addAttribute('INATIVO');
        parent::addAttribute('PFVENDEDOR');
        parent::addAttribute('PFCAIXA');
        parent::addAttribute('PFSUPERVISOR');
        parent::addAttribute('PFGERENTE');
        parent::addAttribute('IDFUNCIONARIO');
        parent::addAttribute('COMISSAO4');
        parent::addAttribute('DESCMAXIMO');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
            
    }

    
}

