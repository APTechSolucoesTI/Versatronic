<?php

class NegociacaoService
{
    public static function podeEditar($interacao_id)
    {
        $interacao = new Interacao($interacao_id);
        
        if(EtapaInteracao::where('permite_edicao', '=', 'T')->where('id', '=', $interacao->etapa_interacao_id)->first())
        {
            return true;
        }
        
        return false;
    }
    
    public static function podeExcluir($interacao_id)
    {
        $interacao = new Interacao($interacao_id);
        
        if(EtapaInteracao::where('permite_exclusao', '=', 'T')->where('id', '=', $interacao->etapa_interacao_id)->first())
        {
            return true;
        }
        
        return false;
    }
}
