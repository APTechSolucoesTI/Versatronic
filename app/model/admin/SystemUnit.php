<?php

class SystemUnit extends TRecord
{
    const TABLENAME  = 'system_unit';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'max'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('name');
        parent::addAttribute('connection_name');
            
    }

    /**
     * Method getLogCrontabs
     */
    public function getLogCrontabs()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('system_unit_id', '=', $this->id));
        return LogCrontab::getObjects( $criteria );
    }

    public function set_log_crontab_system_unit_to_string($log_crontab_system_unit_to_string)
    {
        if(is_array($log_crontab_system_unit_to_string))
        {
            $values = SystemUnit::where('id', 'in', $log_crontab_system_unit_to_string)->getIndexedArray('name', 'name');
            $this->log_crontab_system_unit_to_string = implode(', ', $values);
        }
        else
        {
            $this->log_crontab_system_unit_to_string = $log_crontab_system_unit_to_string;
        }

        $this->vdata['log_crontab_system_unit_to_string'] = $this->log_crontab_system_unit_to_string;
    }

    public function get_log_crontab_system_unit_to_string()
    {
        if(!empty($this->log_crontab_system_unit_to_string))
        {
            return $this->log_crontab_system_unit_to_string;
        }
    
        $values = LogCrontab::where('system_unit_id', '=', $this->id)->getIndexedArray('system_unit_id','{system_unit->name}');
        return implode(', ', $values);
    }

    
}

