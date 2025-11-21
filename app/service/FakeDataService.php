<?php

class FakeDataService
{
    public static function generatePedidosVenda()
    {
        // Código gerado pelo snippet: "Conexão com banco de dados"
        TTransaction::open('minicrm');

        $produtos = Produto::getObjects();
        $etapasInteracao = EtapaInteracao::getObjects();
        $pessoas = Pessoa::getObjects();
        $origensContato = OrigemContato::getObjects();
        
        $vendedores = [];
        $clientes = [];
        for($i = 0; $i <= 5; $i++)
        {
            $vendedor = new Pessoa();
            $vendedor->razao_social = "Vendedor {$i}";
            $vendedor->cpf_cnpj = '11111111111';
            $vendedor->tipo_pessoa_id = 2;
            $vendedor->store();
            
            $vendedores[] = $vendedor;
            
            $grupoPessoa = new PessoaGrupo();
            $grupoPessoa->pessoa_id = $vendedor->id;
            $grupoPessoa->grupo_pessoa_id = Grupo::VENDEDOR;
            $grupoPessoa->store();
        }
        
        for($i = 0; $i <= 50; $i++)
        {
            $cliente = new Pessoa();
            $cliente->razao_social = "Cliente {$i}";
            $cliente->cpf_cnpj = '11111111111';
            $cliente->tipo_pessoa_id = 1;
            $cliente->store();
            
            $clientes[] = $cliente;
            
            $grupoPessoa = new PessoaGrupo();
            $grupoPessoa->pessoa_id = $cliente->id;
            $grupoPessoa->grupo_pessoa_id = Grupo::CLIENTE;
            $grupoPessoa->store();
        }
        
        
       
        
        for($i = 0; $i <= 400; $i++)
        {
            $mes = str_pad(rand(1,12), 2, "0", STR_PAD_LEFT);
            $dia = str_pad(rand(1,28), 2, "0", STR_PAD_LEFT);
            
            $interacao = new Interacao();
            $interacao->cliente_id = $clientes[rand(0, count($clientes) -1)]->id;
            $interacao->vendedor_id = $vendedores[rand(0, count($vendedores) -1)]->id;
            $interacao->data_inicio = '2024-'.$mes.'-'.$dia;
            
            $data_fechamento_esperada = new DateTime($interacao->data_inicio);
            $data_fechamento_esperada->add(new DateInterval("P15D"));
            $interacao->data_fechamento_esperada = $data_fechamento_esperada->format('Y-m-d');
            
            $interacao->etapa_interacao_id = rand(1, count($etapasInteracao));
            $interacao->origem_contato_id = rand(1, count($origensContato));
            
            if($interacao->etapa_interacao_id == EtapaInteracao::FINALIZADA)
            {
                $data_fechamento_esperada = new DateTime($interacao->data_inicio);
                $data_fechamento_esperada->add(new DateInterval("P8D"));
            
                $interacao->data_fechamento = $data_fechamento_esperada->format('Y-m-d');
            }
            
            $interacao->mes = $mes;
            $interacao->ano = '2024';
            $interacao->valor_total = 0;
            $interacao->store();
            
            for($x = 0; $x <= rand(1,5); $x++)
            {
                $produto = $produtos[rand(0, count($produtos)-1)] ?? $produtos[0];
                
                $interacaoItem = new InteracaoItem();
                $interacaoItem->interacao_id = $interacao->id;
                $interacaoItem->produto_id = $produto->id;
                $interacaoItem->quantidade = rand(1,10);
                $interacaoItem->valor = $produto->preco_venda;
                $interacaoItem->valor_total = $interacaoItem->quantidade * $interacaoItem->valor;
                $interacaoItem->store();
                
                $interacao->valor_total += $interacaoItem->valor_total;
            }
            $interacao->store();
        }
        
        
        if(!is_dir('app/fotos/produtos'))
        {
            mkdir('app/fotos/produtos', 0777, true);    
        }
        
        file_put_contents('app/fotos/produtos/cadeiragamer.png',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/cadeiragamer.png'));
        file_put_contents('app/fotos/produtos/fifa.jpeg',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/fifa.jpeg'));
        file_put_contents('app/fotos/produtos/fifa2021.jpeg',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/fifa2021.jpeg'));
        file_put_contents('app/fotos/produtos/galaxy.png',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/galaxy.png'));
        file_put_contents('app/fotos/produtos/ipadpro.png',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/ipadpro.png'));
        file_put_contents('app/fotos/produtos/iphone.png',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/iphone.png'));
        file_put_contents('app/fotos/produtos/macboook.png',file_get_contents('https://www.madbuilder.com.br/images/mini-erp-builder-cast-images/macboook.png'));
        
        $produto_5 = new Produto(5);
        $produto_4 = new Produto(4);
        $produto_3 = new Produto(3);
        $produto_7 = new Produto(7);
        $produto_6 = new Produto(6);
        $produto_2 = new Produto(2);
        $produto_1 = new Produto(1);
        
        $produto_5->foto = 'app/fotos/produtos/cadeiragamer.png';
        $produto_4->foto = 'app/fotos/produtos/fifa.jpeg';
        $produto_3->foto = 'app/fotos/produtos/fifa2021.jpeg';
        $produto_7->foto = 'app/fotos/produtos/galaxy.png';
        $produto_6->foto = 'app/fotos/produtos/ipadpro.png';
        $produto_2->foto = 'app/fotos/produtos/iphone.png';
        $produto_1->foto = 'app/fotos/produtos/macboook.png';
        
        $produto_5->store();
        $produto_4->store();
        $produto_3->store();
        $produto_7->store();
        $produto_6->store();
        $produto_2->store();
        $produto_1->store();
        
        
        TTransaction::close();
        // -----
    }
}
