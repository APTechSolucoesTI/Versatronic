<?php

class LogCrontab extends TRecord
{
    const TABLENAME  = 'log_crontab';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATEDAT  = 'data_hora';

    private SystemUnit $system_unit;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('classe');
        parent::addAttribute('metodo');
        parent::addAttribute('data_hora');
        parent::addAttribute('status');
        parent::addAttribute('mensagem');
        parent::addAttribute('observacao');
    
    }

    /**
     * Method set_system_unit
     * Sample of usage: $var->system_unit = $object;
     * @param $object Instance of SystemUnit
     */
    public function set_system_unit(SystemUnit $object)
    {
        $this->system_unit = $object;
        $this->system_unit_id = $object->id;
    }

    /**
     * Method get_system_unit
     * Sample of usage: $var->system_unit->attribute;
     * @returns SystemUnit instance
     */
    public function get_system_unit()
    {
    
        // loads the associated object
        if (empty($this->system_unit))
            $this->system_unit = new SystemUnit($this->system_unit_id);
    
        // returns the associated object
        return $this->system_unit;
    }

    public static function registrarLog($classe, $metodo, $status, $mensagem, $obs, $unit = 1){
        TTransaction::open('minicrm');
    
        $whatsapp = ($status == 1) ? "CRM: Verificar execução de crontab." : "CRM: Crontab concluído";
        //self::enviarAppChat($whatsapp);
        
        $log = new LogCrontab();
        $log->system_unit_id = $unit ?? '';
        $log->classe         = $classe ?? '';
        $log->metodo         = $metodo ?? '';
        $log->mensagem       = $mensagem ?? '';
        $log->observacao     = $obs ?? '';
        $log->status         = $status ?? '';
        $log->store();
    
        TTransaction::close();
    }

    public static function enviarAppChat($mensagem){
    
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0ZW5hbnRJZCI6MSwicHJvZmlsZSI6ImFkbWluIiwic2Vzc2lvbklkIjo0NCwiaWF0IjoxNzQ1NDIwMjA2LCJleHAiOjE4MDg0OTIyMDZ9.P5D2aFKD_OZdPm0-0FTqltyGhyHYPAyY9th2T3yHwhc';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api1.apchat.com.br/v2/api/external/066de006-74e0-4815-8339-03b62bf2987b",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                "body" => $mensagem,
                "number" => "5519993666468",
                "externalKey" => "unique_id",
                "isClosed" => false
            ]),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $token",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
    }
                                                                
}

