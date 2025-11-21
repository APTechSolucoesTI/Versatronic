<?php

class RepresentanteTotvs extends TRecord
{
    const TABLENAME  = 'representante_totvs';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('razao_social');
        parent::addAttribute('fantasia');
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('inscrestadual');
        parent::addAttribute('cep');
        parent::addAttribute('rua');
        parent::addAttribute('numero');
        parent::addAttribute('complemento');
        parent::addAttribute('bairro');
        parent::addAttribute('cidade_id');
        parent::addAttribute('contato');
        parent::addAttribute('telefone');
        parent::addAttribute('pais_id');
        parent::addAttribute('percentual_comissao');
        parent::addAttribute('fatclientedireto');
        parent::addAttribute('ativo');
        parent::addAttribute('celular');
        parent::addAttribute('email');
        parent::addAttribute('codcoligada');
        parent::addAttribute('cor');
            
    }

    
}

