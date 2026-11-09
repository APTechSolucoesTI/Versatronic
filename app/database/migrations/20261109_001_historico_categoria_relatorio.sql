-- Executar uma vez no banco versatronic, com o administrador existente.
-- Somente leitura do banco versatronic_log, via socket local do mesmo PostgreSQL.
-- Não contém senha, não copia logs e não altera notas/comissões/categorias.
BEGIN;
SET LOCAL lock_timeout = '5s';

CREATE SCHEMA IF NOT EXISTS crm_auditoria;
REVOKE ALL ON SCHEMA crm_auditoria FROM PUBLIC;
CREATE EXTENSION IF NOT EXISTS dblink WITH SCHEMA crm_auditoria;

DO $guard$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_extension e
        JOIN pg_namespace n ON n.oid = e.extnamespace
        WHERE e.extname = 'dblink' AND n.nspname = 'crm_auditoria'
    ) THEN
        RAISE EXCEPTION 'dblink já existe em outro schema; revisar a configuração antes de prosseguir';
    END IF;
END
$guard$;

CREATE OR REPLACE VIEW public.view_cliente_categoria_historico
WITH (security_barrier = true) AS
SELECT h.*
FROM crm_auditoria.dblink(
    $connection$dbname=versatronic_log connect_timeout=5 options='-c default_transaction_read_only=on -c search_path=pg_catalog'$connection$,
    $remote$
        SELECT
            id::bigint,
            pkvalue::text,
            logdate::timestamp without time zone,
            NULLIF(oldvalue, '')::text,
            NULLIF(newvalue, '')::text,
            operation::text,
            NULLIF(login, '')::text
        FROM public.system_change_log
        WHERE tablename = 'pessoa'
          AND columnname = 'categoria_cliente_id'
          AND operation IN ('created', 'changed', 'deleted')
          AND logdate IS NOT NULL
          AND pkvalue ~ '^[0-9]+$'
          AND NULLIF(oldvalue, '') IS DISTINCT FROM NULLIF(newvalue, '')
    $remote$
) AS h (
    evento_id bigint,
    pessoa_id text,
    registrado_em timestamp without time zone,
    categoria_anterior_id text,
    categoria_nova_id text,
    operacao text,
    usuario text
);

REVOKE ALL ON public.view_cliente_categoria_historico FROM PUBLIC;
COMMENT ON VIEW public.view_cliente_categoria_historico IS
    'Leitura restrita da auditoria de categorias do CRM, no banco versatronic_log. Datas são de registro no CRM; não necessariamente a data original da alteração na TOTVS. Requer administrador autenticado com acesso ao socket local; não conceder privilégios adicionais sem revisar a conexão.';

COMMIT;
