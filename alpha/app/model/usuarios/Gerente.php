<?php

use Adianti\Database\TRecord;

class Gerente extends TRecord
{
    const TABLENAME     = 'gerentes';
    const PRIMARYKEY    = 'id';
    const IDPOLICY      = 'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('user_id');
        parent::addAttribute('regiao_id');
        parent::addAttribute('unit_id');
    }

    public function get_usuario()
    {
        return SystemUser::find($this->user_id);
    }

    public function get_regiao()
    {
        return Regiao::find($this->regiao_id);
    }

    public function get_unit()
    {
        return SystemUnit::find($this->unit_id);
    }
}