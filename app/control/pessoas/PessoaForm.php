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
class PessoaForm extends TWindow
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        parent::setSize(0.8, null);
        parent::removePadding();
        parent::removeTitleBar();
        //parent::disableEscape();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Pessoa');
        $this->form->setFormTitle('Beneficiário / Voluntário');
        $this->form->setProperty('style', 'margin:0;border:0');
        $this->form->setClientValidation(true);

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');        
        $fone1 = new TEntry('fone1');
        $fone2 = new TEntry('fone2');
        $email = new TEntry('email');
        $cep = new TEntry('cep');
        $endereco = new TEntry('endereco');
        $numero = new TEntry('numero');        
        $bairro = new TEntry('bairro');
        $tipo_id = new TDBCombo('tipo_id', 'ong', 'Tipo', 'id', 'nome');
        
        $filter = new TCriteria;
        $filter->add(new TFilter('id', '<', '0'));
        $cidade_id = new TDBCombo('cidade_id', 'ong', 'Cidade', 'id', 'nome', 'nome', $filter);
        $estado_id = new TDBCombo('estado_id', 'ong', 'Estado', 'id', '{nome} ({uf})');
        
        $estado_id->setChangeAction( new TAction( [$this, 'onChangeEstado'] ) );
        $cep->setExitAction( new TAction([ $this, 'onExitCEP']) );
        
        $cidade_id->enableSearch();
        $estado_id->enableSearch();
        
        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ],  [ new TLabel('Tipo') ], [ $tipo_id ]);
        $this->form->addFields( [ new TLabel('Nome') ], [ $nome ] );
        $this->form->addFields( [ new TLabel('Fone1') ], [ $fone1 ], [ new TLabel('Fone2') ], [ $fone2 ] );
        $this->form->addFields( [ new TLabel('E-mail') ], [ $email ] );
        
        $this->form->addContent( [new TFormSeparator('Endereço')]);
        $this->form->addFields( [ new TLabel('Cep') ], [ $cep ] )->layout = ['col-sm-2 control-label', 'col-sm-4'];
        $this->form->addFields( [ new TLabel('Endereço') ], [ $endereco ]);
        $this->form->addFields(  [ new TLabel('Numero') ], [ $numero ], [ new TLabel('Bairro') ], [ $bairro ] );
        $this->form->addFields( [ new TLabel('Estado') ], [$estado_id], [ new TLabel('Cidade') ], [ $cidade_id ] );
        
        $this->form->addContent( [new TFormSeparator('Dados Complementares')]);
        
        // set sizes
        $id->setSize('100%');
        $nome->setSize('100%');        
        $fone1->setSize('100%');
        $fone2->setSize('100%');
        $email->setSize('100%');
        $cep->setSize('100%');
        $endereco->setSize('100%');
        $numero->setSize('100%');        
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');        
        //$cep->setMask('99999-999');
        $fone1->setMask('(99) 99999-9999');
        $fone2->setMask('(99) 99999-9999');
        
        $id->setEditable(FALSE);
        $tipo_id->addValidation('Tipo', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        $fone1->addValidation('Fone', new TRequiredValidator);
        $email->addValidation('Email', new TRequiredValidator);
        $email->addValidation('Email', new TEmailValidator);
        $cidade_id->addValidation('Cidade', new TRequiredValidator);
        $cep->addValidation('CEP', new TRequiredValidator);
        $endereco->addValidation('endereco', new TRequiredValidator);
        $numero->addValidation('Número', new TRequiredValidator);
        
        // create the form actions
        $this->form->addHeaderActionLink( _t('Close'),  new TAction([__CLASS__, 'onClose'], ['static'=>'1']), 'fa:times red');
        $btn = $this->form->addAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'),  new TAction([$this, 'onEdit']), 'fa:plus green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
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
            
            $object = new Pessoa;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
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
                TDBCombo::reloadFromModel('form_Pessoa', 'cidade_id', 'ong', 'Cidade', 'id', '{nome} ({id})', 'nome', $criteria, TRUE);
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
    
    /**
     * Closes window
     */
    public static function onClose()
    {
        parent::closeWindow();
    }
}
