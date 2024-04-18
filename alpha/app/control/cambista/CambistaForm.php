<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Form\TTime;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class CambistaForm extends TPage
{
    protected $form;

    public function __construct()
    {
        parent::__construct();

        //parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_Cambista');
        $this->form->setFormTitle('Cambista');
        $this->form->setFieldSizes('100%');
        $this->form->generateAria();

        TTransaction::open('permission');
        $userId = TSession::getValue('userid');
        $unit = (object) SystemUser::find($userId)->get_unit();
        TTransaction::close();

        $regiaoCriteria = new TCriteria();
        $regiaoCriteria->add(new TFilter('unit_id', '=', $unit->id));

        $id                     = new TEntry('id');
        $nome                   = new TEntry('name');
        $login                  = new TEntry('login');
        $password               = new TPassword('password');
        $repassword             = new TPassword('repassword');
        $email                  = new TEntry('email');
        $regiao_id              = new TDBCombo('regiao_id', 'permission', 'Regiao', 'id', 'nome', 'nome', $regiaoCriteria);
        $gerente                = new TCombo('gerente_id');
        $unit_id                = new TCombo('system_unit_id');
        $frontpage_id           = new TDBUniqueSearch('frontpage_id', 'permission', 'SystemProgram', 'id', 'name', 'name');
        $phone                  = new TEntry('phone');
        $address                = new TEntry('address');
        $function_name          = new TEntry('function_name');
        $comissao               = new TEntry('comissao');
        $pode_cancelar          = new TCombo('pode_cancelar');
        $pode_cancelar_tempo    = new TTime('pode_cancelar_tempo');
        $limite_venda           = new TNumeric('limite_venda', 2, ',', '.', true);
        $exibe_comissao         = new TCombo('exibe_comissao');
        $pode_reimprimir        = new TCombo('pode_reimprimir');

        $password->disableAutoComplete();
        $repassword->disableAutoComplete();

        $combo_items = [];
        $combo_items[$unit->id] = $unit->name;

        $unit_id->addItems($combo_items);
        $unit_id->setDefaultOption(false);
        $unit_id->setValue($unit->id);
        $unit_id->setEditable(false);

        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink(_t('Back'), new TAction(array('CambistaList','onReload')), 'far:arrow-alt-circle-left blue');

        $id->setEditable(false);
        $phone->setMask('(99) 9 9999-9999');
        $function_name->setValue('Cambista');
        $function_name->setEditable(false);
        $limite_venda->setValue(0);
        $comissao->setMask('99');
        $pode_cancelar->addItems(['S' => 'Sim', 'N' => 'Não']);
        $exibe_comissao->addItems(['S' => 'Sim', 'N' => 'Não']);
        $pode_reimprimir->addItems(['S' => 'Sim', 'N' => 'Não']);

        $regiao_id->setChangeAction(new TAction([$this, 'onChangeRegiao']));

        $nome->addValidation('Nome', new TRequiredValidator());
        $login->addValidation('Login', new TRequiredValidator());
        //$email->addValidation('Email', new TEmailValidator());
        $regiao_id->addValidation('Região', new TRequiredValidator());
        $gerente->addValidation('Gerente', new TRequiredValidator());
        $comissao->addValidation('Comissão', new TRequiredValidator());
        $pode_cancelar->addValidation('Pode cancelar', new TRequiredValidator());
        $limite_venda->addValidation('Limite venda', new TRequiredValidator());
        $exibe_comissao->addValidation('Exibe comissão', new TRequiredValidator());
        $pode_reimprimir->addValidation('Pode reimprimir', new TRequiredValidator());

        $id->setSize('50%');

        $this->form->addFields([new TLabel('ID')], [$id], [new TLabel('Nome <span style="color:red">*</span>')], [$nome]);
        $this->form->addFields([new TLabel('Região <span style="color:red">*</span>')], [$regiao_id], [new TLabel('Gerente <span style="color:red">*</span>')], [$gerente]);
        $this->form->addFields([new TLabel('Login <span style="color:red">*</span>')], [$login], [new TLabel('Unidade')], [$unit_id]);
        $this->form->addFields([new TLabel('Senha <span style="color:red">*</span>')], [$password], [new TLabel('Confirmar senha <span style="color:red">*</span>')], [$repassword]);
        $this->form->addFields([new TLabel('Email')], [$email], [new TLabel('Telefone')], [$phone]);
        $this->form->addFields([new TLabel('Endereço')], [$address], [new TLabel('Funcionalidade')], [$function_name]);
        $this->form->addFields([new TLabel('Frontpage')], [$frontpage_id], [new TLabel('Comissão <span style="color:red">*</span>')], [$comissao]);
        $this->form->addFields([new TLabel('Pode cancelar <span style="color:red">*</span>')], [$pode_cancelar], [new TLabel('Tempo')], [$pode_cancelar_tempo]);
        $this->form->addFields([new TLabel('Limite venda <span style="color:red">*</span>')], [$limite_venda], [new TLabel('Exibe comissão <span style="color:red">*</span>')], [$exibe_comissao]);
        $this->form->addFields([new TLabel('Pode reimprimir <span style="color:red">*</span>')], [$pode_reimprimir]);

        $this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');

        $container = new TVBox();
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'CambistaList'));
        $container->add($this->form);

        // add the container to the page
        parent::add($container);
    }

    public function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('permission');
                $object = new Cambista($key);
                $user = $object->get_usuario();

                $object->login      = $user->login;
                $object->name       = $user->name;
                $object->phone      = $user->phone;
                $object->address    = $user->address;
                $object->email      = $user->email;

                $data = new stdClass;
                $data->regiao_id    = $object->regiao_id;
                $data->gerente_id   = $object->gerente_id;

                $this->form->setData($object);

                TForm::sendData('form_Cambista', $data);  
                TTransaction::close();
                
            } else {
                $this->form->clear();
            }

        } catch (\Exception $e) {
            $this->form->clear();
        }
    }


    public function onSave($param)
    {
        try {
            TTransaction::open('permission');
            $data = $this->form->getData();
            $this->form->setData($data);

            $object = new SystemUser();
            $object->fromArray((array) $data);

            unset($object->accepted_term_policy);

            $senha = $object->password;

            if (empty($object->login)) {
                throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Login')));
            }

            if (empty($object->name)) {
                throw new Exception('O campo nome é obrigatório.');
            }


            if (empty($object->id)) {
                if (SystemUser::newFromLogin($object->login) instanceof SystemUser) {
                    throw new Exception(_t('An user with this login is already registered'));
                }

                if (SystemUser::newFromEmail($object->email) instanceof SystemUser) {
                    throw new Exception(_t('An user with this e-mail is already registered'));
                }

                if (empty($object->password)) {
                    throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Password')));
                }

                $object->active = 'Y';
            }

            if($object->password) {
                if($object->password !== $param['repassword']) {
                    throw new Exception(_t('The passwords do not match'));
                }

                $object->password = md5($object->password);

                if ($object->id) {
                    SystemUserOldPassword::validate($object->id, $object->password);
                }
            } else {
                unset($object->password);
            }

            $object->store();

            $userCambista = $object->getUserCambistaForUser();

            if (empty($userCambista->id)) {
                $object->addUserCambista(new Regiao($data->regiao_id), $data);
            }else {
                $object->editUserCambista(new Regiao($data->regiao_id), $data);
            }

            if ($object->password) {
                SystemUserOldPassword::register($object->id, $object->password);
            }
            $object->clearParts();

            //ADICIONA ID DO CAMBISTA
            $object->addSystemUserGroup(new SystemGroup(4));

            if(!empty($data->units)) {
                foreach($param['units'] as $unit_id) {
                    $object->addSystemUserUnit(new SystemUnit($unit_id));
                }
            }

            if (!empty($data->program_list)) {
                foreach ($data->program_list as $program_id) {
                    $object->addSystemUserProgram(new SystemProgram($program_id));
                }
            }

            $data = new stdClass();
            $data->id = $object->id;
            TForm::sendData('form_Cambista', $data);

            TTransaction::close();
            $pos_action = new TAction(['CambistaList', 'onReload']);
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $pos_action);
        } catch (\Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onChangeRegiao($param)
    {
        try {
            TTransaction::open('permission');
            if (!empty($param['regiao_id'])) {
                $criteria = TCriteria::create(['regiao_id' => $param['regiao_id'] ]);
                TDBCombo::reloadFromModel('form_Cambista', 'gerente_id', 'permission', 'Gerente', 'id', 'nome', 'nome', $criteria, true);
            } else {
                TCombo::clearField('form_Cambista', 'gerente_id');
            }
            TTransaction::close();
        } catch (\Exception $e) {

        }
    }

    public static function onClose($param)
    {
        
        TScript::create("Template.closeRightPanel()");
        new TAction(['CambistaList', 'onReload']);
    }
}