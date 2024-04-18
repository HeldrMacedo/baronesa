<?php

use Adianti\Database\TRecord;

class Cambista extends TRecord
{
    const TABLENAME     = 'cambistas';
    const PRIMARYKEY    = 'id';
    const IDPOLICY      = 'serial';

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('regiao_id');
        parent::addAttribute('gerente_id');
        parent::addAttribute('nome');
        parent::addAttribute('comissao');
        parent::addAttribute('pode_cancelar');
        parent::addAttribute('limite_venda');
        parent::addAttribute('exibe_comissao');
        parent::addAttribute('usuario_id');
        parent::addAttribute('pode_cancelar_tempo');
        parent::addAttribute('pode_reimprimir');
        parent::addAttribute('usuario_id');
        parent::addAttribute('unit_id');
    }

    public function get_gerente()
    {
        return Gerente::find($this->gerente_id);
    }

    public function get_regiao()
    {
        return Regiao::find($this->regiao_id);
    }

    public function get_usuario()
    {
        return SystemUser::find($this->usuario_id);
    }

    public function get_unit()
    {
        return SystemUnit::find($this->unit_id);
    }
}