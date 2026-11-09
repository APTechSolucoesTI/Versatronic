<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
chdir(dirname(__DIR__));
require 'init.php';
$file = tempnam(sys_get_temp_dir(), 'versa_list_');
foreach (['minicrm','permission','log'] as $name) {
    TConnection::setDatabaseInfo($name, ['type'=>'sqlite','name'=>$file,'prep'=>'1']);
}
function ensure($condition, string $message): void {
    if (!$condition) { throw new RuntimeException($message); }
    echo "OK: $message\n";
}
try {
    TTransaction::open('minicrm');
    $db = TTransaction::get();
    foreach (['Coligada','CategoriaCliente','Representante','Pessoa'] as $model) {
        $record = new $model;
        $fields = ['id INTEGER PRIMARY KEY'];
        foreach ($record->getAttributes() as $attribute) {
            if ($attribute !== 'id') { $fields[] = $attribute . ' TEXT'; }
        }
        $db->exec('CREATE TABLE ' . $record->getEntity() . ' (' . implode(',', $fields) . ')');
    }
    $db->exec('CREATE TABLE IF NOT EXISTS system_preference (id text PRIMARY KEY,preference text)');
    $db->exec('CREATE TABLE view_comissao_repres (id integer PRIMARY KEY,coligada_id integer,data_emissao text,data_emissao_os text,codigo_cliente text,fantasia text,categoria_cliente text,numero_nota text,valor numeric,tem_comissao text,comissao numeric,representante text)');
    $db->exec("INSERT INTO coligada (id,nome) VALUES (1,'Teste'); INSERT INTO categoria_cliente (id,codigo,nome) VALUES (1,'06','E')");
    $insert = $db->prepare("INSERT INTO view_comissao_repres VALUES (?,1,'2026-10-01','2026-10-01','C-TESTE','Cliente teste','E',?,100,'S',3,?)");
    for ($id=1;$id<=75;$id++) { $insert->execute([$id,'NF'.$id, $id % 2 ? 'Representante A' : 'Representante B']); }
    TTransaction::close();
    $page = new ViewComissaoRepresHeaderList();
    $first = $page->onReload([]);
    ensure(count($first) === 20, 'Abertura padrão traz vinte notas, não todos os registros');
    $second = $page->onReload(['offset'=>20]);
    ensure(count($second) === 20 && $first[19]->id !== $second[0]->id, 'Navegação carrega a próxima página sem repetir notas');
    $gridProperty = new ReflectionProperty($page, 'datagrid');
    $gridProperty->setAccessible(true);
    $grid = $gridProperty->getValue($page);
    $totals = [];
    foreach ($grid->getColumns() as $column) {
        if (in_array($column->getName(), ['valor','comissao'], true)) {
            $callback = $column->getTotalCallback();
            $totals[$column->getName()] = $callback([100,100]);
        }
    }
    ensure((float)$totals['valor'] === 7500.0 && (float)$totals['comissao'] === 225.0,
        'Totais abrangem todas as notas filtradas, sem carregá-las na tela');
    TSession::setValue('ViewComissaoRepresHeaderList_filters', [new TFilter('representante','=','Representante B')]);
    $filtered = $page->onReload(['offset'=>0]);
    ensure(count($filtered) === 20 && $filtered[0]->representante === 'Representante B', 'Filtros continuam sendo aplicados no banco');
    foreach ($grid->getColumns() as $column) {
        if ($column->getName() === 'valor') { ensure((float)($column->getTotalCallback())([]) === 3700.0, 'Total acompanha o filtro ativo'); }
    }
    TSession::setValue('ViewComissaoRepresHeaderList_datagrid_properties', (object)['pageLimit'=>100000]);
    $unsafePreference = new ViewComissaoRepresHeaderList();
    ensure(count($unsafePreference->onReload([])) <= 100, 'Preferência antiga não permite uma página ilimitada');
} finally {
    TTransaction::rollbackAll();
    @unlink($file);
}
