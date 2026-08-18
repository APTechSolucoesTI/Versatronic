<?php

use Firebase\JWT\JWT;

class RelatorioAnaliticoMetabase extends TPage
{
    public function __construct()
    {
        parent::__construct();

        $METABASE_SITE_URL = 'http://194.140.198.97:3001';
        $METABASE_SECRET_KEY = '15dc0a57fafc8bdff8ec7032c44ce9c25147b1ff2cd621f71ae575e04f6130cd';

        $payload = [
            'resource' => ['dashboard' => 12],
            'params'   => new stdClass(),
            'exp'      => time() + (100 * 365 * 24 * 60 * 60)
        ];

        $token = JWT::encode($payload, $METABASE_SECRET_KEY, 'HS256');

        $html = new TElement('div');
        $html->id = 'metabase-dashboard-wrapper';
        $html->style = '
            width: 100vw;
            min-height: calc(100vh - 70px);
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            background: #ffffff;
            overflow: auto;
            padding: 0;
            position: relative;
        ';

        $html->add("
            <script defer src=\"{$METABASE_SITE_URL}/app/embed.js\"></script>

            <script>
                window.metabaseConfig = {
                    theme: { preset: 'light' },
                    isGuest: true,
                    instanceUrl: '{$METABASE_SITE_URL}'
                };

                function matarFooter() {
                    var footer = document.querySelector('[data-testid=\"embedding-footer\"]');

                    if (footer) {
                        footer.remove();
                    }

                    var mb = document.querySelector('metabase-dashboard');

                    if (mb && mb.shadowRoot) {
                        var shadowFooter = mb.shadowRoot.querySelector('[data-testid=\"embedding-footer\"]');

                        if (shadowFooter) {
                            shadowFooter.remove();
                        }

                        var observer = new MutationObserver(function() {
                            var sf = mb.shadowRoot.querySelector('[data-testid=\"embedding-footer\"]');

                            if (sf) {
                                sf.remove();
                            }
                        });

                        observer.observe(mb.shadowRoot, {
                            childList: true,
                            subtree: true
                        });
                    }
                }

                var tentativas = 0;

                var intervalo = setInterval(function() {
                    matarFooter();
                    tentativas++;

                    if (tentativas > 30) {
                        clearInterval(intervalo);
                    }
                }, 500);
            </script>

            <style>
                #metabase-dashboard-wrapper {
                    width: 100vw !important;
                    max-width: 100vw !important;
                    min-height: calc(100vh - 70px) !important;
                    margin-left: calc(50% - 50vw) !important;
                    margin-right: calc(50% - 50vw) !important;
                    background: #fff !important;
                    overflow: auto !important;
                    padding: 0 !important;
                    position: relative !important;
                }

                #metabase-dashboard-wrapper metabase-dashboard {
                    display: block !important;
                    width: 100vw !important;
                    max-width: 100vw !important;
                    min-height: calc(100vh - 70px) !important;
                    height: calc(100vh - 70px) !important;
                    border: 0 !important;
                }

                #metabase-footer-cover {
                    position: fixed !important;
                    bottom: 0 !important;
                    left: 0 !important;
                    width: 100vw !important;
                    height: 50px !important;
                    background: #ffffff !important;
                    z-index: 2147483647 !important;
                    pointer-events: none !important;
                }
            </style>

            <metabase-dashboard
                token=\"{$token}\"
                with-title=\"true\"
                with-downloads=\"true\">
            </metabase-dashboard>

            <div id='metabase-footer-cover'></div>
        ");

        parent::add($html);
    }
}