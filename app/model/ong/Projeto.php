<?php
/**
 * Estado Active Record
 * @author  <your-name-here>
 */
class Projeto extends TRecord
{
    const TABLENAME = 'projeto';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);        
        parent::addAttribute('nome', function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null; }, function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null;});
    }


}
