WITH notas_filtradas AS (
    SELECT
        nb.id AS nota_id,
        nb.numero,
        nb.numero_nf,
        nb.documento,
        regexp_replace(
            COALESCE(nb.documento, ''),
            '[^0-9]',
            '',
            'g'
        ) AS documento_limpo,

        nb.razao_social,
        nb.coligada_id,
        nb.valor_total,
        nb.data_emissao,
        nb.data_emissao_os,
        nb.nota_status_id,
        nb.tem_comissao,
        nb.comissao,

        CASE
            WHEN nb.data_emissao_os IS NOT NULL
            THEN nb.data_emissao_os::date + 2
            ELSE NULL
        END AS data_base,

        TRIM(v.representante) AS representante,

        COALESCE(
            NULLIF(
                TRIM(
                    COALESCE(
                        v.fantasia,
                        nb.razao_social
                    )
                ),
                ''
            ),
            'Sem cliente'
        ) AS cliente,

        v.codigo_cliente,

        COALESCE(
            NULLIF(TRIM(col.nome), ''),
            'Sem coligada'
        ) AS coligada,

        COALESCE(
            NULLIF(TRIM(nb.numero_nf), ''),
            NULLIF(TRIM(v.numero_nota), ''),
            'Sem numero'
        ) AS numero_exibicao

    FROM nota_baixada nb

    LEFT JOIN view_comissao_repres v
        ON v.id = nb.id

    LEFT JOIN coligada col
        ON col.id = nb.coligada_id

    WHERE
        nb.data_emissao >= TIMESTAMP '2026-06-01 00:00:00'

        AND NULLIF(TRIM(v.representante), '') IS NOT NULL

        /*
         * DATAS
         * Configurar como Field Filter no Metabase.
         */
        [[AND {{data_emissao}}]]
        [[AND {{data_os}}]]

        /*
         * COLIGADA
         */
        [[AND
            COALESCE(
                NULLIF(TRIM(col.nome), ''),
                'Sem coligada'
            ) = {{coligada}}
        ]]

        /*
         * REPRESENTANTE
         */
        [[AND
            TRIM(v.representante) = {{representante}}
        ]]

        /*
         * STATUS
         */
        [[AND
            CASE
                WHEN nb.tem_comissao = 'S' THEN 'Sim'
                WHEN nb.tem_comissao = 'C' THEN 'Cancelada'
                WHEN nb.tem_comissao = 'P' THEN 'P'
                ELSE 'Não'
            END = {{status}}
        ]]

        /*
         * NÚMERO DA NF
         */
        [[AND
            COALESCE(
                NULLIF(TRIM(nb.numero_nf), ''),
                NULLIF(TRIM(v.numero_nota), ''),
                'Sem numero'
            ) = {{numero_nf}}
        ]]

        /*
         * CLIENTE
         */
        [[AND
            COALESCE(
                NULLIF(
                    TRIM(
                        COALESCE(
                            v.fantasia,
                            nb.razao_social
                        )
                    ),
                    ''
                ),
                'Sem cliente'
            )
            ILIKE CONCAT('%', {{cliente}}, '%')
        ]]

        /*
         * CÓDIGO
         */
        [[AND
            CAST(v.codigo_cliente AS text)
            =
            CAST({{codigo}} AS text)
        ]]

        /*
         * CENTRO DE CUSTO
         *
         * EXISTS evita trazer o rateio inteiro agora.
         */
        [[AND EXISTS (
            SELECT 1
            FROM centro_custo_nota ccn_f
            INNER JOIN centro_custo cc_f
                ON cc_f.id = ccn_f.centro_custo_id
            WHERE
                ccn_f.nota_baixada_id = nb.id
                AND COALESCE(
                    NULLIF(
                        TRIM(
                            cc_f.codcusto
                            || ' - '
                            || cc_f.nome
                        ),
                        ''
                    ),
                    'Sem centro de custo'
                ) = {{centro_custo}}
        )]]
),

/* =========================================================
   DOCUMENTOS QUE REALMENTE PRECISAMOS PROCURAR
   ========================================================= */
documentos AS (
    SELECT DISTINCT
        documento_limpo

    FROM notas_filtradas

    WHERE documento_limpo <> ''
),

/* =========================================================
   CLIENTE

   Faz a limpeza do CPF/CNPJ da pessoa uma vez sobre
   os documentos necessários, em vez de um LATERAL
   para cada nota.
   ========================================================= */
pessoas_match AS (
    SELECT DISTINCT ON (
        regexp_replace(
            COALESCE(p.cpf_cnpj, ''),
            '[^0-9]',
            '',
            'g'
        )
    )
        regexp_replace(
            COALESCE(p.cpf_cnpj, ''),
            '[^0-9]',
            '',
            'g'
        ) AS documento_limpo,

        p.id AS pessoa_id,
        p.codigo AS codigo_pessoa,
        p.categoria_cliente_id

    FROM pessoa p

    INNER JOIN documentos d
        ON d.documento_limpo =
           regexp_replace(
               COALESCE(p.cpf_cnpj, ''),
               '[^0-9]',
               '',
               'g'
           )

    WHERE
        p.deleted_at IS NULL

    ORDER BY
        regexp_replace(
            COALESCE(p.cpf_cnpj, ''),
            '[^0-9]',
            '',
            'g'
        ),
        p.id DESC
),

/* =========================================================
   NOTAS + CLIENTE
   ========================================================= */
notas_cliente AS (
    SELECT
        n.*,

        p.pessoa_id,
        p.codigo_pessoa,
        p.categoria_cliente_id,

        cat.nome AS categoria

    FROM notas_filtradas n

    LEFT JOIN pessoas_match p
        ON p.documento_limpo = n.documento_limpo

    LEFT JOIN categoria_cliente cat
        ON cat.id = p.categoria_cliente_id
),

/* =========================================================
   PESSOAS NECESSÁRIAS
   ========================================================= */
pessoas_relevantes AS (
    SELECT DISTINCT pessoa_id
    FROM notas_cliente
    WHERE pessoa_id IS NOT NULL
),

/* =========================================================
   ÚLTIMO REPRESENTANTE DA PESSOA
   ========================================================= */
complementos AS (
    SELECT DISTINCT ON (c.pessoa_id)
        c.pessoa_id,
        c.representante_id

    FROM complemento c

    INNER JOIN pessoas_relevantes pr
        ON pr.pessoa_id = c.pessoa_id

    WHERE
        c.deleted_at IS NULL
        AND c.representante_id IS NOT NULL

    ORDER BY
        c.pessoa_id,
        c.created_at DESC NULLS LAST,
        c.id DESC
),

/* =========================================================
   CATEGORIAS QUE REALMENTE APARECEM
   ========================================================= */
categorias_relevantes AS (
    SELECT DISTINCT categoria_cliente_id
    FROM notas_cliente
    WHERE categoria_cliente_id IS NOT NULL
),

/* =========================================================
   ÚLTIMA REGRA DE CADA TIPO PARA CADA CATEGORIA

   tipo 1 = Física
   tipo 2 = Contato
   ========================================================= */
regras_categoria_raw AS (
    SELECT DISTINCT ON (
        pa.categoria_cliente_id,
        rta.tipo
    )
        pa.categoria_cliente_id,
        rta.tipo,

        pa.regras_tipo_atividade_id,
        pa.dias,
        COALESCE(pa.ambos, 'N') AS ambos

    FROM prazo_atividade pa

    INNER JOIN regras_tipo_atividade rta
        ON rta.id = pa.regras_tipo_atividade_id

    INNER JOIN categorias_relevantes cr
        ON cr.categoria_cliente_id = pa.categoria_cliente_id

    WHERE
        rta.tipo IN (1, 2)

    ORDER BY
        pa.categoria_cliente_id,
        rta.tipo,
        pa.id DESC
),

/* =========================================================
   PIVOTA FÍSICA / CONTATO PARA UMA LINHA POR CATEGORIA
   ========================================================= */
regras_categoria AS (
    SELECT
        categoria_cliente_id,

        MAX(regras_tipo_atividade_id)
            FILTER (WHERE tipo = 1)
            AS regra_tipo1_id,

        MAX(dias)
            FILTER (WHERE tipo = 1)
            AS dias_tipo1,

        MAX(regras_tipo_atividade_id)
            FILTER (WHERE tipo = 2)
            AS regra_tipo2_id,

        MAX(dias)
            FILTER (WHERE tipo = 2)
            AS dias_tipo2,

        COALESCE(
            MAX(ambos)
                FILTER (WHERE tipo = 2),
            'N'
        ) AS ambos_tipo2

    FROM regras_categoria_raw

    GROUP BY
        categoria_cliente_id
),

/* =========================================================
   BASE COMPLETA
   ========================================================= */
base AS (
    SELECT
        n.*,

        comp.representante_id,

        rc.regra_tipo1_id,
        rc.dias_tipo1,

        rc.regra_tipo2_id,
        rc.dias_tipo2,
        COALESCE(rc.ambos_tipo2, 'N') AS ambos_tipo2

    FROM notas_cliente n

    LEFT JOIN complementos comp
        ON comp.pessoa_id = n.pessoa_id

    LEFT JOIN regras_categoria rc
        ON rc.categoria_cliente_id = n.categoria_cliente_id
),

/* =========================================================
   ATIVIDADES

   ESSA É A PRINCIPAL OTIMIZAÇÃO.

   Antes eram 3 LATERALs:
       1. Física da regra
       2. Contato da regra
       3. Qualquer Física real

   Agora fazemos UMA busca nas atividades da pessoa/nota
   e calculamos os 3 MAX() de uma vez.
   ========================================================= */
atividades AS (
    SELECT
        b.*,

        at.ultima_tipo1,
        at.ultima_tipo2,
        at.ultima_tipo1_real

    FROM base b

    LEFT JOIN LATERAL (
        SELECT
            MAX(ia.horario_final)
                FILTER (
                    WHERE
                        ta.regras_tipo_atividade_id =
                        b.regra_tipo1_id
                )
                AS ultima_tipo1,

            MAX(ia.horario_final)
                FILTER (
                    WHERE
                        ta.regras_tipo_atividade_id =
                        b.regra_tipo2_id
                )
                AS ultima_tipo2,

            MAX(ia.horario_final)
                FILTER (
                    WHERE
                        rta.tipo = 1
                )
                AS ultima_tipo1_real

        FROM interacao i

        INNER JOIN interacao_atividade ia
            ON ia.interacao_id = i.id

        INNER JOIN tipo_atividade ta
            ON ta.id = ia.tipo_atividade_id

        INNER JOIN regras_tipo_atividade rta
            ON rta.id = ta.regras_tipo_atividade_id

        WHERE
            i.cliente_id = b.pessoa_id

            AND ia.estado_atividade_id = 2

            AND ia.horario_final IS NOT NULL

            AND b.data_base IS NOT NULL

            AND ia.horario_final <
                (b.data_base + 1)

            /*
             * Só procura tipos que podem importar
             * para essa nota.
             */
            AND (
                ta.regras_tipo_atividade_id = b.regra_tipo1_id
                OR
                ta.regras_tipo_atividade_id = b.regra_tipo2_id
                OR
                rta.tipo = 1
            )

    ) at ON true
),

/* =========================================================
   SALDO DE PRAZO
   ========================================================= */
prazos AS (
    SELECT
        a.*,

        /* =================================================
           FÍSICA
           ================================================= */
        CASE
            WHEN a.pessoa_id IS NULL
                THEN 0

            WHEN a.categoria_cliente_id IS NULL
                THEN 9999

            /*
             * D/E:
             * Física usa o prazo de Contato.
             */
            WHEN a.categoria IN ('D', 'E')
             AND a.dias_tipo2 IS NULL
                THEN 9999

            WHEN a.categoria IN ('D', 'E')
             AND a.ultima_tipo1_real IS NULL
                THEN 0

            WHEN a.categoria IN ('D', 'E')
                THEN GREATEST(
                    a.dias_tipo2
                    -
                    (
                        a.data_base
                        -
                        a.ultima_tipo1_real::date
                    ),
                    0
                )

            /*
             * Demais categorias.
             */
            WHEN a.regra_tipo1_id IS NULL
                THEN 9999

            WHEN a.ultima_tipo1 IS NULL
                THEN 0

            ELSE
                GREATEST(
                    a.dias_tipo1
                    -
                    (
                        a.data_base
                        -
                        a.ultima_tipo1::date
                    ),
                    0
                )

        END AS tipo1,

        /* =================================================
           CONTATO
           ================================================= */
        CASE
            WHEN a.pessoa_id IS NULL
                THEN 0

            WHEN a.categoria_cliente_id IS NULL
                THEN 9999

            WHEN a.regra_tipo2_id IS NULL
                THEN 9999

            /*
             * Física também válida = SIM
             */
            WHEN COALESCE(a.ambos_tipo2, 'N') = 'S'
                THEN
                    CASE
                        WHEN a.ultima_tipo1_real IS NULL
                         AND a.ultima_tipo2 IS NULL
                            THEN 0

                        ELSE
                            GREATEST(
                                a.dias_tipo2
                                -
                                (
                                    a.data_base
                                    -
                                    GREATEST(
                                        a.ultima_tipo1_real,
                                        a.ultima_tipo2
                                    )::date
                                ),
                                0
                            )
                    END

            /*
             * Física também válida = NÃO
             */
            WHEN a.ultima_tipo2 IS NULL
                THEN 0

            ELSE
                GREATEST(
                    a.dias_tipo2
                    -
                    (
                        a.data_base
                        -
                        a.ultima_tipo2::date
                    ),
                    0
                )

        END AS tipo2

    FROM atividades a
),

/* =========================================================
   CLIENTE ATIVO

   D/E = Física OU Contato.

   Demais = Física E Contato,
   respeitando 9999 como N/A.
   ========================================================= */
status_cliente AS (
    SELECT
        p.*,

        CASE
            WHEN p.pessoa_id IS NULL
                THEN false

            WHEN p.categoria IN ('D', 'E')
             AND (
                    p.tipo1 > 0
                    OR p.tipo2 > 0
                 )
                THEN true

            WHEN p.tipo1 = 9999
             AND p.tipo2 = 9999
                THEN false

            WHEN (
                    p.tipo1 > 0
                    OR p.tipo1 = 9999
                 )
             AND (
                    p.tipo2 > 0
                    OR p.tipo2 = 9999
                 )
                THEN true

            ELSE false
        END AS cliente_ativo

    FROM prazos p
),

/* =========================================================
   REPRESENTANTES QUE REALMENTE PRECISAMOS
   ========================================================= */
representantes_relevantes AS (
    SELECT DISTINCT representante_id
    FROM status_cliente
    WHERE representante_id IS NOT NULL
),

/* =========================================================
   ÚLTIMA REGRA PADRÃO DE CADA REPRESENTANTE
   ========================================================= */
regra_representante AS (
    SELECT DISTINCT ON (c.representante_id)
        c.representante_id,
        c.id AS regra_representante_id,
        c.tipo_comissao,
        c.valor

    FROM comissao_repres c

    INNER JOIN representantes_relevantes rr
        ON rr.representante_id = c.representante_id

    WHERE
        c.deleted_at IS NULL

    ORDER BY
        c.representante_id,
        c.id DESC
),

/* =========================================================
   PARES CLIENTE + REPRESENTANTE NECESSÁRIOS
   ========================================================= */
pares_relevantes AS (
    SELECT DISTINCT
        pessoa_id,
        representante_id

    FROM status_cliente

    WHERE
        pessoa_id IS NOT NULL
        AND representante_id IS NOT NULL
),

/* =========================================================
   ÚLTIMA EXCEÇÃO ATIVA CLIENTE + REPRESENTANTE
   ========================================================= */
excecoes AS (
    SELECT DISTINCT ON (
        e.pessoa_id,
        e.representante_id
    )
        e.pessoa_id,
        e.representante_id,
        e.id AS excecao_comissao_id,
        e.tipo_comissao,
        e.valor

    FROM comissao_repres_excecao e

    INNER JOIN pares_relevantes pr
        ON pr.pessoa_id = e.pessoa_id
       AND pr.representante_id = e.representante_id

    WHERE
        e.deleted_at IS NULL
        AND e.ativo = 'S'

    ORDER BY
        e.pessoa_id,
        e.representante_id,
        e.id DESC
),

/* =========================================================
   APLICA REGRA FINANCEIRA
   ========================================================= */
regra_comissao AS (
    SELECT
        s.*,

        exc.excecao_comissao_id,
        rr.regra_representante_id,

        COALESCE(
            exc.tipo_comissao,
            rr.tipo_comissao
        ) AS tipo_comissao_final,

        COALESCE(
            exc.valor,
            rr.valor
        ) AS valor_comissao_regra

    FROM status_cliente s

    LEFT JOIN excecoes exc
        ON exc.pessoa_id = s.pessoa_id
       AND exc.representante_id = s.representante_id

    LEFT JOIN regra_representante rr
        ON rr.representante_id = s.representante_id
),

/* =========================================================
   RESULTADO ATUAL DAS REGRAS
   ========================================================= */
calculo AS (
    SELECT
        r.*,

        CASE
            WHEN r.nota_status_id = 1
             AND r.data_emissao_os IS NOT NULL
             AND r.cliente_ativo = true
             AND r.tipo_comissao_final IS NOT NULL
                THEN 'S'

            ELSE 'N'
        END AS resultado_regra_atual,

        CASE
            WHEN r.nota_status_id = 1
             AND r.data_emissao_os IS NOT NULL
             AND r.cliente_ativo = true
             AND r.tipo_comissao_final = 'P'
                THEN ROUND(
                    COALESCE(r.valor_total, 0)::numeric
                    *
                    COALESCE(r.valor_comissao_regra, 0)::numeric
                    / 100,
                    2
                )

            WHEN r.nota_status_id = 1
             AND r.data_emissao_os IS NOT NULL
             AND r.cliente_ativo = true
             AND r.tipo_comissao_final = 'V'
                THEN ROUND(
                    COALESCE(
                        r.valor_comissao_regra,
                        0
                    )::numeric,
                    2
                )

            ELSE 0::numeric
        END AS comissao_regra_atual

    FROM regra_comissao r
),

/* =========================================================
   HISTORICO DE CATEGORIA - somente contexto da observacao.
   A data-base inclui o dia inteiro, como o corte das atividades.
   Nao altera categoria, regras ou valores usados no calculo.
   ========================================================= */
historico_categoria_fonte AS MATERIALIZED (
    SELECT h.pessoa_id, ARRAY_AGG(h ORDER BY h.registrado_em, h.evento_id) AS eventos
    FROM public.view_cliente_categoria_historico h
    INNER JOIN pessoas_relevantes pr
        ON pr.pessoa_id::text = h.pessoa_id
    GROUP BY h.pessoa_id
),
categoria_nota_eventos AS (
    SELECT
        c.nota_id,
        c.data_base,
        c.categoria_cliente_id,
        anterior.evento_id AS evento_anterior_id,
        anterior.categoria_nova_id AS anterior_nova_id,
        posterior.evento_id AS evento_posterior_id,
        posterior.registrado_em AS alteracao_registrada_em,
        posterior.categoria_anterior_id AS alteracao_anterior_id,
        posterior.categoria_nova_id AS alteracao_nova_id,
        posterior.operacao AS alteracao_operacao,
        posterior.usuario AS alteracao_usuario,
        posterior.registros_posteriores,
        CASE
            WHEN anterior.evento_id IS NOT NULL
                THEN anterior.categoria_nova_id
            ELSE posterior.categoria_anterior_id
        END AS categoria_base_id,
        CASE
            WHEN anterior.evento_id IS NOT NULL THEN
                anterior.categoria_nova_id IS NOT DISTINCT FROM posterior.categoria_anterior_id
            ELSE posterior.operacao IN ('changed', 'deleted')
        END AS categoria_base_confirmada
    FROM calculo c
    LEFT JOIN historico_categoria_fonte hp ON hp.pessoa_id = c.pessoa_id::text
    LEFT JOIN LATERAL (
        SELECT h.*
        FROM UNNEST(hp.eventos) h
        WHERE h.pessoa_id = c.pessoa_id::text
          AND h.registrado_em < (c.data_base + 1)
        ORDER BY h.registrado_em DESC, h.evento_id DESC
        LIMIT 1
    ) anterior ON true
    LEFT JOIN LATERAL (
        SELECT h.*, COUNT(*) OVER () AS registros_posteriores
        FROM UNNEST(hp.eventos) h
        WHERE h.pessoa_id = c.pessoa_id::text
          AND h.registrado_em >= (c.data_base + 1)
        ORDER BY h.registrado_em, h.evento_id
        LIMIT 1
    ) posterior ON true
),
categoria_nota_rotulos AS (
    SELECT
        e.*,
        CASE WHEN e.categoria_base_id IS NULL THEN 'Sem categoria informada'
             ELSE COALESCE(NULLIF(CONCAT_WS(' — ', NULLIF(TRIM(cb.codigo), ''), cb.nome), ''), 'Categoria ID ' || e.categoria_base_id)
        END AS categoria_base_nome,
        CASE WHEN e.alteracao_anterior_id IS NULL THEN 'Sem categoria informada'
             ELSE COALESCE(NULLIF(CONCAT_WS(' — ', NULLIF(TRIM(ca.codigo), ''), ca.nome), ''), 'Categoria ID ' || e.alteracao_anterior_id)
        END AS alteracao_anterior_nome,
        CASE WHEN e.alteracao_nova_id IS NULL THEN 'Sem categoria informada'
             ELSE COALESCE(NULLIF(CONCAT_WS(' — ', NULLIF(TRIM(cn.codigo), ''), cn.nome), ''), 'Categoria ID ' || e.alteracao_nova_id)
        END AS alteracao_nova_nome,
        CASE WHEN e.categoria_cliente_id IS NULL THEN 'Sem categoria informada'
             ELSE COALESCE(NULLIF(CONCAT_WS(' — ', NULLIF(TRIM(catual.codigo), ''), catual.nome), ''), 'Categoria ID ' || e.categoria_cliente_id::text)
        END AS categoria_atual_nome
    FROM categoria_nota_eventos e
    LEFT JOIN categoria_cliente cb ON cb.id::text = e.categoria_base_id
    LEFT JOIN categoria_cliente ca ON ca.id::text = e.alteracao_anterior_id
    LEFT JOIN categoria_cliente cn ON cn.id::text = e.alteracao_nova_id
    LEFT JOIN categoria_cliente catual ON catual.id = e.categoria_cliente_id
),
diagnostico_categoria AS (
    SELECT
        h.nota_id,
        h.categoria_base_id,
        COALESCE(h.categoria_base_confirmada, false) AS categoria_base_confirmada,
        CASE
            WHEN h.data_base IS NULL OR h.evento_posterior_id IS NULL THEN NULL
            WHEN h.categoria_base_confirmada THEN CONCAT(
                'HISTÓRICO DE CATEGORIA: na data-base ', TO_CHAR(h.data_base, 'DD/MM/YYYY'),
                ', a categoria registrada no CRM era ', h.categoria_base_nome, '. ',
                'Em ', TO_CHAR(h.alteracao_registrada_em, 'DD/MM/YYYY HH24:MI:SS'),
                ', foi registrada no CRM a alteração de ', h.alteracao_anterior_nome,
                ' para ', h.alteracao_nova_nome,
                CASE WHEN NULLIF(TRIM(h.alteracao_usuario), '') IS NOT NULL
                     THEN CONCAT(', pelo login ', h.alteracao_usuario) ELSE '' END,
                '. Categoria atual: ', h.categoria_atual_nome, '. ',
                CASE WHEN h.registros_posteriores > 1
                     THEN CONCAT('Há ', h.registros_posteriores, ' registros posteriores à data-base no histórico do cliente. ')
                     ELSE '' END,
                'Datas de registro no CRM; nomes e códigos das categorias seguem o cadastro atual.'
            )
            ELSE CONCAT(
                'HISTÓRICO DE CATEGORIA: há registro em ',
                TO_CHAR(h.alteracao_registrada_em, 'DD/MM/YYYY HH24:MI:SS'),
                ', após a data-base ', TO_CHAR(h.data_base, 'DD/MM/YYYY'),
                ', mas os registros disponíveis não permitem confirmar a categoria naquela data. ',
                'Categoria atual: ', h.categoria_atual_nome,
                '. O cadastro atual não comprova a classificação histórica.'
            )
        END AS detalhe_historico_categoria
    FROM categoria_nota_rotulos h
),

/* =========================================================
   ÚLTIMO CONTROLE MANUAL

   Somente das notas que realmente estão no relatório.
   ========================================================= */
controle_manual AS (
    SELECT DISTINCT ON (cn.nota_baixada_id)
        cn.nota_baixada_id,
        cn.id AS controle_nota_id,

        NULLIF(
            TRIM(
                regexp_replace(
                    regexp_replace(
                        COALESCE(cn.obs, ''),
                        '<[^>]+>',
                        ' ',
                        'g'
                    ),
                    '[[:space:]]+',
                    ' ',
                    'g'
                )
            ),
            ''
        ) AS obs_limpa,

        cn.created_at,
        cn.created_by

    FROM controle_nota cn

    INNER JOIN notas_filtradas nf
        ON nf.nota_id = cn.nota_baixada_id

    ORDER BY
        cn.nota_baixada_id,
        cn.created_at DESC NULLS LAST,
        cn.id DESC
),

/* =========================================================
   TEXTO DA REGRA

   Aqui já gera diretamente a observação final.
   ========================================================= */
auditoria AS (
    SELECT
        c.*,
        hc.detalhe_historico_categoria,
        hc.categoria_base_id AS categoria_base_auditoria_id,
        hc.categoria_base_confirmada,

        cm.controle_nota_id,
        cm.obs_limpa,
        cm.created_at AS controle_created_at,
        cm.created_by AS controle_created_by,

        /* =================================================
           EXPLICAÇÃO DA CATEGORIA
           ================================================= */
        CASE
            WHEN c.pessoa_id IS NULL THEN
                'Cliente não encontrado pelo CPF/CNPJ da nota.'

            WHEN c.categoria_cliente_id IS NULL THEN
                'Cliente sem Categoria de Cliente cadastrada.'

            WHEN c.categoria IN ('D', 'E') THEN
                CONCAT(
                    'Categoria atual ',
                    c.categoria,
                    ': basta Física OU Contato estar válido.'
                )

            ELSE
                CONCAT(
                    'Categoria atual ',
                    COALESCE(c.categoria, 'não informada'),
                    ': Física e Contato devem estar válidos quando as duas regras existirem.'
                )

        END AS detalhe_categoria,

        /* =================================================
           FÍSICA
           ================================================= */
        CASE
            WHEN c.data_emissao_os IS NULL THEN
                'Física não avaliada: nota sem Data O.S.'

            WHEN c.pessoa_id IS NULL THEN
                'Física não avaliada: cliente não encontrado.'

            WHEN c.categoria_cliente_id IS NULL THEN
                'Física: N/A.'

            WHEN c.categoria IN ('D', 'E')
             AND c.dias_tipo2 IS NULL THEN
                'Física: N/A, pois não existe prazo de Contato configurado.'

            WHEN c.categoria IN ('D', 'E')
             AND c.ultima_tipo1_real IS NULL THEN
                CONCAT(
                    'Física: nenhuma atividade encontrada nos ',
                    COALESCE(c.dias_tipo2, 0),
                    ' dias previstos. Física VENCIDA.'
                )

            WHEN c.categoria IN ('D', 'E') THEN
                CONCAT(
                    'Física: última em ',
                    TO_CHAR(c.ultima_tipo1_real, 'DD/MM/YYYY'),
                    ', prazo ',
                    c.dias_tipo2,
                    ' dias, saldo ',
                    c.tipo1,
                    ' dias. ',
                    CASE
                        WHEN c.tipo1 > 0
                            THEN 'Física VÁLIDA.'
                        ELSE 'Física VENCIDA.'
                    END
                )

            WHEN c.regra_tipo1_id IS NULL THEN
                'Física: N/A para esta categoria.'

            WHEN c.ultima_tipo1 IS NULL THEN
                CONCAT(
                    'Física: nenhuma atividade encontrada nos ',
                    COALESCE(c.dias_tipo1, 0),
                    ' dias previstos. Física VENCIDA.'
                )

            ELSE
                CONCAT(
                    'Física: última em ',
                    TO_CHAR(c.ultima_tipo1, 'DD/MM/YYYY'),
                    ', prazo ',
                    c.dias_tipo1,
                    ' dias, saldo ',
                    c.tipo1,
                    ' dias. ',
                    CASE
                        WHEN c.tipo1 > 0
                            THEN 'Física VÁLIDA.'
                        ELSE 'Física VENCIDA.'
                    END
                )

        END AS detalhe_fisica,

        /* =================================================
           CONTATO
           ================================================= */
        CASE
            WHEN c.data_emissao_os IS NULL THEN
                'Contato não avaliado: nota sem Data O.S.'

            WHEN c.pessoa_id IS NULL THEN
                'Contato não avaliado: cliente não encontrado.'

            WHEN c.categoria_cliente_id IS NULL THEN
                'Contato: N/A.'

            WHEN c.regra_tipo2_id IS NULL THEN
                'Contato: N/A para esta categoria.'

            WHEN c.ambos_tipo2 = 'S'
             AND c.ultima_tipo1_real IS NULL
             AND c.ultima_tipo2 IS NULL THEN
                CONCAT(
                    'Contato: nenhuma Física ou Contato encontrada nos ',
                    c.dias_tipo2,
                    ' dias previstos. ',
                    'Atividade física também válida: SIM. ',
                    'Contato VENCIDO.'
                )

            WHEN c.ambos_tipo2 = 'S' THEN
                CONCAT(
                    'Contato: prazo ',
                    c.dias_tipo2,
                    ' dias. Atividade física também válida: SIM. ',
                    'Última atividade considerada: ',
                    CASE
                        WHEN c.ultima_tipo1_real IS NOT NULL
                         AND (
                                c.ultima_tipo2 IS NULL
                                OR c.ultima_tipo1_real >= c.ultima_tipo2
                             )
                            THEN 'Física'
                        ELSE 'Contato'
                    END,
                    ' em ',
                    TO_CHAR(
                        GREATEST(
                            c.ultima_tipo1_real,
                            c.ultima_tipo2
                        ),
                        'DD/MM/YYYY'
                    ),
                    ', saldo ',
                    c.tipo2,
                    ' dias. ',
                    CASE
                        WHEN c.tipo2 > 0
                            THEN 'Contato VÁLIDO.'
                        ELSE 'Contato VENCIDO.'
                    END
                )

            WHEN c.ultima_tipo2 IS NULL THEN
                CONCAT(
                    'Contato: nenhuma atividade encontrada nos ',
                    c.dias_tipo2,
                    ' dias previstos. ',
                    'Atividade física também válida: NÃO. ',
                    'Contato VENCIDO.'
                )

            ELSE
                CONCAT(
                    'Contato: última em ',
                    TO_CHAR(c.ultima_tipo2, 'DD/MM/YYYY'),
                    ', prazo ',
                    c.dias_tipo2,
                    ' dias, saldo ',
                    c.tipo2,
                    ' dias. ',
                    'Atividade física também válida: NÃO. ',
                    CASE
                        WHEN c.tipo2 > 0
                            THEN 'Contato VÁLIDO.'
                        ELSE 'Contato VENCIDO.'
                    END
                )

        END AS detalhe_contato

    FROM calculo c

    LEFT JOIN controle_manual cm
        ON cm.nota_baixada_id = c.nota_id
    LEFT JOIN diagnostico_categoria hc
        ON hc.nota_id = c.nota_id
),

/* =========================================================
   OBSERVAÇÃO FINAL

   controle_nota SÓ aparece se existir.

   NÃO TEM MAIS:
   "não foi encontrado registro de aprovação..."
   ========================================================= */
resultado AS (
    SELECT
        a.*,

        CONCAT_WS(
            ' ',

            /* =============================================
               CONTROLE MANUAL: SÓ SE EXISTIR
               ============================================= */
            CASE
                WHEN a.controle_nota_id IS NOT NULL
                 AND a.tem_comissao = 'S'
                THEN
                    CONCAT(
                        'APROVAÇÃO MANUAL.',
                        CASE
                            WHEN a.obs_limpa IS NOT NULL
                            THEN CONCAT(
                                ' Observação: "',
                                a.obs_limpa,
                                '".'
                            )
                            ELSE ''
                        END
                    )

                WHEN a.controle_nota_id IS NOT NULL
                 AND a.tem_comissao = 'N'
                THEN
                    CONCAT(
                        'REPROVAÇÃO MANUAL.',
                        CASE
                            WHEN a.obs_limpa IS NOT NULL
                            THEN CONCAT(
                                ' Observação: "',
                                a.obs_limpa,
                                '".'
                            )
                            ELSE ''
                        END
                    )

                WHEN a.controle_nota_id IS NOT NULL
                 AND a.tem_comissao = 'C'
                 AND a.obs_limpa IS NOT NULL
                THEN
                    CONCAT(
                        'Observação do cancelamento: "',
                        a.obs_limpa,
                        '".'
                    )

                ELSE NULL
            END,

            /* =============================================
               RESULTADO PRINCIPAL
               ============================================= */
            CASE
                WHEN a.tem_comissao = 'C' THEN
                    'COMISSÃO CANCELADA.'

                WHEN a.tem_comissao = 'S' THEN
                    'COMISSÃO GERADA.'

                ELSE
                    'COMISSÃO NÃO GERADA.'
            END,

            /* =============================================
               DATA BASE
               ============================================= */
            CASE
                WHEN a.data_emissao_os IS NOT NULL THEN
                    CONCAT(
                        'Data O.S. ',
                        TO_CHAR(
                            a.data_emissao_os,
                            'DD/MM/YYYY'
                        ),
                        '; data-base ',
                        TO_CHAR(
                            a.data_base,
                            'DD/MM/YYYY'
                        ),
                        ' (O.S. + 2 dias).'
                    )

                ELSE
                    'Nota sem Data O.S.'
            END,

            /* =============================================
               CATEGORIA / ATIVIDADES
               ============================================= */
            a.detalhe_historico_categoria,
            CASE
                WHEN a.detalhe_historico_categoria IS NOT NULL
                 AND a.categoria_base_confirmada
                 AND a.categoria_base_auditoria_id IS DISTINCT FROM a.categoria_cliente_id::text
                 AND a.controle_nota_id IS NULL
                 AND COALESCE(a.tem_comissao, 'N') NOT IN ('S', 'C', 'P')
                 AND a.resultado_regra_atual = 'S'
                THEN 'A mudança posterior de categoria pode explicar a divergência entre a comissão gravada e a simulação pelas regras atuais; não comprova, isoladamente, falha na geração original.'
                ELSE NULL
            END,
            a.detalhe_categoria,
            a.detalhe_fisica,
            a.detalhe_contato,

            /* =============================================
               CLIENTE ATIVO
               ============================================= */
            CASE
                WHEN a.cliente_ativo = true THEN
                    'Resultado das atividades: cliente ATIVO.'

                ELSE
                    'Resultado das atividades: cliente INATIVO.'
            END,

            /* =============================================
               REPRESENTANTE / COMISSÃO
               ============================================= */
            CASE
                WHEN a.pessoa_id IS NULL THEN NULL

                WHEN a.representante_id IS NULL THEN
                    'Cliente sem representante vinculado.'

                WHEN a.tipo_comissao_final IS NULL THEN
                    'Representante sem regra de comissão e sem exceção ativa para este cliente.'

                WHEN a.excecao_comissao_id IS NOT NULL
                 AND a.tipo_comissao_final = 'P' THEN
                    CONCAT(
                        'Exceção específica do cliente: ',
                        a.valor_comissao_regra,
                        '%.'
                    )

                WHEN a.excecao_comissao_id IS NOT NULL
                 AND a.tipo_comissao_final = 'V' THEN
                    CONCAT(
                        'Exceção específica do cliente: valor fixo R$ ',
                        REPLACE(
                            TO_CHAR(
                                a.valor_comissao_regra,
                                'FM9999999990.00'
                            ),
                            '.',
                            ','
                        ),
                        '.'
                    )

                WHEN a.tipo_comissao_final = 'P' THEN
                    CONCAT(
                        'Regra do representante: ',
                        a.valor_comissao_regra,
                        '%.'
                    )

                WHEN a.tipo_comissao_final = 'V' THEN
                    CONCAT(
                        'Regra do representante: valor fixo R$ ',
                        REPLACE(
                            TO_CHAR(
                                a.valor_comissao_regra,
                                'FM9999999990.00'
                            ),
                            '.',
                            ','
                        ),
                        '.'
                    )

                ELSE NULL
            END,

            /* =============================================
               RESULTADO AUTOMÁTICO ATUAL

               Só chama atenção se divergir.
               ============================================= */
            CASE
                WHEN a.controle_nota_id IS NULL
                 AND a.tem_comissao = 'S'
                 AND a.resultado_regra_atual = 'N'
                THEN
                    'ATENÇÃO: pelas regras atuais esta nota resultaria em NÃO.'

                WHEN a.controle_nota_id IS NULL
                 AND COALESCE(a.tem_comissao, 'N') <> 'S'
                 AND a.resultado_regra_atual = 'S'
                THEN
                    CONCAT(
                        'ATENÇÃO: pelas regras atuais esta nota resultaria em SIM, com comissão de R$ ',
                        REPLACE(
                            TO_CHAR(
                                a.comissao_regra_atual,
                                'FM9999999990.00'
                            ),
                            '.',
                            ','
                        ),
                        '.'
                    )

                ELSE NULL
            END,

            /* =============================================
               VALOR QUANDO GEROU
               ============================================= */
            CASE
                WHEN a.tem_comissao = 'S' THEN
                    CONCAT(
                        'Comissão gravada: R$ ',
                        REPLACE(
                            TO_CHAR(
                                COALESCE(a.comissao, 0),
                                'FM9999999990.00'
                            ),
                            '.',
                            ','
                        ),
                        '.'
                    )

                ELSE NULL
            END

        ) AS observacao

    FROM auditoria a
),

/* =========================================================
   RATEIO
   ========================================================= */
rateio AS (
    SELECT
        ccn.nota_baixada_id,
        ccn.centro_custo_id,

        SUM(
            COALESCE(
                ccn.valor_centro_custo,
                0
            )
        ) AS valor_centro_custo,

        SUM(
            COALESCE(
                ccn.comissao_centro_custo,
                0
            )
        ) AS comissao_centro_custo

    FROM centro_custo_nota ccn

    INNER JOIN notas_filtradas nf
        ON nf.nota_id = ccn.nota_baixada_id

    GROUP BY
        ccn.nota_baixada_id,
        ccn.centro_custo_id
)

/* =========================================================
   SELECT FINAL
   ========================================================= */
SELECT
    r.representante AS "Representante",

    COALESCE(
        NULLIF(
            TRIM(
                cc.codcusto
                || ' - '
                || cc.nome
            ),
            ''
        ),
        'Sem centro de custo'
    ) AS "Centro de Custo",

    r.cliente AS "Cliente",

    r.coligada AS "Coligada",

    r.numero_exibicao AS "Numero da Nota",

    r.data_emissao::date AS "Data da Emissão",

    r.data_emissao_os::date AS "Data O.S",

    CASE
        WHEN r.tem_comissao = 'S' THEN 'Sim'
        WHEN r.tem_comissao = 'C' THEN 'Cancelada'
        WHEN r.tem_comissao = 'P' THEN 'P'
        ELSE 'Não'
    END AS "Tem Comissão?",

    r.codigo_cliente AS "Código",

    COALESCE(
        rt.comissao_centro_custo,
        0
    ) AS "Soma de Comissao",

    r.valor_total AS "Soma de Valor Nota",

    COALESCE(
        rt.valor_centro_custo,
        0
    ) AS "Soma de Valor Centro de Custo",

    CONCAT_WS(
        ' ',

        r.observacao,

        CASE
            WHEN rt.centro_custo_id IS NULL THEN
                'Sem rateio de centro de custo.'

            ELSE
                CONCAT(
                    'Centro de custo: ',
                    COALESCE(cc.codcusto, '-'),
                    ' - ',
                    COALESCE(cc.nome, '-'),
                    '. Valor do CC: R$ ',
                    REPLACE(
                        TO_CHAR(
                            COALESCE(
                                rt.valor_centro_custo,
                                0
                            ),
                            'FM9999999990.00'
                        ),
                        '.',
                        ','
                    ),
                    '. Comissão do CC: R$ ',
                    REPLACE(
                        TO_CHAR(
                            COALESCE(
                                rt.comissao_centro_custo,
                                0
                            ),
                            'FM9999999990.00'
                        ),
                        '.',
                        ','
                    ),
                    '.'
                )
        END

    ) AS "Observação"

FROM resultado r

LEFT JOIN rateio rt
    ON rt.nota_baixada_id = r.nota_id

LEFT JOIN centro_custo cc
    ON cc.id = rt.centro_custo_id

/*
 * Necessário porque o filtro de centro de custo foi
 * aplicado por EXISTS lá em cima, mas aqui precisamos
 * retornar somente o CC selecionado.
 */
WHERE 1 = 1

[[AND
    COALESCE(
        NULLIF(
            TRIM(
                cc.codcusto
                || ' - '
                || cc.nome
            ),
            ''
        ),
        'Sem centro de custo'
    ) = {{centro_custo}}
]]

ORDER BY
    r.representante,
    COALESCE(
        NULLIF(
            TRIM(
                cc.codcusto
                || ' - '
                || cc.nome
            ),
            ''
        ),
        'Sem centro de custo'
    ),
    r.cliente,
    r.coligada,
    r.numero_exibicao;