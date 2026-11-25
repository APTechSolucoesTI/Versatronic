<?php

class CNPJService
{
    private const URL = 'https://brasilapi.com.br/api/cnpj/v1/';

    public static function get(string $cnpj): stdClass
    {
        $cnpj = str_replace(['.', '/', '-'], '', trim($cnpj));
        if (!preg_match('/^[0-9]{14}$/D', $cnpj)) {
            throw new DomainException('Informe um CNPJ com 14 dígitos.');
        }

        $dados = self::consultarBrasilApi($cnpj);
        $cidade = self::localizarCidade($dados);
        $logradouro = self::texto($dados, 'logradouro');
        $tipo = self::texto($dados, 'descricao_tipo_de_logradouro');
        if ($logradouro !== '' && $tipo !== '' && strcasecmp($logradouro, $tipo) !== 0
            && stripos($logradouro, $tipo . ' ') !== 0) {
            $logradouro = $tipo . ' ' . $logradouro;
        }

        return (object) [
            'cnpj' => $cnpj,
            'razao_social' => self::texto($dados, 'razao_social'),
            'nome_fantasia' => self::texto($dados, 'nome_fantasia') ?: self::texto($dados, 'razao_social'),
            'ddd_telefone_1' => self::texto($dados, 'ddd_telefone_1') ?: self::texto($dados, 'ddd_telefone_2'),
            'email' => self::texto($dados, 'email'),
            'cep' => self::texto($dados, 'cep'),
            'logradouro' => $logradouro,
            'bairro' => self::texto($dados, 'bairro'),
            'numero' => self::texto($dados, 'numero'),
            'complemento' => self::texto($dados, 'complemento'),
            'cidade_id' => $cidade->id ?? null,
            'estado_id' => $cidade->estado_id ?? null,
        ];
    }

    private static function consultarBrasilApi(string $cnpj): stdClass
    {
        $curl = curl_init(self::URL . $cnpj);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_USERAGENT => 'VersatronicCRM/1.0',
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $erro = curl_errno($curl);
        curl_close($curl);

        switch ($status) {
            case 400:
                throw new DomainException('CNPJ inválido. Confira o número informado.', 400);
            case 404:
                throw new DomainException('CNPJ não encontrado na BrasilAPI.', 404);
            case 429:
                throw new DomainException('A BrasilAPI atingiu o limite de consultas. Tente novamente em instantes.', 429);
        }
        if ($body === false || $status !== 200) {
            error_log("Falha na consulta de CNPJ à BrasilAPI (HTTP {$status}, cURL {$erro}).");
            throw new DomainException('A consulta de CNPJ está temporariamente indisponível. Tente novamente em instantes.', 503);
        }

        $dados = json_decode($body);
        if (!$dados instanceof stdClass || self::texto($dados, 'cnpj') !== $cnpj || self::texto($dados, 'razao_social') === '') {
            error_log('Resposta inválida da BrasilAPI na consulta de CNPJ.');
            throw new DomainException('A BrasilAPI retornou dados inválidos para esse CNPJ. Tente novamente em instantes.', 503);
        }
        return $dados;
    }

    private static function localizarCidade(stdClass $dados): ?Cidade
    {
        $ibge = self::texto($dados, 'codigo_municipio_ibge');
        $uf = strtoupper(self::texto($dados, 'uf'));
        $municipio = self::normalizarNome(self::texto($dados, 'municipio'));
        $usaIbge = preg_match('/^[0-9]{7}$/D', $ibge) === 1;
        if (!$usaIbge && ($uf === '' || $municipio === '')) {
            return null;
        }

        $abriuTransacao = TTransaction::getDatabase() !== 'minicrm';
        if ($abriuTransacao) {
            TTransaction::open('minicrm');
        }
        try {
            $estado = $uf !== '' ? Estado::where('sigla', '=', $uf)->first() : null;
            $cidade = null;
            if ($usaIbge) {
                $cidades = Cidade::where('codigo_ibge', '=', $ibge);
                if ($estado) {
                    $cidades->where('estado_id', '=', $estado->id);
                }
                $cidade = $cidades->first();
            }
            if (!$cidade && $estado && $municipio !== '') {
                $nomes = Cidade::where('estado_id', '=', $estado->id)->getIndexedArray('id', 'nome');
                $ids = array_keys(array_filter($nomes, static function ($nome) use ($municipio) {
                    return self::normalizarNome((string) $nome) === $municipio;
                }));
                if (count($ids) === 1) {
                    $cidade = Cidade::find($ids[0]);
                }
            }
            if ($abriuTransacao) {
                TTransaction::close();
            }
            return $cidade ?: null;
        } catch (Throwable $e) {
            if ($abriuTransacao) {
                TTransaction::rollback();
            }
            throw $e;
        }
    }

    private static function normalizarNome(string $nome): string
    {
        $nome = TratamentosService::removerAcentos(str_replace(['ç', 'Ç'], ['c', 'C'], $nome));
        return strtoupper(trim(preg_replace('/\s+/', ' ', $nome)));
    }

    private static function texto(stdClass $dados, string $campo): string
    {
        $valor = $dados->$campo ?? null;
        return is_string($valor) || is_int($valor) ? trim((string) $valor) : '';
    }
}
