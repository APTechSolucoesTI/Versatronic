<?php

class BuscarCNPJForm extends TWindow
{
    public function __construct($param = null)
    {
        parent::__construct();
        $this->setTitle('Buscar CNPJ');
        $this->setSize(0.3, null);
        $this->setMinWidth(0.95, 480);
        $this->removePadding();

        $form = new BootstrapFormBuilder('form_BuscarCNPJForm');
        $cnpj = new TEntry('cnpj');
        $cnpj->setSize('100%');
        $cnpj->setMaxLength(18);
        $cnpj->setProperty('inputmode', 'numeric');
        $cnpj->setProperty('aria-required', 'true');
        $cnpj->setProperty('autofocus', 'autofocus');
        $label = new TLabel('CNPJ');
        $label->setProperty('for', $cnpj->getId());
        $form->addFields([$label, $cnpj]);
        $form->addAction('Buscar', new TAction([self::class, 'onBuscarCNPJ'], ['static' => 1]), 'fas:search')
            ->addStyleClass('btn-primary');
        $this->add($form);
    }

    public static function onBuscarCNPJ($param = null)
    {
        try {
            $cnpj = $param['cnpj'] ?? '';
            if (!is_string($cnpj)) {
                throw new DomainException('Informe um CNPJ com 14 dígitos.');
            }
            $dados = CNPJService::get($cnpj);

            $campos = [
                'razao_social' => $dados->razao_social,
                'nome_fantasia' => $dados->nome_fantasia,
                'cpf_cnpj' => $dados->cnpj,
                'pessoa_endereco_pessoa_cep' => $dados->cep,
                'pessoa_endereco_pessoa_rua' => $dados->logradouro,
                'pessoa_endereco_pessoa_bairro' => $dados->bairro,
                'pessoa_endereco_pessoa_numero' => $dados->numero,
                'pessoa_endereco_pessoa_complemento' => $dados->complemento,
                'pessoa_endereco_pessoa_cidade_id' => $dados->cidade_id ?? '',
            ];
            if ($dados->ddd_telefone_1 !== '') {
                $campos['fone'] = $dados->ddd_telefone_1;
            }
            if (filter_var($dados->email, FILTER_VALIDATE_EMAIL)) {
                $campos['email'] = $dados->email;
            }

            $form = json_encode(PessoaForm::getFormName(), JSON_HEX_TAG | JSON_THROW_ON_ERROR);
            $valores = json_encode($campos, JSON_HEX_TAG | JSON_THROW_ON_ERROR);
            TScript::create("Object.entries({$valores}).forEach(([campo, valor]) => tform_send_data({$form}, campo, String(valor), true, 0));");
            TWindow::closeWindow();
            if ($dados->cidade_id === null) {
                TToast::show('warning', 'CNPJ encontrado. Selecione a cidade no cadastro de endereço.', 'topRight', 'fas:exclamation-circle');
            } else {
                TToast::show('success', 'Dados do CNPJ carregados.', 'topRight', 'fas:check-circle');
            }
        } catch (DomainException $e) {
            new TMessage('error', $e->getMessage());
        } catch (Throwable $e) {
            error_log('Falha ao preencher cadastro por CNPJ: ' . get_class($e));
            new TMessage('error', 'Não foi possível carregar os dados do CNPJ. Tente novamente.');
        }
    }

    public function onShow($param = null)
    {
    }
}
