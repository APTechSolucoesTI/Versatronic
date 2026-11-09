# Histórico de categorias do cliente

## Escopo e decisões

- Nova aba **Histórico de Categorias** em `ClienteFormView`.
- A lupa da comissão passa a abrir **Histórico do Cliente**, com abas **Interações** e **Histórico de Categorias**.
- OBS da comissão fica para outra entrega. Nenhuma aba de OBS do cliente foi adicionada.
- Manter PHP/Adianti, Bootstrap e componentes existentes; shadcn/React não são compatíveis com esta aplicação.
- Reutilizar `Pessoa` / `SystemChangeLogTrait` / `system_change_log`. Criar outra tabela duplicaria a auditoria já existente.
- Listar somente eventos existentes, dez por página, com data, categoria anterior/nova, login e origem. Não gerar histórico retroativo a partir da categoria atual.

## Origem dos dados

`PessoaCategoriaClienteForm::onSave` salva `Pessoa` no CRM e depois atualiza `FCFO.CODTCF` na TOTVS. O hook de `Pessoa` já registra a mudança no banco configurado como `log`. `AtualizacaoService::atualizarCliente` também utiliza `Pessoa::store`, registrando mudanças recebidas da TOTVS.

A consulta usa exclusivamente `tablename = 'pessoa'`, `columnname = 'categoria_cliente_id'` e o ID do cliente. A tela não expõe a auditoria geral, IPs, rastros de execução ou campos de outros clientes. O acesso continua nas duas telas existentes e nas suas permissões; não foi criado endpoint público.

As datas são as de gravação no CRM, exibidas em Brasília. Uma alteração recebida da TOTVS pode ter sido feita antes da sincronização. A origem é identificada pelas classes específicas `PessoaCategoriaClienteForm` e `AtualizacaoService` ou pelo rastreio de seus métodos de salvamento/sincronização. Outras entradas mostram **Origem não identificada**, porque listagens também podem executar sincronizações. Eventos antigos `created` mostram **Categoria registrada**, sem afirmar que era o primeiro registro do cliente. Os nomes/códigos das categorias são resolvidos pelo cadastro atual, com aviso na interface; a auditoria guarda os IDs, não os nomes históricos. Categorias removidas aparecem com seu ID.

Mudanças por SQL direto não passam pelo hook do model. A integração futura da OBS da comissão deve ser definida separadamente; não há garantia de captura de escritas externas.

O login do log é o da sessão do CRM. A coluna **Responsável / origem**, nas duas telas, exibe o login somente quando a alteração é comprovada pela classe `PessoaCategoriaClienteForm` ou pelo rastreio de `onSave` desse formulário. Se faltar login nesse caso, mostra **Não identificado**. Sincronizações mostram apenas **Sincronização TOTVS**, sem o nome de quem sincronizou; o rastreio de `AtualizacaoService::atualizarCliente` tem prioridade sobre a classe da requisição. Origem desconhecida também não exibe usuário. O login bruto e o rastreio não são enviados para a interface. Os registros originais da auditoria são preservados, sem migration ou alteração dos dados.

A listagem de comissões agora limita a abertura a 20 notas por página e aceita somente tamanhos entre 10 e 100. O carregamento ilimitado anterior podia enviar todas as notas e seus controles ao navegador. Os totais são calculados no banco para todo o conjunto filtrado, preservando seu significado sem renderizar todas as linhas.

## Implementação e validação

1. Serviço de leitura `ClienteHistoricoService`, com consultas parametrizadas e paginação no banco.
2. Componente `ClienteHistoricoPanel`, compartilhado pelas duas telas, com tabela no desktop, lista no mobile, estados vazio/erro e navegação por página.
3. Correção da auditoria para distinguir uma coluna nula existente de uma coluna ausente: preencher a categoria nula passa a ser **Alteração**, em vez de evento `created`.
4. Testes de integração em SQLite isolado, sem conexão com os bancos configurados: `php -d extension=mbstring -d extension=pdo_sqlite tests/cliente-historico.php` (ajustar `extension_dir` ao PHP instalado).
5. Validar as duas telas em 1440×900, 768×1024 e 390×844, incluindo abas, teclado, estado vazio, falha e paginação.

Validação realizada em 09/11/2026: 20 verificações de integração passaram; os seis arquivos PHP envolvidos passaram em `php -l`. As duas telas foram renderizadas localmente com seus componentes reais e dados do PostgreSQL em conexões forçadas a somente leitura. Os três tamanhos foram inspecionados sem overflow na nova listagem. Paginação (23 registros), vazio e falha foram exercitados com auditoria SQLite temporária, sem alterar os bancos reais. A paginação da comissão atualiza um contêiner interno, porque ações estáticas do Adianti acrescentam painéis inteiros. As abas e a navegação por teclado foram verificadas, e a revisão final do navegador não registrou erros ou avisos. O componente também foi inspecionado em tema escuro. O projeto não possui scripts de lint, typecheck ou build; não foram declarados como executados.

## Configurações e publicação

Não são necessárias tabelas, migrations ou novas dependências. Os aliases `minicrm` e `log` continuam usando a configuração do ambiente. As configurações em `app/config` (exceto regras de acesso e hashes do framework) e os bancos SQLite locais saem do versionamento, permanecendo fisicamente em cada ambiente. O `.gitignore` impede sua inclusão nos próximos commits. Modelos sem credenciais estão em `app/config/examples`; uma instalação nova deve copiá-los para `app/config`, retirando o sufixo `.example` e preenchendo os valores do ambiente. Tokens e chaves precisam de valores próprios, não dos marcadores dos exemplos.

Na primeira atualização de um checkout antigo, fazer backup privado dos arquivos de configuração e SQLite antes do pull. Adicionar esses caminhos ao `.git/info/exclude` do servidor e removê-los apenas do índice com `git rm --cached`, preservando os arquivos em disco. Esse procedimento foi ensaiado em repositório temporário: o avanço de histórico mantém os arquivos ignorados que já saíram do índice. Comparar checksums após o pull.

Código publicado pelo Builder pode coincidir com commits ainda não registrados no checkout. Comparar o conteúdo antes de atualizar. Na primeira reconciliação, o índice pode ser alinhado à árvore da revisão-base recebida, sem escrever na cópia de trabalho; depois, um avanço fast-forward registra essa revisão. Isso preserva inclusive as diferenças exclusivas do servidor como alterações locais. O procedimento deve ser ensaiado antes e os checksums dos arquivos em uso devem permanecer idênticos nessa etapa. Os três arquivos de código alterados nesta entrega precisam coincidir com a revisão-base antes de receber o novo commit.

Usar `git pull --ff-only`, executar como o usuário proprietário do site (`www`) e conferir os arquivos PHP, testes e acesso HTTP após a atualização. O origin do servidor usa HTTPS público para este repositório, sem token ou chave privada; `core.filemode=false` evita que as permissões preexistentes do ambiente apareçam como milhares de mudanças de código. Não executar migrations ou instalação de dependências para esta entrega. Alterações locais remanescentes em templates e schemas continuam preservadas; um pull futuro que conflite com elas deve ser revisado antes de prosseguir.
