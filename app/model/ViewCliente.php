<?php

class ViewCliente extends TRecord
{
    const TABLENAME  = 'view_cliente';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('codigo');
        parent::addAttribute('categoria');
        parent::addAttribute('razao_social');
        parent::addAttribute('cpf_cnpj');
        parent::addAttribute('ativo');
        parent::addAttribute('data_alteracao_totvs');
        parent::addAttribute('cidade');
        parent::addAttribute('estado');
        parent::addAttribute('uf');
        parent::addAttribute('representante_id');
        parent::addAttribute('representante_razao');
        parent::addAttribute('bloqueado');
        parent::addAttribute('cidade_uf');
        parent::addAttribute('cidade_id');
        parent::addAttribute('estado_id');
    
    }

    public function get_repres_user(){
        if (!empty($this->representante_id)){
            $pessoa = Representante::find((int)$id);
            if($pessoa)
                return $pessoa->system_users_id;
        }
        return null;
    }
                    
}

