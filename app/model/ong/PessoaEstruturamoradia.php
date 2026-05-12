<?php
/**
 * PessoaEstruturamoradia Active Record
 * @author  <your-name-here>
 */
class PessoaEstruturamoradia extends TRecord
{
    const TABLENAME = 'pessoa_estruturamoradia';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('pessoa_id');
        parent::addAttribute('estruturamoradia_id');
    }


}
