<?php

/** Shared audit list for the customer view and the commission right panel. */
class ClienteHistoricoPanel
{
    public static function carregar(int $pessoaId, int $pagina, TAction $action): TElement
    {
        try {
            return self::criar(ClienteHistoricoService::carregar($pessoaId, $pagina), $action);
        } catch (Throwable $e) {
            // Do not expose connection strings, SQL, traces or other clients' audit data.
            $panel = self::container();
            $panel->add('<div class="cliente-history-state" role="alert"><strong>Não foi possível carregar o histórico.</strong><p>Tente novamente em instantes.</p></div>');
            $retry = new TActionLink('Tentar novamente', $action);
            $retry->class = 'btn btn-default';
            $panel->add($retry);
            return $panel;
        }
    }

    public static function criar(array $dados, TAction $action): TElement
    {
        $panel = self::container();
        $descricao = 'Mudanças de categoria do cliente';
        $panel->add('<p class="cliente-history-intro">Datas de registro no CRM, no horário de Brasília. Mudanças na TOTVS podem aparecer aqui após a sincronização. O responsável é exibido somente em alterações feitas pelo CRM. Nomes e códigos das categorias refletem o cadastro atual.</p>');

        if (!$dados['total']) {
            $panel->add('<div class="cliente-history-state" role="status"><strong>Nenhuma alteração registrada</strong><p>Não há mudanças de categoria na auditoria do CRM. A categoria atual não comprova a classificação em datas anteriores.</p></div>');
            return $panel;
        }

        $inicio = ($dados['page'] - 1) * $dados['pageSize'] + 1;
        $fim = min($dados['page'] * $dados['pageSize'], $dados['total']);
        $panel->add('<p class="cliente-history-summary" role="status">' . $inicio . '–' . $fim . ' de ' . $dados['total'] . ' registros · Mais recentes primeiro</p>');
        $table = '<div class="cliente-history-table-wrap"><table class="cliente-history-table"><caption class="sr-only">' . $descricao . '</caption><thead><tr><th scope="col">Registrado em</th><th scope="col">Valor anterior</th><th scope="col">Novo valor</th><th scope="col">Responsável / origem</th></tr></thead><tbody>';
        foreach ($dados['items'] as $item) {
            $data = self::data((string) $item['logdate']);
            $table .= '<tr><td data-label="Registrado em"><span class="cliente-history-date">' . self::escape($data) . '</span><span class="cliente-history-meta">' . self::escape($item['evento']) . '</span></td>'
                . '<td data-label="Valor anterior"><div class="cliente-history-value">' . self::escape($item['anterior']) . '</div></td>'
                . '<td data-label="Novo valor"><div class="cliente-history-value cliente-history-new">' . self::escape($item['novo']) . '</div></td>'
                . '<td data-label="Responsável / origem">'
                . ($item['responsavel'] !== null ? '<div class="cliente-history-value">' . self::escape($item['responsavel']) . '</div>' : '')
                . '<span class="cliente-history-meta">' . self::escape($item['origem']) . '</span></td></tr>';
        }
        $panel->add($table . '</tbody></table></div>');
        $nav = new TElement('nav');
        $nav->class = 'cliente-history-pagination';
        $nav->{'aria-label'} = 'Paginação do histórico de categorias';
        $nav->add('<span>Página ' . $dados['page'] . ' de ' . $dados['pages'] . '</span>');
        foreach (['Anterior' => -1, 'Próxima' => 1] as $label => $step) {
            $page = $dados['page'] + $step;
            if ($page < 1 || $page > $dados['pages']) {
                $nav->add('<button type="button" class="btn btn-default" disabled>' . $label . '</button>');
            } else {
                $link = new TActionLink($label, $action->cloneWithParameters(['categorias_page' => $page]));
                $link->class = 'btn btn-default';
                $nav->add($link);
            }
        }
        $panel->add($nav);
        return $panel;
    }

    private static function container(): TElement
    {
        $panel = new TElement('section');
        $panel->class = 'cliente-history';
        $panel->add('<link rel="stylesheet" href="app/resources/cliente-historico.css?v=1">');
        return $panel;
    }

    private static function escape($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function data(string $value): string
    {
        if ($value === '') {
            return 'Data não informada';
        }
        try {
            return (new DateTimeImmutable($value))->setTimezone(new DateTimeZone('America/Sao_Paulo'))->format('d/m/Y H:i:s');
        } catch (Exception $e) {
            return 'Data não informada';
        }
    }
}
