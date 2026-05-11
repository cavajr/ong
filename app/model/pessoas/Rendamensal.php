<?php
/**
 * Estado Active Record
 * @author  <your-name-here>
 */
class Rendamensal extends TRecord
{
    const TABLENAME = 'rendamensal';
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
