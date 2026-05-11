<?php
/**
 * PessoaList
 *
 * @version    1.0
 * @package    ong
 * @subpackage control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class PessoaList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    
    use Adianti\base\AdiantiStandardListTrait;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setDatabase('ong');            // defines the database
        $this->setActiveRecord('Pessoa');   // defines the active record
        $this->setDefaultOrder('nome', 'asc');         // defines the default order
        $this->setLimit(10);
        // $this->setCriteria($criteria) // define a standard filter

        $this->addFilterField('id', '=', 'id'); // filterField, operator, formField
        $this->addFilterField('nome', 'like', 'nome'); // filterField, operator, formField
        $this->addFilterField('bairro', 'like', 'bairro'); // filterField, operator, formField
        $this->addFilterField('cidade_id', '=', 'cidade_id'); // filterField, operator, formField
        $this->addFilterField('projeto_id', '=', 'projeto_id'); // filterField, operator, formField
        $this->addFilterField('tipo_id', 'like', 'tipo'); // filterField, operator, formField
                    
       
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Pessoa');
        $this->form->setFormTitle('Beneficiários / Voluntários');
        

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');        
        $bairro = new TEntry('bairro');
        $date_from = new TDate('date_from');
        $date_to   = new TDate('date_to');
        $tipo = new TRadioGroup('tipo');
        
        $cidade_id = new TDBUniqueSearch('cidade_id', 'ong', 'Cidade', 'id', 'nome');
        $cidade_id->setMinLength(3);
        $cidade_id->setMask('{nome} ({estado->uf})');
        
        $projeto_id = new TDBUniqueSearch('projeto_id', 'ong', 'Projeto', 'id', 'nome');
        $projeto_id->setMinLength(3);
        $projeto_id->setMask('{nome} ({id})');
        
        $tipo->addItems( ['1' => 'Beneficiários', '2' => 'Voluntários', '' => 'Ambos'] );
        $tipo->setLayout('horizontal');
        
        $date_from->setMask('dd/mm/yyyy');
        $date_from->setDatabaseMask('yyyy-mm-dd');
        
        $date_to->setMask('dd/mm/yyyy');
        $date_to->setDatabaseMask('yyyy-mm-dd');
        
        // add the fields
        $this->form->addFields( [ new TLabel('Id') ], [ $id ], [ new TLabel('Nome') ], [ $nome ] );        
        $this->form->addFields( [new TLabel('Dt Cadastro (de)')], [$date_from],
                                [new TLabel('Dt Cadastro (até)')],   [$date_to] );
        $this->form->addFields( [ new TLabel('Tipo') ], [ $tipo ], [ new TLabel('Projeto') ], [ $projeto_id ] );    
        $this->form->addFields(
            [ new TLabel('Bairro') ], [ $bairro ],
            [ new TLabel('Cidade') ], [ $cidade_id ]
        );        

        // set sizes
        $id->setSize('100%');
        $date_from->setSize('100%');
        $date_to->setSize('100%');
        $nome->setSize('100%');
        $tipo->setSize('100%');
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');   
        $projeto_id->setSize('100%');           

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['PessoaForm', 'onEdit']), 'fa:plus green');
        
        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';
        //$this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'left');
        $column_nis = new TDataGridColumn('nis', 'NIS', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_cidade = new TDataGridColumn('cidade->nome', 'Cidade', 'left');
        $column_fone = new TDataGridColumn('fone1', 'Fone', 'left');
        $column_tipo = new TDataGridColumn('tipo', 'Tipo', 'left');
        
        $column_fone->enableAutoHide(500);
        $column_bairro->enableAutoHide(500);        
        
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_tipo);
        $this->datagrid->addColumn($column_cpf);
        $this->datagrid->addColumn($column_nis);        
        $this->datagrid->addColumn($column_nome);
        $this->datagrid->addColumn($column_bairro);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_fone);
        
        $column_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
        $column_nome->setAction(new TAction([$this, 'onReload']), ['order' => 'nome']);

        
        $action1 = new TDataGridAction(['PessoaFormView', 'onEdit'], ['id'=>'{id}', 'register_state' => 'false']);
        $action2 = new TDataGridAction(['PessoaForm', 'onEdit'], ['id'=>'{id}']);
        $action3 = new TDataGridAction([$this, 'onDelete'], ['id'=>'{id}', 'register_state' => 'false']);
        
        $this->datagrid->addAction($action1, _t('View'),   'fa:search gray');
        $this->datagrid->addAction($action2, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action3 ,_t('Delete'), 'far:trash-alt red');
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
        
        $panel = new TPanelGroup('', '');
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // header actions
        $dropdown = new TDropDown(_t('Export'), 'fa:list');
        $dropdown->setPullSide('right');
        $dropdown->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown->addAction( _t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static'=>'1']), 'fa:table blue' );
        $dropdown->addAction( _t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static'=>'1']), 'far:file-pdf red' );
        $panel->addHeaderWidget( $dropdown );
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        parent::add($container);
    }
}
