<?php
/**
 * Estado Active Record
 * @author  <your-name-here>
 */
class Moradia extends TRecord
{
    const TABLENAME = 'moradia';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);        
        parent::addAttribute('nome');
    }


}
