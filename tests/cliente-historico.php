<?php
// Isolated SQLite integration test. Never connects to the configured databases.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
chdir(dirname(__DIR__));
require 'init.php';

$databaseFile = tempnam(sys_get_temp_dir(), 'versa_history_');
$info = ['type' => 'sqlite', 'name' => $databaseFile, 'prep' => '1'];
foreach (['minicrm', 'log'] as $database) {
    TConnection::setDatabaseInfo($database, $info);
}

function check($condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
    echo "OK: $message\n";
}

try {
    TTransaction::open('minicrm');
    $db = TTransaction::get();
    $db->exec(file_get_contents('app/database/log-sqlite.sql'));
    $db->exec('CREATE TABLE categoria_cliente (id INTEGER PRIMARY KEY, codigo TEXT, nome TEXT)');
    $db->exec("INSERT INTO categoria_cliente VALUES (1, '01', 'Não Classificado'), (6, '06', 'E')");
    $db->exec('CREATE TABLE pessoa (id INTEGER PRIMARY KEY, codigo TEXT, tipo_pessoa_id INTEGER,
        categoria_cliente_id INTEGER, system_user_id INTEGER, origem TEXT, razao_social TEXT,
        nome_fantasia TEXT, cpf_cnpj TEXT, rg_ie TEXT, nacionalidade_id INTEGER, fone TEXT,
        email TEXT, obs TEXT, created_at TEXT, updated_at TEXT, deleted_at TEXT, ativo TEXT,
        data_alteracao_totvs TEXT, bloqueado TEXT)');
    $db->exec("INSERT INTO pessoa (id, codigo, razao_social, categoria_cliente_id, obs)
        VALUES (1, 'C-TESTE', 'Cliente de teste', 1, NULL), (2, 'OUTRO', 'Outro cliente', 6, NULL)");
    TSession::setValue('login', 'teste');
    $_REQUEST['class'] = 'PessoaCategoriaClienteForm';

    // Record a real change through the existing model hooks.
    $pessoa = new Pessoa(1);
    $pessoa->categoria_cliente_id = 6;
    $pessoa->store();
    check((int) $db->query("SELECT count(*) FROM system_change_log WHERE columnname='categoria_cliente_id' AND oldvalue='1' AND newvalue='6'")->fetchColumn() === 1,
        'Alteração pelo model usado no formulário registra categoria anterior e nova');
    $before = (int) $db->query('SELECT count(*) FROM system_change_log')->fetchColumn();
    $pessoa->store();
    check((int) $db->query('SELECT count(*) FROM system_change_log')->fetchColumn() === $before,
        'Salvar sem mudança não duplica o histórico');
    $pessoa->categoria_cliente_id = null;
    $pessoa->store();
    TTransaction::close();

    $historico = ClienteHistoricoService::carregar(1, 1);
    check($historico['total'] === 2, 'Carrega somente mudanças da categoria do cliente solicitado');
    check($historico['items'][1]['anterior'] === '01 — Não Classificado' && $historico['items'][1]['novo'] === '06 — E',
        'Resolve códigos e nomes das categorias');
    check($historico['items'][0]['novo'] === 'Não informada', 'Representa categoria removida');
    check($historico['items'][0]['responsavel'] === 'teste' && $historico['items'][0]['origem'] === 'Alteração no CRM',
        'Salvamento pela tela de categoria preserva o usuário do CRM');
    $action = new TAction(['ClienteFormView', 'onShow'], ['key' => 1]);
    $crmHtml = (string) ClienteHistoricoPanel::criar($historico, $action);
    check(strpos($crmHtml, '>teste<') !== false && strpos($crmHtml, 'Alteração no CRM') !== false,
        'Exibe o responsável quando a alteração foi feita pela tela do CRM');

    // Setting a previously null category is a change on an existing customer.
    TTransaction::open('minicrm');
    $pessoa = new Pessoa(1);
    $pessoa->categoria_cliente_id = 6;
    $pessoa->store();
    check(TTransaction::get()->query("SELECT operation FROM system_change_log WHERE columnname='categoria_cliente_id' ORDER BY id DESC LIMIT 1")->fetchColumn() === 'changed',
        'Categoria nula para preenchida é alteração, não primeiro registro do cliente');
    TTransaction::close();

    // Add old audit records, including a different client and non-history fields.
    TTransaction::open('log');
    $insert = TTransaction::get()->prepare("INSERT INTO system_change_log
        (id,tablename,pkvalue,columnname,operation,logdate,oldvalue,newvalue,login,class_name,php_sapi)
        VALUES (?, 'pessoa', ?, ?, 'changed', '2026-01-01 10:00:00', '1', ?, '', '', 'cli')");
    for ($i = 100; $i < 112; $i++) {
        $insert->execute([$i, '1', 'categoria_cliente_id', '6']);
    }
    $insert->execute([200, '2', 'email', 'DADO DE OUTRO CLIENTE']);
    $insert->execute([201, '1', 'email', 'DADO FORA DO ESCOPO']);
    TTransaction::get()->exec("UPDATE categoria_cliente SET nome = 'E <script>alert(1)</script>' WHERE id=6");
    TTransaction::close();
    $historico = ClienteHistoricoService::carregar(1, 999);
    check($historico['total'] === 15 && $historico['page'] === 2 && count($historico['items']) === 5,
        'Paginação tem total confiável e limita página inválida');
    check($historico['items'][0]['responsavel'] === null, 'Não inventa autor em execução automática antiga');
    $action = new TAction(['ClienteFormView', 'onShow'], ['key' => 1]);
    $html = (string) ClienteHistoricoPanel::criar($historico, $action);
    check(strpos($html, '<script>alert(1)</script>') === false && strpos($html, '&lt;script&gt;') !== false,
        'Nomes de categoria são escapados contra HTML e scripts');
    check(strpos($html, 'DADO DE OUTRO CLIENTE') === false, 'Não exibe dados de outro cliente');
    check(strpos($html, 'Página 2 de 2') !== false, 'Exibe paginação acessível');
    $vazio = ClienteHistoricoService::carregar(2);
    check($vazio['total'] === 0, 'Cliente sem alterações possui estado vazio');
    check(strpos((string) ClienteHistoricoPanel::criar($vazio, $action),
        'Nenhuma alteração registrada') !== false, 'Estado vazio não inventa histórico');
    try {
        ClienteHistoricoService::carregar(0);
        throw new RuntimeException('Cliente inválido aceito');
    } catch (InvalidArgumentException $expected) {
        echo "OK: Cliente inválido é recusado\n";
    }

    TTransaction::open('log');
    TTransaction::get()->exec("INSERT INTO system_change_log
        (id,tablename,pkvalue,columnname,operation,logdate,oldvalue,newvalue,class_name)
        VALUES (300,'pessoa','1','categoria_cliente_id','created','2026-12-01 10:00:00',NULL,'6','AtualizaOutraCoisa')");
    TTransaction::close();
    $legado = ClienteHistoricoService::carregar(1);
    check($legado['items'][0]['evento'] === 'Categoria registrada',
        'Evento created legado não é apresentado como primeiro registro do cliente');
    check($legado['items'][0]['origem'] === 'Origem não identificada',
        'Não deduz sincronização a partir de nome de classe parecido');
    check(strpos((string) ClienteHistoricoPanel::criar($legado, $action), 'cadastro atual') !== false,
        'Interface informa que nomes das categorias refletem o cadastro atual');

    TTransaction::open('log');
    TTransaction::get()->exec("INSERT INTO system_change_log
        (id,tablename,pkvalue,columnname,operation,logdate,oldvalue,newvalue,login,class_name,log_trace)
        VALUES (301,'pessoa','1','categoria_cliente_id','changed','2026-12-02 10:00:00','1','6','usuario_sync','ClienteList','AtualizacaoService::atualizarCliente()')");
    TTransaction::close();
    $sync = ClienteHistoricoService::carregar(1)['items'][0];
    check($sync['origem'] === 'Sincronização TOTVS' && $sync['responsavel'] === null,
        'Sincronização não atribui a alteração ao usuário que sincronizou');
    check(!array_key_exists('log_trace', $sync) && !array_key_exists('login', $sync),
        'Rastreio e login bruto não são expostos no resultado do histórico');
    $syncHtml = (string) ClienteHistoricoPanel::criar(ClienteHistoricoService::carregar(1), $action);
    check(strpos($syncHtml, 'usuario_sync') === false && strpos($syncHtml, 'Usuário da sessão') === false,
        'Histórico não exibe o login da sincronização como autor da alteração');
    check(strpos($syncHtml, 'Sincronização TOTVS') !== false,
        'Interface identifica mudanças recebidas da TOTVS sem mostrar usuário');

    TTransaction::open('log');
    $insertOrigin = TTransaction::get()->prepare("INSERT INTO system_change_log
        (id,tablename,pkvalue,columnname,operation,logdate,oldvalue,newvalue,login,class_name,log_trace)
        VALUES (?,'pessoa','1','categoria_cliente_id','changed','2026-12-03 10:00:00','1','6',?,?,?)");
    foreach ([
        [302, 'autor_crm', 'OutraTela', 'PessoaCategoriaClienteForm->onSave()'],
        [303, 'sync_direto', 'AtualizacaoService', ''],
        [304, 'legado_sem_origem', 'ClienteList', ''],
        [305, 'sync_sobrepoe', 'PessoaCategoriaClienteForm', 'AtualizacaoService::atualizarCliente()'],
        [306, '', 'PessoaCategoriaClienteForm', ''],
    ] as $fixture) {
        $insertOrigin->execute($fixture);
    }
    TTransaction::close();
    $origens = array_column(ClienteHistoricoService::carregar(1)['items'], null, 'id');
    check($origens[302]['responsavel'] === 'autor_crm' && $origens[302]['origem'] === 'Alteração no CRM',
        'Rastreio do formulário também comprova alteração direta pelo CRM');
    foreach ([303, 304, 305] as $id) {
        check($origens[$id]['responsavel'] === null, 'Não atribui autoria de sincronização ou origem desconhecida: ' . $id);
    }
    check($origens[306]['responsavel'] === 'Não identificado', 'Alteração comprovada no CRM sem login não inventa usuário');
    $origensHtml = (string) ClienteHistoricoPanel::criar(ClienteHistoricoService::carregar(1), $action);
    check(strpos($origensHtml, '>autor_crm<') !== false && strpos($origensHtml, 'legado_sem_origem') === false
        && strpos($origensHtml, 'sync_direto') === false && strpos($origensHtml, 'sync_sobrepoe') === false,
        'Lista mista exibe somente os autores comprovados de alterações no CRM');

    // Audit failure must not leak configuration or break the customer's other tabs.
    TConnection::setDatabaseInfo('log', ['type' => 'sqlite', 'name' => sys_get_temp_dir() . '/versa-missing-directory/secret.db']);
    $erro = (string) ClienteHistoricoPanel::carregar(1, 1, $action);
    check(strpos($erro, 'Não foi possível carregar') !== false && strpos($erro, 'Tentar novamente') !== false,
        'Falha no banco de auditoria oferece recuperação');
    check(strpos($erro, 'secret.db') === false && strpos($erro, 'SQLSTATE') === false,
        'Erro não expõe detalhes de conexão');
    echo "Todos os testes passaram.\n";
} finally {
    TTransaction::rollbackAll();
    @unlink($databaseFile);
}
