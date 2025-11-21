<?php

class Ttra extends TRecord
{
    const TABLENAME  = 'TTRA';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('USE');
        parent::addAttribute('CODTRA');
        parent::addAttribute('NOME');
        parent::addAttribute('RUA');
        parent::addAttribute('NUMERO');
        parent::addAttribute('COMPLEMENTO');
        parent::addAttribute('BAIRRO');
        parent::addAttribute('CIDADE');
        parent::addAttribute('CODETD');
        parent::addAttribute('CEP');
        parent::addAttribute('CGC');
        parent::addAttribute('INSCRESTADUAL');
        parent::addAttribute('CONTATO');
        parent::addAttribute('TELEFONE');
        parent::addAttribute('TELEX');
        parent::addAttribute('FAX');
        parent::addAttribute('LIVRE');
        parent::addAttribute('NOMEFANTASIA');
        parent::addAttribute('PAIS');
        parent::addAttribute('CEI');
        parent::addAttribute('INSCRMUNICIPAL');
        parent::addAttribute('INATIVO');
        parent::addAttribute('EMAIL');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
        parent::addAttribute('CODMUNICIPIO');
            
    }

    
}

