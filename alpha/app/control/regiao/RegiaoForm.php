<?php

use Adianti\Base\TStandardForm;
use Adianti\Control\TAction;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

class RegiaoForm extends TStandardForm
{
    protected $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTargetContainer('adianti_right_panel');

        $this->setDatabase('permission');
        $this->setActiveRecord('Regiao');

        $this->form = new BootstrapFormBuilder('form_RegiaoForm');
        $this->form->setFormTitle('Região');
        $this->form->enableClientValidation();

        $id     = new TEntry('id');
        $name   = new TEntry('nome');

        $this->form->addFields( [new TLabel('Id')], [$id] );
        $this->form->addFields( [new TLabel(_t('Name'))], [$name] );

        $id->setEditable(FALSE);
        $id->setSize('30%');
        $name->setSize('100%');
        $name->addValidation( _t('Name'), new TRequiredValidator );

        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('Clear'),  new TAction(array($this, 'onEdit')), 'fa:eraser red');

        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', 'SystemRoleList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }

    public function onSave()
    {
        try 
        {
            TTransaction::open('permission');

            $data = $this->form->getData();
            $this->form->setData($data);

            $userId = TSession::getValue('userid');
            $unit = (object) SystemUser::find($userId)->get_unit();

            $data->unit_id = $unit->id;
            
            $object = new Regiao();
            $object->fromArray( (array) $data );
            $object->store();            

            $data = new stdClass;
            $data->id = $object->id;
            TForm::sendData('form_RegiaoForm', $data);            

            TTransaction::close(); 
            $pos_action = new TAction(['RegiaoList', 'onReload']);
            new TMessage('info', _t('Record saved'), $pos_action); 
        }catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}