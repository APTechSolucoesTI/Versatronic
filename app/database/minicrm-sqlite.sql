PRAGMA foreign_keys=OFF; 

CREATE TABLE api_error( 
      id  INTEGER    NOT NULL  , 
      classe varchar  (255)   , 
      metodo varchar  (255)   , 
      url varchar  (500)   , 
      dados varchar  (3000)   , 
      error_message varchar  (3000)   , 
      created_at datetime   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE categoria_cliente( 
      id  INTEGER    NOT NULL  , 
      codigo varchar  (255)   , 
      nome varchar  (255)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE centro_custo( 
      id  INTEGER    NOT NULL  , 
      codcusto varchar  (25)   NOT NULL  , 
      nome varchar  (60)   , 
      ativo char  (1)   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE cep_cache( 
      id  INTEGER    NOT NULL  , 
      cep varchar  (10)   , 
      rua varchar  (150)   , 
      cidade varchar  (500)   , 
      bairro varchar  (500)   , 
      codigo_ibge varchar  (20)   , 
      uf varchar  (2)   , 
      cidade_id int   , 
      estado_id int   , 
      created_at datetime   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE cidade( 
      id  INTEGER    NOT NULL  , 
      estado_id int   NOT NULL  , 
      cod_municipio varchar  (255)   , 
      nome varchar  (255)   NOT NULL  , 
      codigo_ibge varchar  (10)   , 
 PRIMARY KEY (id),
FOREIGN KEY(estado_id) REFERENCES estado(id)) ; 

CREATE TABLE coligada( 
      id  INTEGER    NOT NULL  , 
      cnpj varchar  (14)   , 
      nome varchar  (255)   NOT NULL  , 
      senha varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE complemento( 
      id  INTEGER    NOT NULL  , 
      vendedor_id int   , 
      pessoa_id int   , 
      representante_id int   , 
      transportadora_id int   , 
      transportadora1_id int   , 
      ciffob varchar  (100)   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
      codcoligada text   , 
      data_alteracao_totvs datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(representante_id) REFERENCES representante(id),
FOREIGN KEY(transportadora_id) REFERENCES transportadora(id),
FOREIGN KEY(transportadora1_id) REFERENCES transportadora(id),
FOREIGN KEY(vendedor_id) REFERENCES vendedor(id),
FOREIGN KEY(pessoa_id) REFERENCES pessoa(id)) ; 

CREATE TABLE condicao_pagamento( 
      id  INTEGER    NOT NULL  , 
      codcpg varchar  (5)   NOT NULL  , 
      nome varchar  (100)   , 
      ativo char  (1)   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE email_template( 
      id  INTEGER    NOT NULL  , 
      titulo text   , 
      mensagem text   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE estado( 
      id  INTEGER    NOT NULL  , 
      pais_id int   NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
      sigla char  (2)   NOT NULL  , 
      codigo_ibge varchar  (10)   , 
 PRIMARY KEY (id),
FOREIGN KEY(pais_id) REFERENCES pais(id)) ; 

CREATE TABLE estado_atividade( 
      id  INTEGER    NOT NULL  , 
      nome text   NOT NULL  , 
      cor text   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE etapa_interacao( 
      id  INTEGER    NOT NULL  , 
      nome text   , 
      cor text   , 
      ordem int   , 
      roteiro text   , 
      kanban char  (1)   , 
      permite_edicao char  (1)   , 
      permite_exclusao char  (1)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE fabricante( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE familia_produto( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE grupo( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao( 
      id  INTEGER    NOT NULL  , 
      tipo_interacao_id int   NOT NULL  , 
      cliente_id int   , 
      vendedor_id int   NOT NULL  , 
      origem_contato_id int   , 
      etapa_interacao_id int   NOT NULL  , 
      data_inicio date   NOT NULL  , 
      data_fechamento date   , 
      data_fechamento_esperada date   , 
      valor_total double   , 
      ordem int   , 
      mes int   , 
      ano int   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
      cliente_nome varchar  (255)   , 
 PRIMARY KEY (id),
FOREIGN KEY(cliente_id) REFERENCES pessoa(id),
FOREIGN KEY(vendedor_id) REFERENCES representante(id),
FOREIGN KEY(origem_contato_id) REFERENCES origem_contato(id),
FOREIGN KEY(etapa_interacao_id) REFERENCES etapa_interacao(id),
FOREIGN KEY(tipo_interacao_id) REFERENCES tipo_interacao(id)) ; 

CREATE TABLE interacao_arquivo( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      nome_arquivo text   , 
      conteudo_arquivo text   , 
      dt_arquivo datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id)) ; 

CREATE TABLE interacao_atividade( 
      id  INTEGER    NOT NULL  , 
      tipo_atividade_id int   NOT NULL  , 
      interacao_id int   NOT NULL  , 
      estado_atividade_id int   NOT NULL  , 
      descricao text   , 
      horario_inicial datetime   , 
      horario_final datetime   , 
      observacao text   , 
      dt_atividade datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id),
FOREIGN KEY(tipo_atividade_id) REFERENCES tipo_atividade(id),
FOREIGN KEY(estado_atividade_id) REFERENCES estado_atividade(id)) ; 

CREATE TABLE interacao_historico_arquivo( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      dt_arquivo datetime   , 
      movimentacao_id int   NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id),
FOREIGN KEY(movimentacao_id) REFERENCES movimentacao(id)) ; 

CREATE TABLE interacao_historico_atividade( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      movimentacao_id int   NOT NULL  , 
      tipo_atividade_id int   NOT NULL  , 
      estado_atividade_id int   NOT NULL  , 
      dt_atividade datetime   , 
      descricao text   , 
      observacao text   , 
      horario_inicial datetime   , 
      horario_final datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(estado_atividade_id) REFERENCES estado_atividade(id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id),
FOREIGN KEY(movimentacao_id) REFERENCES movimentacao(id),
FOREIGN KEY(tipo_atividade_id) REFERENCES tipo_atividade(id)) ; 

CREATE TABLE interacao_historico_etapa( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      etapa_interacao_id int   NOT NULL  , 
      dt_etapa datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id),
FOREIGN KEY(etapa_interacao_id) REFERENCES etapa_interacao(id)) ; 

CREATE TABLE interacao_historico_observacao( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      dt_observacao datetime   , 
      movimentacao_id int   NOT NULL  , 
      descricao text   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id),
FOREIGN KEY(movimentacao_id) REFERENCES movimentacao(id)) ; 

CREATE TABLE interacao_item( 
      id  INTEGER    NOT NULL  , 
      produto_id int   NOT NULL  , 
      interacao_id int   NOT NULL  , 
      quantidade double   , 
      valor double   , 
      valor_total double   , 
      dt_item datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id),
FOREIGN KEY(produto_id) REFERENCES produto(id)) ; 

CREATE TABLE interacao_localizacao( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      descricao text   NOT NULL  , 
      latitude varchar  (50)   NOT NULL  , 
      longitude varchar  (50)   NOT NULL  , 
      dt_localizacao datetime   NOT NULL  , 
      created_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id)) ; 

CREATE TABLE interacao_observacao( 
      id  INTEGER    NOT NULL  , 
      interacao_id int   NOT NULL  , 
      observacao text   , 
      dt_observacao datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(interacao_id) REFERENCES interacao(id)) ; 

CREATE TABLE log_crontab( 
      system_unit_id int   NOT NULL  , 
      id  INTEGER    NOT NULL  , 
      classe text   NOT NULL  , 
      metodo text   , 
      data_hora datetime   , 
      status int   , 
      mensagem text   , 
      observacao text   , 
 PRIMARY KEY (id),
FOREIGN KEY(system_unit_id) REFERENCES system_unit(id)) ; 

CREATE TABLE movimentacao( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (50)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE nacionalidade( 
      id  INTEGER    NOT NULL  , 
      pais_id int   NOT NULL  , 
      descricao varchar  (255)   NOT NULL  , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(pais_id) REFERENCES pais(id)) ; 

CREATE TABLE nota_baixada( 
      id  INTEGER    NOT NULL  , 
      coligada_id int   NOT NULL  , 
      nota_status_id int   NOT NULL  , 
      numero varchar  (255)   NOT NULL  , 
      numero_nf varchar  (255)   , 
      serie_nf varchar  (255)   , 
      data_emissao datetime   , 
      razao_social varchar  (255)   , 
      documento varchar  (25)   , 
      valor_total double   , 
      enviado_email int   , 
      totvs_xml text   , 
      rps_xml text   , 
      nfs_xml text   , 
      nfs_pdf text   , 
      obs text   , 
 PRIMARY KEY (id),
FOREIGN KEY(nota_status_id) REFERENCES nota_status(id),
FOREIGN KEY(coligada_id) REFERENCES coligada(id)) ; 

CREATE TABLE nota_status( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
      cor varchar  (7)   , 
      icone varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE origem_contato( 
      id  INTEGER    NOT NULL  , 
      nome text   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pais( 
      id  INTEGER    NOT NULL  , 
      codigo varchar  (5)   , 
      nome varchar  (255)   NOT NULL  , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pessoa( 
      id  INTEGER    NOT NULL  , 
      codigo varchar  (255)   , 
      tipo_pessoa_id int   NOT NULL  , 
      categoria_cliente_id int   , 
      system_user_id int   , 
      origem char  (2)   , 
      razao_social varchar  (500)   NOT NULL  , 
      nome_fantasia varchar  (255)   , 
      cpf_cnpj varchar  (20)   , 
      rg_ie varchar  (30)   , 
      nacionalidade_id int   , 
      fone varchar  (255)   , 
      email varchar  (255)   , 
      obs varchar  (1000)   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
      ativo char  (1)   , 
      data_alteracao_totvs datetime   , 
      bloqueado char  (1)   , 
 PRIMARY KEY (id),
FOREIGN KEY(tipo_pessoa_id) REFERENCES tipo_pessoa(id),
FOREIGN KEY(categoria_cliente_id) REFERENCES categoria_cliente(id),
FOREIGN KEY(system_user_id) REFERENCES system_users(id),
FOREIGN KEY(nacionalidade_id) REFERENCES nacionalidade(id)) ; 

CREATE TABLE pessoa_contato( 
      id  INTEGER    NOT NULL  , 
      pessoa_id int   NOT NULL  , 
      nome varchar  (255)   , 
      email varchar  (255)   , 
      telefone varchar  (255)   , 
      obs varchar  (500)   , 
      created_at datetime   , 
      updated_at datetime   , 
      idcontato int   , 
      codcoligada text   , 
      data_alteracao_totvs datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(pessoa_id) REFERENCES pessoa(id)) ; 

CREATE TABLE pessoa_endereco( 
      id  INTEGER    NOT NULL  , 
      pessoa_id int   NOT NULL  , 
      cidade_id int   , 
      nome varchar  (255)   , 
      principal char  (1)   , 
      cep varchar  (10)   , 
      rua varchar  (500)   , 
      numero varchar  (20)   , 
      bairro varchar  (500)   , 
      complemento varchar  (500)   , 
      data_desativacao date   , 
      created_at datetime   , 
      updated_at datetime   , 
      data_alteracao_totvs datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(pessoa_id) REFERENCES pessoa(id),
FOREIGN KEY(cidade_id) REFERENCES cidade(id)) ; 

CREATE TABLE pessoa_grupo( 
      id  INTEGER    NOT NULL  , 
      pessoa_id int   NOT NULL  , 
      grupo_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(pessoa_id) REFERENCES pessoa(id),
FOREIGN KEY(grupo_id) REFERENCES grupo(id)) ; 

CREATE TABLE preferencia_sistema( 
      id  INTEGER    NOT NULL  , 
      system_users_id int   NOT NULL  , 
      zoom int   NOT NULL    DEFAULT 100, 
      menu_fixado int   NOT NULL    DEFAULT 0, 
      data_criacao datetime   , 
      criacao_user_id int   , 
      data_modificacao datetime   , 
      modificacao_user_id int   , 
 PRIMARY KEY (id),
FOREIGN KEY(system_users_id) REFERENCES system_users(id)) ; 

CREATE TABLE produto( 
      id  INTEGER    NOT NULL  , 
      tipo_produto_id int   NOT NULL  , 
      familia_produto_id int   NOT NULL  , 
      fornecedor_id int   NOT NULL  , 
      unidade_medida_id int   NOT NULL  , 
      fabricante_id int   , 
      nome varchar  (255)   NOT NULL  , 
      cod_barras varchar  (255)   , 
      preco_venda double   , 
      preco_custo double   , 
      peso_liquido double   , 
      peso_bruto double   , 
      largura double   , 
      altura double   , 
      volume double   , 
      estoque_minimo double   , 
      qtde_estoque double   , 
      estoque_maximo double   , 
      obs varchar  (500)   , 
      ativo char  (1)   , 
      foto varchar  (500)   , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
 PRIMARY KEY (id),
FOREIGN KEY(tipo_produto_id) REFERENCES tipo_produto(id),
FOREIGN KEY(familia_produto_id) REFERENCES familia_produto(id),
FOREIGN KEY(fabricante_id) REFERENCES fabricante(id),
FOREIGN KEY(unidade_medida_id) REFERENCES unidade_medida(id),
FOREIGN KEY(fornecedor_id) REFERENCES pessoa(id)) ; 

CREATE TABLE representante( 
      id  INTEGER    NOT NULL  , 
      codigo varchar  (15)   NOT NULL  , 
      system_user_id int   , 
      razao_social varchar  (255)   NOT NULL  , 
      cpf_cnpj varchar  (20)   , 
      inscrestadual varchar  (20)   , 
      telefone varchar  (20)   , 
      ativo char  (1)   , 
      email varchar  (40)   , 
      cor text   , 
 PRIMARY KEY (id),
FOREIGN KEY(system_user_id) REFERENCES system_users(id)) ; 

CREATE TABLE representante_divergente( 
      id  INTEGER    NOT NULL  , 
      pessoa_id int   NOT NULL  , 
      rep_ap_id int   NOT NULL  , 
      rep_totvs_id int   NOT NULL  , 
      status int   NOT NULL    DEFAULT 0, 
 PRIMARY KEY (id),
FOREIGN KEY(rep_ap_id) REFERENCES representante(id),
FOREIGN KEY(rep_totvs_id) REFERENCES representante(id),
FOREIGN KEY(pessoa_id) REFERENCES pessoa(id)) ; 

CREATE TABLE representante_totvs( 
      id  INTEGER    NOT NULL  , 
      codigo varchar  (15)   NOT NULL  , 
      razao_social varchar  (255)   NOT NULL  , 
      fantasia varchar  (255)   , 
      cpf_cnpj varchar  (20)   , 
      inscrestadual varchar  (20)   , 
      cep varchar  (20)   , 
      rua varchar  (255)   , 
      numero varchar  (20)   , 
      complemento text   , 
      bairro varchar  (255)   , 
      cidade_id int   , 
      contato varchar  (255)   , 
      telefone varchar  (20)   , 
      pais_id int   , 
      percentual_comissao double   , 
      fatclientedireto int   , 
      ativo char  (1)   , 
      celular varchar  (20)   , 
      email varchar  (40)   , 
      codcoligada text   , 
      cor text   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_group( 
      id int   NOT NULL  , 
      name text   NOT NULL  , 
      uuid varchar  (36)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_group_program( 
      id int   NOT NULL  , 
      system_group_id int   NOT NULL  , 
      system_program_id int   NOT NULL  , 
      actions text   , 
 PRIMARY KEY (id),
FOREIGN KEY(system_program_id) REFERENCES system_program(id),
FOREIGN KEY(system_group_id) REFERENCES system_group(id)) ; 

CREATE TABLE system_preference( 
      id varchar  (255)   NOT NULL  , 
      preference text   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_program( 
      id int   NOT NULL  , 
      name text   NOT NULL  , 
      controller text   NOT NULL  , 
      actions text   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_unit( 
      id int   NOT NULL  , 
      name text   NOT NULL  , 
      connection_name text   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_user_group( 
      id int   NOT NULL  , 
      system_user_id int   NOT NULL  , 
      system_group_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(system_group_id) REFERENCES system_group(id),
FOREIGN KEY(system_user_id) REFERENCES system_users(id)) ; 

CREATE TABLE system_user_program( 
      id int   NOT NULL  , 
      system_user_id int   NOT NULL  , 
      system_program_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(system_program_id) REFERENCES system_program(id),
FOREIGN KEY(system_user_id) REFERENCES system_users(id)) ; 

CREATE TABLE system_users( 
      id int   NOT NULL  , 
      name text   NOT NULL  , 
      login text   NOT NULL  , 
      password text   NOT NULL  , 
      email text   , 
      frontpage_id int   , 
      system_unit_id int   , 
      active char  (1)   , 
      accepted_term_policy_at text   , 
      accepted_term_policy char  (1)   , 
      two_factor_enabled char  (1)     DEFAULT 'N', 
      two_factor_type varchar  (100)   , 
      two_factor_secret varchar  (255)   , 
 PRIMARY KEY (id),
FOREIGN KEY(system_unit_id) REFERENCES system_unit(id),
FOREIGN KEY(frontpage_id) REFERENCES system_program(id)) ; 

CREATE TABLE system_user_unit( 
      id int   NOT NULL  , 
      system_user_id int   NOT NULL  , 
      system_unit_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(system_user_id) REFERENCES system_users(id),
FOREIGN KEY(system_unit_id) REFERENCES system_unit(id)) ; 

CREATE TABLE tipo_atividade( 
      id  INTEGER    NOT NULL  , 
      nome text   , 
      cor text   , 
      icone text   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_atividade_interacao( 
      id  INTEGER    NOT NULL  , 
      tipo_atividade_id int   NOT NULL  , 
      tipo_interacao_id int   NOT NULL  , 
 PRIMARY KEY (id),
FOREIGN KEY(tipo_atividade_id) REFERENCES tipo_atividade(id),
FOREIGN KEY(tipo_interacao_id) REFERENCES tipo_interacao(id)) ; 

CREATE TABLE tipo_interacao( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_pessoa( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
      sigla char  (2)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_produto( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
 PRIMARY KEY (id)) ; 

CREATE TABLE transportadora( 
      id  INTEGER    NOT NULL  , 
      created_at datetime   , 
      updated_at datetime   , 
      deleted_at datetime   , 
      codtra varchar  (5)   NOT NULL  , 
      nome varchar  (40)   , 
      rua varchar  (100)   , 
      numero varchar  (8)   , 
      complemento varchar  (100)   , 
      bairro varchar  (100)   , 
      cidade_id int   , 
      cep varchar  (10)   , 
      cgc varchar  (20)   , 
      inscrestadual varchar  (20)   , 
      contato varchar  (30)   , 
      telefone varchar  (15)   , 
      telex varchar  (15)   , 
      fax varchar  (15)   , 
      livre varchar  (20)   , 
      nomefantasia varchar  (60)   , 
      cei varchar  (20)   , 
      inscrmunicipal varchar  (20)   , 
      ativo char  (1)   , 
      email varchar  (60)   , 
 PRIMARY KEY (id),
FOREIGN KEY(cidade_id) REFERENCES cidade(id)) ; 

CREATE TABLE unidade_medida( 
      id  INTEGER    NOT NULL  , 
      nome varchar  (255)   NOT NULL  , 
      sigla char  (2)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE vendedor( 
      system_user_id int   , 
      id  INTEGER    NOT NULL  , 
      codigo varchar  (16)   NOT NULL  , 
      razao_social varchar  (255)   NOT NULL  , 
      fantasia varchar  (255)   , 
      cpf varchar  (20)   , 
      cargo varchar  (255)   , 
      telefone varchar  (20)   , 
      codfilial varchar  (255)   , 
      codloc varchar  (255)   , 
      vendecompra varchar  (255)   , 
      codusuario varchar  (255)   , 
      senha varchar  (255)   , 
      ativo varchar  (255)   , 
      pfvendedor varchar  (255)   , 
      pfcaixa varchar  (255)   , 
      pfsupervisor varchar  (255)   , 
      pfgerente varchar  (255)   , 
      descmaximo double   , 
      cor text   , 
      codcoligada text   , 
 PRIMARY KEY (id),
FOREIGN KEY(system_user_id) REFERENCES system_users(id)) ; 

 
 
 CREATE VIEW view_cliente AS SELECT 
    p.id AS "id",
    p.codigo AS "codigo",
    cat.nome AS "categoria",
    p.razao_social AS "razao_social",
    p.cpf_cnpj AS "cpf_cnpj",
    p.ativo AS "ativo",
    p.bloqueado AS "bloqueado",
    p.data_alteracao_totvs AS "data_alteracao_totvs",
    cd.nome AS "cidade",
    uf.nome AS "estado",
    uf.sigla AS "uf",
    cd.nome || '/' || uf.sigla as "cidade_uf",
    comp.representante_id AS "representante_id",
    rep.razao_social AS "representante_razao"
FROM
    pessoa p
    INNER JOIN pessoa_grupo pg 
		ON pg.pessoa_id = p.id AND pg.grupo_id = 2
    LEFT JOIN pessoa_endereco pe 
		ON p.id = pe.pessoa_id
    LEFT JOIN cidade cd 
		ON pe.cidade_id = cd.id
    LEFT JOIN estado uf 
		ON cd.estado_id = uf.id
    LEFT JOIN categoria_cliente cat 
		ON cat.id = p.categoria_cliente_id
    LEFT JOIN complemento comp 
		ON comp.pessoa_id = p.id 
    LEFT JOIN representante rep
		ON comp.representante_id = rep.id
GROUP BY
	p.id,
	p.codigo,
	cat.nome,
	p.razao_social,
	p.cpf_cnpj,
	p.ativo,
	p.data_alteracao_totvs,
	cd.nome,
	uf.nome,
	uf.sigla,
	comp.representante_id,
	rep.razao_social
ORDER BY p.id ASC; 

CREATE VIEW view_cliente_cidade AS SELECT 
    pessoa.id as "cliente_id",
    pessoa.codigo as "cliente_codigo",
    pessoa.tipo_pessoa_id as "tipo_pessoa_id",
    pessoa.razao_social as "cliente_razao_social",
    interacao.id as "interacao_id",
    interacao.vendedor_id as "representante_id",
    representante.razao_social as "representante",
    interacao.mes as "mes",
    interacao.ano as "ano",
    cidade.nome as "cidade",
    estado.sigla as "uf",
    cidade.nome||'/'||estado.sigla as "cidade_uf"
FROM 
    pessoa, 
    pessoa_endereco, 
    interacao, 
    cidade, 
    estado,
    representante
WHERE 
    pessoa_endereco.pessoa_id = pessoa.id AND 
    pessoa_endereco.cidade_id = cidade.id AND 
    interacao.cliente_id = pessoa.id AND 
    cidade.estado_id = estado.id AND
    interacao.vendedor_id = representante.id AND
    pessoa_endereco.principal = 'S'; 

CREATE VIEW view_interacao_timeline AS SELECT
    id as "chave",
    interacao_id as "interacao_id",
    dt_observacao as "dt_historico",
    'observacao' as "tipo"
 FROM interacao_historico_observacao

UNION ALL

SELECT
    id as "chave",
    interacao_id as "interacao_id",
    dt_arquivo as "dt_historico",
    'arquivo' as "tipo"
 FROM interacao_historico_arquivo

UNION ALL

SELECT
    id as "chave",
    interacao_id as "interacao_id",
    dt_atividade as "dt_historico",
    'atividade' as "tipo"
 FROM interacao_historico_atividade

UNION ALL

SELECT
    id as "chave",
    interacao_id as "interacao_id",
    dt_etapa as "dt_historico",
    'etapa' as "tipo"
 FROM interacao_historico_etapa
 
 UNION ALL
 
 SELECT
    id as "chave",
    interacao_id as "interacao_id",
    dt_localizacao as "dt_historico",
    'localizacao' as "tipo"
FROM interacao_localizacao;; 
 
