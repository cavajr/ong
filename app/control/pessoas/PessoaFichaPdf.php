<?php

class PessoaFichaPdf extends TWindow
{
     public function __construct($param)
    {
        parent::__construct();

        parent::setTitle('Ficha Cadastral');
        parent::setSize(0.9, 0.9);

        if (empty($param['file'])) {
            throw new Exception('Arquivo não informado');
        }

        $arquivo = $param['file'];

        $object = new TElement('object');

        $object->data  = $arquivo;
        $object->type  = 'application/pdf';
        $object->style = 'width:100%; height:calc(100% - 10px)';

        $object->add(
            'Seu navegador não suporta PDF. 
            <a target="_blank" href="'.$arquivo.'">
                Clique aqui para baixar
            </a>'
        );

        parent::add($object);
    }
}
