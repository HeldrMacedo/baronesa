<?php

use Adianti\Database\TRecord;

class Extracao extends TRecord
{
    const TABLENAME     = 'extracoes';
    const PRIMARYKEY    = 'id';
    const IDPOLICY      = 'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('abreviacao');
        parent::addAttribute('hora_limite');
        parent::addAttribute('premiacao_maxima');
        parent::addAttribute('segunda');
        parent::addAttribute('terca');
        parent::addAttribute('quarta');
        parent::addAttribute('quinta');
        parent::addAttribute('sexta');
        parent::addAttribute('sabado');
        parent::addAttribute('domingo');
        parent::addAttribute('ativo');
        parent::addAttribute('ultimo_sorteio_numero');
        parent::addAttribute('data_primeiro_sorteio');
        parent::addAttribute('limite_palpite');
    }    
}