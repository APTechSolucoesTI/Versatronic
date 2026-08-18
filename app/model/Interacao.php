<?php

class Interacao extends TRecord
{
    const TABLENAME  = 'interacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    const CREATEDAT  = 'created_at';
    const UPDATEDAT  = 'updated_at';

    private Pessoa $cliente;
    private Representante $vendedor;
    private OrigemContato $origem_contato;
    private EtapaInteracao $etapa_interacao;
    private TipoInteracao $tipo_interacao;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('tipo_interacao_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('vendedor_id');
        parent::addAttribute('origem_contato_id');
        parent::addAttribute('etapa_interacao_id');
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_fechamento');
        parent::addAttribute('data_fechamento_esperada');
        parent::addAttribute('valor_total');
        parent::addAttribute('ordem');
        parent::addAttribute('mes');
        parent::addAttribute('ano');
        parent::addAttribute('created_at');
        parent::addAttribute('updated_at');
        parent::addAttribute('deleted_at');
        parent::addAttribute('cliente_nome');
        parent::addAttribute('cidade');
        parent::addAttribute('estado');
    
    }

    /**
     * Method set_pessoa
     * Sample of usage: $var->pessoa = $object;
     * @param $object Instance of Pessoa
     */
    public function set_cliente(Pessoa $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }

    /**
     * Method get_cliente
     * Sample of usage: $var->cliente->attribute;
     * @returns Pessoa instance
     */
    public function get_cliente()
    {
    
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Pessoa($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
    }
    /**
     * Method set_representante
     * Sample of usage: $var->representante = $object;
     * @param $object Instance of Representante
     */
    public function set_vendedor(Representante $object)
    {
        $this->vendedor = $object;
        $this->vendedor_id = $object->id;
    }

    /**
     * Method get_vendedor
     * Sample of usage: $var->vendedor->attribute;
     * @returns Representante instance
     */
    public function get_vendedor()
    {
    
        // loads the associated object
        if (empty($this->vendedor))
            $this->vendedor = new Representante($this->vendedor_id);
    
        // returns the associated object
        return $this->vendedor;
    }
    /**
     * Method set_origem_contato
     * Sample of usage: $var->origem_contato = $object;
     * @param $object Instance of OrigemContato
     */
    public function set_origem_contato(OrigemContato $object)
    {
        $this->origem_contato = $object;
        $this->origem_contato_id = $object->id;
    }

    /**
     * Method get_origem_contato
     * Sample of usage: $var->origem_contato->attribute;
     * @returns OrigemContato instance
     */
    public function get_origem_contato()
    {
    
        // loads the associated object
        if (empty($this->origem_contato))
            $this->origem_contato = new OrigemContato($this->origem_contato_id);
    
        // returns the associated object
        return $this->origem_contato;
    }
    /**
     * Method set_etapa_interacao
     * Sample of usage: $var->etapa_interacao = $object;
     * @param $object Instance of EtapaInteracao
     */
    public function set_etapa_interacao(EtapaInteracao $object)
    {
        $this->etapa_interacao = $object;
        $this->etapa_interacao_id = $object->id;
    }

    /**
     * Method get_etapa_interacao
     * Sample of usage: $var->etapa_interacao->attribute;
     * @returns EtapaInteracao instance
     */
    public function get_etapa_interacao()
    {
    
        // loads the associated object
        if (empty($this->etapa_interacao))
            $this->etapa_interacao = new EtapaInteracao($this->etapa_interacao_id);
    
        // returns the associated object
        return $this->etapa_interacao;
    }
    /**
     * Method set_tipo_interacao
     * Sample of usage: $var->tipo_interacao = $object;
     * @param $object Instance of TipoInteracao
     */
    public function set_tipo_interacao(TipoInteracao $object)
    {
        $this->tipo_interacao = $object;
        $this->tipo_interacao_id = $object->id;
    }

    /**
     * Method get_tipo_interacao
     * Sample of usage: $var->tipo_interacao->attribute;
     * @returns TipoInteracao instance
     */
    public function get_tipo_interacao()
    {
    
        // loads the associated object
        if (empty($this->tipo_interacao))
            $this->tipo_interacao = new TipoInteracao($this->tipo_interacao_id);
    
        // returns the associated object
        return $this->tipo_interacao;
    }

    /**
     * Method getInteracaoArquivos
     */
    public function getInteracaoArquivos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoArquivo::getObjects( $criteria );
    }
    /**
     * Method getInteracaoObservacaos
     */
    public function getInteracaoObservacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoObservacao::getObjects( $criteria );
    }
    /**
     * Method getInteracaoAtividades
     */
    public function getInteracaoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoAtividade::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoEtapas
     */
    public function getInteracaoHistoricoEtapas()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoHistoricoEtapa::getObjects( $criteria );
    }
    /**
     * Method getInteracaoItems
     */
    public function getInteracaoItems()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoItem::getObjects( $criteria );
    }
    /**
     * Method getInteracaoLocalizacaos
     */
    public function getInteracaoLocalizacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoLocalizacao::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoAtividades
     */
    public function getInteracaoHistoricoAtividades()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoHistoricoAtividade::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoObservacaos
     */
    public function getInteracaoHistoricoObservacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoHistoricoObservacao::getObjects( $criteria );
    }
    /**
     * Method getInteracaoHistoricoArquivos
     */
    public function getInteracaoHistoricoArquivos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('interacao_id', '=', $this->id));
        return InteracaoHistoricoArquivo::getObjects( $criteria );
    }

    public function set_interacao_arquivo_interacao_to_string($interacao_arquivo_interacao_to_string)
    {
        if(is_array($interacao_arquivo_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_arquivo_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_arquivo_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_arquivo_interacao_to_string = $interacao_arquivo_interacao_to_string;
        }

        $this->vdata['interacao_arquivo_interacao_to_string'] = $this->interacao_arquivo_interacao_to_string;
    }

    public function get_interacao_arquivo_interacao_to_string()
    {
        if(!empty($this->interacao_arquivo_interacao_to_string))
        {
            return $this->interacao_arquivo_interacao_to_string;
        }
    
        $values = InteracaoArquivo::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_observacao_interacao_to_string($interacao_observacao_interacao_to_string)
    {
        if(is_array($interacao_observacao_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_observacao_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_observacao_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_observacao_interacao_to_string = $interacao_observacao_interacao_to_string;
        }

        $this->vdata['interacao_observacao_interacao_to_string'] = $this->interacao_observacao_interacao_to_string;
    }

    public function get_interacao_observacao_interacao_to_string()
    {
        if(!empty($this->interacao_observacao_interacao_to_string))
        {
            return $this->interacao_observacao_interacao_to_string;
        }
    
        $values = InteracaoObservacao::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_atividade_tipo_atividade_to_string($interacao_atividade_tipo_atividade_to_string)
    {
        if(is_array($interacao_atividade_tipo_atividade_to_string))
        {
            $values = TipoAtividade::where('id', 'in', $interacao_atividade_tipo_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_atividade_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_atividade_tipo_atividade_to_string = $interacao_atividade_tipo_atividade_to_string;
        }

        $this->vdata['interacao_atividade_tipo_atividade_to_string'] = $this->interacao_atividade_tipo_atividade_to_string;
    }

    public function get_interacao_atividade_tipo_atividade_to_string()
    {
        if(!empty($this->interacao_atividade_tipo_atividade_to_string))
        {
            return $this->interacao_atividade_tipo_atividade_to_string;
        }
    
        $values = InteracaoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_atividade_interacao_to_string($interacao_atividade_interacao_to_string)
    {
        if(is_array($interacao_atividade_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_atividade_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_atividade_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_atividade_interacao_to_string = $interacao_atividade_interacao_to_string;
        }

        $this->vdata['interacao_atividade_interacao_to_string'] = $this->interacao_atividade_interacao_to_string;
    }

    public function get_interacao_atividade_interacao_to_string()
    {
        if(!empty($this->interacao_atividade_interacao_to_string))
        {
            return $this->interacao_atividade_interacao_to_string;
        }
    
        $values = InteracaoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_atividade_estado_atividade_to_string($interacao_atividade_estado_atividade_to_string)
    {
        if(is_array($interacao_atividade_estado_atividade_to_string))
        {
            $values = EstadoAtividade::where('id', 'in', $interacao_atividade_estado_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_atividade_estado_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_atividade_estado_atividade_to_string = $interacao_atividade_estado_atividade_to_string;
        }

        $this->vdata['interacao_atividade_estado_atividade_to_string'] = $this->interacao_atividade_estado_atividade_to_string;
    }

    public function get_interacao_atividade_estado_atividade_to_string()
    {
        if(!empty($this->interacao_atividade_estado_atividade_to_string))
        {
            return $this->interacao_atividade_estado_atividade_to_string;
        }
    
        $values = InteracaoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('estado_atividade_id','{estado_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_etapa_interacao_to_string($interacao_historico_etapa_interacao_to_string)
    {
        if(is_array($interacao_historico_etapa_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_etapa_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_etapa_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_etapa_interacao_to_string = $interacao_historico_etapa_interacao_to_string;
        }

        $this->vdata['interacao_historico_etapa_interacao_to_string'] = $this->interacao_historico_etapa_interacao_to_string;
    }

    public function get_interacao_historico_etapa_interacao_to_string()
    {
        if(!empty($this->interacao_historico_etapa_interacao_to_string))
        {
            return $this->interacao_historico_etapa_interacao_to_string;
        }
    
        $values = InteracaoHistoricoEtapa::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_etapa_etapa_interacao_to_string($interacao_historico_etapa_etapa_interacao_to_string)
    {
        if(is_array($interacao_historico_etapa_etapa_interacao_to_string))
        {
            $values = EtapaInteracao::where('id', 'in', $interacao_historico_etapa_etapa_interacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_etapa_etapa_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_etapa_etapa_interacao_to_string = $interacao_historico_etapa_etapa_interacao_to_string;
        }

        $this->vdata['interacao_historico_etapa_etapa_interacao_to_string'] = $this->interacao_historico_etapa_etapa_interacao_to_string;
    }

    public function get_interacao_historico_etapa_etapa_interacao_to_string()
    {
        if(!empty($this->interacao_historico_etapa_etapa_interacao_to_string))
        {
            return $this->interacao_historico_etapa_etapa_interacao_to_string;
        }
    
        $values = InteracaoHistoricoEtapa::where('interacao_id', '=', $this->id)->getIndexedArray('etapa_interacao_id','{etapa_interacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_item_produto_to_string($interacao_item_produto_to_string)
    {
        if(is_array($interacao_item_produto_to_string))
        {
            $values = Produto::where('id', 'in', $interacao_item_produto_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_item_produto_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_item_produto_to_string = $interacao_item_produto_to_string;
        }

        $this->vdata['interacao_item_produto_to_string'] = $this->interacao_item_produto_to_string;
    }

    public function get_interacao_item_produto_to_string()
    {
        if(!empty($this->interacao_item_produto_to_string))
        {
            return $this->interacao_item_produto_to_string;
        }
    
        $values = InteracaoItem::where('interacao_id', '=', $this->id)->getIndexedArray('produto_id','{produto->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_item_interacao_to_string($interacao_item_interacao_to_string)
    {
        if(is_array($interacao_item_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_item_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_item_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_item_interacao_to_string = $interacao_item_interacao_to_string;
        }

        $this->vdata['interacao_item_interacao_to_string'] = $this->interacao_item_interacao_to_string;
    }

    public function get_interacao_item_interacao_to_string()
    {
        if(!empty($this->interacao_item_interacao_to_string))
        {
            return $this->interacao_item_interacao_to_string;
        }
    
        $values = InteracaoItem::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_localizacao_interacao_to_string($interacao_localizacao_interacao_to_string)
    {
        if(is_array($interacao_localizacao_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_localizacao_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_localizacao_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_localizacao_interacao_to_string = $interacao_localizacao_interacao_to_string;
        }

        $this->vdata['interacao_localizacao_interacao_to_string'] = $this->interacao_localizacao_interacao_to_string;
    }

    public function get_interacao_localizacao_interacao_to_string()
    {
        if(!empty($this->interacao_localizacao_interacao_to_string))
        {
            return $this->interacao_localizacao_interacao_to_string;
        }
    
        $values = InteracaoLocalizacao::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_interacao_to_string($interacao_historico_atividade_interacao_to_string)
    {
        if(is_array($interacao_historico_atividade_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_atividade_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_atividade_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_interacao_to_string = $interacao_historico_atividade_interacao_to_string;
        }

        $this->vdata['interacao_historico_atividade_interacao_to_string'] = $this->interacao_historico_atividade_interacao_to_string;
    }

    public function get_interacao_historico_atividade_interacao_to_string()
    {
        if(!empty($this->interacao_historico_atividade_interacao_to_string))
        {
            return $this->interacao_historico_atividade_interacao_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_movimentacao_to_string($interacao_historico_atividade_movimentacao_to_string)
    {
        if(is_array($interacao_historico_atividade_movimentacao_to_string))
        {
            $values = Movimentacao::where('id', 'in', $interacao_historico_atividade_movimentacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_atividade_movimentacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_movimentacao_to_string = $interacao_historico_atividade_movimentacao_to_string;
        }

        $this->vdata['interacao_historico_atividade_movimentacao_to_string'] = $this->interacao_historico_atividade_movimentacao_to_string;
    }

    public function get_interacao_historico_atividade_movimentacao_to_string()
    {
        if(!empty($this->interacao_historico_atividade_movimentacao_to_string))
        {
            return $this->interacao_historico_atividade_movimentacao_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_tipo_atividade_to_string($interacao_historico_atividade_tipo_atividade_to_string)
    {
        if(is_array($interacao_historico_atividade_tipo_atividade_to_string))
        {
            $values = TipoAtividade::where('id', 'in', $interacao_historico_atividade_tipo_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_atividade_tipo_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_tipo_atividade_to_string = $interacao_historico_atividade_tipo_atividade_to_string;
        }

        $this->vdata['interacao_historico_atividade_tipo_atividade_to_string'] = $this->interacao_historico_atividade_tipo_atividade_to_string;
    }

    public function get_interacao_historico_atividade_tipo_atividade_to_string()
    {
        if(!empty($this->interacao_historico_atividade_tipo_atividade_to_string))
        {
            return $this->interacao_historico_atividade_tipo_atividade_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('tipo_atividade_id','{tipo_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_atividade_estado_atividade_to_string($interacao_historico_atividade_estado_atividade_to_string)
    {
        if(is_array($interacao_historico_atividade_estado_atividade_to_string))
        {
            $values = EstadoAtividade::where('id', 'in', $interacao_historico_atividade_estado_atividade_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_atividade_estado_atividade_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_atividade_estado_atividade_to_string = $interacao_historico_atividade_estado_atividade_to_string;
        }

        $this->vdata['interacao_historico_atividade_estado_atividade_to_string'] = $this->interacao_historico_atividade_estado_atividade_to_string;
    }

    public function get_interacao_historico_atividade_estado_atividade_to_string()
    {
        if(!empty($this->interacao_historico_atividade_estado_atividade_to_string))
        {
            return $this->interacao_historico_atividade_estado_atividade_to_string;
        }
    
        $values = InteracaoHistoricoAtividade::where('interacao_id', '=', $this->id)->getIndexedArray('estado_atividade_id','{estado_atividade->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_observacao_interacao_to_string($interacao_historico_observacao_interacao_to_string)
    {
        if(is_array($interacao_historico_observacao_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_observacao_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_observacao_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_observacao_interacao_to_string = $interacao_historico_observacao_interacao_to_string;
        }

        $this->vdata['interacao_historico_observacao_interacao_to_string'] = $this->interacao_historico_observacao_interacao_to_string;
    }

    public function get_interacao_historico_observacao_interacao_to_string()
    {
        if(!empty($this->interacao_historico_observacao_interacao_to_string))
        {
            return $this->interacao_historico_observacao_interacao_to_string;
        }
    
        $values = InteracaoHistoricoObservacao::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_observacao_movimentacao_to_string($interacao_historico_observacao_movimentacao_to_string)
    {
        if(is_array($interacao_historico_observacao_movimentacao_to_string))
        {
            $values = Movimentacao::where('id', 'in', $interacao_historico_observacao_movimentacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_observacao_movimentacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_observacao_movimentacao_to_string = $interacao_historico_observacao_movimentacao_to_string;
        }

        $this->vdata['interacao_historico_observacao_movimentacao_to_string'] = $this->interacao_historico_observacao_movimentacao_to_string;
    }

    public function get_interacao_historico_observacao_movimentacao_to_string()
    {
        if(!empty($this->interacao_historico_observacao_movimentacao_to_string))
        {
            return $this->interacao_historico_observacao_movimentacao_to_string;
        }
    
        $values = InteracaoHistoricoObservacao::where('interacao_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_arquivo_interacao_to_string($interacao_historico_arquivo_interacao_to_string)
    {
        if(is_array($interacao_historico_arquivo_interacao_to_string))
        {
            $values = Interacao::where('id', 'in', $interacao_historico_arquivo_interacao_to_string)->getIndexedArray('id', 'id');
            $this->interacao_historico_arquivo_interacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_arquivo_interacao_to_string = $interacao_historico_arquivo_interacao_to_string;
        }

        $this->vdata['interacao_historico_arquivo_interacao_to_string'] = $this->interacao_historico_arquivo_interacao_to_string;
    }

    public function get_interacao_historico_arquivo_interacao_to_string()
    {
        if(!empty($this->interacao_historico_arquivo_interacao_to_string))
        {
            return $this->interacao_historico_arquivo_interacao_to_string;
        }
    
        $values = InteracaoHistoricoArquivo::where('interacao_id', '=', $this->id)->getIndexedArray('interacao_id','{interacao->id}');
        return implode(', ', $values);
    }

    public function set_interacao_historico_arquivo_movimentacao_to_string($interacao_historico_arquivo_movimentacao_to_string)
    {
        if(is_array($interacao_historico_arquivo_movimentacao_to_string))
        {
            $values = Movimentacao::where('id', 'in', $interacao_historico_arquivo_movimentacao_to_string)->getIndexedArray('nome', 'nome');
            $this->interacao_historico_arquivo_movimentacao_to_string = implode(', ', $values);
        }
        else
        {
            $this->interacao_historico_arquivo_movimentacao_to_string = $interacao_historico_arquivo_movimentacao_to_string;
        }

        $this->vdata['interacao_historico_arquivo_movimentacao_to_string'] = $this->interacao_historico_arquivo_movimentacao_to_string;
    }

    public function get_interacao_historico_arquivo_movimentacao_to_string()
    {
        if(!empty($this->interacao_historico_arquivo_movimentacao_to_string))
        {
            return $this->interacao_historico_arquivo_movimentacao_to_string;
        }
    
        $values = InteracaoHistoricoArquivo::where('interacao_id', '=', $this->id)->getIndexedArray('movimentacao_id','{movimentacao->nome}');
        return implode(', ', $values);
    }

    public function get_timeline(){
        $timeline = ViewInteracaoTimeline::where('interacao_id','=',$this->id)->load();
        foreach ($timeline as $value) {
            $retorno .= "<br/>".$value->informacao_documento;
        }
        return $retorno;
    }
        
}

