<?php

/** Read-only projection of the existing Pessoa audit; does not create past events. */
class ClienteHistoricoService
{
    private const PAGE_SIZE = 10;

    public static function carregar(int $pessoaId, int $pagina = 1): array
    {
        if ($pessoaId < 1) {
            throw new InvalidArgumentException('Histórico inválido.');
        }

        // Use a separate connection so an unavailable audit never rolls back the page's transaction.
        $conn = TConnection::open('log');
        $where = "tablename = 'pessoa' AND pkvalue = :pessoa AND columnname = 'categoria_cliente_id'
            AND operation IN ('created', 'changed', 'deleted')
            AND COALESCE(oldvalue, '') <> COALESCE(newvalue, '')";
        $params = [':pessoa' => (string) $pessoaId];
        $count = $conn->prepare("SELECT count(*) FROM system_change_log WHERE $where");
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / self::PAGE_SIZE));
        $pagina = min(max(1, $pagina), $pages);
        $offset = ($pagina - 1) * self::PAGE_SIZE;
        $query = $conn->prepare("SELECT id, logdate, login, operation, oldvalue, newvalue, class_name, php_sapi, log_trace
            FROM system_change_log WHERE $where
            ORDER BY logdate DESC, id DESC LIMIT " . self::PAGE_SIZE . " OFFSET $offset");
        $query->execute($params);
        $items = $query->fetchAll(PDO::FETCH_ASSOC);

        $categorias = [];
        if ($items) {
            $crm = TConnection::open('minicrm');
            foreach ($crm->query('SELECT id, codigo, nome FROM categoria_cliente')->fetchAll(PDO::FETCH_ASSOC) as $categoria) {
                $categorias[(string) $categoria['id']] = trim((string) $categoria['codigo']) !== ''
                    ? $categoria['codigo'] . ' — ' . $categoria['nome'] : $categoria['nome'];
            }
        }

        foreach ($items as &$item) {
            $item['anterior'] = self::valor($item['oldvalue'], $categorias);
            $item['novo'] = self::valor($item['newvalue'], $categorias);
            // The logger records the request class, not necessarily the method that performed the write.
            // Only confirmed CRM form writes may attribute the change to the session user.
            $origens = ['PessoaCategoriaClienteForm' => 'Alteração no CRM', 'AtualizacaoService' => 'Sincronização TOTVS'];
            $item['origem'] = $origens[$item['class_name'] ?? ''] ?? 'Origem não identificada';
            $trace = (string) ($item['log_trace'] ?? '');
            if (preg_match('/\bPessoaCategoriaClienteForm(?:->|::)onSave\(/', $trace)) {
                $item['origem'] = 'Alteração no CRM';
            }
            // A list can trigger synchronization while retaining the initiating user's session.
            // The trace identifies that path; the session login is not the author in TOTVS.
            if (strpos($trace, 'AtualizacaoService::atualizarCliente') !== false) {
                $item['origem'] = 'Sincronização TOTVS';
            }
            $item['responsavel'] = $item['origem'] === 'Alteração no CRM'
                ? (trim((string) $item['login']) !== '' ? $item['login'] : 'Não identificado') : null;
            unset($item['log_trace'], $item['login']);
            // Older logger versions classified NULL -> category as "created" even on existing customers.
            $item['evento'] = $item['operation'] === 'created' ? 'Categoria registrada'
                : ($item['operation'] === 'deleted' ? 'Remoção registrada' : 'Alteração');
        }
        unset($item);

        return ['items' => $items, 'total' => $total, 'page' => $pagina, 'pages' => $pages, 'pageSize' => self::PAGE_SIZE];
    }

    private static function valor($valor, array $categorias): string
    {
        if ($valor === null || (string) $valor === '') {
            return 'Não informada';
        }
        return $categorias[(string) $valor] ?? 'Categoria ID ' . $valor . ' (indisponível)';
    }
}
