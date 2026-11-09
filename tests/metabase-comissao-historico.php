<?php
// Run only against the disposable PostgreSQL fixture, never production.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
chdir(dirname(__DIR__));
$db = new PDO('pgsql:host=127.0.0.1;port=15437;dbname=versa_obs_test', 'postgres', 'local-fixture-only', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
if ($db->query("SELECT current_setting('versa.test_context', true)")->fetchColumn() !== 'obs_categoria') {
    throw new RuntimeException('This is not the isolated fixture server.');
}
$db->exec('SET jit=off');
if (!$db->query("SELECT 1 FROM pg_database WHERE datname='versatronic_log'")->fetchColumn()) {
    $db->exec('CREATE DATABASE versatronic_log');
}
$kind = $db->query("SELECT relkind FROM pg_class WHERE oid=to_regclass('public.view_cliente_categoria_historico')")->fetchColumn();
if ($kind === 'v') { $db->exec('DROP VIEW public.view_cliente_categoria_historico'); }
function verify($condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
    echo "OK: $message\n";
}
function executableSql(string $file): string {
    // POSIX regex classes such as [[:space:]] are SQL, not Metabase optional blocks.
    return preg_replace('/\[\[(?:(?!\]\]).)*\{\{.*?\}\}(?:(?!\]\]).)*\]\]/s', '', file_get_contents($file));
}

$db->exec("CREATE TABLE IF NOT EXISTS nota_baixada (id integer,numero text,numero_nf text,documento text,razao_social text,coligada_id integer,valor_total numeric,data_emissao timestamp,data_emissao_os timestamp,nota_status_id integer,tem_comissao text,comissao numeric);
 CREATE TABLE IF NOT EXISTS view_comissao_repres (id integer,representante text,fantasia text,codigo_cliente text,numero_nota text);
 CREATE TABLE IF NOT EXISTS coligada (id integer,nome text);
 CREATE TABLE IF NOT EXISTS pessoa (id integer,codigo text,cpf_cnpj text,categoria_cliente_id integer,deleted_at timestamp);
 CREATE TABLE IF NOT EXISTS categoria_cliente (id integer,codigo text,nome text);
 CREATE TABLE IF NOT EXISTS complemento (id integer,pessoa_id integer,representante_id integer,created_at timestamp,deleted_at timestamp);
 CREATE TABLE IF NOT EXISTS prazo_atividade (id integer,categoria_cliente_id integer,regras_tipo_atividade_id integer,dias integer,ambos text);
 CREATE TABLE IF NOT EXISTS regras_tipo_atividade (id integer,tipo integer);
 CREATE TABLE IF NOT EXISTS interacao (id integer,cliente_id integer);
 CREATE TABLE IF NOT EXISTS interacao_atividade (interacao_id integer,tipo_atividade_id integer,horario_final timestamp,estado_atividade_id integer);
 CREATE TABLE IF NOT EXISTS tipo_atividade (id integer,regras_tipo_atividade_id integer);
 CREATE TABLE IF NOT EXISTS comissao_repres (id integer,representante_id integer,tipo_comissao text,valor numeric,deleted_at timestamp);
 CREATE TABLE IF NOT EXISTS comissao_repres_excecao (id integer,pessoa_id integer,representante_id integer,tipo_comissao text,valor numeric,deleted_at timestamp,ativo text);
 CREATE TABLE IF NOT EXISTS controle_nota (id integer,nota_baixada_id integer,obs text,created_at timestamp,created_by text);
 CREATE TABLE IF NOT EXISTS centro_custo (id integer,codcusto text,nome text);
 CREATE TABLE IF NOT EXISTS centro_custo_nota (nota_baixada_id integer,centro_custo_id integer,valor_centro_custo numeric,comissao_centro_custo numeric);");
// Initially a fixture relation; the complete bridge migration is tested separately below.
$db->exec('CREATE TABLE IF NOT EXISTS view_cliente_categoria_historico (evento_id bigint,pessoa_id text,registrado_em timestamp,categoria_anterior_id text,categoria_nova_id text,operacao text,usuario text)');
$tables = ['nota_baixada','view_comissao_repres','coligada','pessoa','categoria_cliente','complemento','prazo_atividade','regras_tipo_atividade','interacao','interacao_atividade','tipo_atividade','comissao_repres','comissao_repres_excecao','controle_nota','centro_custo','centro_custo_nota','view_cliente_categoria_historico'];
$db->exec('TRUNCATE ' . implode(',', $tables));
$db->exec("INSERT INTO coligada VALUES (1,'Coligada teste');
 INSERT INTO categoria_cliente VALUES (1,'01','NÃO CLASSIFICADO'),(6,'06','E'),(7,'07','D');
 INSERT INTO regras_tipo_atividade VALUES (2,2);
 INSERT INTO tipo_atividade VALUES (2,2);
 INSERT INTO prazo_atividade VALUES (1,6,2,30,'N');
 INSERT INTO comissao_repres VALUES (1,1,'P',10,NULL);
 INSERT INTO centro_custo VALUES (1,'CC1','Centro teste'),(2,'CC2','Outro centro');");
for ($i = 1; $i <= 10; $i++) {
    $doc = str_pad((string) $i, 14, '0', STR_PAD_LEFT);
    $db->prepare("INSERT INTO pessoa VALUES (?, ?, ?, 6, NULL)")->execute([$i, "C$i", $doc]);
    $db->prepare("INSERT INTO nota_baixada VALUES (?, ?, ?, ?, 'Cliente teste', 1, 3997.50, '2026-10-13', '2026-10-13', 1, 'N', 0)")->execute([$i, "OS$i", "NF$i", $doc]);
    $db->prepare("INSERT INTO view_comissao_repres VALUES (?, 'Representante teste', 'Cliente teste', ?, ?)")->execute([$i, "C$i", "NF$i"]);
    $db->exec("INSERT INTO complemento VALUES ($i,$i,1,'2026-01-01',NULL);
       INSERT INTO interacao VALUES ($i,$i);
       INSERT INTO interacao_atividade VALUES ($i,2,'2026-10-09 10:00:00',2);
       INSERT INTO centro_custo_nota VALUES ($i,1,3000,0),($i,2,997.50,0)");
}
$event = $db->prepare('INSERT INTO view_cliente_categoria_historico VALUES (?,?,?,?,?,?,?)');
foreach ([
 [1,'1','2026-10-08 10:00:00',null,'1','created',null],
 [2,'1','2026-11-03 09:34:17','1','6','changed','marcio'],
 [3,'2','2026-10-01 10:00:00',null,'6','created',null],
 [4,'3','2026-10-01 10:00:00','1','6','changed','teste'],
 [5,'4','2026-11-03 10:00:00','1','7','changed','teste'],
 [6,'4','2026-11-06 10:00:00','7','6','changed','teste'],
 [7,'6','2026-11-03 10:00:00',null,'6','created','teste'],
 [8,'7','2026-10-15 10:00:00','1','6','changed','teste'],
 [9,'8','2026-10-16 00:00:00','1','6','changed','teste'],
 [10,'9','2026-10-01 10:00:00',null,'6','created','teste'],
 [11,'9','2026-11-01 10:00:00','6','7','changed','teste'],
 [12,'9','2026-11-03 10:00:00','7','6','changed','teste'],
 [13,'10','2026-10-01 10:00:00',null,'7','created','teste'],
 [14,'10','2026-11-03 10:00:00','1','6','changed','teste'],
] as $row) { $event->execute($row); }
$original = $db->query(executableSql('tests/fixtures/metabase-comissoes-original.sql'))->fetchAll(PDO::FETCH_ASSOC);
$new = $db->query(executableSql('docs/metabase/comissoes-com-historico-categoria.sql'))->fetchAll(PDO::FETCH_ASSOC);
preg_match_all('/\{\{\s*\w+\s*\}\}/', file_get_contents('tests/fixtures/metabase-comissoes-original.sql'), $originalFilters);
preg_match_all('/\{\{\s*\w+\s*\}\}/', file_get_contents('docs/metabase/comissoes-com-historico-categoria.sql'), $newFilters);
verify($originalFilters[0] === $newFilters[0], 'Parâmetros e ocorrências dos filtros do Metabase preservados');
verify(count($new) === 20, 'Rateios continuam com uma linha por nota e centro de custo');
$observations = [];
foreach ($new as $i => $row) {
    $oldRow = $original[$i];
    $observations[$row['Código']] = $row['Observação'];
    unset($row['Observação'], $oldRow['Observação']);
    verify($row === $oldRow, 'Valores e demais colunas preservados: linha ' . ($i + 1));
}
$petra = $observations['C1'];
verify(strpos($petra, '01 — NÃO CLASSIFICADO') !== false && strpos($petra, '03/11/2026 09:34:17') !== false && strpos($petra, '06 — E') !== false,
    'Explica a categoria na data-base e a alteração posterior no caso original');
verify(strpos($petra, 'pode explicar') !== false, 'Não afirma causa definitiva da comissão não gerada');
foreach (['C2','C3','C5','C7'] as $code) {
    verify(strpos($observations[$code], 'HISTÓRICO DE CATEGORIA') === false, 'Não inventa alteração posterior: ' . $code);
}
verify(strpos($observations['C4'], '2 registros posteriores') !== false, 'Informa múltiplas alterações posteriores');
verify(strpos($observations['C6'], 'não permitem confirmar') !== false, 'Registro inicial posterior não prova categoria anterior');
verify(strpos($observations['C8'], '16/10/2026 00:00:00') !== false, 'Meia-noite seguinte já é posterior ao dia da data-base');
verify(strpos($observations['C9'], 'pode explicar') === false, 'Retorno à categoria original não é apontado como causa da divergência atual');
verify(strpos($observations['C10'], 'não permitem confirmar') !== false, 'Lacuna entre registros não vira certeza histórica');

$db->exec("INSERT INTO controle_nota VALUES (1,1,'Decisão manual','2026-11-01','operador')");
$manual = $db->query(executableSql('docs/metabase/comissoes-com-historico-categoria.sql'))->fetchAll(PDO::FETCH_ASSOC);
foreach ($manual as $row) {
    if ($row['Código'] === 'C1') {
        verify(strpos($row['Observação'], 'REPROVAÇÃO MANUAL') !== false && strpos($row['Observação'], 'pode explicar') === false,
            'Histórico não substitui a decisão manual por uma causa automática');
    }
}
$db->exec("TRUNCATE controle_nota; UPDATE nota_baixada SET tem_comissao='C' WHERE id=1");
$cancelled = $db->query(executableSql('docs/metabase/comissoes-com-historico-categoria.sql'))->fetchAll(PDO::FETCH_ASSOC);
foreach ($cancelled as $row) {
    if ($row['Código'] === 'C1') {
        verify(strpos($row['Observação'], 'COMISSÃO CANCELADA') !== false && strpos($row['Observação'], 'pode explicar') === false,
            'Não atribui cancelamento à mudança de categoria');
    }
}
$db->exec("UPDATE nota_baixada SET tem_comissao='N' WHERE id=1");

// Exercise the real cross-database view in the same PostgreSQL version as production.
$historyRows = $db->query('SELECT * FROM view_cliente_categoria_historico')->fetchAll(PDO::FETCH_ASSOC);
$log = new PDO('pgsql:host=127.0.0.1;port=15437;dbname=versatronic_log', 'postgres', 'local-fixture-only', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
if ($log->query("SELECT current_setting('versa.test_context', true)")->fetchColumn() !== 'obs_categoria') {
    throw new RuntimeException('Log database is not the isolated fixture.');
}
$log->exec('CREATE TABLE IF NOT EXISTS system_change_log (id bigint,pkvalue text,logdate timestamp,oldvalue text,newvalue text,operation text,login text,tablename text,columnname text); TRUNCATE system_change_log');
$insert = $log->prepare("INSERT INTO system_change_log VALUES (?,?,?,?,?,?,?,'pessoa','categoria_cliente_id')");
foreach ($historyRows as $row) { $insert->execute(array_values($row)); }
$log->exec("INSERT INTO system_change_log VALUES (1000,'1','2026-11-01','secret old','secret new','changed','operador','pessoa','email'),(1001,'1','2026-11-01','6','6','changed','operador','pessoa','categoria_cliente_id')");
$db->exec('DROP TABLE public.view_cliente_categoria_historico');
$migration = file_get_contents('app/database/migrations/20261109_001_historico_categoria_relatorio.sql');
$db->exec($migration);
$db->exec($migration);
$bridge = $db->query(executableSql('docs/metabase/comissoes-com-historico-categoria.sql'))->fetchAll(PDO::FETCH_ASSOC);
verify($bridge === $new, 'Visão entre bancos produz exatamente as mesmas observações e valores');
verify((int)$db->query('SELECT count(*) FROM view_cliente_categoria_historico')->fetchColumn() === count($historyRows),
    'Visão não expõe outros campos da auditoria nem eventos sem mudança');
verify((int)$db->query("SELECT count(*) FROM pg_namespace n CROSS JOIN LATERAL aclexplode(n.nspacl) a WHERE n.nspname='crm_auditoria' AND a.grantee=0")->fetchColumn() === 0,
    'Schema da conexão não fica acessível ao papel PUBLIC');
echo "Consulta completa validada.\n";
