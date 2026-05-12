<?php
/**
 * PessoaForm
 *
 * @version    1.0
 * @package    ong
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PessoaForm extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
//         parent::setSize(0.8, null);
//         parent::removePadding();
//         parent::removeTitleBar();
        //parent::disableEscape();
        
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Pessoa');
        $this->form->setFormTitle('Beneficiário / Voluntário');
        //$this->form->setProperty('style', 'margin:0;border:0');
        $this->form->setClientValidation(true);
        $this->form->setFieldSizes('100%');

        // create the form fields
        $this->form->appendPage('Dados Gerais');
        $id = new TEntry('id');
        $cpf = new TEntry('cpf');
        $nis = new TEntry('nis');
        $rg = new TEntry('rg');
        $nome = new TEntry('nome');        
        $indicado_por = new TEntry('indicado_por');
        $fone1 = new TEntry('fone1');
        $fone2 = new TEntry('fone2');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $endereco = new TEntry('endereco');
        $numero = new TEntry('numero');        
        $bairro = new TEntry('bairro');
        $idade = new TEntry('idade');
        $tipo_id = new TDBCombo('tipo_id', 'ong', 'Tipo', 'id', 'nome');
        $projeto_id = new TDBCombo('projeto_id', 'ong', 'Projeto', 'id', 'nome');
        
        
        $estado_civil_id = new TDBCombo('estado_civil_id', 'ong', 'Estadocivil', 'id', 'nome');
        $escolariedade_id = new TDBCombo('escolariedade_id', 'ong', 'Escolariedade', 'id', 'nome');
        $sexo_id = new TDBCombo('sexo_id', 'ong', 'Sexo', 'id', 'nome');
        $profissao = new TEntry('profissao');
        
        $filter = new TCriteria;
        $filter->add(new TFilter('id', '<', '0'));
        $cidade_id = new TDBCombo('cidade_id', 'ong', 'Cidade', 'id', 'nome', 'nome', $filter);
        $estado_id = new TDBCombo('estado_id', 'ong', 'Estado', 'id', '{nome} ({uf})');
        
        $estado_id->setChangeAction( new TAction( [$this, 'onChangeEstado'] ) );
        $tipo_id->setChangeAction(new TAction([$this, 'onChangeTipo']));
        //$cep->setExitAction( new TAction([ $this, 'onExitCEP']) );
        
        $cidade_id->enableSearch();
        $estado_id->enableSearch();
        $projeto_id->enableSearch();
        
        $cep->setMask('99999-999');
        $fone1->setMask('(99) 99999-9999');
        $fone2->setMask('(99) 99999-9999');
        $cpf->setMask('999.999.999-99');
        $nome->style = 'text-transform: uppercase';
        $endereco->style = 'text-transform: uppercase';
        $bairro->style = 'text-transform: uppercase';
        $profissao->style = 'text-transform: uppercase';
        $indicado_por->style = 'text-transform: uppercase';
        
        $id->setEditable(FALSE);
        $sexo_id->addValidation('Sexo', new TRequiredValidator);
        $escolariedade_id->addValidation('Escolariedade', new TRequiredValidator);
        $estado_civil_id->addValidation('Estado Civil', new TRequiredValidator);
        $cpf->addValidation('CPF', new TRequiredValidator);
        $idade->addValidation('Idade', new TRequiredValidator);
        $rg->addValidation('RG', new TRequiredValidator);
        $nis->addValidation('Mis', new TRequiredValidator);
        $tipo_id->addValidation('Tipo', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        $fone1->addValidation('Fone', new TRequiredValidator);
        $email->addValidation('Email', new TRequiredValidator);
        $email->addValidation('Email', new TEmailValidator);
        $cidade_id->addValidation('Cidade', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $endereco->addValidation('endereco', new TRequiredValidator);
        $numero->addValidation('Número', new TRequiredValidator);
        
        // add the fields
        $row = $this->form->addFields( [ new TLabel('Id'),  $id ],  [ new TLabel('Tipo'),  $tipo_id ], [ new TLabel('Projeto'),  $projeto_id ]);
        $row->layout = ['col-sm-2', 'col-sm-3',  'col-sm-7' ];
        
        $row = $this->form->addFields( [ new TLabel('Nome') ,  $nome ] );
        $row->layout =['col-sm-12'];
        
        $row = $this->form->addFields( [ new TLabel('CPF') ,  $cpf ], [ new TLabel('RG') ,  $rg ], [ new TLabel('Nis') ,  $nis ], [ new TLabel('Idade') ,  $idade ] );
        $row->layout = ['col-sm-3', 'col-sm-3',  'col-sm-4', 'col-sm-2'];
        
        $row = $this->form->addFields( [ new TLabel('Sexo') ,  $sexo_id ], [ new TLabel('Escolariedade') ,  $escolariedade_id ], [ new TLabel('Estado Civil') ,  $estado_civil_id ] );
        $row->layout = ['col-sm-3', 'col-sm-5',  'col-sm-4' ];
        
        $row = $this->form->addFields( [ new TLabel('Profissão') , $profissao ], [ new TLabel('Fone1') , $fone1 ], [ new TLabel('Fone2') ,  $fone2 ], [ new TLabel('E-mail') , $email ]  );
        $row->layout = ['col-sm-4', 'col-sm-2', 'col-sm-2', 'col-sm-4' ];                     
        
        $this->form->addContent( [new TFormSeparator('Endereço')]);
        
        $this->form->addFields( [ new TLabel('Cep') , $cep ] )->layout = ['col-sm-2'];
        $this->form->addFields( [ new TLabel('Endereço') ,  $endereco ])->layout = ['col-sm-12'];
        $this->form->addFields( [ new TLabel('Numero') ,  $numero ], [ new TLabel('Bairro'), $bairro ], [ new TLabel('UF') , $estado_id ])->layout = ['col-sm-2', 'col-sm-7', 'col-sm-3'];        
        $this->form->addFields( [ new TLabel('Cidade'), $cidade_id ] )->layout = ['col-sm-12'];
        
        $this->form->addContent( [new TFormSeparator('Indicação')]);
        $this->form->addFields( [ new TLabel('Indicado Por') , $indicado_por ] )->layout = ['col-sm-12'];
        
        $this->form->appendPage('Dados Complementares');                
        $fonterenda_id = new TDBCombo('fonterenda_id', 'ong', 'Fonterenda', 'id', 'nome');
        $rendamensal_id = new TDBCombo('rendamensal_id', 'ong', 'Rendamensal', 'id', 'nome');
        $moradia_id = new TDBCombo('moradia_id', 'ong', 'Moradia', 'id', 'nome');
        $moradores = new TEntry('moradores');
        
        $moradores->addValidation('Moradores', new TRequiredValidator);
        
        $estruturamoradia_list = new TDBCheckGroup('estruturamoradia_list', 'ong', 'Estruturamoradia', 'id', 'nome');
        $tipoatendimento_list = new TDBCheckGroup('tipoatendimento_list', 'ong', 'Tipoatendimento', 'id', 'nome');        
              
        $this->form->addFields( [ new TLabel('QUAL SUA PRINCIPAL FONTE DE RENDA?') , $fonterenda_id ], [ new TLabel('QUAL A RENDA MENSAL DA FAMILIA?') , $rendamensal_id ] )->layout = ['col-sm-6', 'col-sm-6'];        
        $this->form->addFields( [ new TLabel('QUANTAS PESSOAS MORAM COM VOCÊ?') , $moradores ], [ new TLabel('A CASA ONDE VOCÊ MORA É?') , $moradia_id ] )->layout = ['col-sm-3', 'col-sm-9'];
        $this->form->addFields( [ new TLabel('O LUGAR ONDE VOCÊ MORA TEM:') , $estruturamoradia_list ], [ new TLabel('BUSCA ATENDIMENTO PARA:') , $tipoatendimento_list ] )->layout = ['col-sm-3', 'col-sm-9'];
        
        // create the form actions        
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:plus green');
        $this->form->addActionLink( 'Listagem', new TAction(['PessoaList', 'onReload']), 'fa:table blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    public static function onChangeTipo($param)
    {
        if (isset($param['tipo_id']))
        {
            if ($param['tipo_id'] == 1)
            {
                TScript::create("
                    $('a:contains(\"Dados Complementares\")').closest('li').show();
                ");
            }
            else
            {
                TScript::create("
                    $('a:contains(\"Dados Complementares\")').closest('li').hide();
                ");
            }
        }
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('ong'); // open a transaction
            
            $this->form->validate(); // validate form data
            $data = $this->form->getData(); // get form data as array
            
            if ($data->tipo_id == 1)
            {
                if (empty($data->fonterenda_id))
                {
                    throw new Exception('Informe a fonte de renda');
                }
            
                if (empty($data->rendamensal_id))
                {
                    throw new Exception('Informe a renda mensal');
                }
            
                if (empty($data->moradia_id))
                {
                    throw new Exception('Informe a moradia');
                }
            
                if (empty($data->moradores))
                {
                    throw new Exception('Informe quantas pessoas moram com você');
                }
            
                if (empty($data->estruturamoradia_list))
                {
                    throw new Exception('Selecione ao menos uma estrutura de moradia');
                }
            
                if (empty($data->tipoatendimento_list))
                {
                    throw new Exception('Selecione ao menos um tipo de atendimento');
                }
            }
            
            $object = new Pessoa;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data                       
            
            if ( !empty($param['estruturamoradia_list']) )
            {
                foreach ($param['estruturamoradia_list'] as $estruturamoradia_id)
                {
                    // add the skill to the customer
                    $object->addEstruturamoradia(new Estruturamoradia($estruturamoradia_id));
                }
            }
            
            if ( !empty($param['tipoatendimento_list']) )
            {
                foreach ($param['tipoatendimento_list'] as $tipoatendimento_id)
                {
                    // add the skill to the customer
                    $object->addTipoatendimento(new Tipoatendimento($tipoatendimento_id));
                }
            }
            
            $object->store(); // save the object                   
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];
                TTransaction::open('ong');
                $object = new Pessoa($key);                                         
                
                // load the skills (aggregation)
                $estruturamoradias = $object->getEstruturamoradias();
                $estruturamoradia_list = array();
                if ($estruturamoradias)
                {
                    foreach ($estruturamoradias as $skill)
                    {
                        $estruturamoradia_list[] = $skill->id;
                    }
                }
                $object->estruturamoradia_list = $estruturamoradia_list;
                
                
                // load the tipoatendimento (aggregation)
                $tipoatendimentos = $object->getTipoatendimentos();
                $tipoatendimento_list = array();
                if ($tipoatendimentos)
                {
                    foreach ($tipoatendimentos as $skill)
                    {
                        $tipoatendimento_list[] = $skill->id;
                    }
                }
                $object->tipoatendimento_list = $tipoatendimento_list;
                                 
                
                $this->form->setData($object);
                
                // force fire events
                $data = new stdClass;
                $data->estado_id = $object->cidade->estado->id;
                $data->cidade_id = $object->cidade_id;
                TForm::sendData('form_Pessoa', $data);
                
                TTransaction::close();
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Action to be executed when the user changes the state
     * @param $param Action parameters
     */
    public static function onChangeEstado($param)
    {
        try
        {
            TTransaction::open('ong');
            if (!empty($param['estado_id']))
            {
                $criteria = TCriteria::create( ['estado_id' => $param['estado_id'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_Pessoa', 'cidade_id', 'ong', 'Cidade', 'id', '{nome}', 'nome', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_Pessoa', 'cidade_id');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
   
    
    /**
     * Autocompleta outros campos a partir do CEP
     */
    public static function onExitCEP($param)
    {
         session_write_close();                                 
        
         try
         {
             $cep = preg_replace('/[^0-9]/', '', $param['cep']);
             $url = 'https://viacep.com.br/ws/'.$cep.'/json/';
          
             //$content = @file_get_contents($url);
             
             //$content = file_get_contents($url);

            //var_dump(error_get_last());
            
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 10
            ]);
            
            $content = curl_exec($ch);
            
            curl_close($ch);                                    
            
             if ($content !== false)
             {
                 $cep_data = json_decode($content);
                
                 $data = new stdClass;
                 if (is_object($cep_data) && empty($cep_data->erro))
                 {
                     TTransaction::open('ong');
                     $estado = Estado::where('uf', '=', $cep_data->uf)->first();
                     $cidade = Cidade::where('codigo_ibge', '=', $cep_data->ibge)->first();
                     TTransaction::close();
                    
                     $data->endereco  = $cep_data->endereco;
                     $data->complemento = $cep_data->complemento;
                     $data->bairro      = $cep_data->bairro;
                     $data->estado_id   = $estado->id ?? '';
                     $data->cidade_id   = $cidade->id ?? '';
                    
                     TForm::sendData('form_Pessoa', $data, false, true);
                 }
                 else
                 {
                     $data->endereco  = '';
                     $data->complemento = '';
                     $data->bairro      = '';
                     $data->estado_id   = '';
                     $data->cidade_id   = '';
                    
                     TForm::sendData('form_Pessoa', $data, false, true);
                 }
             }
         }
         catch (Exception $e)
         {
             new TMessage('error', $e->getMessage());
         }
    }
    
//     /**
//      * Closes window
//      */
//     public static function onClose()
//     {
//         parent::closeWindow();
//     }
}
