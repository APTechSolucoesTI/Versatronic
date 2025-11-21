<?php

class Tcpg extends TRecord
{
    const TABLENAME  = 'TCPG';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CODCOLIGADA');
        parent::addAttribute('CODCPG');
        parent::addAttribute('NOME');
        parent::addAttribute('ARREDPRIMOUULT');
        parent::addAttribute('ARREDDEZOUCENT');
        parent::addAttribute('NUMVETCONDICOES');
        parent::addAttribute('VALORPAGAMENTO1');
        parent::addAttribute('QUANTASVEZES1');
        parent::addAttribute('PERIODOEMDIAS1');
        parent::addAttribute('PRAZO1');
        parent::addAttribute('CONTAGEMDIAS1');
        parent::addAttribute('TIPO1');
        parent::addAttribute('VALORPAGAMENTO2');
        parent::addAttribute('QUANTASVEZES2');
        parent::addAttribute('PERIODOEMDIAS2');
        parent::addAttribute('PRAZO2');
        parent::addAttribute('CONTAGEMDIAS2');
        parent::addAttribute('TIPO2');
        parent::addAttribute('VALORPAGAMENTO3');
        parent::addAttribute('QUANTASVEZES3');
        parent::addAttribute('PERIODOEMDIAS3');
        parent::addAttribute('PRAZO3');
        parent::addAttribute('CONTAGEMDIAS3');
        parent::addAttribute('TIPO3');
        parent::addAttribute('VALORPAGAMENTO4');
        parent::addAttribute('QUANTASVEZES4');
        parent::addAttribute('PERIODOEMDIAS4');
        parent::addAttribute('PRAZO4');
        parent::addAttribute('CONTAGEMDIAS4');
        parent::addAttribute('TIPO4');
        parent::addAttribute('VALORPAGAMENTO5');
        parent::addAttribute('QUANTASVEZES5');
        parent::addAttribute('PERIODOEMDIAS5');
        parent::addAttribute('PRAZO5');
        parent::addAttribute('CONTAGEMDIAS5');
        parent::addAttribute('TIPO5');
        parent::addAttribute('DEFLATOR');
        parent::addAttribute('APLICACAOFRM');
        parent::addAttribute('CODFRMPRECO1');
        parent::addAttribute('CODFRMPRECO2');
        parent::addAttribute('DIACARENCIA');
        parent::addAttribute('TAXAJUROS');
        parent::addAttribute('JUROSCOMPOSTO');
        parent::addAttribute('PLANOPAGTO');
        parent::addAttribute('CAPITALIZMENSAL');
        parent::addAttribute('PLANOCOMPRA');
        parent::addAttribute('PLANOVENDA');
        parent::addAttribute('DIASVENCSEMANA');
        parent::addAttribute('CODFRMPRIMPARCELA');
        parent::addAttribute('INATIVO');
        parent::addAttribute('RECCREATEDBY');
        parent::addAttribute('RECCREATEDON');
        parent::addAttribute('RECMODIFIEDBY');
        parent::addAttribute('RECMODIFIEDON');
        parent::addAttribute('IDFORMAPAGTO');
            
    }

    
}

