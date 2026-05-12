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
    
    
    private $estado_civil;
    private $tipo;
    private $projeto;
    private $cidade;
    private $sexo;
    private $escolariedade;
    private $fonterenda;
    private $rendamensal;
    private $moradia;
    private $estruturamoradias;
    private $tipoatendimentos;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_id');
        parent::addAttribute('cpf');
        parent::addAttribute('nome', function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null; }, function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null;});
        parent::addAttribute('rg');
        parent::addAttribute('nis');
        parent::addAttribute('endereco', function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null; }, function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null;});
        parent::addAttribute('numero');
        parent::addAttribute('bairro', function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null; }, function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null;});
        parent::addAttribute('cidade_id');
        parent::addAttribute('estado_id');
        parent::addAttribute('idade');
        parent::addAttribute('estado_civil_id');
        parent::addAttribute('sexo_id');
        parent::addAttribute('escolariedade_id');
        parent::addAttribute('profissao', function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null; }, function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null;});
        parent::addAttribute('fone1');
        parent::addAttribute('fone2');
        parent::addAttribute('email');
        parent::addAttribute('projeto_id');
        parent::addAttribute('indicado_por', function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null; }, function($value) { return !empty($value) ? mb_strtoupper($value, 'UTF-8') : null;});
        parent::addAttribute('fonterenda_id');
        parent::addAttribute('rendamensal_id');
        parent::addAttribute('moradia_id');
        parent::addAttribute('moradores');
        parent::addAttribute('cep');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
    }

     
    /**
     * Method get_estado_civil
     * Sample of usage: $pessoa->estado_civil->attribute;
     * @returns EstadoCivil instance
     */
    public function get_estado_civil()
    {
        // loads the associated object
        if (empty($this->estado_civil))
            $this->estado_civil = new EstadoCivil($this->estado_civil_id);
    
        // returns the associated object
        return $this->estado_civil;
    }
    
      
    /**
     * Method get_tipo
     * Sample of usage: $pessoa->tipo->attribute;
     * @returns Tipo instance
     */
    public function get_tipo()
    {
        // loads the associated object
        if (empty($this->tipo))
            $this->tipo = new Tipo($this->tipo_id);
    
        // returns the associated object
        return $this->tipo;
    }
    
       
    /**
     * Method get_projeto
     * Sample of usage: $pessoa->projeto->attribute;
     * @returns Projeto instance
     */
    public function get_projeto()
    {
        // loads the associated object
        if (empty($this->projeto))
            $this->projeto = new Projeto($this->projeto_id);
    
        // returns the associated object
        return $this->projeto;
    }
    
      
    
    /**
     * Method get_cidade
     * Sample of usage: $pessoa->cidade->attribute;
     * @returns Cidade instance
     */
    public function get_cidade()
    {
        // loads the associated object
        if (empty($this->cidade))
            $this->cidade = new Cidade($this->cidade_id);
    
        // returns the associated object
        return $this->cidade;
    }
    
       
    /**
     * Method get_sexo
     * Sample of usage: $pessoa->sexo->attribute;
     * @returns Sexo instance
     */
    public function get_sexo()
    {
        // loads the associated object
        if (empty($this->sexo))
            $this->sexo = new Sexo($this->sexo_id);
    
        // returns the associated object
        return $this->sexo;
    }
    
      
    
    /**
     * Method get_escolariedade
     * Sample of usage: $pessoa->escolariedade->attribute;
     * @returns Escolariedade instance
     */
    public function get_escolariedade()
    {
        // loads the associated object
        if (empty($this->escolariedade))
            $this->escolariedade = new Escolariedade($this->escolariedade_id);
    
        // returns the associated object
        return $this->escolariedade;
    }
       
    
    /**
     * Method get_fonterenda
     * Sample of usage: $pessoa->fonterenda->attribute;
     * @returns Fonterenda instance
     */
    public function get_fonterenda()
    {
        // loads the associated object
        if (empty($this->fonterenda))
            $this->fonterenda = new Fonterenda($this->fonterenda_id);
    
        // returns the associated object
        return $this->fonterenda;
    }
    
       
    
    /**
     * Method get_rendamensal
     * Sample of usage: $pessoa->rendamensal->attribute;
     * @returns Rendamensal instance
     */
    public function get_rendamensal()
    {
        // loads the associated object
        if (empty($this->rendamensal))
            $this->rendamensal = new Rendamensal($this->rendamensal_id);
    
        // returns the associated object
        return $this->rendamensal;
    }
    
       
    /**
     * Method get_moradia
     * Sample of usage: $pessoa->moradia->attribute;
     * @returns Moradia instance
     */
    public function get_moradia()
    {
        // loads the associated object
        if (empty($this->moradia))
            $this->moradia = new Moradia($this->moradia_id);
    
        // returns the associated object
        return $this->moradia;
    }
    
    
    /**
     * Method addEstruturamoradia
     * Add a Estruturamoradia to the Pessoa
     * @param $object Instance of Estruturamoradia
     */
    public function addEstruturamoradia(Estruturamoradia $object)
    {
        $this->estruturamoradias[] = $object;
    }
    
    /**
     * Method getEstruturamoradias
     * Return the Pessoa' Estruturamoradia's
     * @return Collection of Estruturamoradia
     */
    public function getEstruturamoradias()
    {
        return $this->estruturamoradias;
    }
    
    /**
     * Method addTipoatendimento
     * Add a Tipoatendimento to the Pessoa
     * @param $object Instance of Tipoatendimento
     */
    public function addTipoatendimento(Tipoatendimento $object)
    {
        $this->tipoatendimentos[] = $object;
    }
    
    /**
     * Method getTipoatendimentos
     * Return the Pessoa' Tipoatendimento's
     * @return Collection of Tipoatendimento
     */
    public function getTipoatendimentos()
    {
        return $this->tipoatendimentos;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->estruturamoradias = array();
        $this->tipoatendimentos = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->estruturamoradias = parent::loadAggregate('Estruturamoradia', 'PessoaEstruturamoradia', 'pessoa_id', 'estruturamoradia_id', $id);
        $this->tipoatendimentos = parent::loadAggregate('Tipoatendimento', 'PessoaTipoatendimento', 'pessoa_id', 'tipoatendimento_id', $id);
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        parent::saveAggregate('PessoaEstruturamoradia', 'pessoa_id', 'estruturamoradia_id', $this->id, $this->estruturamoradias);
        parent::saveAggregate('PessoaTipoatendimento', 'pessoa_id', 'tipoatendimento_id', $this->id, $this->tipoatendimentos);
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        parent::deleteComposite('PessoaEstruturamoradia', 'pessoa_id', $id);
        parent::deleteComposite('PessoaTipoatendimento', 'pessoa_id', $id);
    
        // delete the object itself
        parent::delete($id);
    }


}
