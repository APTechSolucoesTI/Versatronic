CREATE TABLE DTIPOBAIRRO( 
      DESCRICAO varchar  (20)    NOT NULL , 
      CODIGO number(10)    NOT NULL , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (CODIGO)) ; 

CREATE TABLE DTIPORUA( 
      DESCRICAO varchar  (20)    NOT NULL , 
      CODIGO number(10)    NOT NULL , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (CODIGO)) ; 

CREATE TABLE FCFO( 
      id number(10)    NOT NULL , 
      CODCFO varchar  (25)    NOT NULL , 
      USE varchar(3000)    NOT NULL , 
      NOMEFANTASIA varchar  (100)   , 
      NOME varchar  (100)   , 
      CGCCFO varchar  (20)   , 
      PAGREC number(10)    NOT NULL , 
      RUA varchar  (100)   , 
      NUMERO varchar  (8)   , 
      COMPLEMENTO varchar(3000)   , 
      BAIRRO varchar(3000)   , 
      CIDADE varchar(3000)   , 
      CODETD varchar  (2)   , 
      CEP varchar(3000)   , 
      TELEFONE varchar  (15)   , 
      RUAPGTO varchar  (100)   , 
      NUMEROPGTO varchar  (8)   , 
      COMPLEMENTOPGTO varchar(3000)   , 
      BAIRROPGTO varchar(3000)   , 
      CIDADEPGTO varchar(3000)   , 
      CODETDPGTO varchar  (2)   , 
      CEPPGTO varchar(3000)   , 
      TELEFONEPGTO varchar  (15)   , 
      RUAENTREGA varchar  (100)   , 
      NUMEROENTREGA varchar  (8)   , 
      COMPLEMENTREGA varchar(3000)   , 
      BAIRROENTREGA varchar(3000)   , 
      CIDADEENTREGA varchar(3000)   , 
      CODETDENTREGA varchar  (2)   , 
      CEPENTREGA varchar(3000)   , 
      TELEFONEENTREGA varchar  (15)   , 
      FAX varchar  (15)   , 
      TELEX varchar  (15)   , 
      EMAIL varchar  (250)   , 
      CONTATO varchar  (40)   , 
      CODTCF varchar  (25)   , 
      ATIVO number(10)    NOT NULL , 
      LIMITECREDITO varchar(3000)   , 
      VALORULTIMOLAN varchar(3000)   , 
      TIPOINSCRCNAB number(10)   , 
      DATAULTALTERACAO timestamp(0)   , 
      DATACRIACAO timestamp(0)   , 
      DATAULTMOVIMENTO timestamp(0)   , 
      CONTEVENTOCONTAB number(10)   , 
      CAMPOLIVRE varchar  (40)   , 
      CAMPOALFAOP1 varchar  (40)   , 
      CAMPOALFAOP2 varchar  (40)   , 
      CAMPOALFAOP3 varchar  (40)   , 
      VALOROP1 varchar(3000)   , 
      VALOROP2 varchar(3000)   , 
      VALOROP3 varchar(3000)   , 
      DATAOP1 timestamp(0)   , 
      DATAOP2 timestamp(0)   , 
      DATAOP3 timestamp(0)   , 
      CODTRA varchar  (5)   , 
      CHAPA varchar  (16)   , 
      STATUSCOTACAO varchar  (1)   , 
      DTINICATIVIDADES timestamp(0)   , 
      PATRIMONIO varchar(3000)   , 
      NUMFUNCIONARIOS number(10)   , 
      CODCOLCHAVESESTRANG number(10)   , 
      CODCOLTCF number(10)   , 
      FAXDEDICADO number(10)   , 
      CODMUNICIPIO varchar  (20)   , 
      CODCOLCONTAGER number(10)   , 
      CODCONTAGER varchar(3000)   , 
      FORMAPAGAMENTO number(10)   , 
      IDENTPORCNPJ varchar(3000)   , 
      INSCRMUNICIPAL varchar  (20)   , 
      PESSOAFISOUJUR varchar  (1)    NOT NULL , 
      CONTATOPGTO varchar  (40)   , 
      CONTATOENTREGA varchar  (40)   , 
      PAIS varchar  (20)   , 
      PAISPAGTO varchar  (20)   , 
      PAISENTREGA varchar  (20)   , 
      ULTIMODOCUMENTO varchar  (40)   , 
      CONTRIBUINTE number(10)   , 
      CFOIMOB number(10)   , 
      TIPODOC varchar  (1)   , 
      CODFINALIDADE number(10)   , 
      AGRUPCOB char  (1)   , 
      CODCARGO varchar  (3)   , 
      CODVINCULO char  (1)   , 
      ENDCOBC char  (1)   , 
      CIDENTIDADE varchar  (20)   , 
      CI_ORGAO varchar  (15)   , 
      CI_UF varchar  (2)   , 
      CODPROF number(10)   , 
      CODPAGTOGPS varchar  (5)   , 
      FAXENTREGA varchar  (15)   , 
      EMAILENTREGA varchar  (250)   , 
      FAXPGTO varchar  (15)   , 
      EMAILPGTO varchar  (250)   , 
      SATISFACAO number(10)   , 
      VALFRETE varchar(3000)   , 
      TPTOMADOR number(10)   , 
      CONTRIBUINTEISS number(10)   , 
      NUMDEPENDENTES number(10)   , 
      EMPRESA varchar  (60)   , 
      ESTADOCIVIL varchar  (1)   , 
      CODCOLCXA varchar(3000)   , 
      CODCXA varchar  (10)   , 
      PRODUTORRURAL char(1)   , 
      USUARIOALTERACAO varchar  (20)   , 
      SUFRAMA varchar  (14)   , 
      CODMUNICIPIOPGTO varchar  (20)   , 
      CODMUNICIPIOENTREGA varchar  (20)   , 
      ORGAOPUBLICO number(10)   , 
      TELEFONECOMERCIAL varchar  (15)   , 
      CAIXAPOSTAL varchar  (10)   , 
      CAIXAPOSTALENTREGA varchar  (10)   , 
      CAIXAPOSTALPAGAMENTO varchar  (10)   , 
      CATEGORIAAUTONOMO number(10)   , 
      CBOAUTONOMO varchar  (10)   , 
      CIAUTONOMO varchar  (11)   , 
      IDCFO number(10)    NOT NULL , 
      CODIGOINSS varchar  (10)   , 
      VROUTRASDEDUCOESIRRF varchar(3000)   , 
      CODRECEITA varchar  (10)   , 
      CEI varchar  (20)   , 
      OPTANTEPELOSIMPLES number(10)   , 
      TIPORUA number(10)   , 
      TIPOBAIRRO number(10)   , 
      REGIMEISS varchar  (1)   , 
      RETENCAOISS number(10)   , 
      DTNASCIMENTO timestamp(0)   , 
      USUARIOCRIACAO varchar  (20)   , 
      TIPOOPCOMBUSTIVEL number(10)   , 
      INSCRESTADUALST varchar  (20)   , 
      LOCALIDADE varchar  (40)   , 
      LOCALIDADEPGTO varchar  (40)   , 
      LOCALIDADEENTREGA varchar  (40)   , 
      TIPORUAPGTO number(10)   , 
      TIPORUAENTREGA number(10)   , 
      TIPOBAIRROPGTO number(10)   , 
      TIPOBAIRROENTREGA number(10)   , 
      PORTE number(10)   , 
      RAMOATIV number(10)    NOT NULL , 
      NIT varchar  (15)   , 
      CEPCAIXAPOSTAL varchar(3000)   , 
      NUMDIASATRASO number(10)   , 
      IDPAIS number(10)   , 
      IDPAISPGTO number(10)   , 
      IDPAISENTREGA number(10)   , 
      TIPOCONTRIBUINTEINSS number(10)    NOT NULL , 
      NACIONALIDADE number(10)    NOT NULL , 
      CODCOLCFOFISCAL varchar(3000)   , 
      IDCFOFISCAL number(10)   , 
      EMAILFISCAL varchar  (250)   , 
      CALCULAAVP number(10)    NOT NULL , 
      CODUSUARIOACESSO varchar  (20)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
      IDINTEGRACAO varchar  (100)   , 
      USARCUMULATRETENCAOPAGAR number(10)   , 
      NIF varchar  (50)   , 
      SITUACAONIF number(10)   , 
      TIPORENDIMENTO varchar  (3)   , 
      FORMATRIBUTACAO varchar  (2)   , 
      INDNATRET varchar  (5)   , 
      DOCUMENTOESTRANGEIRO varchar  (30)   , 
      TPLOTACAO_OLD varchar  (2)   , 
      INOVAR_AUTO number(10)   , 
      FILIALFINANCEIRA number(10)   , 
      TOMADORFOLHA number(10)   , 
      CNAEPREP varchar  (7)   , 
      PERCENTACIDTRAB varchar(3000)   , 
      CODCOLFORMULA varchar(3000)   , 
      FORMULAVALDEDUCAOVARIAVEL varchar  (8)   , 
      APLICFORMULA varchar  (1)   , 
      CODCFOCOLINTEGRACAO varchar(3000)   , 
      CODCFOINTEGRACAO varchar  (25)   , 
      DIGVERIFICDEBAUTOMATICO varchar  (1)   , 
      CODLOJA varchar  (8)   , 
      CODFILIALINTEGRACAO number(10)   , 
      CODEXTERNO varchar  (25)   , 
      TIPOCLIENTE varchar  (2)   , 
      CONSIDERAFILIALOBRA number(10)   , 
      CODFILIALOBRA number(10)   , 
      TIPOCONTROLEPONTO number(10)   , 
      FAP varchar(3000)   , 
      CODCOLIGADAFILIALOBRA varchar(3000)   , 
      OBRAPROPRIA number(10)   , 
      ENTIDADEEXECUTORAPAA number(10)    NOT NULL , 
      APOSENTADOOUPENSIONISTA number(10)    NOT NULL , 
      CODCATEGORIAESOCIAL number(10)   , 
      CNPJRURAL varchar  (20)   , 
      CODIGOCAEPF varchar  (18)   , 
      ISENTOTRIBUTOS varchar(3000)   , 
      SOCIOCOOPERADO number(10)    NOT NULL , 
 PRIMARY KEY (id)) ; 

CREATE TABLE FCFOCONTATO( 
      IDINTEGRACAO number(10)    NOT NULL , 
      IDCONTATO number(10)    NOT NULL , 
      CODCOLIGADA varchar(3000)    NOT NULL , 
      CODCFO varchar  (25)    NOT NULL , 
      NOME varchar  (50)    NOT NULL , 
      EMAIL varchar  (80)   , 
      TELEFONE varchar  (15)   , 
      RAMAL varchar  (6)   , 
      FAX varchar  (15)   , 
      FUNCAO varchar  (50)   , 
      OBSERVACAO varchar  (80)   , 
      CODUSUARIO varchar  (20)   , 
      RUA varchar  (100)   , 
      NUMERO varchar  (8)   , 
      COMPLEMENTO varchar(3000)   , 
      BAIRRO varchar(3000)   , 
      CIDADE varchar(3000)   , 
      CODETD varchar  (2)   , 
      CEP varchar(3000)   , 
      PAIS varchar  (20)   , 
      USUARIOALTERACAO varchar  (20)   , 
      DATAALTERACAO timestamp(0)   , 
      ATIVO number(10)   , 
      DATANASCIMENTO timestamp(0)   , 
      DEFAUTPARAEMAIL number(10)   , 
      CODAPLIC varchar  (2)   , 
      CODMUNICIPIO varchar  (20)   , 
      LOCALIDADE varchar  (40)   , 
      CODUSUARIOACESSO varchar  (20)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (IDINTEGRACAO)) ; 

CREATE TABLE FCFODEF( 
      CODCOLIGADA varchar(3000)    NOT NULL , 
      id number(10)    NOT NULL , 
      CODCFO varchar  (25)    NOT NULL , 
      CODTB1FLX varchar  (25)   , 
      CODTB2FLX varchar  (25)   , 
      CODTB3FLX varchar  (25)   , 
      CODTB4FLX varchar  (25)   , 
      CODTB5FLX varchar  (25)   , 
      CODDEPARTAMENTO varchar  (25)   , 
      CODCCUSTO varchar  (25)   , 
      CODFILIAL number(10)   , 
      CODCOLCFO varchar(3000)    NOT NULL , 
      CODBANCOCOBRANCA varchar  (3)   , 
      CNABCARTEIRA number(10)   , 
      CODCPG varchar  (5)   , 
      CODVEN varchar  (16)   , 
      PERCENTUALDESC varchar(3000)   , 
      CODRPR varchar  (15)   , 
      CODTDO varchar  (10)   , 
      CODCOLCXA varchar(3000)   , 
      CODCXA varchar  (10)   , 
      CODTRA varchar  (5)   , 
      TIPOCONTABILLAN number(10)   , 
      CODCPGVENDA varchar  (5)   , 
      CIFFOB number(10)   , 
      CODTRA2 varchar  (5)   , 
      DIASVENCSEMANA number(10)   , 
      PERCDESCCOMPRA varchar(3000)   , 
      CODIGOINSS varchar  (10)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
      IDITEMCONTABIL number(10)   , 
      IDCONVENIO number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE FTCF( 
      CODCOLIGADA varchar(3000)    NOT NULL , 
      id number(10)    NOT NULL , 
      CODTCF varchar  (25)    NOT NULL , 
      DESCRICAO varchar  (40)   , 
      CAMPOLIVRE varchar  (40)   , 
      CODSEGM varchar  (5)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE GCCUSTO( 
      USE varchar(3000)    NOT NULL , 
      ID number(10)    NOT NULL , 
      CODCCUSTO varchar  (25)    NOT NULL , 
      NOME varchar  (60)   , 
      CODCOLCONTAGER varchar(3000)   , 
      CODCONTAGER varchar(3000)   , 
      CODCOLCONTA varchar(3000)   , 
      CODCONTA varchar(3000)   , 
      CODREDUZIDO varchar  (25)    NOT NULL , 
      CAMPOLIVRE varchar  (100)   , 
      ATIVO char(1)   , 
      PERMITELANC char(1)   , 
      CODCLASSIFICA varchar  (10)   , 
      ENVIASPED char(1)    NOT NULL , 
      DATAINCLUSAO timestamp(0)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
      RESPONSAVEL number(10)   , 
 PRIMARY KEY (ID)) ; 

CREATE TABLE GETD( 
      id number(10)    NOT NULL , 
      CODETD varchar  (2)    NOT NULL , 
      NOME varchar  (40)   , 
      NACIONAL varchar  (1)   , 
      CODIGOSINIEF varchar  (4)   , 
      IDPAIS number(10)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE GMUNICIPIO( 
      id number(10)    NOT NULL , 
      CODMUNICIPIO varchar  (20)    NOT NULL , 
      CODETDMUNICIPIO varchar  (2)    NOT NULL , 
      NOMEMUNICIPIO varchar  (32)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE GPAIS( 
      IDPAIS number(10)    NOT NULL , 
      CODPAIS varchar  (5)    NOT NULL , 
      DESCRICAO varchar  (60)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (IDPAIS)) ; 

CREATE TABLE orcamentos_cnc( 
      row_num number(10)   , 
      coligada varchar  (255)   , 
      orcamento varchar  (255)   , 
      data_emissao date   , 
      cod_cliente char  (7)   , 
      cliente varchar  (255)   , 
      dias number(10)   , 
      id_objeto varchar  (255)   , 
      objeto varchar  (255)   , 
      centro_custo varchar  (255)   , 
      os varchar  (255)   , 
      tecnico varchar  (255)   , 
      status varchar  (255)   , 
      cor_status varchar  (10)   , 
      intervalo varchar  (20)   , 
      qtde number(10)   , 
 PRIMARY KEY (row_num)) ; 

CREATE TABLE orcamentos_comercio( 
      row_num number(10)   , 
      coligada varchar  (255)   , 
      orcamento varchar  (255)   , 
      data_emissao date   , 
      cod_cliente char  (7)   , 
      cliente varchar  (255)   , 
      dias number(10)   , 
      id_objeto varchar  (255)   , 
      objeto varchar  (255)   , 
      centro_custo varchar  (255)   , 
      os varchar  (255)   , 
      tecnico varchar  (255)   , 
      status varchar  (255)   , 
      cor_status varchar  (10)   , 
      intervalo varchar  (25)   , 
      qtde number(10)   , 
 PRIMARY KEY (row_num)) ; 

CREATE TABLE TCPG( 
      id number(10)    NOT NULL , 
      CODCOLIGADA varchar(3000)    NOT NULL , 
      CODCPG varchar  (5)    NOT NULL , 
      NOME varchar  (100)   , 
      ARREDPRIMOUULT varchar  (1)   , 
      ARREDDEZOUCENT varchar  (1)   , 
      NUMVETCONDICOES number(10)   , 
      VALORPAGAMENTO1 varchar(3000)   , 
      QUANTASVEZES1 number(10)   , 
      PERIODOEMDIAS1 number(10)   , 
      PRAZO1 number(10)   , 
      CONTAGEMDIAS1 varchar  (1)   , 
      TIPO1 varchar  (1)   , 
      VALORPAGAMENTO2 varchar(3000)   , 
      QUANTASVEZES2 number(10)   , 
      PERIODOEMDIAS2 number(10)   , 
      PRAZO2 number(10)   , 
      CONTAGEMDIAS2 varchar  (1)   , 
      TIPO2 varchar  (1)   , 
      VALORPAGAMENTO3 varchar(3000)   , 
      QUANTASVEZES3 number(10)   , 
      PERIODOEMDIAS3 number(10)   , 
      PRAZO3 number(10)   , 
      CONTAGEMDIAS3 varchar  (1)   , 
      TIPO3 varchar  (1)   , 
      VALORPAGAMENTO4 varchar(3000)   , 
      QUANTASVEZES4 number(10)   , 
      PERIODOEMDIAS4 number(10)   , 
      PRAZO4 number(10)   , 
      CONTAGEMDIAS4 varchar  (1)   , 
      TIPO4 varchar  (1)   , 
      VALORPAGAMENTO5 varchar(3000)   , 
      QUANTASVEZES5 number(10)   , 
      PERIODOEMDIAS5 number(10)   , 
      PRAZO5 number(10)   , 
      CONTAGEMDIAS5 varchar  (1)   , 
      TIPO5 varchar  (1)   , 
      DEFLATOR varchar(3000)   , 
      APLICACAOFRM varchar  (1)   , 
      CODFRMPRECO1 varchar  (8)   , 
      CODFRMPRECO2 varchar  (8)   , 
      DIACARENCIA number(10)   , 
      TAXAJUROS varchar(3000)   , 
      JUROSCOMPOSTO varchar(3000)   , 
      PLANOPAGTO number(10)   , 
      CAPITALIZMENSAL varchar(3000)   , 
      PLANOCOMPRA varchar(3000)   , 
      PLANOVENDA varchar(3000)   , 
      DIASVENCSEMANA number(10)   , 
      CODFRMPRIMPARCELA varchar  (8)   , 
      INATIVO number(10)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
      IDFORMAPAGTO number(10)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE TRPR( 
      id number(10)    NOT NULL , 
      CODCOLIGADA varchar(3000)    NOT NULL , 
      CODRPR varchar  (15)    NOT NULL , 
      NOME varchar  (40)   , 
      SIGLA varchar  (5)   , 
      NOMEFANTASIA varchar  (20)   , 
      RUA varchar  (100)   , 
      NUMERO number(10)   , 
      COMPLEMENTO varchar(3000)   , 
      BAIRRO varchar(3000)   , 
      CIDADE varchar(3000)   , 
      CODETD varchar  (2)   , 
      CEP varchar(3000)   , 
      CGC varchar  (20)   , 
      INSCRESTADUAL varchar  (20)   , 
      CONTATO varchar  (20)   , 
      TELEFONE varchar  (15)   , 
      FAX varchar  (15)   , 
      RUAPGTO varchar  (100)   , 
      NUMEROPGTO number(10)   , 
      COMPLEMENTOPGTO varchar(3000)   , 
      BAIRROPGTO varchar(3000)   , 
      CIDADEPGTO varchar(3000)   , 
      CODETDPGTO varchar  (2)   , 
      CEPPGTO varchar(3000)   , 
      PERCENTCOMISSAO varchar(3000)   , 
      FATCLIENTEDIRETO number(10)   , 
      DIAFATURAMENTO number(10)   , 
      PERCENTREPASSE varchar(3000)   , 
      CODTB1FLX varchar  (25)   , 
      CODTB2FLX varchar  (25)   , 
      CODTB3FLX varchar  (25)   , 
      CODTB4FLX varchar  (25)   , 
      CODTB5FLX varchar  (25)   , 
      CLCONTABIL varchar  (15)   , 
      INATIVO varchar(3000)   , 
      HOMEPAGE varchar  (80)   , 
      EMAIL varchar  (40)   , 
      CELULAR varchar  (15)   , 
      PAIS varchar  (20)   , 
      CODUSUARIO varchar  (20)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE TTRA( 
      USE varchar(3000)    NOT NULL , 
      id number(10)    NOT NULL , 
      CODTRA varchar  (5)    NOT NULL , 
      NOME varchar  (40)   , 
      RUA varchar  (100)   , 
      NUMERO varchar  (8)   , 
      COMPLEMENTO varchar(3000)   , 
      BAIRRO varchar(3000)   , 
      CIDADE varchar(3000)   , 
      CODETD varchar  (2)   , 
      CEP varchar(3000)   , 
      CGC varchar  (20)   , 
      INSCRESTADUAL varchar  (20)   , 
      CONTATO varchar  (30)   , 
      TELEFONE varchar  (15)   , 
      TELEX varchar  (15)   , 
      FAX varchar  (15)   , 
      LIVRE varchar  (20)   , 
      NOMEFANTASIA varchar  (60)   , 
      PAIS varchar  (20)   , 
      CEI varchar  (20)   , 
      INSCRMUNICIPAL varchar  (20)   , 
      INATIVO number(10)   , 
      EMAIL varchar  (60)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
      CODMUNICIPIO varchar  (20)   , 
 PRIMARY KEY (id)) ; 

CREATE TABLE TVEN( 
      CODCOLIGADA varchar(3000)    NOT NULL , 
      id number(10)    NOT NULL , 
      CODVEN varchar  (16)    NOT NULL , 
      NOME varchar  (80)   , 
      CARGO varchar  (30)   , 
      CODFILIAL number(10)   , 
      CODLOC varchar  (15)   , 
      COMISSAO1 varchar(3000)   , 
      COMISSAO2 varchar(3000)   , 
      COMISSAO3 varchar(3000)   , 
      CODPESSOA number(10)   , 
      VENDECOMPRA number(10)   , 
      CODUSUARIO varchar  (20)   , 
      SENHA varchar  (80)   , 
      INATIVO varchar(3000)   , 
      PFVENDEDOR varchar(3000)   , 
      PFCAIXA varchar(3000)   , 
      PFSUPERVISOR varchar(3000)   , 
      PFGERENTE varchar(3000)   , 
      IDFUNCIONARIO number(10)    NOT NULL , 
      COMISSAO4 varchar(3000)   , 
      DESCMAXIMO varchar(3000)   , 
      RECCREATEDBY varchar  (50)   , 
      RECCREATEDON timestamp(0)   , 
      RECMODIFIEDBY varchar  (50)   , 
      RECMODIFIEDON timestamp(0)   , 
 PRIMARY KEY (id)) ; 

 
  CREATE SEQUENCE DTIPOBAIRRO_CODIGO_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER DTIPOBAIRRO_CODIGO_seq_tr 

BEFORE INSERT ON DTIPOBAIRRO FOR EACH ROW 

    WHEN 

        (NEW.CODIGO IS NULL) 

    BEGIN 

        SELECT DTIPOBAIRRO_CODIGO_seq.NEXTVAL INTO :NEW.CODIGO FROM DUAL; 

END;
CREATE SEQUENCE DTIPORUA_CODIGO_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER DTIPORUA_CODIGO_seq_tr 

BEFORE INSERT ON DTIPORUA FOR EACH ROW 

    WHEN 

        (NEW.CODIGO IS NULL) 

    BEGIN 

        SELECT DTIPORUA_CODIGO_seq.NEXTVAL INTO :NEW.CODIGO FROM DUAL; 

END;
CREATE SEQUENCE FCFO_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER FCFO_id_seq_tr 

BEFORE INSERT ON FCFO FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT FCFO_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE FCFOCONTATO_IDINTEGRACAO_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER FCFOCONTATO_IDINTEGRACAO_seq_tr 

BEFORE INSERT ON FCFOCONTATO FOR EACH ROW 

    WHEN 

        (NEW.IDINTEGRACAO IS NULL) 

    BEGIN 

        SELECT FCFOCONTATO_IDINTEGRACAO_seq.NEXTVAL INTO :NEW.IDINTEGRACAO FROM DUAL; 

END;
CREATE SEQUENCE FCFODEF_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER FCFODEF_id_seq_tr 

BEFORE INSERT ON FCFODEF FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT FCFODEF_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE FTCF_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER FTCF_id_seq_tr 

BEFORE INSERT ON FTCF FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT FTCF_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE GCCUSTO_ID_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER GCCUSTO_ID_seq_tr 

BEFORE INSERT ON GCCUSTO FOR EACH ROW 

    WHEN 

        (NEW.ID IS NULL) 

    BEGIN 

        SELECT GCCUSTO_ID_seq.NEXTVAL INTO :NEW.ID FROM DUAL; 

END;
CREATE SEQUENCE GETD_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER GETD_id_seq_tr 

BEFORE INSERT ON GETD FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT GETD_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE GMUNICIPIO_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER GMUNICIPIO_id_seq_tr 

BEFORE INSERT ON GMUNICIPIO FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT GMUNICIPIO_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE GPAIS_IDPAIS_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER GPAIS_IDPAIS_seq_tr 

BEFORE INSERT ON GPAIS FOR EACH ROW 

    WHEN 

        (NEW.IDPAIS IS NULL) 

    BEGIN 

        SELECT GPAIS_IDPAIS_seq.NEXTVAL INTO :NEW.IDPAIS FROM DUAL; 

END;
CREATE SEQUENCE orcamentos_cnc_row_num_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER orcamentos_cnc_row_num_seq_tr 

BEFORE INSERT ON orcamentos_cnc FOR EACH ROW 

    WHEN 

        (NEW.row_num IS NULL) 

    BEGIN 

        SELECT orcamentos_cnc_row_num_seq.NEXTVAL INTO :NEW.row_num FROM DUAL; 

END;
CREATE SEQUENCE orcamentos_comercio_row_num_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER orcamentos_comercio_row_num_seq_tr 

BEFORE INSERT ON orcamentos_comercio FOR EACH ROW 

    WHEN 

        (NEW.row_num IS NULL) 

    BEGIN 

        SELECT orcamentos_comercio_row_num_seq.NEXTVAL INTO :NEW.row_num FROM DUAL; 

END;
CREATE SEQUENCE TCPG_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER TCPG_id_seq_tr 

BEFORE INSERT ON TCPG FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT TCPG_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE TRPR_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER TRPR_id_seq_tr 

BEFORE INSERT ON TRPR FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT TRPR_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE TTRA_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER TTRA_id_seq_tr 

BEFORE INSERT ON TTRA FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT TTRA_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
CREATE SEQUENCE TVEN_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER TVEN_id_seq_tr 

BEFORE INSERT ON TVEN FOR EACH ROW 

    WHEN 

        (NEW.id IS NULL) 

    BEGIN 

        SELECT TVEN_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
 