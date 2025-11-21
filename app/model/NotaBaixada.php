<?php

class NotaBaixada extends TRecord
{
    const TABLENAME  = 'nota_baixada';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private NotaStatus $nota_status;
    private Coligada $coligada;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('coligada_id');
        parent::addAttribute('nota_status_id');
        parent::addAttribute('numero');
        parent::addAttribute('numero_nf');
        parent::addAttribute('serie_nf');
        parent::addAttribute('data_emissao');
        parent::addAttribute('razao_social');
        parent::addAttribute('documento');
        parent::addAttribute('valor_total');
        parent::addAttribute('enviado_email');
        parent::addAttribute('totvs_xml');
        parent::addAttribute('rps_xml');
        parent::addAttribute('nfs_xml');
        parent::addAttribute('nfs_pdf');
        parent::addAttribute('obs');
            
    }

    /**
     * Method set_nota_status
     * Sample of usage: $var->nota_status = $object;
     * @param $object Instance of NotaStatus
     */
    public function set_nota_status(NotaStatus $object)
    {
        $this->nota_status = $object;
        $this->nota_status_id = $object->id;
    }

    /**
     * Method get_nota_status
     * Sample of usage: $var->nota_status->attribute;
     * @returns NotaStatus instance
     */
    public function get_nota_status()
    {
    
        // loads the associated object
        if (empty($this->nota_status))
            $this->nota_status = new NotaStatus($this->nota_status_id);
    
        // returns the associated object
        return $this->nota_status;
    }
    /**
     * Method set_coligada
     * Sample of usage: $var->coligada = $object;
     * @param $object Instance of Coligada
     */
    public function set_coligada(Coligada $object)
    {
        $this->coligada = $object;
        $this->coligada_id = $object->id;
    }

    /**
     * Method get_coligada
     * Sample of usage: $var->coligada->attribute;
     * @returns Coligada instance
     */
    public function get_coligada()
    {
    
        // loads the associated object
        if (empty($this->coligada))
            $this->coligada = new Coligada($this->coligada_id);
    
        // returns the associated object
        return $this->coligada;
    }

    
}

