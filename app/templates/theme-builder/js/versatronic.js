(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    var refreshScheduled = false;

    function textOf(element) {
        return element ? String(element.textContent || '').replace(/\s+/g, ' ').trim() : '';
    }

    function findCurrentPage() {
        var pages = document.querySelectorAll('#adianti_div_content [page-name], #adianti_online_content [page-name]');
        var page = pages.length ? pages[pages.length - 1] : null;
        var title = page && page.querySelector('.card-title, .panel-title, h1, h2');
        var breadcrumbItems = document.querySelectorAll('#adianti_div_content .tbreadcrumb li');
        var breadcrumb = breadcrumbItems.length ? breadcrumbItems[breadcrumbItems.length - 1] : null;
        var pageName = page ? (page.getAttribute('page-name') || '') : '';
        var label = textOf(title) || textOf(breadcrumb) || pageName || 'Visão geral';
        var context = document.getElementById('versatronic-current-page');

        if (context) {
            context.textContent = label;
            context.setAttribute('title', label);
        }

        document.body.classList.toggle('versatronic-dashboard', /dashboard/i.test(pageName));
        document.body.setAttribute('data-current-page', pageName);
    }

    function enhanceIndicators(root) {
        $(root).find('.info-box, .bindicator-card').addBack('.info-box, .bindicator-card').each(function () {
            if (this.classList.contains('vt-indicator')) {
                return;
            }

            var inlineColor = this.style.backgroundColor;

            if (inlineColor && inlineColor !== 'transparent') {
                this.style.setProperty('--vt-indicator-accent', inlineColor);
            }

            this.classList.add('vt-indicator');
        });
    }

    function enhanceLogin(root) {
        if (!document.body.classList.contains('builder-template-login')) {
            return;
        }

        var labels = {
            login: 'Usuário',
            password: 'Senha',
            unit_id: 'Unidade',
            lang_id: 'Idioma'
        };

        Object.keys(labels).forEach(function (name) {
            var field = $(root).find('[page-name="LoginForm"] [name="' + name + '"]').addBack('[name="' + name + '"]').first();

            if (!field.length || field.attr('data-vt-labelled') === 'true') {
                return;
            }

            var id = field.attr('id') || name;
            var row = field.closest('.tformrow, .form-group, [class*="col-"]').first();

            field.attr({
                'aria-label': labels[name],
                'data-vt-labelled': 'true'
            });

            if (row.length && !row.children('.vt-login-label').length) {
                row.addClass('vt-login-field');
                $('<label>', {
                    'class': 'vt-login-label',
                    'for': id,
                    text: labels[name]
                }).prependTo(row);
            }
        });
    }

    function enhanceAccessibility(root) {
        $(root).find('.toggle-menu, .toggle-top-menu').addBack('.toggle-menu, .toggle-top-menu').each(function () {
            var isMenu = this.classList.contains('toggle-menu');
            this.setAttribute('role', 'button');
            this.setAttribute('tabindex', '0');
            this.setAttribute('aria-label', isMenu ? 'Abrir menu principal' : 'Abrir ações do cabeçalho');
            this.setAttribute('aria-expanded', 'false');
        });

        $(root).find('a[title], button[title]').each(function () {
            if (!this.getAttribute('aria-label') && !textOf(this)) {
                this.setAttribute('aria-label', this.getAttribute('title'));
            }
        });

        $(root).find('.nav-tabs').attr('role', 'tablist');
        $(root).find('.nav-tabs .nav-link').attr('role', 'tab');

        $(root).find('label').each(function () {
            var color = String(this.style.color || '').toLowerCase();
            if (color === '#f44336' || color === '#ff0000' || color === 'rgb(244, 67, 54)' || color === 'rgb(255, 0, 0)') {
                this.classList.add('vt-required-label');
            }
        });
    }

    function enhanceTables(root) {
        $(root).find('.table').addBack('.table').each(function () {
            var table = $(this);
            if (table.attr('data-vt-table') === 'true') {
                return;
            }

            table.attr('data-vt-table', 'true');
            table.find('thead th').each(function (index) {
                var label = textOf(this);
                if (!label) {
                    return;
                }
                table.find('tbody tr').each(function () {
                    $(this).children('td').eq(index).attr('data-label', label);
                });
            });
        });
    }

    function enhance(root) {
        findCurrentPage();
        enhanceIndicators(root);
        enhanceLogin(root);
        enhanceAccessibility(root);
        enhanceTables(root);
    }

    function scheduleEnhance(root) {
        if (refreshScheduled) {
            return;
        }

        refreshScheduled = true;
        window.requestAnimationFrame(function () {
            refreshScheduled = false;
            enhance(root || document);
        });
    }

    function syncMobileMenu() {
        var open = $('.master-menu-content').hasClass('open');
        document.body.classList.toggle('vt-menu-open', open);
        $('.toggle-menu').attr('aria-expanded', open ? 'true' : 'false');
    }

    function setupShell() {
        if ($('.master-menu-content').length && !$('.versatronic-menu-backdrop').length) {
            $('<button>', {
                type: 'button',
                'class': 'versatronic-menu-backdrop',
                'aria-label': 'Fechar menu principal'
            }).appendTo(document.body).on('click', function () {
                $('.master-menu-content').removeClass('open');
                $('.toggle-menu i').addClass('fa-bars').removeClass('fa-times');
                syncMobileMenu();
            });
        }

        $(document).on('click', '.toggle-menu', function () {
            window.setTimeout(syncMobileMenu, 0);
        });

        $(document).on('click', '.toggle-top-menu', function () {
            var button = $(this);
            window.setTimeout(function () {
                button.attr('aria-expanded', $('.header-track').is(':visible') ? 'true' : 'false');
            }, 0);
        });

        $(document).on('keydown', '.toggle-menu, .toggle-top-menu', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                this.click();
            }
        });

        $(document).on('click', '.container-submenu a:not(.sub)', function () {
            window.setTimeout(syncMobileMenu, 0);
        });
    }

    $(function () {
        setupShell();
        enhance(document);

        var content = document.getElementById('adianti_content') || document.body;
        var observer = new MutationObserver(function (mutations) {
            var target = mutations.length ? mutations[mutations.length - 1].target : content;
            scheduleEnhance(target.nodeType === 1 ? target : content);
        });

        observer.observe(content, {
            childList: true,
            subtree: true
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 992) {
                document.body.classList.remove('vt-menu-open');
            }
        });
    });
})(window, document, window.jQuery);
