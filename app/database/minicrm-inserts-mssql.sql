SET IDENTITY_INSERT cidade ON; 

INSERT INTO cidade (id,estado_id,cod_municipio,nome,codigo_ibge) VALUES (1,1,null,'Lajeado','123123'); 

SET IDENTITY_INSERT cidade OFF; 

SET IDENTITY_INSERT email_template ON; 

INSERT INTO email_template (id,titulo,mensagem,created_at,updated_at,deleted_at,assunto,conteudo_arquivo) VALUES (1,'Sua proposta está vencendo','Olá {nome},<br><br>A proposta que lhe enviamos está próxima de vencer.<br><br>Estamos enviando essa mensagem para lhe falar que conseguiremos manter os preços apenas por mais 2 dias. Caso queira confirmar a compra e aproveitar o valor atual, favor entrar em contato para que possamos dar prosseguimento à venda.',null,null,null,null,null); 

SET IDENTITY_INSERT email_template OFF; 

SET IDENTITY_INSERT estado ON; 

INSERT INTO estado (id,pais_id,nome,sigla,codigo_ibge) VALUES (1,1,'Rio Grande do Sul','RS',''); 

SET IDENTITY_INSERT estado OFF; 

SET IDENTITY_INSERT estado_atividade ON; 

INSERT INTO estado_atividade (id,nome,cor) VALUES (1,'Em Andamento','#FFD700'); 

INSERT INTO estado_atividade (id,nome,cor) VALUES (2,'Concluido','#32CD32'); 

SET IDENTITY_INSERT estado_atividade OFF; 

SET IDENTITY_INSERT etapa_interacao ON; 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (1,'Prospectar','#1abc9c',1,'','T','T','T'); 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (2,'Qualificar','#2ecc71',2,'','T','T','T'); 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (3,'Levantar necessidades','#3498db',3,'','T','T','T'); 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (4,'Elaborar proposta','#9b59b6',4,'','T','T','T'); 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (5,'Iniciar interação','#f1c40f',5,'','T','T','T'); 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (6,'Interação finalizada','#2ecc71',6,'','T','F','F'); 

INSERT INTO etapa_interacao (id,nome,cor,ordem,roteiro,kanban,permite_edicao,permite_exclusao) VALUES (7,'Interação cancelada','#c0392b',7,'','F','F','F'); 

SET IDENTITY_INSERT etapa_interacao OFF; 

SET IDENTITY_INSERT fabricante ON; 

INSERT INTO fabricante (id,nome) VALUES (1,'Apple'); 

INSERT INTO fabricante (id,nome) VALUES (2,'LG'); 

INSERT INTO fabricante (id,nome) VALUES (3,'Samsung'); 

INSERT INTO fabricante (id,nome) VALUES (4,'Sony'); 

INSERT INTO fabricante (id,nome) VALUES (5,'Nikon'); 

INSERT INTO fabricante (id,nome) VALUES (6,'Dell'); 

SET IDENTITY_INSERT fabricante OFF; 

SET IDENTITY_INSERT familia_produto ON; 

INSERT INTO familia_produto (id,nome) VALUES (1,'Games'); 

INSERT INTO familia_produto (id,nome) VALUES (2,'Cadeiras'); 

INSERT INTO familia_produto (id,nome) VALUES (3,'Computadores'); 

INSERT INTO familia_produto (id,nome) VALUES (4,'Tablets'); 

INSERT INTO familia_produto (id,nome) VALUES (5,'Smartphones'); 

INSERT INTO familia_produto (id,nome) VALUES (6,'Tvs'); 

INSERT INTO familia_produto (id,nome) VALUES (7,'Audio'); 

INSERT INTO familia_produto (id,nome) VALUES (8,'Camêras'); 

SET IDENTITY_INSERT familia_produto OFF; 

SET IDENTITY_INSERT grupo ON; 

INSERT INTO grupo (id,nome) VALUES (1,'Vendedor'); 

INSERT INTO grupo (id,nome) VALUES (2,'Cliente'); 

INSERT INTO grupo (id,nome) VALUES (3,'Fornecedor'); 

INSERT INTO grupo (id,nome) VALUES (4,'Representante'); 

SET IDENTITY_INSERT grupo OFF; 

SET IDENTITY_INSERT movimentacao ON; 

INSERT INTO movimentacao (id,nome) VALUES (1,'Criado'); 

INSERT INTO movimentacao (id,nome) VALUES (2,'Alterado'); 

INSERT INTO movimentacao (id,nome) VALUES (3,'Excluido'); 

SET IDENTITY_INSERT movimentacao OFF; 

SET IDENTITY_INSERT nota_status ON; 

INSERT INTO nota_status (id,nome,cor,icone) VALUES (1,'Autorizada','00ff00',''); 

INSERT INTO nota_status (id,nome,cor,icone) VALUES (2,'Erro','ffff00',''); 

INSERT INTO nota_status (id,nome,cor,icone) VALUES (3,'Cancelada','ff0000',''); 

SET IDENTITY_INSERT nota_status OFF; 

SET IDENTITY_INSERT origem_contato ON; 

INSERT INTO origem_contato (id,nome) VALUES (1,'Anúncio Facebook'); 

INSERT INTO origem_contato (id,nome) VALUES (2,'Anúncio Google Ads'); 

INSERT INTO origem_contato (id,nome) VALUES (3,'Indicacão'); 

SET IDENTITY_INSERT origem_contato OFF; 

SET IDENTITY_INSERT pais ON; 

INSERT INTO pais (id,codigo,nome,created_at,updated_at,deleted_at) VALUES (1,'','Brasil',null,null,null); 

SET IDENTITY_INSERT pais OFF; 

SET IDENTITY_INSERT pessoa_grupo ON; 

INSERT INTO pessoa_grupo (id,pessoa_id,grupo_id) VALUES (1,1,3); 

INSERT INTO pessoa_grupo (id,pessoa_id,grupo_id) VALUES (2,2,2); 

INSERT INTO pessoa_grupo (id,pessoa_id,grupo_id) VALUES (3,3,4); 

SET IDENTITY_INSERT pessoa_grupo OFF; 

SET IDENTITY_INSERT produto ON; 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (1,2,3,3,1,1,'Macbook','',25000,15000,null,null,null,null,null,5,3,10,'','T','',null,null,null); 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (2,2,5,3,1,1,'Iphone','',5000,2500,null,null,null,null,null,5,3,10,'','T','',null,null,null); 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (3,1,1,3,1,4,'Fifa 2021','',120,30,null,null,null,null,null,5,3,10,'','T','',null,null,null); 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (4,1,1,3,1,4,'Fifa 2022','', 120,30,null,null,null,null,null,5,3,10,'','T','',null,null,null); 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (5,1,2,3,1,2,'Cadeira Gamer','',1200,550,null,null,null,null,null,null,null,null,'','','',null,null,null); 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (6,1,4,3,1,1,'Ipda PRO','',8000,5000,null,null,null,null,null,null,null,null,'','','',null,null,null); 

INSERT INTO produto (id,tipo_produto_id,familia_produto_id,fornecedor_id,unidade_medida_id,fabricante_id,nome,cod_barras,preco_venda,preco_custo,peso_liquido,peso_bruto,largura,altura,volume,estoque_minimo,qtde_estoque,estoque_maximo,obs,ativo,foto,created_at,updated_at,deleted_at) VALUES (7,1,5,3,1,3,'Galaxy S22','',5500,3500,null,null,null,null,null,null,null,null,'','','',null,null,null); 

SET IDENTITY_INSERT produto OFF; 

INSERT INTO system_group (id,name,uuid) VALUES (1,'Admin',null); 

INSERT INTO system_group (id,name,uuid) VALUES (2,'Standard',null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (1,1,1,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (2,1,2,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (3,1,3,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (4,1,4,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (5,1,5,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (6,1,6,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (7,1,8,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (8,1,9,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (9,1,11,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (10,1,14,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (11,1,15,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (12,2,10,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (13,2,12,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (14,2,13,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (15,2,16,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (16,2,17,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (17,2,18,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (18,2,19,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (19,2,20,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (20,1,21,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (21,2,22,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (22,2,23,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (23,2,24,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (24,2,25,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (25,1,26,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (26,1,27,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (27,1,28,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (28,1,29,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (29,2,30,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (30,1,31,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (31,1,32,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (32,1,33,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (33,1,34,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (34,1,35,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (35,1,36,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (36,1,37,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (37,1,38,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (38,1,39,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (39,1,40,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (40,1,41,null); 

INSERT INTO system_group_program (id,system_group_id,system_program_id,actions) VALUES (41,1,42,null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (1,'System Group Form','SystemGroupForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (2,'System Group List','SystemGroupList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (3,'System Program Form','SystemProgramForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (4,'System Program List','SystemProgramList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (5,'System User Form','SystemUserForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (6,'System User List','SystemUserList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (7,'Common Page','CommonPage',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (8,'System PHP Info','SystemPHPInfoView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (9,'System ChangeLog View','SystemChangeLogView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (10,'Welcome View','WelcomeView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (11,'System Sql Log','SystemSqlLogList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (12,'System Profile View','SystemProfileView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (13,'System Profile Form','SystemProfileForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (14,'System SQL Panel','SystemSQLPanel',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (15,'System Access Log','SystemAccessLogList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (16,'System Message Form','SystemMessageForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (17,'System Message List','SystemMessageList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (18,'System Message Form View','SystemMessageFormView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (19,'System Notification List','SystemNotificationList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (20,'System Notification Form View','SystemNotificationFormView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (21,'System Document Category List','SystemDocumentCategoryFormList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (22,'System Document Form','SystemDocumentForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (23,'System Document Upload Form','SystemDocumentUploadForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (24,'System Document List','SystemDocumentList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (25,'System Shared Document List','SystemSharedDocumentList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (26,'System Unit Form','SystemUnitForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (27,'System Unit List','SystemUnitList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (28,'System Access stats','SystemAccessLogStats',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (29,'System Preference form','SystemPreferenceForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (30,'System Support form','SystemSupportForm',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (31,'System PHP Error','SystemPHPErrorLogView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (32,'System Database Browser','SystemDatabaseExplorer',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (33,'System Table List','SystemTableList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (34,'System Data Browser','SystemDataBrowser',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (35,'System Menu Editor','SystemMenuEditor',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (36,'System Request Log','SystemRequestLogList',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (37,'System Request Log View','SystemRequestLogView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (38,'System Administration Dashboard','SystemAdministrationDashboard',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (39,'System Log Dashboard','SystemLogDashboard',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (40,'System Session dump','SystemSessionDumpView',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (41,'Files diff','SystemFilesDiff',null); 

INSERT INTO system_program (id,name,controller,actions) VALUES (42,'System Information','SystemInformationView',null); 

INSERT INTO system_unit (id,name,connection_name) VALUES (1,'Matriz','matriz'); 

INSERT INTO system_user_group (id,system_user_id,system_group_id) VALUES (1,1,1); 

INSERT INTO system_user_group (id,system_user_id,system_group_id) VALUES (2,2,2); 

INSERT INTO system_user_group (id,system_user_id,system_group_id) VALUES (3,1,2); 

INSERT INTO system_user_program (id,system_user_id,system_program_id) VALUES (1,2,7); 

INSERT INTO system_users (id,name,login,password,email,frontpage_id,system_unit_id,active,accepted_term_policy_at,accepted_term_policy,two_factor_enabled,two_factor_type,two_factor_secret) VALUES (1,'Administrator','admin','21232f297a57a5a743894a0e4a801fc3','admin@admin.net',10,null,'Y','','',null,null,null); 

INSERT INTO system_users (id,name,login,password,email,frontpage_id,system_unit_id,active,accepted_term_policy_at,accepted_term_policy,two_factor_enabled,two_factor_type,two_factor_secret) VALUES (2,'User','user','ee11cbb19052e40b07aac0ca060c23ee','user@user.net',7,null,'Y','','',null,null,null); 

INSERT INTO system_user_unit (id,system_user_id,system_unit_id) VALUES (1,1,1); 

SET IDENTITY_INSERT tipo_interacao ON; 

INSERT INTO tipo_interacao (id,nome) VALUES (1,'Completa'); 

INSERT INTO tipo_interacao (id,nome) VALUES (2,'Simples'); 

SET IDENTITY_INSERT tipo_interacao OFF; 

SET IDENTITY_INSERT tipo_pessoa ON; 

INSERT INTO tipo_pessoa (id,nome,sigla) VALUES (1,'Física','PF'); 

INSERT INTO tipo_pessoa (id,nome,sigla) VALUES (2,'Jurídica','PJ'); 

SET IDENTITY_INSERT tipo_pessoa OFF; 

SET IDENTITY_INSERT tipo_produto ON; 

INSERT INTO tipo_produto (id,nome) VALUES (1,'Mercadoria'); 

INSERT INTO tipo_produto (id,nome) VALUES (2,'Produto'); 

INSERT INTO tipo_produto (id,nome) VALUES (3,'Serviço'); 

SET IDENTITY_INSERT tipo_produto OFF; 

SET IDENTITY_INSERT unidade_medida ON; 

INSERT INTO unidade_medida (id,nome,sigla) VALUES (1,'Peça','PC'); 

INSERT INTO unidade_medida (id,nome,sigla) VALUES (2,'Litro','LT'); 

INSERT INTO unidade_medida (id,nome,sigla) VALUES (3,'Metro cúbico','M3'); 

SET IDENTITY_INSERT unidade_medida OFF; 
