# OBS de comissão com histórico de categoria

Copiar todo o conteúdo de `comissoes-com-historico-categoria.sql` para a pergunta SQL existente no Metabase, mantendo a mesma conexão com o banco `versatronic` e a configuração dos filtros. O arquivo contém somente a consulta; a preparação do banco já foi aplicada separadamente em 09/11/2026.

A OBS mostra a categoria registrada na data-base e a primeira alteração posterior, além da categoria atual e da quantidade de registros posteriores quando há mais de um. Não recalcula comissões históricas nem muda os valores do relatório. Mudança posterior é apresentada como possível explicação de divergência, nunca como prova definitiva da causa.

O corte considera o dia inteiro da data-base, igual ao corte das atividades na consulta original. Datas são do registro no CRM, que pode ocorrer depois da alteração na TOTVS. Códigos e nomes vêm do cadastro atual. Registro inicial posterior e lacunas contraditórias entre logs não são tratados como certeza sobre a categoria antiga.

## Banco

Migration: `app/database/migrations/20261109_001_historico_categoria_relatorio.sql`.

Cria `public.view_cliente_categoria_historico` e utiliza `dblink` em schema privado `crm_auditoria` para ler somente os eventos de categoria em `versatronic_log`. A conexão utiliza o socket local e a autenticação local existente; não há senha na SQL. Não altera autenticação, notas, categorias, comissões, políticas ou dados de negócio. Não concede acesso ao papel PUBLIC. Foi validada com o administrador `postgres` já utilizado no acesso fornecido; uma futura conexão do Metabase com outro papel exige revisão específica de permissões e autenticação entre bancos.

O histórico é lido uma vez e agrupado por pessoa em arrays; as buscas por data examinam somente os eventos daquele cliente, evitando duas varreduras do histórico inteiro para cada nota.

## Validação

Validado em PostgreSQL 16.3 isolado: filtros, rateios, valores, mudança posterior, mudança anterior, mesmo dia, meia-noite seguinte, ausência de logs, registro inicial posterior, múltiplas mudanças, reversão, lacunas, controle manual e cancelamento. A visão real entre dois bancos também foi testada.

Comparação executada em produção antes do commit da migration: 1.566 linhas, todas as colunas exceto OBS idênticas à consulta recebida. Em 172 linhas há contexto histórico adicional. O caso original também foi conferido com dados reais; identificadores de cliente e nota não são versionados neste documento. A consulta completa levou aproximadamente 2,6 segundos no teste, sem os filtros opcionais e com JIT desativado na sessão de validação. O tempo pode variar no Metabase.

Teste local: iniciar PostgreSQL 16.3 descartável em `127.0.0.1:15437`, banco `versa_obs_test`, usuário `postgres`, senha fictícia `local-fixture-only`, parâmetro `-c versa.test_context=obs_categoria`. Executar `php -d extension=pdo_pgsql tests/metabase-comissao-historico.php`. O teste recusa um servidor sem esse marcador e não deve ser executado contra produção.
