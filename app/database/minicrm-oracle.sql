CREATE TABLE api_error( 
      id number(10)    NOT NULL , 
      classe varchar  (255)   , 
      metodo varchar  (255)   , 
      url varchar  (500)   , 
      dados varchar  (3000)   , 
      error_message varchar  (3000)   , 
      created_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE categoria_cliente( 
      id number(10)    NOT NULL , 
      codigo varchar  (255)   , 
      nome varchar  (255)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE centro_custo( 
      id number(10)    NOT NULL , 
      codcusto varchar  (25)    NOT NULL , 
      nome varchar  (60)   , 
      ativo char  (1)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE centro_custo_nota( 
      id number(10)    NOT NULL , 
      nota_baixada_id number(10)    NOT NULL , 
      centro_custo_id number(10)    NOT NULL , 
      valor_centro_custo binary_double  (15,2)   , 
      comissao_centro_custo binary_double   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE cep_cache( 
      id number(10)    NOT NULL , 
      cep varchar  (10)   , 
      rua varchar  (150)   , 
      cidade varchar  (500)   , 
      bairro varchar  (500)   , 
      codigo_ibge varchar  (20)   , 
      uf varchar  (2)   , 
      cidade_id number(10)   , 
      estado_id number(10)   , 
      created_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE cidade( 
      id number(10)    NOT NULL , 
      estado_id number(10)    NOT NULL , 
      cod_municipio varchar  (255)   , 
      nome varchar  (255)    NOT NULL , 
      codigo_ibge varchar  (10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE coligada( 
      id number(10)    NOT NULL , 
      cnpj varchar  (14)   , 
      nome varchar  (255)    NOT NULL , 
      senha varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE comissao_repres( 
      id number(10)    NOT NULL , 
      representante_id number(10)    NOT NULL , 
      tipo_comissao char    DEFAULT 'P' , 
      valor binary_double   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE comissao_repres_excecao( 
      id number(10)    NOT NULL , 
      pessoa_id number(10)    NOT NULL , 
      representante_id number(10)    NOT NULL , 
      valor binary_double   , 
      ativo char   , 
      tipo_comissao char    DEFAULT 'P' , 
      deleted_at timestamp(0)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE complemento( 
      id number(10)    NOT NULL , 
      vendedor_id number(10)   , 
      pessoa_id number(10)   , 
      representante_id number(10)   , 
      transportadora_id number(10)   , 
      transportadora1_id number(10)   , 
      ciffob varchar  (100)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
      codcoligada varchar(3000)   , 
      data_alteracao_totvs timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE condicao_pagamento( 
      id number(10)    NOT NULL , 
      codcpg varchar  (5)    NOT NULL , 
      nome varchar  (100)   , 
      ativo char  (1)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE configuracao_email( 
      id number(10)    NOT NULL , 
      system_users_id number(10)    NOT NULL , 
      mail_from varchar(3000)   , 
      smtp_auth varchar(3000)   , 
      smtp_host varchar(3000)   , 
      smtp_port varchar(3000)   , 
      smtp_user varchar(3000)   , 
      smtp_pass varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE controle_nota( 
      id number(10)    NOT NULL , 
      nota_baixada_id number(10)    NOT NULL , 
      obs varchar(3000)   , 
      created_at timestamp(0)   , 
      created_by number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE email_template( 
      id number(10)    NOT NULL , 
      titulo varchar(3000)   , 
      mensagem varchar(3000)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
      assunto varchar(3000)   , 
      conteudo_arquivo varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE estado( 
      id number(10)    NOT NULL , 
      pais_id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
      sigla char  (2)    NOT NULL , 
      codigo_ibge varchar  (10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE estado_atividade( 
      id number(10)    NOT NULL , 
      nome varchar(3000)    NOT NULL , 
      cor varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE etapa_interacao( 
      id number(10)    NOT NULL , 
      nome varchar(3000)   , 
      cor varchar(3000)   , 
      ordem number(10)   , 
      roteiro varchar(3000)   , 
      kanban char  (1)   , 
      permite_edicao char  (1)   , 
      permite_exclusao char  (1)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE fabricante( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE familia_produto( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE grupo( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao( 
      id number(10)    NOT NULL , 
      tipo_interacao_id number(10)    NOT NULL , 
      cliente_id number(10)   , 
      vendedor_id number(10)    NOT NULL , 
      origem_contato_id number(10)   , 
      etapa_interacao_id number(10)    NOT NULL , 
      data_inicio date    NOT NULL , 
      data_fechamento date   , 
      data_fechamento_esperada date   , 
      valor_total binary_double   , 
      ordem number(10)   , 
      mes number(10)   , 
      ano number(10)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
      cliente_nome varchar  (255)   , 
      cidade varchar  (50)   , 
      estado varchar  (50)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_arquivo( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      nome_arquivo varchar(3000)   , 
      conteudo_arquivo varchar(3000)   , 
      dt_arquivo timestamp(0)   , 
      interacao_atividade number(10)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_atividade( 
      id number(10)    NOT NULL , 
      tipo_atividade_id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      estado_atividade_id number(10)    NOT NULL , 
      descricao varchar(3000)   , 
      horario_inicial timestamp(0)   , 
      horario_final timestamp(0)   , 
      observacao varchar(3000)   , 
      dt_atividade timestamp(0)   , 
      conteudo_arquivo varchar(3000)   , 
      destinatario varchar(3000)   , 
      copia varchar(3000)   , 
      assunto varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_atividade_revisao( 
      id number(10)    NOT NULL , 
      system_users_id number(10)    NOT NULL , 
      interacao_atividade_id number(10)    NOT NULL , 
      observacao varchar(3000)   , 
      created_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_historico_arquivo( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      dt_arquivo timestamp(0)   , 
      movimentacao_id number(10)    NOT NULL , 
      descricao varchar(3000)   , 
      interacao_arquivo_id number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_historico_atividade( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      movimentacao_id number(10)    NOT NULL , 
      tipo_atividade_id number(10)    NOT NULL , 
      estado_atividade_id number(10)    NOT NULL , 
      dt_atividade timestamp(0)   , 
      observacao varchar(3000)   , 
      horario_inicial timestamp(0)   , 
      horario_final timestamp(0)   , 
      deleted_at timestamp(0)   , 
      interacao_atividade_id number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_historico_etapa( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      etapa_interacao_id number(10)    NOT NULL , 
      dt_etapa timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_historico_observacao( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      dt_observacao timestamp(0)   , 
      movimentacao_id number(10)    NOT NULL , 
      descricao varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_item( 
      id number(10)    NOT NULL , 
      produto_id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      quantidade binary_double   , 
      valor binary_double   , 
      valor_total binary_double   , 
      dt_item timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_localizacao( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      descricao varchar(3000)    NOT NULL , 
      latitude varchar  (50)    NOT NULL , 
      longitude varchar  (50)    NOT NULL , 
      dt_localizacao timestamp(0)    NOT NULL , 
      created_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
      interacao_atividade number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE interacao_observacao( 
      id number(10)    NOT NULL , 
      interacao_id number(10)    NOT NULL , 
      observacao varchar(3000)   , 
      dt_observacao timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE log_crontab( 
      system_unit_id number(10)    NOT NULL , 
      id number(10)    NOT NULL , 
      classe varchar(3000)    NOT NULL , 
      metodo varchar(3000)   , 
      data_hora timestamp(0)   , 
      status number(10)   , 
      mensagem varchar(3000)   , 
      observacao varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE movimentacao( 
      id number(10)    NOT NULL , 
      nome varchar  (50)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE nacionalidade( 
      id number(10)    NOT NULL , 
      pais_id number(10)    NOT NULL , 
      descricao varchar  (255)    NOT NULL , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE nota_baixada( 
      id number(10)    NOT NULL , 
      coligada_id number(10)    NOT NULL , 
      nota_status_id number(10)    NOT NULL , 
      numero varchar  (255)    NOT NULL , 
      numero_nf varchar  (255)   , 
      serie_nf varchar  (255)   , 
      data_emissao timestamp(0)   , 
      data_emissao_os timestamp(0)   , 
      razao_social varchar  (255)   , 
      documento varchar  (25)   , 
      valor_total binary_double   , 
      enviado_email number(10)   , 
      totvs_xml varchar(3000)   , 
      rps_xml varchar(3000)   , 
      nfs_xml varchar(3000)   , 
      obs varchar(3000)   , 
      nfs_pdf varchar(3000)   , 
      tem_comissao char   , 
      comissao binary_double   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE nota_baixada_teste( 
      id number(10)    NOT NULL , 
      nota_status_id number(10)    NOT NULL , 
      coligada_id number(10)    NOT NULL , 
      numero varchar  (255)    NOT NULL , 
      numero_nf varchar  (255)   , 
      serie_nf varchar  (255)   , 
      data_emissao timestamp(0)   , 
      razao_social varchar  (255)   , 
      documento varchar  (25)   , 
      valor_total binary_double   , 
      enviado_email number(10)   , 
      totvs_xml varchar(3000)   , 
      rps_xml varchar(3000)   , 
      nfs_xml varchar(3000)   , 
      nfs_pdf varchar(3000)   , 
      obs varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE nota_status( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
      cor varchar  (7)   , 
      icone varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE origem_contato( 
      id number(10)    NOT NULL , 
      nome varchar(3000)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pais( 
      id number(10)    NOT NULL , 
      codigo varchar  (5)   , 
      nome varchar  (255)    NOT NULL , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pessoa( 
      id number(10)    NOT NULL , 
      codigo varchar  (255)   , 
      tipo_pessoa_id number(10)    NOT NULL , 
      categoria_cliente_id number(10)   , 
      system_user_id number(10)   , 
      origem char  (2)   , 
      razao_social varchar  (500)    NOT NULL , 
      nome_fantasia varchar  (255)   , 
      cpf_cnpj varchar  (20)   , 
      rg_ie varchar  (30)   , 
      nacionalidade_id number(10)   , 
      fone varchar  (255)   , 
      email varchar  (255)   , 
      obs varchar  (1000)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
      ativo char  (1)   , 
      data_alteracao_totvs timestamp(0)   , 
      bloqueado char  (1)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pessoa_contato( 
      id number(10)    NOT NULL , 
      pessoa_id number(10)    NOT NULL , 
      nome varchar  (255)   , 
      email varchar  (255)   , 
      telefone varchar  (255)   , 
      obs varchar  (500)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      idcontato number(10)   , 
      codcoligada varchar(3000)   , 
      data_alteracao_totvs timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pessoa_endereco( 
      id number(10)    NOT NULL , 
      pessoa_id number(10)    NOT NULL , 
      cidade_id number(10)   , 
      nome varchar  (255)   , 
      principal char  (1)   , 
      cep varchar  (10)   , 
      rua varchar  (500)   , 
      numero varchar  (20)   , 
      bairro varchar  (500)   , 
      complemento varchar  (500)   , 
      data_desativacao date   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      data_alteracao_totvs timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE pessoa_grupo( 
      id number(10)    NOT NULL , 
      pessoa_id number(10)    NOT NULL , 
      grupo_id number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE prazo_atividade( 
      id number(10)    NOT NULL , 
      regras_tipo_atividade_id number(10)    NOT NULL , 
      categoria_cliente_id number(10)    NOT NULL , 
      ambos char  (1)    DEFAULT 'N' , 
      dias number(10)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE preferencia_sistema( 
      id number(10)    NOT NULL , 
      system_users_id number(10)    NOT NULL , 
      zoom number(10)    DEFAULT 100  NOT NULL , 
      menu_fixado number(10)    DEFAULT 0  NOT NULL , 
      data_criacao timestamp(0)   , 
      criacao_user_id number(10)   , 
      data_modificacao timestamp(0)   , 
      modificacao_user_id number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE produto( 
      id number(10)    NOT NULL , 
      tipo_produto_id number(10)    NOT NULL , 
      familia_produto_id number(10)    NOT NULL , 
      fornecedor_id number(10)    NOT NULL , 
      unidade_medida_id number(10)    NOT NULL , 
      fabricante_id number(10)   , 
      nome varchar  (255)    NOT NULL , 
      cod_barras varchar  (255)   , 
      preco_venda binary_double   , 
      preco_custo binary_double   , 
      peso_liquido binary_double   , 
      peso_bruto binary_double   , 
      largura binary_double   , 
      altura binary_double   , 
      volume binary_double   , 
      estoque_minimo binary_double   , 
      qtde_estoque binary_double   , 
      estoque_maximo binary_double   , 
      obs varchar  (500)   , 
      ativo char  (1)   , 
      foto varchar  (500)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE regras_tipo_atividade( 
      id number(10)    NOT NULL , 
      tipo number(10)   , 
      nome varchar(3000)   , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE representante( 
      id number(10)    NOT NULL , 
      codigo varchar  (15)    NOT NULL , 
      system_user_id number(10)   , 
      razao_social varchar  (255)    NOT NULL , 
      cpf_cnpj varchar  (20)   , 
      inscrestadual varchar  (20)   , 
      telefone varchar  (20)   , 
      ativo char  (1)   , 
      email varchar  (40)   , 
      cor varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE representante_divergente( 
      id number(10)    NOT NULL , 
      pessoa_id number(10)    NOT NULL , 
      rep_ap_id number(10)    NOT NULL , 
      rep_totvs_id number(10)    NOT NULL , 
      status number(10)    DEFAULT 0  NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE representante_totvs( 
      id number(10)    NOT NULL , 
      codigo varchar  (15)    NOT NULL , 
      razao_social varchar  (255)    NOT NULL , 
      fantasia varchar  (255)   , 
      cpf_cnpj varchar  (20)   , 
      inscrestadual varchar  (20)   , 
      cep varchar  (20)   , 
      rua varchar  (255)   , 
      numero varchar  (20)   , 
      complemento varchar(3000)   , 
      bairro varchar  (255)   , 
      cidade_id number(10)   , 
      contato varchar  (255)   , 
      telefone varchar  (20)   , 
      pais_id number(10)   , 
      percentual_comissao binary_double   , 
      fatclientedireto number(10)   , 
      ativo char  (1)   , 
      celular varchar  (20)   , 
      email varchar  (40)   , 
      codcoligada varchar(3000)   , 
      cor varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_group( 
      id number(10)    NOT NULL , 
      name varchar(3000)    NOT NULL , 
      uuid varchar  (36)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_group_program( 
      id number(10)    NOT NULL , 
      system_group_id number(10)    NOT NULL , 
      system_program_id number(10)    NOT NULL , 
      actions varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_preference( 
      id varchar  (255)    NOT NULL , 
      preference varchar(3000)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_program( 
      id number(10)    NOT NULL , 
      name varchar(3000)    NOT NULL , 
      controller varchar(3000)    NOT NULL , 
      actions varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_unit( 
      id number(10)    NOT NULL , 
      name varchar(3000)    NOT NULL , 
      connection_name varchar(3000)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_user_group( 
      id number(10)    NOT NULL , 
      system_user_id number(10)    NOT NULL , 
      system_group_id number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_user_program( 
      id number(10)    NOT NULL , 
      system_user_id number(10)    NOT NULL , 
      system_program_id number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_users( 
      id number(10)    NOT NULL , 
      name varchar(3000)    NOT NULL , 
      login varchar(3000)    NOT NULL , 
      password varchar(3000)    NOT NULL , 
      email varchar(3000)   , 
      frontpage_id number(10)   , 
      system_unit_id number(10)   , 
      active char  (1)   , 
      accepted_term_policy_at varchar(3000)   , 
      accepted_term_policy char  (1)   , 
      two_factor_enabled char  (1)    DEFAULT 'N' , 
      two_factor_type varchar  (100)   , 
      two_factor_secret varchar  (255)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE system_user_unit( 
      id number(10)    NOT NULL , 
      system_user_id number(10)    NOT NULL , 
      system_unit_id number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_atividade( 
      id number(10)    NOT NULL , 
      nome varchar(3000)   , 
      cor varchar(3000)   , 
      icone varchar(3000)   , 
      regras_tipo_atividade_id number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_atividade_interacao( 
      id number(10)    NOT NULL , 
      tipo_atividade_id number(10)    NOT NULL , 
      tipo_interacao_id number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_interacao( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_pessoa( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
      sigla char  (2)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE tipo_produto( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE transportadora( 
      id number(10)    NOT NULL , 
      created_at timestamp(0)   , 
      updated_at timestamp(0)   , 
      deleted_at timestamp(0)   , 
      codtra varchar  (5)    NOT NULL , 
      nome varchar  (40)   , 
      rua varchar  (100)   , 
      numero varchar  (8)   , 
      complemento varchar  (100)   , 
      bairro varchar  (100)   , 
      cidade_id number(10)   , 
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
 PRIMARY KEY (id)) ; 

CREATE TABLE unidade_medida( 
      id number(10)    NOT NULL , 
      nome varchar  (255)    NOT NULL , 
      sigla char  (2)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE vendedor( 
      system_user_id number(10)   , 
      id number(10)    NOT NULL , 
      codigo varchar  (16)    NOT NULL , 
      razao_social varchar  (255)    NOT NULL , 
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
      descmaximo binary_double   , 
      cor varchar(3000)   , 
      codcoligada varchar(3000)   , 
 PRIMARY KEY (id)) ; 

 
  
 ALTER TABLE centro_custo_nota ADD CONSTRAINT fk_centro_custo_nota_1 FOREIGN KEY (centro_custo_id) references centro_custo(id); 
ALTER TABLE centro_custo_nota ADD CONSTRAINT fk_centro_custo_nota_2 FOREIGN KEY (nota_baixada_id) references nota_baixada(id); 
ALTER TABLE cidade ADD CONSTRAINT fk_cidade_1 FOREIGN KEY (estado_id) references estado(id); 
ALTER TABLE comissao_repres ADD CONSTRAINT fk_comissao_repres_1 FOREIGN KEY (representante_id) references representante(id); 
ALTER TABLE comissao_repres_excecao ADD CONSTRAINT fk_comissao_repres_excecao_1 FOREIGN KEY (representante_id) references representante(id); 
ALTER TABLE comissao_repres_excecao ADD CONSTRAINT fk_comissao_repres_excecao_2 FOREIGN KEY (pessoa_id) references pessoa(id); 
ALTER TABLE complemento ADD CONSTRAINT fk_complemento_7 FOREIGN KEY (representante_id) references representante(id); 
ALTER TABLE complemento ADD CONSTRAINT fk_complemento_4 FOREIGN KEY (transportadora_id) references transportadora(id); 
ALTER TABLE complemento ADD CONSTRAINT fk_complemento_5 FOREIGN KEY (transportadora1_id) references transportadora(id); 
ALTER TABLE complemento ADD CONSTRAINT fk_fcfo_def_1 FOREIGN KEY (vendedor_id) references vendedor(id); 
ALTER TABLE complemento ADD CONSTRAINT fk_fcfo_def_2 FOREIGN KEY (pessoa_id) references pessoa(id); 
ALTER TABLE configuracao_email ADD CONSTRAINT fk_configuracao_email_1 FOREIGN KEY (system_users_id) references system_users(id); 
ALTER TABLE controle_nota ADD CONSTRAINT fk_controle_nota_1 FOREIGN KEY (nota_baixada_id) references nota_baixada(id); 
ALTER TABLE estado ADD CONSTRAINT fk_estado_1 FOREIGN KEY (pais_id) references pais(id); 
ALTER TABLE interacao ADD CONSTRAINT fk_interacao_5 FOREIGN KEY (cliente_id) references pessoa(id); 
ALTER TABLE interacao ADD CONSTRAINT fk_interacao_5 FOREIGN KEY (vendedor_id) references representante(id); 
ALTER TABLE interacao ADD CONSTRAINT fk_interacao_3 FOREIGN KEY (origem_contato_id) references origem_contato(id); 
ALTER TABLE interacao ADD CONSTRAINT fk_interacao_4 FOREIGN KEY (etapa_interacao_id) references etapa_interacao(id); 
ALTER TABLE interacao ADD CONSTRAINT fk_interacao_5 FOREIGN KEY (tipo_interacao_id) references tipo_interacao(id); 
ALTER TABLE interacao_arquivo ADD CONSTRAINT fk_interacao_arquivo_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_atividade ADD CONSTRAINT fk_interacao_atividade_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_atividade ADD CONSTRAINT fk_interacao_atividade_2 FOREIGN KEY (tipo_atividade_id) references tipo_atividade(id); 
ALTER TABLE interacao_atividade ADD CONSTRAINT fk_interacao_atividade_3 FOREIGN KEY (estado_atividade_id) references estado_atividade(id); 
ALTER TABLE interacao_atividade_revisao ADD CONSTRAINT fk_interacao_atividade_revisao_2 FOREIGN KEY (interacao_atividade_id) references interacao_atividade(id); 
ALTER TABLE interacao_atividade_revisao ADD CONSTRAINT fk_interacao_atividade_revisao_2 FOREIGN KEY (system_users_id) references system_users(id); 
ALTER TABLE interacao_historico_arquivo ADD CONSTRAINT fk_interacao_historico_arquivo_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_historico_arquivo ADD CONSTRAINT fk_interacao_historico_arquivo_2 FOREIGN KEY (movimentacao_id) references movimentacao(id); 
ALTER TABLE interacao_historico_atividade ADD CONSTRAINT fk_interacao_historico_atividade_4 FOREIGN KEY (estado_atividade_id) references estado_atividade(id); 
ALTER TABLE interacao_historico_atividade ADD CONSTRAINT fk_interacao_historico_atividade_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_historico_atividade ADD CONSTRAINT fk_interacao_historico_atividade_2 FOREIGN KEY (movimentacao_id) references movimentacao(id); 
ALTER TABLE interacao_historico_atividade ADD CONSTRAINT fk_interacao_historico_atividade_3 FOREIGN KEY (tipo_atividade_id) references tipo_atividade(id); 
ALTER TABLE interacao_historico_etapa ADD CONSTRAINT fk_interacao_historico_etapa_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_historico_etapa ADD CONSTRAINT fk_interacao_historico_etapa_2 FOREIGN KEY (etapa_interacao_id) references etapa_interacao(id); 
ALTER TABLE interacao_historico_observacao ADD CONSTRAINT fk_interacao_historico_observacao_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_historico_observacao ADD CONSTRAINT fk_interacao_historico_observacao_2 FOREIGN KEY (movimentacao_id) references movimentacao(id); 
ALTER TABLE interacao_item ADD CONSTRAINT fk_interacao_item_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_item ADD CONSTRAINT fk_interacao_item_2 FOREIGN KEY (produto_id) references produto(id); 
ALTER TABLE interacao_localizacao ADD CONSTRAINT fk_interacao_localizacao_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE interacao_observacao ADD CONSTRAINT fk_interacao_observacao_1 FOREIGN KEY (interacao_id) references interacao(id); 
ALTER TABLE log_crontab ADD CONSTRAINT fk_log_crontab_1 FOREIGN KEY (system_unit_id) references system_unit(id); 
ALTER TABLE nacionalidade ADD CONSTRAINT fk_nacionalidade_1 FOREIGN KEY (pais_id) references pais(id); 
ALTER TABLE nota_baixada ADD CONSTRAINT fk_nota_baixada_2 FOREIGN KEY (nota_status_id) references nota_status(id); 
ALTER TABLE nota_baixada ADD CONSTRAINT fk_nota_baixada_1 FOREIGN KEY (coligada_id) references coligada(id); 
ALTER TABLE nota_baixada_teste ADD CONSTRAINT fk_nota_baixada_teste_1 FOREIGN KEY (nota_status_id) references nota_status(id); 
ALTER TABLE nota_baixada_teste ADD CONSTRAINT fk_nota_baixada_teste_2 FOREIGN KEY (coligada_id) references coligada(id); 
ALTER TABLE pessoa ADD CONSTRAINT fk_pessoa_1 FOREIGN KEY (tipo_pessoa_id) references tipo_pessoa(id); 
ALTER TABLE pessoa ADD CONSTRAINT fk_pessoa_2 FOREIGN KEY (categoria_cliente_id) references categoria_cliente(id); 
ALTER TABLE pessoa ADD CONSTRAINT fk_pessoa_3 FOREIGN KEY (system_user_id) references system_users(id); 
ALTER TABLE pessoa ADD CONSTRAINT fk_pessoa_4 FOREIGN KEY (nacionalidade_id) references nacionalidade(id); 
ALTER TABLE pessoa_contato ADD CONSTRAINT fk_pessoa_contato_1 FOREIGN KEY (pessoa_id) references pessoa(id); 
ALTER TABLE pessoa_endereco ADD CONSTRAINT fk_pessoa_endereco_1 FOREIGN KEY (pessoa_id) references pessoa(id); 
ALTER TABLE pessoa_endereco ADD CONSTRAINT fk_pessoa_endereco_2 FOREIGN KEY (cidade_id) references cidade(id); 
ALTER TABLE pessoa_grupo ADD CONSTRAINT fk_pessoa_grupo_1 FOREIGN KEY (pessoa_id) references pessoa(id); 
ALTER TABLE pessoa_grupo ADD CONSTRAINT fk_pessoa_grupo_2 FOREIGN KEY (grupo_id) references grupo(id); 
ALTER TABLE prazo_atividade ADD CONSTRAINT fk_prazo_atividade_1 FOREIGN KEY (regras_tipo_atividade_id) references regras_tipo_atividade(id); 
ALTER TABLE prazo_atividade ADD CONSTRAINT fk_prazo_atividade_2 FOREIGN KEY (categoria_cliente_id) references categoria_cliente(id); 
ALTER TABLE preferencia_sistema ADD CONSTRAINT fk_preferencia_sistema_1 FOREIGN KEY (system_users_id) references system_users(id); 
ALTER TABLE produto ADD CONSTRAINT fk_produto_1 FOREIGN KEY (tipo_produto_id) references tipo_produto(id); 
ALTER TABLE produto ADD CONSTRAINT fk_produto_2 FOREIGN KEY (familia_produto_id) references familia_produto(id); 
ALTER TABLE produto ADD CONSTRAINT fk_produto_3 FOREIGN KEY (fabricante_id) references fabricante(id); 
ALTER TABLE produto ADD CONSTRAINT fk_produto_4 FOREIGN KEY (unidade_medida_id) references unidade_medida(id); 
ALTER TABLE produto ADD CONSTRAINT fk_produto_5 FOREIGN KEY (fornecedor_id) references pessoa(id); 
ALTER TABLE representante ADD CONSTRAINT fk_representante_1 FOREIGN KEY (system_user_id) references system_users(id); 
ALTER TABLE representante_divergente ADD CONSTRAINT fk_representante_divergente_1 FOREIGN KEY (rep_ap_id) references representante(id); 
ALTER TABLE representante_divergente ADD CONSTRAINT fk_representante_divergente_2 FOREIGN KEY (rep_totvs_id) references representante(id); 
ALTER TABLE representante_divergente ADD CONSTRAINT fk_representante_divergente_3 FOREIGN KEY (pessoa_id) references pessoa(id); 
ALTER TABLE system_group_program ADD CONSTRAINT fk_system_group_program_1 FOREIGN KEY (system_program_id) references system_program(id); 
ALTER TABLE system_group_program ADD CONSTRAINT fk_system_group_program_2 FOREIGN KEY (system_group_id) references system_group(id); 
ALTER TABLE system_user_group ADD CONSTRAINT fk_system_user_group_1 FOREIGN KEY (system_group_id) references system_group(id); 
ALTER TABLE system_user_group ADD CONSTRAINT fk_system_user_group_2 FOREIGN KEY (system_user_id) references system_users(id); 
ALTER TABLE system_user_program ADD CONSTRAINT fk_system_user_program_1 FOREIGN KEY (system_program_id) references system_program(id); 
ALTER TABLE system_user_program ADD CONSTRAINT fk_system_user_program_2 FOREIGN KEY (system_user_id) references system_users(id); 
ALTER TABLE system_users ADD CONSTRAINT fk_system_user_1 FOREIGN KEY (system_unit_id) references system_unit(id); 
ALTER TABLE system_users ADD CONSTRAINT fk_system_user_2 FOREIGN KEY (frontpage_id) references system_program(id); 
ALTER TABLE system_user_unit ADD CONSTRAINT fk_system_user_unit_1 FOREIGN KEY (system_user_id) references system_users(id); 
ALTER TABLE system_user_unit ADD CONSTRAINT fk_system_user_unit_2 FOREIGN KEY (system_unit_id) references system_unit(id); 
ALTER TABLE tipo_atividade ADD CONSTRAINT fk_tipo_atividade_1 FOREIGN KEY (regras_tipo_atividade_id) references regras_tipo_atividade(id); 
ALTER TABLE tipo_atividade_interacao ADD CONSTRAINT fk_tipo_atividade_interacao_1 FOREIGN KEY (tipo_atividade_id) references tipo_atividade(id); 
ALTER TABLE tipo_atividade_interacao ADD CONSTRAINT fk_tipo_atividade_interacao_2 FOREIGN KEY (tipo_interacao_id) references tipo_interacao(id); 
ALTER TABLE transportadora ADD CONSTRAINT fk_ttra_1 FOREIGN KEY (cidade_id) references cidade(id); 
ALTER TABLE vendedor ADD CONSTRAINT fk_vendedor_1 FOREIGN KEY (system_user_id) references system_users(id); 
 CREATE SEQUENCE api_error_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER api_error_id_seq_tr 

BEFORE INSERT ON api_error FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT api_error_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE categoria_cliente_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER categoria_cliente_id_seq_tr 

BEFORE INSERT ON categoria_cliente FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT categoria_cliente_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE centro_custo_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER centro_custo_id_seq_tr 

BEFORE INSERT ON centro_custo FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT centro_custo_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE centro_custo_nota_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER centro_custo_nota_id_seq_tr 

BEFORE INSERT ON centro_custo_nota FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT centro_custo_nota_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE cep_cache_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER cep_cache_id_seq_tr 

BEFORE INSERT ON cep_cache FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT cep_cache_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE cidade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER cidade_id_seq_tr 

BEFORE INSERT ON cidade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT cidade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE coligada_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER coligada_id_seq_tr 

BEFORE INSERT ON coligada FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT coligada_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE comissao_repres_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER comissao_repres_id_seq_tr 

BEFORE INSERT ON comissao_repres FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT comissao_repres_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE comissao_repres_excecao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER comissao_repres_excecao_id_seq_tr 

BEFORE INSERT ON comissao_repres_excecao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT comissao_repres_excecao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE complemento_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER complemento_id_seq_tr 

BEFORE INSERT ON complemento FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT complemento_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE condicao_pagamento_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER condicao_pagamento_id_seq_tr 

BEFORE INSERT ON condicao_pagamento FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT condicao_pagamento_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE configuracao_email_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER configuracao_email_id_seq_tr 

BEFORE INSERT ON configuracao_email FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT configuracao_email_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE controle_nota_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER controle_nota_id_seq_tr 

BEFORE INSERT ON controle_nota FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT controle_nota_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE email_template_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER email_template_id_seq_tr 

BEFORE INSERT ON email_template FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT email_template_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE estado_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER estado_id_seq_tr 

BEFORE INSERT ON estado FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT estado_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE estado_atividade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER estado_atividade_id_seq_tr 

BEFORE INSERT ON estado_atividade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT estado_atividade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE etapa_interacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER etapa_interacao_id_seq_tr 

BEFORE INSERT ON etapa_interacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT etapa_interacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE fabricante_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER fabricante_id_seq_tr 

BEFORE INSERT ON fabricante FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT fabricante_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE familia_produto_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER familia_produto_id_seq_tr 

BEFORE INSERT ON familia_produto FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT familia_produto_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE grupo_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER grupo_id_seq_tr 

BEFORE INSERT ON grupo FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT grupo_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_id_seq_tr 

BEFORE INSERT ON interacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_arquivo_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_arquivo_id_seq_tr 

BEFORE INSERT ON interacao_arquivo FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_arquivo_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_atividade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_atividade_id_seq_tr 

BEFORE INSERT ON interacao_atividade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_atividade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_atividade_revisao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_atividade_revisao_id_seq_tr 

BEFORE INSERT ON interacao_atividade_revisao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_atividade_revisao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_historico_arquivo_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_historico_arquivo_id_seq_tr 

BEFORE INSERT ON interacao_historico_arquivo FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_historico_arquivo_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_historico_atividade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_historico_atividade_id_seq_tr 

BEFORE INSERT ON interacao_historico_atividade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_historico_atividade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_historico_etapa_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_historico_etapa_id_seq_tr 

BEFORE INSERT ON interacao_historico_etapa FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_historico_etapa_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_historico_observacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_historico_observacao_id_seq_tr 

BEFORE INSERT ON interacao_historico_observacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_historico_observacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_item_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_item_id_seq_tr 

BEFORE INSERT ON interacao_item FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_item_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_localizacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_localizacao_id_seq_tr 

BEFORE INSERT ON interacao_localizacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_localizacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE interacao_observacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER interacao_observacao_id_seq_tr 

BEFORE INSERT ON interacao_observacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT interacao_observacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE log_crontab_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER log_crontab_id_seq_tr 

BEFORE INSERT ON log_crontab FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT log_crontab_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE movimentacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER movimentacao_id_seq_tr 

BEFORE INSERT ON movimentacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT movimentacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE nacionalidade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER nacionalidade_id_seq_tr 

BEFORE INSERT ON nacionalidade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT nacionalidade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE nota_baixada_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER nota_baixada_id_seq_tr 

BEFORE INSERT ON nota_baixada FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT nota_baixada_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE nota_baixada_teste_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER nota_baixada_teste_id_seq_tr 

BEFORE INSERT ON nota_baixada_teste FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT nota_baixada_teste_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE nota_status_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER nota_status_id_seq_tr 

BEFORE INSERT ON nota_status FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT nota_status_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE origem_contato_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER origem_contato_id_seq_tr 

BEFORE INSERT ON origem_contato FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT origem_contato_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE pais_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER pais_id_seq_tr 

BEFORE INSERT ON pais FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT pais_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE pessoa_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER pessoa_id_seq_tr 

BEFORE INSERT ON pessoa FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT pessoa_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE pessoa_contato_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER pessoa_contato_id_seq_tr 

BEFORE INSERT ON pessoa_contato FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT pessoa_contato_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE pessoa_endereco_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER pessoa_endereco_id_seq_tr 

BEFORE INSERT ON pessoa_endereco FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT pessoa_endereco_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE pessoa_grupo_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER pessoa_grupo_id_seq_tr 

BEFORE INSERT ON pessoa_grupo FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT pessoa_grupo_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE prazo_atividade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER prazo_atividade_id_seq_tr 

BEFORE INSERT ON prazo_atividade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT prazo_atividade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE preferencia_sistema_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER preferencia_sistema_id_seq_tr 

BEFORE INSERT ON preferencia_sistema FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT preferencia_sistema_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE produto_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER produto_id_seq_tr 

BEFORE INSERT ON produto FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT produto_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE regras_tipo_atividade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER regras_tipo_atividade_id_seq_tr 

BEFORE INSERT ON regras_tipo_atividade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT regras_tipo_atividade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE representante_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER representante_id_seq_tr 

BEFORE INSERT ON representante FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT representante_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE representante_divergente_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER representante_divergente_id_seq_tr 

BEFORE INSERT ON representante_divergente FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT representante_divergente_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE representante_totvs_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER representante_totvs_id_seq_tr 

BEFORE INSERT ON representante_totvs FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT representante_totvs_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tipo_atividade_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tipo_atividade_id_seq_tr 

BEFORE INSERT ON tipo_atividade FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tipo_atividade_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tipo_atividade_interacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tipo_atividade_interacao_id_seq_tr 

BEFORE INSERT ON tipo_atividade_interacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tipo_atividade_interacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tipo_interacao_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tipo_interacao_id_seq_tr 

BEFORE INSERT ON tipo_interacao FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tipo_interacao_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tipo_pessoa_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tipo_pessoa_id_seq_tr 

BEFORE INSERT ON tipo_pessoa FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tipo_pessoa_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE tipo_produto_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER tipo_produto_id_seq_tr 

BEFORE INSERT ON tipo_produto FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT tipo_produto_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE transportadora_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER transportadora_id_seq_tr 

BEFORE INSERT ON transportadora FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT transportadora_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE unidade_medida_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER unidade_medida_id_seq_tr 

BEFORE INSERT ON unidade_medida FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT unidade_medida_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE vendedor_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER vendedor_id_seq_tr 

BEFORE INSERT ON vendedor FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT vendedor_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
 
 CREATE VIEW view_classificacao AS SELECT
    x.id as id,
    x.categoria as categoria,
    x.representante as representante,
    x.codigo_cliente as codigo_cliente,
    x.nome_cliente as nome_cliente,
    x.cidade as cidade,
    x.uf as uf,
    x.s_id as s_id,

    CASE
        WHEN x.tipo1 = 9999 THEN 'Não Aplicável'
        ELSE CAST(x.tipo1 AS varchar)
    END as tipo1,

    CASE
        WHEN x.tipo2 = 9999 THEN 'Não Aplicável'
        ELSE CAST(x.tipo2 AS varchar)
    END as tipo2,

    CASE
        WHEN x.tipo1 = 9999 THEN 'N/A'
        WHEN x.tipo1 > 0 THEN 'Sim'
        ELSE 'Não'
    END AS dentro_prazo_tipo1,

    CASE
        WHEN x.tipo2 = 9999 THEN 'N/A'
        WHEN x.tipo2 > 0 THEN 'Sim'
        ELSE 'Não'
    END AS dentro_prazo_tipo2,

    CASE
        WHEN x.categoria IN ('D', 'E') AND (x.tipo1 > 0 OR x.tipo2 > 0) THEN 'Sim'
        WHEN x.tipo1 = 9999 AND x.tipo2 = 9999 THEN 'Não'
        WHEN (x.tipo1 > 0 OR x.tipo1 = 9999)
         AND (x.tipo2 > 0 OR x.tipo2 = 9999)
        THEN 'Sim'
        ELSE 'Não'
    END AS dentro_prazo_ambos

FROM
(
    SELECT
        p.id AS id,
        cc.nome AS categoria,
        r.razao_social AS representante,
        p.codigo AS codigo_cliente,
        p.razao_social AS nome_cliente,
        cid.nome AS cidade,
        est.sigla AS uf,
        r.system_user_id AS s_id,

        CASE
            WHEN ult_tipo1_real.ultima_atividade IS NOT NULL THEN 1
            ELSE 0
        END AS tem_tipo1_real,

        CASE
            WHEN p.categoria_cliente_id IS NULL THEN 9999

            WHEN cc.nome IN ('D', 'E') AND pr2.dias IS NULL THEN 9999
            WHEN cc.nome IN ('D', 'E') AND ult_tipo1_real.ultima_atividade IS NULL THEN 0
            WHEN cc.nome IN ('D', 'E') AND pr2.dias - (CURRENT_DATE - ult_tipo1_real.ultima_atividade::date) < 0 THEN 0
            WHEN cc.nome IN ('D', 'E') THEN pr2.dias - (CURRENT_DATE - ult_tipo1_real.ultima_atividade::date)

            WHEN pr1.regras_tipo_atividade_id IS NULL THEN 9999
            WHEN ult1.ultima_atividade IS NULL THEN 0
            WHEN pr1.dias - (CURRENT_DATE - ult1.ultima_atividade::date) < 0 THEN 0
            ELSE pr1.dias - (CURRENT_DATE - ult1.ultima_atividade::date)
        END AS tipo1,

        CASE
            WHEN p.categoria_cliente_id IS NULL THEN 9999

            WHEN pr2.regras_tipo_atividade_id IS NULL THEN 9999

            /*
            * Se o Contato estiver com ambos = S,
            * usa a atividade mais recente entre Física e Contato.
            *
            * A atividade Física não precisa ter um prazo próprio cadastrado.
            * O prazo utilizado será o prazo do Contato: pr2.dias.
            */
            WHEN COALESCE(pr2.ambos, 'N') = 'S' THEN
                CASE
                    WHEN ult_tipo1_real.ultima_atividade IS NULL
                    AND ult2.ultima_atividade IS NULL
                    THEN 0

                    ELSE GREATEST(
                        pr2.dias - (
                            CURRENT_DATE
                            - GREATEST(
                                ult_tipo1_real.ultima_atividade,
                                ult2.ultima_atividade
                            )::date
                        ),
                        0
                    )
                END

            /*
            * Se ambos = N, somente a atividade de Contato
            * renova o prazo de Contato.
            */
            WHEN ult2.ultima_atividade IS NULL THEN 0

            ELSE GREATEST(
                pr2.dias - (
                    CURRENT_DATE - ult2.ultima_atividade::date
                ),
                0
            )
        END AS tipo2

    FROM pessoa p

    LEFT JOIN categoria_cliente cc
        ON cc.id = p.categoria_cliente_id

    LEFT JOIN (
        SELECT x.pessoa_id, x.representante_id
        FROM (
            SELECT
                c.pessoa_id,
                c.representante_id,
                ROW_NUMBER() OVER (
                    PARTITION BY c.pessoa_id
                    ORDER BY c.created_at DESC NULLS LAST, c.id DESC
                ) AS rn
            FROM complemento c
        ) x
        WHERE x.rn = 1
    ) comp
        ON comp.pessoa_id = p.id

    LEFT JOIN representante r
        ON r.id = comp.representante_id

    LEFT JOIN (
        SELECT x.pessoa_id, x.cidade_id
        FROM (
            SELECT
                pe.pessoa_id,
                pe.cidade_id,
                ROW_NUMBER() OVER (
                    PARTITION BY pe.pessoa_id
                    ORDER BY pe.id DESC
                ) AS rn
            FROM pessoa_endereco pe
            WHERE pe.principal = 'S'
        ) x
        WHERE x.rn = 1
    ) pe
        ON pe.pessoa_id = p.id

    LEFT JOIN cidade cid
        ON cid.id = pe.cidade_id

    LEFT JOIN estado est
        ON est.id = cid.estado_id

    LEFT JOIN (
        SELECT
            z.categoria_cliente_id,
            z.regras_tipo_atividade_id,
            z.dias
        FROM (
            SELECT
                pa.categoria_cliente_id,
                pa.regras_tipo_atividade_id,
                pa.dias,
                ROW_NUMBER() OVER (
                    PARTITION BY pa.categoria_cliente_id
                    ORDER BY pa.id DESC
                ) AS rn
            FROM prazo_atividade pa
            INNER JOIN regras_tipo_atividade rta
                ON rta.id = pa.regras_tipo_atividade_id
            WHERE rta.tipo = 1
        ) z
        WHERE z.rn = 1
    ) pr1
        ON pr1.categoria_cliente_id = p.categoria_cliente_id

        LEFT JOIN (
        SELECT
            z.categoria_cliente_id,
            z.regras_tipo_atividade_id,
            z.dias,
            z.ambos
        FROM (
            SELECT
                pa.categoria_cliente_id,
                pa.regras_tipo_atividade_id,
                pa.dias,
                pa.ambos,
                ROW_NUMBER() OVER (
                    PARTITION BY pa.categoria_cliente_id
                    ORDER BY pa.id DESC
                ) AS rn
            FROM prazo_atividade pa
            INNER JOIN regras_tipo_atividade rta
                ON rta.id = pa.regras_tipo_atividade_id
            WHERE rta.tipo = 2
        ) z
        WHERE z.rn = 1
    ) pr2
        ON pr2.categoria_cliente_id = p.categoria_cliente_id

    LEFT JOIN (
        SELECT
            i.cliente_id,
            ta.regras_tipo_atividade_id,
            MAX(ia.horario_final) AS ultima_atividade
        FROM interacao i
        INNER JOIN interacao_atividade ia
            ON ia.interacao_id = i.id
        INNER JOIN tipo_atividade ta
            ON ta.id = ia.tipo_atividade_id
        WHERE ia.estado_atividade_id = 2
          AND ta.regras_tipo_atividade_id IS NOT NULL
          AND ia.horario_final IS NOT NULL
        GROUP BY i.cliente_id, ta.regras_tipo_atividade_id
    ) ult1
        ON ult1.cliente_id = p.id
       AND ult1.regras_tipo_atividade_id = pr1.regras_tipo_atividade_id

    LEFT JOIN (
        SELECT
            i.cliente_id,
            ta.regras_tipo_atividade_id,
            MAX(ia.horario_final) AS ultima_atividade
        FROM interacao i
        INNER JOIN interacao_atividade ia
            ON ia.interacao_id = i.id
        INNER JOIN tipo_atividade ta
            ON ta.id = ia.tipo_atividade_id
        WHERE ia.estado_atividade_id = 2
          AND ta.regras_tipo_atividade_id IS NOT NULL
          AND ia.horario_final IS NOT NULL
        GROUP BY i.cliente_id, ta.regras_tipo_atividade_id
    ) ult2
        ON ult2.cliente_id = p.id
       AND ult2.regras_tipo_atividade_id = pr2.regras_tipo_atividade_id

    LEFT JOIN (
        SELECT
            i.cliente_id,
            MAX(ia.horario_final) AS ultima_atividade
        FROM interacao i
        INNER JOIN interacao_atividade ia
            ON ia.interacao_id = i.id
        INNER JOIN tipo_atividade ta
            ON ta.id = ia.tipo_atividade_id
        INNER JOIN regras_tipo_atividade rta
            ON rta.id = ta.regras_tipo_atividade_id
        WHERE ia.estado_atividade_id = 2
          AND ia.horario_final IS NOT NULL
          AND rta.tipo = 1
        GROUP BY i.cliente_id
    ) ult_tipo1_real
        ON ult_tipo1_real.cliente_id = p.id

    WHERE comp.representante_id IS NOT NULL
) x

ORDER BY
    x.categoria ASC,
    x.codigo_cliente ASC;; 

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
    rep.razao_social AS "representante_razao",
    cd.id as cidade_id,
    uf.id as estado_id
    
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
	rep.razao_social,
  cd.id,
  uf.id
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

CREATE VIEW view_comissao_repres AS WITH notas AS (
    SELECT
        nb.id,
        nb.coligada_id,
        nb.data_emissao,
        nb.data_emissao_os,
        nb.numero,
        nb.valor_total,
        nb.tem_comissao,
        nb.comissao,
        regexp_replace(COALESCE(nb.documento, ''), '[^0-9]', '', 'g') AS documento_limpo
    FROM nota_baixada nb
    WHERE nb.coligada_id NOT IN (3)
      AND nb.data_emissao_os IS NOT NULL
      AND nb.tem_comissao IS NOT NULL
),

documentos_notas AS (
    SELECT DISTINCT
        documento_limpo
    FROM notas
    WHERE documento_limpo <> ''
),

clientes AS (
    SELECT DISTINCT ON (
        regexp_replace(COALESCE(p.cpf_cnpj, ''), '[^0-9]', '', 'g')
    )
        p.id AS pessoa_id,
        p.codigo AS codigo_cliente,
        regexp_replace(COALESCE(p.cpf_cnpj, ''), '[^0-9]', '', 'g') AS documento_limpo,
        COALESCE(NULLIF(p.nome_fantasia, ''), p.razao_social) AS fantasia,
        p.categoria_cliente_id
    FROM pessoa p
    INNER JOIN documentos_notas dn
        ON dn.documento_limpo = regexp_replace(COALESCE(p.cpf_cnpj, ''), '[^0-9]', '', 'g')
    WHERE p.deleted_at IS NULL
      AND regexp_replace(COALESCE(p.cpf_cnpj, ''), '[^0-9]', '', 'g') <> ''
    ORDER BY
        regexp_replace(COALESCE(p.cpf_cnpj, ''), '[^0-9]', '', 'g'),
        p.id DESC
),

representantes AS (
    SELECT DISTINCT ON (comp.pessoa_id)
        comp.pessoa_id,
        r.razao_social AS representante
    FROM complemento comp
    INNER JOIN clientes c
        ON c.pessoa_id = comp.pessoa_id
    INNER JOIN representante r
        ON r.id = comp.representante_id
    WHERE comp.deleted_at IS NULL
      AND COALESCE(comp.representante_id, 0) <> 0
    ORDER BY
        comp.pessoa_id,
        comp.id DESC
)

SELECT
    nb.id                              AS id,
    nb.coligada_id                     AS coligada_id,
    nb.data_emissao                    AS data_emissao,
    nb.data_emissao_os                 AS data_emissao_os,

    c.codigo_cliente                   AS codigo_cliente,
    c.fantasia                         AS fantasia,
    cc.nome                            AS categoria_cliente,
    rep.representante                  AS representante,

    nb.numero                          AS numero_nota,
    nb.valor_total                     AS valor,
    nb.tem_comissao                    AS tem_comissao,
    nb.comissao                        AS comissao

FROM notas nb

LEFT JOIN clientes c
    ON c.documento_limpo = nb.documento_limpo

LEFT JOIN categoria_cliente cc
    ON cc.id = c.categoria_cliente_id

LEFT JOIN representantes rep
    ON rep.pessoa_id = c.pessoa_id;; 

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
 
