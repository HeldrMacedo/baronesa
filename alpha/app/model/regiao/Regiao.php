<?php

use Adianti\Database\TRecord;

class Regiao extends TRecord
{
    const TABLENAME     = 'regiao';
    const PRIMARYKEY    = 'id';
    const IDPOLICY      = 'serial';

    private $unit;

    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('nome');
        parent::addAttribute('unit_id');
    }

    public function get_unit()
    {
        if (empty($this->unit)) {
            $this->unit = new SystemUnit($this->unit_id);
        }

        return $this->unit;
    }
}