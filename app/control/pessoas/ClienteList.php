<?php

class ClienteList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'minicrm';
    private static $activeRecord = 'ViewCliente';
    private static $primaryKey = 'id';
    private static $formName = 'form_ViewClienteList';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters', 'onGlobalSearch'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Clientes");
        $this->limit = 20;

        $criteria_categoria = new TCriteria();

        $razao_social = new TEntry('razao_social');
        $cpf_cnpj = new TEntry('cpf_cnpj');
        $representante_razao = new TEntry('representante_razao');
        $categoria = new TDBCombo('categoria', 'minicrm', 'CategoriaCliente', 'nome', '{nome}','nome asc' , $criteria_categoria );
        $codigo = new TEntry('codigo');
        $categoria_col = new TEntry('categoria_col');
        $razao_social_col = new TEntry('razao_social_col');
        $cpf_cnpj_col = new TEntry('cpf_cnpj_col');
        $cidade = new TEntry('cidade');
        $estado = new TEntry('estado');
        $ativo = new TCombo('ativo');
        $bloqueado = new TCombo('bloqueado');

        $codigo->exitOnEnter();
        $categoria_col->exitOnEnter();
        $razao_social_col->exitOnEnter();
        $cpf_cnpj_col->exitOnEnter();
        $cidade->exitOnEnter();
        $estado->exitOnEnter();

        $codigo->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $categoria_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $razao_social_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cpf_cnpj_col->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $cidade->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $estado->setExitAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $ativo->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));
        $bloqueado->setChangeAction(new TAction([$this, 'onSearch'], ['static'=>'1']));

        $ativo->addItems(["S"=>"Sim","N"=>"Não"]);
        $bloqueado->addItems(["S"=>"Sim","N"=>"Não"]);

        $ativo->enableSearch();
        $categoria->enableSearch();
        $bloqueado->enableSearch();

        $ativo->setSize('100%');
        $codigo->setSize('100%');
        $cidade->setSize('100%');
        $estado->setSize('100%');
        $cpf_cnpj->setSize('100%');
        $categoria->setSize('100%');
        $bloqueado->setSize('100%');
        $razao_social->setSize('100%');
        $cpf_cnpj_col->setSize('100%');
        $categoria_col->setSize('100%');
        $razao_social_col->setSize('100%');
        $representante_razao->setSize('100%');

        $row1 = $this->form->addFields([new TLabel("Razão Social:", null, '14px', null, '100%'),$razao_social]);
        $row1->layout = [' col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Documento:", null, '14px', null, '100%'),$cpf_cnpj]);
        $row2->layout = [' col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Representante:", null, '14px', null, '100%'),$representante_razao]);
        $row3->layout = [' col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Categoria:", null, '14px', null, '100%'),$categoria]);
        $row4->layout = [' col-sm-12'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid->setGroupColumn('representante_id', " {representante_razao} ");
        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(250);

        $column_codigo = new TDataGridColumn('codigo', "Codigo", 'left');
        $column_categoria = new TDataGridColumn('categoria', "Categoria", 'left');
        $column_razao_social = new TDataGridColumn('razao_social', "Razão Social", 'left');
        $column_cpf_cnpj_transformed = new TDataGridColumn('cpf_cnpj', "Documento", 'left');
        $column_data_alteracao_totvs_transformed = new TDataGridColumn('data_alteracao_totvs', "Ultima Atualização TOTVS", 'left');
        $column_cidade = new TDataGridColumn('cidade', "Cidade", 'left');
        $column_estado = new TDataGridColumn('estado', "Estado", 'left');
        $column_ativo_transformed = new TDataGridColumn('ativo', "Ativo", 'left');
        $column_bloqueado_transformed = new TDataGridColumn('bloqueado', "Bloqueado", 'left');

        $column_cpf_cnpj_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if (strlen($value) === 14) {
                // Formato CNPJ: XX.XXX.XXX/XXXX-XX
                return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $value);
            }
            if (strlen($value) === 11) {
                // Formato CPF: XXX.XXX.XXX-XX
                return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $value);
            }
            return $value;

        });

        $column_data_alteracao_totvs_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {
            if(!empty(trim((string) $value)))
            {
                try
                {
                    $date = new DateTime($value);
                    return $date->format('d/m/Y');
                }
                catch (Exception $e)
                {
                    return $value;
                }
            }
        });

        $column_ativo_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        });

        $column_bloqueado_transformed->setTransformer(function($value, $object, $row, $cell = null, $last_row = null)
        {

            if($value === 'T' || $value === 't' || $value === true || $value === 'S' || $value === 's' || $value === 1)
            {
                return '<span class="label label-success">Sim</span>';
            }

            return '<span class="label label-danger">Não</span>';

        });        

        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_categoria);
        $this->datagrid->addColumn($column_razao_social);
        $this->datagrid->addColumn($column_cpf_cnpj_transformed);
        $this->datagrid->addColumn($column_data_alteracao_totvs_transformed);
        $this->datagrid->addColumn($column_cidade);
        $this->datagrid->addColumn($column_estado);
        $this->datagrid->addColumn($column_ativo_transformed);
        $this->datagrid->addColumn($column_bloqueado_transformed);

        $action_onShow = new TDataGridAction(array('ClienteFormView', 'onShow'));
        $action_onShow->setUseButton(false);
        $action_onShow->setButtonClass('btn btn-default btn-sm');
        $action_onShow->setLabel("Visualizar");
        $action_onShow->setImage('fas:search-plus #000000');
        $action_onShow->setField(self::$primaryKey);

        $action_onShow->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onShow);

        $action_onEdit = new TDataGridAction(array('PessoaCategoriaClienteForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Alterar Tipo de Cliente");
        $action_onEdit->setImage('fas:exchange-alt #000000');
        $action_onEdit->setField(self::$primaryKey);

        $action_onEdit->setParameter('key', '{id}');

        $this->datagrid->addAction($action_onEdit);

        $action_onExcluir = new TDataGridAction(array('ClienteList', 'onExcluir'));
        $action_onExcluir->setUseButton(false);
        $action_onExcluir->setButtonClass('btn btn-default btn-sm');
        $action_onExcluir->setLabel("");
        $action_onExcluir->setImage('fas:trash-alt #F44336');
        $action_onExcluir->setField(self::$primaryKey);
        $action_onExcluir->setDisplayCondition('ClienteList::canExcluir');

        $this->datagrid->addAction($action_onExcluir);

        // create the datagrid model
        $this->datagrid->createModel();

        $tr = new TElement('tr');
        $tr->id = 'datagrid-header-filter-row';
        $this->datagrid->prependRow($tr);

        if(!$action_onShow->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_onEdit->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        if(!$action_onExcluir->isHidden())
        {
            $tr->add(TElement::tag('td', ''));
        }
        $td_codigo = TElement::tag('td', $codigo);
        $tr->add($td_codigo);
        $td_categoria_col = TElement::tag('td', $categoria_col);
        $tr->add($td_categoria_col);
        $td_razao_social_col = TElement::tag('td', $razao_social_col);
        $tr->add($td_razao_social_col);
        $td_cpf_cnpj_col = TElement::tag('td', $cpf_cnpj_col);
        $tr->add($td_cpf_cnpj_col);
        $td_empty = TElement::tag('td', "");
        $tr->add($td_empty);
        $td_cidade = TElement::tag('td', $cidade);
        $tr->add($td_cidade);
        $td_estado = TElement::tag('td', $estado);
        $tr->add($td_estado);
        $td_ativo = TElement::tag('td', $ativo);
        $tr->add($td_ativo);
        $td_bloqueado = TElement::tag('td', $bloqueado);
        $tr->add($td_bloqueado);

        $this->datagrid_form->addField($codigo);
        $this->datagrid_form->addField($categoria_col);
        $this->datagrid_form->addField($razao_social_col);
        $this->datagrid_form->addField($cpf_cnpj_col);
        $this->datagrid_form->addField($cidade);
        $this->datagrid_form->addField($estado);
        $this->datagrid_form->addField($ativo);
        $this->datagrid_form->addField($bloqueado);

        $this->datagrid_form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup("Clientes");
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;

        $panel->add($this->datagrid_form);

        $panel->getBody()->class .= ' table-responsive';

        $panel->addFooter($this->pageNavigation);

        $headerActions = new TElement('div');
        $headerActions->class = ' datagrid-header-actions ';
        $headerActions->style = 'justify-content: space-between;';

        $head_left_actions = new TElement('div');
        $head_left_actions->class = ' datagrid-header-actions-left-actions ';

        $head_right_actions = new TElement('div');
        $head_right_actions->class = ' datagrid-header-actions-left-actions ';

        $headerActions->add($head_left_actions);
        $headerActions->add($head_right_actions);

        $this->datagrid_form->add($headerActions);

        $button_sincronizar = new TButton('button_button_sincronizar');
        $button_sincronizar->setAction(new TAction(['ClienteList', 'onSincronizar']), "Sincronizar");
        $button_sincronizar->addStyleClass('btn-default');
        $button_sincronizar->setImage('fas:sync-alt #000000');

        $this->datagrid_form->addField($button_sincronizar);

        $button_cadastrar = new TButton('button_button_cadastrar');
        $button_cadastrar->setAction(new TAction(['PessoaForm', 'onShow']), "Cadastrar");
        $button_cadastrar->addStyleClass('btn-default');
        $button_cadastrar->setImage('fas:plus #69aa46');

        $this->datagrid_form->addField($button_cadastrar);

        $btnShowCurtainFilters = new TButton('button_btnShowCurtainFilters');
        $btnShowCurtainFilters->setAction(new TAction(['ClienteList', 'onShowCurtainFilters']), "Filtros");
        $btnShowCurtainFilters->addStyleClass('btn-default');
        $btnShowCurtainFilters->setImage('fas:filter #000000');

        $this->datagrid_form->addField($btnShowCurtainFilters);

        $button_limpar_filtros = new TButton('button_button_limpar_filtros');
        $button_limpar_filtros->setAction(new TAction(['ClienteList', 'onClearFilters']), "Limpar filtros");
        $button_limpar_filtros->addStyleClass('btn-default');
        $button_limpar_filtros->setImage('fas:eraser #f44336');

        $this->datagrid_form->addField($button_limpar_filtros);

        $button_atualizar = new TButton('button_button_atualizar');
        $button_atualizar->setAction(new TAction(['ClienteList', 'onRefresh']), "Atualizar");
        $button_atualizar->addStyleClass('btn-default');
        $button_atualizar->setImage('fas:sync-alt #03a9f4');

        $this->datagrid_form->addField($button_atualizar);

        $dropdown_button_exportar = new TDropDown("Exportar", 'fas:file-export #2d3436');
        $dropdown_button_exportar->setPullSide('right');
        $dropdown_button_exportar->setButtonClass('btn btn-default waves-effect dropdown-toggle');
        $dropdown_button_exportar->addPostAction( "CSV", new TAction(['ClienteList', 'onExportCsv'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-csv #00b894' );
        $dropdown_button_exportar->addPostAction( "XLS", new TAction(['ClienteList', 'onExportXls'],['static' => 1]), 'datagrid_'.self::$formName, 'fas:file-excel #4CAF50' );
        $dropdown_button_exportar->addPostAction( "PDF", new TAction(['ClienteList', 'onExportPdf'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-pdf #e74c3c' );
        $dropdown_button_exportar->addPostAction( "XML", new TAction(['ClienteList', 'onExportXml'],['static' => 1]), 'datagrid_'.self::$formName, 'far:file-code #95a5a6' );

        $head_left_actions->add($button_cadastrar);
        $head_left_actions->add($btnShowCurtainFilters);
        $head_left_actions->add($button_limpar_filtros);
        $head_left_actions->add($button_atualizar);

        $head_right_actions->add($dropdown_button_exportar);
        $head_right_actions->add($button_sincronizar);

        $this->datagrid_form->add($this->datagrid);

        $this->btnShowCurtainFilters = $btnShowCurtainFilters;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Pessoas","Clientes"]));
        }

        $container->add($panel);

        parent::add($container);

    }

    public function onExcluir($param = null) 
    {
        if (isset($param['delete']) && $param['delete'] == 1)
            {
                try 
                {
                    if ($param['key'])
                    {
                        TTransaction::open(self::$database);

                        $pessoa = Pessoa::find($param['key']);

                        if ($pessoa)
                        {
                            $interacao = Interacao::where('cliente_id', '=', $pessoa->id)->first();
                            if ($interacao)
                            {
                                new TMessage('error', "Não é possível deletar esse cliente!");
                            }
                            else 
                            {
                                // Deletar endereços
                                $enderecos = PessoaEndereco::where('pessoa_id', '=', $pessoa->id)->load();
                                foreach ($enderecos as $endereco)
                                {
                                    $endereco->delete();
                                }

                                // Deletar complementos
                                $complementos = Complemento::where('pessoa_id', '=', $pessoa->id)->load();
                                foreach ($complementos as $complemento)
                                {
                                    $complemento->delete();
                                }

                                // Deletar contatos
                                $contatos = PessoaContato::where('pessoa_id', '=', $pessoa->id)->load();
                                foreach ($contatos as $contato)
                                {
                                    $contato->delete();
                                }

                                // Deletar grupos
                                $grupos = PessoaGrupo::where('pessoa_id', '=', $pessoa->id)->load();
                                foreach ($grupos as $grupo)
                                {
                                    $grupo->delete();
                                }

                                // Deletar a pessoa
                                $pessoa->delete();

                                new TMessage('info', "Cliente deletado!");
                            }
                        }

                        TTransaction::close(); // Fechar a transação
                        $this->onReload($param); // Recarregar os dados
                    }   
            //</autoCode>
                }
            catch (Exception $e) 
            {
                TTransaction::rollback(); // Reverter a transação em caso de erro
                new TMessage('error', $e->getMessage());
            }
        }
        else 
        {
            // Define a ação de confirmação para deletar
            $action1 = new TAction(array($this, 'onExcluir'));
            $action1->setParameters($param); // Passa o parâmetro key adiante
            $action1->setParameter('delete', 1);

            // Mostra um diálogo de confirmação para o usuário
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action1);
        }
    }
    public static function canExcluir($object)
    {
        try 
        {
            if($object->codigo == null)
            {
                return true;
            }

            return false;
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onExportCsv($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.csv';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    $handler = fopen($output, 'w');
                    TTransaction::open(self::$database);

                    foreach ($objects as $object)
                    {
                        $row = [];
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();

                            if (isset($object->$column_name))
                            {
                                $row[] = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $row[] = $object->render($column_name);
                            }
                        }

                        fputcsv($handler, $row);
                    }

                    fclose($handler);
                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXls($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xls';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $widths = [];
                $titles = [];

                foreach ($this->datagrid->getColumns() as $column)
                {
                    $titles[] = $column->getLabel();
                    $width    = 100;

                    if (is_null($column->getWidth()))
                    {
                        $width = 100;
                    }
                    else if (strpos((string)$column->getWidth(), '%') !== false)
                    {
                        $width = ((int) $column->getWidth()) * 5;
                    }
                    else if (is_numeric($column->getWidth()))
                    {
                        $width = $column->getWidth();
                    }

                    $widths[] = $width;
                }

                $table = new \TTableWriterXLS($widths);
                $table->addStyle('title',  'Helvetica', '10', 'B', '#ffffff', '#617FC3');
                $table->addStyle('data',   'Helvetica', '10', '',  '#000000', '#FFFFFF', 'LR');

                $table->addRow();

                foreach ($titles as $title)
                {
                    $table->addCell($title, 'center', 'title');
                }

                $this->limit = 0;
                $objects = $this->onReload();

                TTransaction::open(self::$database);
                if ($objects)
                {
                    foreach ($objects as $object)
                    {
                        $table->addRow();
                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $value = '';
                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                            }

                            $transformer = $column->getTransformer();
                            if ($transformer)
                            {
                                $value = strip_tags((string)call_user_func($transformer, $value, $object, null));
                            }

                            $table->addCell($value, 'center', 'data');
                        }
                    }
                }
                $table->save($output);
                TTransaction::close();

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportPdf($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.pdf';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $this->datagrid->prepareForPrinting();
                $this->onReload();

                $html = clone $this->datagrid;
                $contents = file_get_contents('app/resources/styles-print.html') . $html->getContents();

                $dompdf = new \Dompdf\Dompdf;
                $dompdf->loadHtml($contents);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($output, $dompdf->output());

                $window = TWindow::create('PDF', 0.8, 0.8);
                $object = new TElement('iframe');
                $object->src  = $output;
                $object->type  = 'application/pdf';
                $object->style = "width: 100%; height:calc(100% - 10px)";

                $window->add($object);
                $window->show();
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
        }
    }
    public function onExportXml($param = null) 
    {
        try
        {
            $output = 'app/output/'.uniqid().'.xml';

            if ( (!file_exists($output) && is_writable(dirname($output))) OR is_writable($output))
            {
                $this->limit = 0;
                $objects = $this->onReload();

                if ($objects)
                {
                    TTransaction::open(self::$database);

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $dom->{'formatOutput'} = true;
                    $dataset = $dom->appendChild( $dom->createElement('dataset') );

                    foreach ($objects as $object)
                    {
                        $row = $dataset->appendChild( $dom->createElement( self::$activeRecord ) );

                        foreach ($this->datagrid->getColumns() as $column)
                        {
                            $column_name = $column->getName();
                            $column_name_raw = str_replace(['(','{','->', '-','>','}',')', ' '], ['','','_','','','','','_'], $column_name);

                            if (isset($object->$column_name))
                            {
                                $value = is_scalar($object->$column_name) ? $object->$column_name : '';
                                $row->appendChild($dom->createElement($column_name_raw, $value)); 
                            }
                            else if (method_exists($object, 'render'))
                            {
                                $column_name = (strpos((string)$column_name, '{') === FALSE) ? ( '{' . $column_name . '}') : $column_name;
                                $value = $object->render($column_name);
                                $row->appendChild($dom->createElement($column_name_raw, $value));
                            }
                        }
                    }

                    $dom->save($output);

                    TTransaction::close();
                }
                else
                {
                    throw new Exception(_t('No records found'));
                }

                TPage::openFile($output);
            }
            else
            {
                throw new Exception(_t('Permission denied') . ': ' . $output);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function onSincronizar($param = null) 
    {
        try 
        {
            AtualizacaoService::atualizarCliente();
            AtualizacaoService::atualizarEndereco();
            AtualizacaoService::atualizarComplemento();
            AtualizacaoService::atualizarContato();
            $this->onReload();
            TToast::show("success", "Atualizado", "topRight", "fas:check-circle");

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public static function onShowCurtainFilters($param = null) 
    {
        try 
        {
            //code here

                        $filter = new self([]);

            $btnClose = new TButton('closeCurtain');
            $btnClose->class = 'btn btn-sm btn-default';
            $btnClose->style = 'margin-right:10px;';
            $btnClose->onClick = "Template.closeRightPanel();";
            $btnClose->setLabel("Fechar");
            $btnClose->setImage('fas:times');

            $filter->form->addHeaderWidget($btnClose);

            $page = new TPage();
            $page->setTargetContainer('adianti_right_panel');
            $page->setProperty('page-name', 'ClienteListSearch');
            $page->setProperty('page_name', 'ClienteListSearch');
            $page->adianti_target_container = 'adianti_right_panel';
            $page->target_container = 'adianti_right_panel';
            $page->add($filter->form);
            $page->setIsWrapped(true);
            $page->show();

            //</autoCode>
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }
    public function onClearFilters($param = null) 
    {
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    public function onRefresh($param = null) 
    {
        $this->onReload([]);
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        if ((isset($param['static']) && ($param['static'] == '1')) || !empty($param['globalSearch']))
        {
            $data = $this->datagrid_form->getData();
        }
        else
        {
            $data = $this->form->getData();
        }
        $filters = [];

        /*
        $data->nome_col = str_replace(' ','%',TratamentosService::removerAcentos($data->nome_col));
        $data->cpf_cnpj = str_replace('.','',str_replace('/','',str_replace('-','',$data->cpf_cnpj)));

        */
        if (isset($data->razao_social) AND ( (is_scalar($data->razao_social) AND $data->razao_social !== '') OR (is_array($data->razao_social) AND (!empty($data->razao_social)) )) )
        {
            $razao_social = $data->razao_social;
            $data->razao_social = str_replace(' ','%',TratamentosService::removerAcentos($data->razao_social));
        }

        if (isset($data->razao_social_col) AND ( (is_scalar($data->razao_social_col) AND $data->razao_social_col !== '') OR (is_array($data->razao_social_col) AND (!empty($data->razao_social_col)) )) )
        {
            $razao_social_col = $data->razao_social_col;
            $data->razao_social_col = str_replace(' ','%',TratamentosService::removerAcentos($data->razao_social_col));
        }

        if (isset($data->representante_razao) AND ( (is_scalar($data->representante_razao) AND $data->representante_razao !== '') OR (is_array($data->representante_razao) AND (!empty($data->representante_razao)) )) )
        {
            $representante_razao = $data->representante_razao;
            $data->representante_razao = str_replace(' ','%',TratamentosService::removerAcentos($data->representante_razao));
        }

        if (isset($data->categoria_col) AND ( (is_scalar($data->categoria_col) AND $data->categoria_col !== '') OR (is_array($data->categoria_col) AND (!empty($data->categoria_col)) )) )
        {
            $categoria_col = $data->categoria_col;
            $data->categoria_col = str_replace(' ','%',TratamentosService::removerAcentos($data->categoria_col));
        }

        if (isset($data->cpf_cnpj) AND ( (is_scalar($data->cpf_cnpj) AND $data->cpf_cnpj !== '') OR (is_array($data->cpf_cnpj) AND (!empty($data->cpf_cnpj)) )) )
        {
            $cpf_cnpj = $data->cpf_cnpj;
            $data->cpf_cnpj = str_replace('.','',str_replace('/','',str_replace('-','',$data->cpf_cnpj)));
        }
        if (isset($data->cpf_cnpj_col) AND ( (is_scalar($data->cpf_cnpj_col) AND $data->cpf_cnpj_col !== '') OR (is_array($data->cpf_cnpj_col) AND (!empty($data->cpf_cnpj_col)) )) )
        {

            $cpf_cnpj_col = $data->cpf_cnpj_col;
            $data->cpf_cnpj_col = str_replace('.','',str_replace('/','',str_replace('-','',$data->cpf_cnpj_col)));
        }

        if (isset($data->cidade) AND ( (is_scalar($data->cidade) AND $data->cidade !== '') OR (is_array($data->cidade) AND (!empty($data->cidade)) )) )
        {
            $cidade = $data->cidade;
            $data->cidade = str_replace(' ','%',TratamentosService::removerAcentos($data->cidade));
        }

        if (isset($data->estado) AND ( (is_scalar($data->estado) AND $data->estado !== '') OR (is_array($data->estado) AND (!empty($data->estado)) )) )
        {
            $estado = $data->estado;
            $data->estado = str_replace(' ','%',TratamentosService::removerAcentos($data->estado));
        } 

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->razao_social) AND ( (is_scalar($data->razao_social) AND $data->razao_social !== '') OR (is_array($data->razao_social) AND (!empty($data->razao_social)) )) )
        {

            $filters[] = new TFilter('razao_social', 'ilike', "%{$data->razao_social}%");// create the filter 
        }

        if (isset($data->cpf_cnpj) AND ( (is_scalar($data->cpf_cnpj) AND $data->cpf_cnpj !== '') OR (is_array($data->cpf_cnpj) AND (!empty($data->cpf_cnpj)) )) )
        {

            $filters[] = new TFilter('cpf_cnpj', 'ilike', "%{$data->cpf_cnpj}%");// create the filter 
        }

        if (isset($data->representante_razao) AND ( (is_scalar($data->representante_razao) AND $data->representante_razao !== '') OR (is_array($data->representante_razao) AND (!empty($data->representante_razao)) )) )
        {

            $filters[] = new TFilter('representante_razao', 'ilike', "%{$data->representante_razao}%");// create the filter 
        }

        if (isset($data->categoria) AND ( (is_scalar($data->categoria) AND $data->categoria !== '') OR (is_array($data->categoria) AND (!empty($data->categoria)) )) )
        {

            $filters[] = new TFilter('categoria', '=', $data->categoria);// create the filter 
        }

        if (isset($data->codigo) AND ( (is_scalar($data->codigo) AND $data->codigo !== '') OR (is_array($data->codigo) AND (!empty($data->codigo)) )) )
        {

            $filters[] = new TFilter('codigo', 'ilike', "%{$data->codigo}%");// create the filter 
        }

        if (isset($data->categoria_col) AND ( (is_scalar($data->categoria_col) AND $data->categoria_col !== '') OR (is_array($data->categoria_col) AND (!empty($data->categoria_col)) )) )
        {

            $filters[] = new TFilter('unaccent(categoria)', 'ilike', "%{$data->categoria_col}%");// create the filter 
        }

        if (isset($data->razao_social_col) AND ( (is_scalar($data->razao_social_col) AND $data->razao_social_col !== '') OR (is_array($data->razao_social_col) AND (!empty($data->razao_social_col)) )) )
        {

            $filters[] = new TFilter('unaccent(razao_social)', 'ilike', "%{$data->razao_social_col}%");// create the filter 
        }

        if (isset($data->cpf_cnpj_col) AND ( (is_scalar($data->cpf_cnpj_col) AND $data->cpf_cnpj_col !== '') OR (is_array($data->cpf_cnpj_col) AND (!empty($data->cpf_cnpj_col)) )) )
        {

            $filters[] = new TFilter('unaccent(cpf_cnpj)', 'like', "%{$data->cpf_cnpj_col}%");// create the filter 
        }

        if (isset($data->cidade) AND ( (is_scalar($data->cidade) AND $data->cidade !== '') OR (is_array($data->cidade) AND (!empty($data->cidade)) )) )
        {

            $filters[] = new TFilter('unaccent(cidade)', 'ilike', "%{$data->cidade}%");// create the filter 
        }

        if (isset($data->estado) AND ( (is_scalar($data->estado) AND $data->estado !== '') OR (is_array($data->estado) AND (!empty($data->estado)) )) )
        {

            $filters[] = new TFilter('unaccent(estado)', 'ilike', "%{$data->estado}%");// create the filter 
        }

        if (isset($data->ativo) AND ( (is_scalar($data->ativo) AND $data->ativo !== '') OR (is_array($data->ativo) AND (!empty($data->ativo)) )) )
        {

            $filters[] = new TFilter('ativo', '=', $data->ativo);// create the filter 
        }

        if (isset($data->bloqueado) AND ( (is_scalar($data->bloqueado) AND $data->bloqueado !== '') OR (is_array($data->bloqueado) AND (!empty($data->bloqueado)) )) )
        {

            $filters[] = new TFilter('bloqueado', '=', $data->bloqueado);// create the filter 
        }

        if (isset($data->razao_social) AND ( (is_scalar($data->razao_social) AND $data->razao_social !== '') OR (is_array($data->razao_social) AND (!empty($data->razao_social)) )) )
        {
            $data->razao_social = $razao_social;
        }

        if (isset($data->razao_social_col) AND ( (is_scalar($data->razao_social_col) AND $data->razao_social_col !== '') OR (is_array($data->razao_social_col) AND (!empty($data->razao_social_col)) )) )
        {
            $data->razao_social_col = $razao_social_col;
        }

        if (isset($data->representante_razao) AND ( (is_scalar($data->representante_razao) AND $data->representante_razao !== '') OR (is_array($data->representante_razao) AND (!empty($data->representante_razao)) )) )
        {
            $data->representante_razao = $representante_razao;
        }

        if (isset($data->categoria_col) AND ( (is_scalar($data->categoria_col) AND $data->categoria_col !== '') OR (is_array($data->categoria_col) AND (!empty($data->categoria_col)) )) )
        {
            $data->categoria_col = $categoria_col;
        }

        if (isset($data->cpf_cnpj) AND ( (is_scalar($data->cpf_cnpj) AND $data->cpf_cnpj !== '') OR (is_array($data->cpf_cnpj) AND (!empty($data->cpf_cnpj)) )) )
        {
            $data->cpf_cnpj = $cpf_cnpj;
        }

        if (isset($data->cpf_cnpj_col) AND ( (is_scalar($data->cpf_cnpj_col) AND $data->cpf_cnpj_col !== '') OR (is_array($data->cpf_cnpj_col) AND (!empty($data->cpf_cnpj_col)) )) )
        {
            $data->cpf_cnpj_col = $cpf_cnpj_col;
        }

        if (isset($data->cidade) AND ( (is_scalar($data->cidade) AND $data->cidade !== '') OR (is_array($data->cidade) AND (!empty($data->cidade)) )) )
        {
            $data->cidade = $cidade;
        }

        if (isset($data->estado) AND ( (is_scalar($data->estado) AND $data->estado !== '') OR (is_array($data->estado) AND (!empty($data->estado)) )) )
        {
            $data->estado = $estado;
        }

        // fill the form with data again
        if ((isset($param['static']) && ($param['static'] == '1')) || !empty($param['globalSearch']))
        {
            $this->datagrid_form->setData($data);
        }
        else
        {
            $this->form->setData($data);
        }

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        if (isset($param['static']) && ($param['static'] == '1') )
        {
            $class = get_class($this);
            $onReloadParam = ['offset' => 0, 'first_page' => 1, 'target_container' => $param['target_container'] ?? null];
            AdiantiCoreApplication::loadPage($class, 'onReload', $onReloadParam);
            TScript::create('$(".select2").prev().select2("close");');
        }
        else
        {
            $this->onReload(['offset' => 0, 'first_page' => 1]);
        }
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'minicrm'
            TTransaction::open(self::$database);

            // creates a repository for ViewCliente
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'representante_id, representante_id';    
            }
            elseif($param['order'] != 'representante_id, representante_id')
            {
                $param['order'] = "representante_id, representante_id,{$param['order']}"; 
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            //</blockLine><btnShowCurtainFiltersAutoCode>
            if(!empty($this->btnShowCurtainFilters) && empty($this->btnShowCurtainFiltersAdjusted))
            {
                $this->btnShowCurtainFiltersAdjusted = true;
                $this->btnShowCurtainFilters->style = 'position: relative';
                $countFilters = count($filters ?? []);
                $this->btnShowCurtainFilters->setLabel($this->btnShowCurtainFilters->getLabel(). "<span class='badge badge-success' style='position: absolute'>{$countFilters}<span>");
            }

            //</blockLine></btnShowCurtainFiltersAutoCode>

            TTransaction::open(self::$database);
            $representante = Representante::where('system_user_id','=',TSession::getValue('userid'))->load();
            if($representante){
                $criteria->add(new TFilter('representante_id', '=', "(SELECT id FROM representante WHERE system_user_id = ".TSession::getValue('userid')." LIMIT 1)"));
            }
            TTransaction::close();

            if (isset($param['key']) && isset($param['voltar'])) {

                $pageParam= ['key' => $param['key']];

                TApplication::loadPage('ClienteFormView', 'onShow', $pageParam);
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

        $this->onSearch();
    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id, $param = [])
    {
        $list = new self($param);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new ViewCliente($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

}

