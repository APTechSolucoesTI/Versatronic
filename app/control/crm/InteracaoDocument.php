<?php

class InteracaoDocument extends TPage
{
    private static $database = 'minicrm';
    private static $activeRecord = 'Interacao';
    private static $primaryKey = 'id';
    private static $htmlFile = 'app/documents/InteracaoDocumentTemplate.html';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {

    }

    public static function onGenerate($param)
    {
        try 
        {
            TTransaction::open(self::$database);

            $class = self::$activeRecord;
            $object = new $class($param['key']);

            $html = new AdiantiHTMLDocumentParser(self::$htmlFile);
            $html->setMaster($object);

            $pageSize = 'A4';
            $document = 'tmp/'.uniqid().'.pdf'; 

            $html->process();

            $html->saveAsPDF($document, $pageSize, 'portrait');

            TTransaction::close();

            if(empty($param['returnFile']))
            {
                parent::openFile($document);

                new TMessage('info', _t('Document successfully generated'));    
            }
            else
            {
                return $document;
            }
        } 
        catch (Exception $e) 
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());

            // undo all pending operations
            TTransaction::rollback();
        }
    }

private static function formatarCpfCnpj($valor)
{
    $valor = preg_replace('/\D/', '', (string) $valor);

    if (strlen($valor) == 11) {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $valor);
    }

    if (strlen($valor) == 14) {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $valor);
    }

    return $valor;
}

private static function formatarTelefone($valor)
{
    $valor = preg_replace('/\D/', '', (string) $valor);

    if (strlen($valor) == 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $valor);
    }

    if (strlen($valor) == 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $valor);
    }

    return $valor;
}

private static function formatarCep($valor)
{
    $valor = preg_replace('/\D/', '', (string) $valor);

    if (strlen($valor) == 8) {
        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $valor);
    }

    return $valor;
}
}

