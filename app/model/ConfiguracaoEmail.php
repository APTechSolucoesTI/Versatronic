<?php

class ConfiguracaoEmail extends TRecord
{
    const TABLENAME  = 'configuracao_email';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private SystemUsers $system_users;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('system_users_id');
        parent::addAttribute('mail_from');
        parent::addAttribute('smtp_auth');
        parent::addAttribute('smtp_host');
        parent::addAttribute('smtp_port');
        parent::addAttribute('smtp_user');
        parent::addAttribute('smtp_pass');
            
    }

    /**
     * Method set_system_users
     * Sample of usage: $var->system_users = $object;
     * @param $object Instance of SystemUsers
     */
    public function set_system_users(SystemUsers $object)
    {
        $this->system_users = $object;
        $this->system_users_id = $object->id;
    }

    /**
     * Method get_system_users
     * Sample of usage: $var->system_users->attribute;
     * @returns SystemUsers instance
     */
    public function get_system_users()
    {
    
        // loads the associated object
        if (empty($this->system_users))
            $this->system_users = new SystemUsers($this->system_users_id);
    
        // returns the associated object
        return $this->system_users;
    }

    
}

