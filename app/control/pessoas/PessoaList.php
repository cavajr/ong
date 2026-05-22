<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PessoaList extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;

    use Adianti\base\AdiantiStandardListTrait;

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
        $this->addFilterField('tipo_id', '=', 'tipo_id'); // filterField, operator, formField                    

        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_Pessoa');
        $this->form->setFormTitle('Beneficiários / Voluntários');

        // create the form fields
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $bairro = new TEntry('bairro');
        $date_from = new TDate('date_from');
        $date_to   = new TDate('date_to');
        $tipo_id = new TRadioGroup('tipo_id');

        $cidade_id = new TDBUniqueSearch('cidade_id', 'ong', 'Cidade', 'id', 'nome');
        $cidade_id->setMinLength(3);
        $cidade_id->setMask('{nome} ({estado->uf})');

        $projeto_id = new TDBUniqueSearch('projeto_id', 'ong', 'Projeto', 'id', 'nome');
        $projeto_id->setMinLength(3);
        $projeto_id->setMask('{nome} ({id})');

        $tipo_id->addItems(['1' => 'Beneficiários', '2' => 'Voluntários', '' => 'Ambos']);
        $tipo_id->setLayout('horizontal');

        $date_from->setMask('dd/mm/yyyy');
        $date_from->setDatabaseMask('yyyy-mm-dd');

        $date_to->setMask('dd/mm/yyyy');
        $date_to->setDatabaseMask('yyyy-mm-dd');

        // add the fields
        $this->form->addFields([new TLabel('Id')], [$id], [new TLabel('Nome')], [$nome]);
        $this->form->addFields(
            [new TLabel('Dt Cadastro (de)')],
            [$date_from],
            [new TLabel('Dt Cadastro (até)')],
            [$date_to]
        );
        $this->form->addFields([new TLabel('Tipo')], [$tipo_id], [new TLabel('Projeto')], [$projeto_id]);
        $this->form->addFields(
            [new TLabel('Bairro')],
            [$bairro],
            [new TLabel('Cidade')],
            [$cidade_id]
        );

        // set sizes
        $id->setSize('100%');
        $date_from->setSize('100%');
        $date_to->setSize('100%');
        $nome->setSize('100%');
        $tipo_id->setSize('100%');
        $bairro->setSize('100%');
        $cidade_id->setSize('100%');
        $projeto_id->setSize('100%');


        // keep the form filled during navigation with session data
        $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction([$this, 'onSearch']), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('New'), new TAction(['PessoaForm', 'onEdit']), 'fa:plus green');

        // creates a Datagrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->style = 'width: 100%';

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'left');
        $column_cpf = new TDataGridColumn('cpf', 'CPF', 'left');
        $column_nis = new TDataGridColumn('nis', 'NIS', 'left');
        $column_nome = new TDataGridColumn('nome', 'Nome', 'left');
        $column_bairro = new TDataGridColumn('bairro', 'Bairro', 'left');
        $column_cidade = new TDataGridColumn('{cidade->nome} - {cidade->estado->uf}', 'Cidade', 'left');
        $column_fone = new TDataGridColumn('fone1', 'Fone', 'left');
        $column_tipo = new TDataGridColumn('tipo_id', 'Tipo', 'left');

        $column_tipo->setTransformer(function ($value) {
            $tipos = [
                1 => 'Beneficiário',
                2 => 'Voluntário'
            ];

            return $tipos[$value] ?? '';
        });

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
        
        $action1 = new TDataGridAction([$this, 'onGerar'], ['id' => '{id}']);
        $action2 = new TDataGridAction(['PessoaForm', 'onEdit'], ['id' => '{id}']);
        $action3 = new TDataGridAction([$this, 'onDelete'], ['id' => '{id}', 'register_state' => 'false']);
        
        $this->datagrid->addAction($action1, 'Ficha PDF', 'far:file-pdf green');
        $this->datagrid->addAction($action2, _t('Edit'),   'far:edit blue');
        $this->datagrid->addAction($action3, _t('Delete'), 'far:trash-alt red');        

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
        $dropdown->addAction(_t('Save as CSV'), new TAction([$this, 'onExportCSV'], ['register_state' => 'false', 'static' => '1']), 'fa:table blue');
        $dropdown->addAction(_t('Save as PDF'), new TAction([$this, 'onExportPDF'], ['register_state' => 'false', 'static' => '1']), 'far:file-pdf red');
        $panel->addHeaderWidget($dropdown);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);
    } 
    
    public static function onGerar($param)
    {
        try {
    
            TTransaction::open('ong');
    
            $id = $param['id'];
    
            $pessoa = new Pessoa($id);
    
            if (!$pessoa) {
                throw new Exception('Pessoa não encontrada');
            }
    
            /*
            |--------------------------------------------------------------------------
            | RELACIONAMENTOS
            |--------------------------------------------------------------------------
            */
    
            $cidade         = new Cidade($pessoa->cidade_id);
            $estado         = new Estado($pessoa->estado_id);
            $sexo           = new Sexo($pessoa->sexo_id);
            $estadoCivil    = new EstadoCivil($pessoa->estado_civil_id);
            $escolariedade  = new Escolariedade($pessoa->escolariedade_id);
            $projeto        = new Projeto($pessoa->projeto_id);
    
            $fonteRenda     = $pessoa->fonterenda_id ? new Fonterenda($pessoa->fonterenda_id) : null;
            $rendaMensal    = $pessoa->rendamensal_id ? new Rendamensal($pessoa->rendamensal_id) : null;
            $moradia        = $pessoa->moradia_id ? new Moradia($pessoa->moradia_id) : null;
    
            /*
            |--------------------------------------------------------------------------
            | UTF8
            |--------------------------------------------------------------------------
            | Use somente se seu banco estiver em latin1
            |--------------------------------------------------------------------------
            */
    
            $converter = function ($valor) {
    
                if (empty($valor)) {
                    return '';
                }
            
                return mb_detect_encoding($valor, 'UTF-8', true)
                    ? $valor
                    : mb_convert_encoding($valor, 'UTF-8', 'ISO-8859-1');
            };
            /*
            |--------------------------------------------------------------------------
            | IMAGENS
            |--------------------------------------------------------------------------
            */
    
            $logo = 'app/images/logo1.jpeg';
    
            $foto = !empty($pessoa->foto)
                ? "files/pessoas/{$pessoa->foto}"
                : 'app/images/sem-foto.gif';
    
            $logoBase64 = '';
            $fotoBase64 = '';
    
            if (file_exists($logo)) {
    
                $type = pathinfo($logo, PATHINFO_EXTENSION);
                $data = file_get_contents($logo);
    
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
    
            if (file_exists($foto)) {
    
                $type = pathinfo($foto, PATHINFO_EXTENSION);
                $data = file_get_contents($foto);
    
                $fotoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
    
            /*
            |--------------------------------------------------------------------------
            | HTML
            |--------------------------------------------------------------------------
            */
    
            $html = "
            <html>
    
            <head>
    
                <meta charset='UTF-8'>
    
                <style>
    
                    @page {
                        margin: 20px;
                    }
    
                    body {
                        font-family: DejaVu Sans, sans-serif;
                        font-size: 10px;
                        color: #000;
                    }
    
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 4px;
                    }
    
                    td {
                        border: 1px solid #000;
                        padding: 2px 4px;
                        vertical-align: top;
                        line-height: 11px;
                        font-size: 10px;
                    }
    
                    .secao {
                        background: #d9d9d9;
                        font-weight: bold;
                        text-align: center;
                        padding: 2px;
                        font-size: 10px;
                        line-height: 10px;
                    }
    
                    .label {
                        font-weight: bold;
                        font-size: 8px;
                        display: block;
                        margin-bottom: 1px;
                        line-height: 8px;
                    }
    
                    .titulo {
                        text-align: center;
                        font-size: 24px;
                        font-weight: bold;
                        margin-top: 20px;
                    }
    
                    .logo {
                        width: 120px;
                    }
    
                    .foto {
                        width: 110px;
                        height: 130px;
                        border: 1px solid #000;
                    }
    
                </style>
    
            </head>
    
            <body>
    
                <table border='0'>
    
                    <tr>
    
                        <td style='border:0; width:20%; text-align:left;'>
    
                            <img src='{$logoBase64}' class='logo'>
    
                        </td>
    
                        <td style='border:0; width:60%; text-align:center; vertical-align:middle;'>
    
                            <div class='titulo'>
                                FICHA CADASTRAL
                            </div>
    
                        </td>
    
                        <td style='border:0; width:20%; text-align:right;'>
    
                            <img src='{$fotoBase64}' class='foto'>
    
                        </td>
    
                    </tr>
    
                </table>
    
                <table>
    
                    <tr>
                        <td colspan='4' class='secao'>
                            DADOS PESSOAIS
                        </td>
                    </tr>
    
                    <tr>
    
                        <td width='40%'>
                            <span class='label'>Nome</span>
                            " . $converter($pessoa->nome) . "
                        </td>
    
                        <td width='20%'>
                            <span class='label'>CPF</span>
                            {$pessoa->cpf}
                        </td>
    
                        <td width='20%'>
                            <span class='label'>RG</span>
                            {$pessoa->rg}
                        </td>
    
                        <td width='20%'>
                            <span class='label'>NIS</span>
                            {$pessoa->nis}
                        </td>
    
                    </tr>
    
                    <tr>
    
                        <td>
                            <span class='label'>Idade</span>
                            {$pessoa->idade}
                        </td>
    
                        <td>
                            <span class='label'>Sexo</span>
                            " . $converter($sexo->nome) . "
                        </td>
    
                        <td>
                            <span class='label'>Estado Civil</span>
                            " . $converter($estadoCivil->nome) . "
                        </td>
    
                        <td>
                            <span class='label'>Escolaridade</span>
                            " . $converter($escolariedade->nome) . "
                        </td>
    
                    </tr>
    
                    <tr>
    
                        <td colspan='4'>
                            <span class='label'>Profissão</span>
                            " . $converter($pessoa->profissao) . "
                        </td>
    
                    </tr>
    
                </table>
    
                <table>
    
                    <tr>
                        <td colspan='4' class='secao'>
                            ENDEREÇO
                        </td>
                    </tr>
    
                    <tr>
    
                        <td width='45%'>
                            <span class='label'>Endereço</span>
                            " . $converter($pessoa->endereco) . "
                        </td>
    
                        <td width='10%'>
                            <span class='label'>Número</span>
                            {$pessoa->numero}
                        </td>
    
                        <td width='25%'>
                            <span class='label'>Bairro</span>
                            " . $converter($pessoa->bairro) . "
                        </td>
    
                        <td width='20%'>
                            <span class='label'>CEP</span>
                            {$pessoa->cep}
                        </td>
    
                    </tr>
    
                    <tr>
    
                        <td colspan='2'>
                            <span class='label'>Cidade</span>
                            " . $converter($cidade->nome) . "
                        </td>
    
                        <td colspan='2'>
                            <span class='label'>Estado</span>
                            " . $converter($estado->nome) . "
                        </td>
    
                    </tr>
    
                </table>
    
                <table>
    
                    <tr>
                        <td colspan='3' class='secao'>
                            CONTATO
                        </td>
                    </tr>
    
                    <tr>
    
                        <td width='25%'>
                            <span class='label'>Telefone 1</span>
                            {$pessoa->fone1}
                        </td>
    
                        <td width='25%'>
                            <span class='label'>Telefone 2</span>
                            {$pessoa->fone2}
                        </td>
    
                        <td width='50%'>
                            <span class='label'>E-mail</span>
                            {$pessoa->email}
                        </td>
    
                    </tr>
    
                </table>
    
                <table>
    
                    <tr>
                        <td colspan='4' class='secao'>
                            DADOS SOCIAIS
                        </td>
                    </tr>
    
                    <tr>
    
                        <td>
                            <span class='label'>Projeto</span>
                            " . $converter($projeto->nome) . "
                        </td>
    
                        <td>
                            <span class='label'>Fonte de Renda</span>
                            " . ($fonteRenda ? $converter($fonteRenda->nome) : '') . "
                        </td>
    
                        <td>
                            <span class='label'>Renda Mensal</span>
                            " . ($rendaMensal ? $converter($rendaMensal->nome) : '') . "
                        </td>
    
                        <td>
                            <span class='label'>Moradia</span>
                            " . ($moradia ? $converter($moradia->nome) : '') . "
                        </td>
    
                    </tr>
    
                    <tr>
    
                        <td width='20%'>
                            <span class='label'>Moradores</span>
                            {$pessoa->moradores}
                        </td>
    
                        <td colspan='3'>
                            <span class='label'>Indicado por</span>
                            " . $converter($pessoa->indicado_por) . "
                        </td>
    
                    </tr>
    
                </table>
    
            </body>
            </html>
            ";
    
            /*
            |--------------------------------------------------------------------------
            | DOMPDF
            |--------------------------------------------------------------------------
            */
    
            $options = new Options();
    
            $options->set('isRemoteEnabled', true);
    
            $dompdf = new Dompdf($options);
    
            $dompdf->loadHtml($html, 'UTF-8');
    
            $dompdf->setPaper('A4', 'portrait');
    
            $dompdf->render();
    
            /*
            |--------------------------------------------------------------------------
            | SALVA PDF
            |--------------------------------------------------------------------------
            */
    
            if (!file_exists('app/output')) {
    
                mkdir('app/output', 0777, true);
            }
    
            $arquivo = "app/output/ficha_{$pessoa->id}_" . uniqid() . ".pdf";
    
            file_put_contents(
                $arquivo,
                $dompdf->output()
            );
    
            /*
            |--------------------------------------------------------------------------
            | WINDOW PDF
            |--------------------------------------------------------------------------
            */
    
            $object = new TElement('object');
    
            $object->data  = $arquivo;
            $object->type  = 'application/pdf';
            $object->style = 'width:100%; height:calc(100% - 10px)';
    
            $window = TWindow::create(
                'Ficha PDF',
                0.9,
                0.9
            );
    
            $window->add($object);
    
            $window->show();
                    
            TTransaction::close();
            
             TScript::create("
                setTimeout(function() {
                    fetch('engine.php?class=PessoaList&method=deleteTempPdf&file=" . base64_encode($arquivo) . "');
                }, 60000);
            ");
    
        } catch (Exception $e) {
    
            TTransaction::rollback();
    
            new TMessage('error', $e->getMessage());
        }
    }
    
    public function onExportPDF($param = null)
    {
        try {
    
            TTransaction::open('ong');
    
            $repository = new TRepository('Pessoa');
    
            $criteria = new TCriteria;
    
            $criteria->setProperty('order', 'nome');
            $criteria->setProperty('direction', 'asc');
    
            /*
            |--------------------------------------------------------------------------
            | FILTROS DA GRID
            |--------------------------------------------------------------------------
            */
    
            if ($filters = TSession::getValue(__CLASS__ . '_filters'))
            {
                foreach ($filters as $filter)
                {
                    $criteria->add($filter);
                }
            }
    
            $objects = $repository->load($criteria);
    
            if (!$objects)
            {
                new TMessage('info', 'Nenhum registro encontrado');
                return;
            }
    
            /*
            |--------------------------------------------------------------------------
            | PDF
            |--------------------------------------------------------------------------
            */
    
            $pdf = new RelatorioGeralPDF('L', 'mm', 'A4');
    
            $pdf->SetAutoPageBreak(true, 10);
    
            $pdf->AddPage();
    
            /*
            |--------------------------------------------------------------------------
            | FONTE DADOS
            |--------------------------------------------------------------------------
            */
    
            $pdf->SetFont('Arial', '', 7);
    
            $pdf->SetTextColor(0, 0, 0);
    
            /*
            |--------------------------------------------------------------------------
            | DADOS
            |--------------------------------------------------------------------------
            */
    
            foreach ($objects as $object)
            {
                $pdf->Cell(
                    20,
                    5,
                    self::iso($object->tipo->nome),
                    1,
                    0,
                    'C'
                );
    
                $pdf->Cell(
                    70,
                    5,
                    self::iso($object->nome),
                    1,
                    0,
                    'L'
                );
    
                $pdf->Cell(
                    20,
                    5,
                    $object->cpf,
                    1,
                    0,
                    'L'
                );
    
                $pdf->Cell(
                    20,
                    5,
                    $object->nis,
                    1,
                    0,
                    'L'
                );
    
                $pdf->Cell(
                    50,
                    5,
                    self::iso($object->bairro),
                    1,
                    0,
                    'L'
                );
    
                $pdf->Cell(
                    70,
                    5,
                    self::iso($object->cidade->nome) . ' - ' . ($object->cidade->estado->uf),
                    1,
                    0,
                    'L'
                );
    
                $pdf->Cell(
                    20,
                    5,
                    $object->fone1,
                    1,
                    1,
                    'L'
                );
            }
    
            /*
            |--------------------------------------------------------------------------
            | OUTPUT
            |--------------------------------------------------------------------------
            */
    
            if (!file_exists('app/output'))
            {
                mkdir('app/output', 0777, true);
            }
    
            $arquivo = 'app/output/relatorio_geral.pdf';
    
            $pdf->Output($arquivo, 'F');
    
            parent::openFile($arquivo);
    
            TTransaction::close();
    
        } catch (Exception $e) {
    
            TTransaction::rollback();
    
            new TMessage('error', $e->getMessage());
        }
    }
    
    public static function deleteTempPdf($param)
    {
        $file = base64_decode($param['file']);
    
        if (file_exists($file))
        {
            unlink($file);
        }
    }
    
    
    
    private static function iso($texto)
    {
        return mb_convert_encoding(
            $texto,
            'ISO-8859-1',
            'UTF-8'
        );
    }
}
