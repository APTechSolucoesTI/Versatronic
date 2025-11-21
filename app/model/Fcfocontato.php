<?php

class Fcfocontato extends TRecord
{
    const TABLENAME  = 'FCFOCONTATO';
    const PRIMARYKEY = 'IDINTEGRACAO';
    const IDPOLICY   =  'serial'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('IDCONTATO');
        parent::addAttribute('CODCOLIGADA');
        parent::addAttribute('CODCFO');
        parent::addAttribute('NOME');
        parent::addAttribute('EMAIL');
        parent::addAttribute('TELEFONE');
        parent::addAttribute('RAMAL');
        parent::addAttribute('FAX');
        parent::addAttribute('FUNCAO');
        parent::addAttribute('OBSERVACAO');
        parent::addAttribute('CODUSUARIO');
        parent::addAttribute('RUA');
        parent::addAttribute('NUMERO');
        parent::addAttribute('COMPLEMENTO');
        parent::addAttribute('BAIRRO');
        parent::addAttribute('CIDADE');
        parent::addAttribute('CODETD');
        parent::addAttribute('CEP');
        parent::addAttribute('PAIS');
        parent::addAttribute('USUARIOALTERACAO');
        parent::addAttribute('DATAALTERACAO');
        parent::addAttribute('ATIVO');
        parent::addAttribute('DATANASCIMENTO');
        parent::addAttribute('DEFAUTPARAEMAIL');
        parent::addAttribute('CODAPLIC');
        parent::addAttribute('CODMUNICIPIO');
        parent::addAttribute('LOCALIDADE');
        parent::addAttribute('CODUSUARIOACESSO');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
    
    }

}

