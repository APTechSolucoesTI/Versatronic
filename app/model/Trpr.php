<?php

class Trpr extends TRecord
{
    const TABLENAME  = 'TRPR';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODCOLIGADA');
        parent::addAttribute('CODRPR');
        parent::addAttribute('NOME');
        parent::addAttribute('SIGLA');
        parent::addAttribute('NOMEFANTASIA');
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
        parent::addAttribute('FAX');
        parent::addAttribute('RUAPGTO');
        parent::addAttribute('NUMEROPGTO');
        parent::addAttribute('COMPLEMENTOPGTO');
        parent::addAttribute('BAIRROPGTO');
        parent::addAttribute('CIDADEPGTO');
        parent::addAttribute('CODETDPGTO');
        parent::addAttribute('CEPPGTO');
        parent::addAttribute('PERCENTCOMISSAO');
        parent::addAttribute('FATCLIENTEDIRETO');
        parent::addAttribute('DIAFATURAMENTO');
        parent::addAttribute('PERCENTREPASSE');
        parent::addAttribute('CODTB1FLX');
        parent::addAttribute('CODTB2FLX');
        parent::addAttribute('CODTB3FLX');
        parent::addAttribute('CODTB4FLX');
        parent::addAttribute('CODTB5FLX');
        parent::addAttribute('CLCONTABIL');
        parent::addAttribute('INATIVO');
        parent::addAttribute('HOMEPAGE');
        parent::addAttribute('EMAIL');
        parent::addAttribute('CELULAR');
        parent::addAttribute('PAIS');
        parent::addAttribute('CODUSUARIO');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
            
    }

    
}

