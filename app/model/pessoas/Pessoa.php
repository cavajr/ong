<?php
/**
 * Pessoa Active Record
 * @author  <your-name-here>
 */
class Pessoa extends TRecord
{
    const TABLENAME = 'pessoa';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}
    
    const CREATEDAT = 'created_at';
    const UPDATEDAT = 'updated_at';
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('cpf');
        parent::addAttribute('nome');
        parent::addAttribute('tipo_id');
        parent::addAttribute('rg');
        parent::addAttribute('nis');
        parent::addAttribute('idade');
        parent::addAttribute('estado_civil_id');
        parent::addAttribute('sexo_id');
        parent::addAttribute('escolariedade_id');
        parent::addAttribute('projeto_id');
        parent::addAttribute('profissao');
        parent::addAttribute('indicado_por');
        parent::addAttribute('fonte_renda');
        parent::addAttribute('renda_mensal');
        parent::addAttribute('tipo_moradia');
        parent::addAttribute('moradores');
        parent::addAttribute('tipo_atendimento');
        parent::addAttribute('estrutura_moradia');
        parent::addAttribute('fone1');
        parent::addAttribute('fone2');
        parent::addAttribute('email');
        parent::addAttribute('cep');
        parent::addAttribute('endereco');
        parent::addAttribute('numero');        
        parent::addAttribute('bairro');
        parent::addAttribute('cidade_id');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');        
    }       
    
    public function get_cidade()
    {
        return Cidade::find($this->cidade_id);
    }       
    
 
    
}
