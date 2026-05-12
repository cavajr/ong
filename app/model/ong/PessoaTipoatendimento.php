<?php
/**
 * PessoaTipoatendimento Active Record
 * @author  <your-name-here>
 */
class PessoaTipoatendimento extends TRecord
{
    const TABLENAME = 'pessoa_tipoatendimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('tipoatendimento_id');
    }


}
