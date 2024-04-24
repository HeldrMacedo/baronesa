<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TTransaction;
use Adianti\Registry\TSession;
use Adianti\Validator\TEmailValidator;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class GerenteForm extends TPage
{
    protected $form; // form
    protected $program_list;
    protected $unit_list;
    protected $group_list;
    protected $role_list;

    public function __construct()
    {
        parent::__construct();

        //parent::setTargetContainer('adianti_right_panel');

        $this->form = new BootstrapFormBuilder('form_Gerente_user');
        $this->form->setFormTitle('Gerente');

        TTransaction::open('permission');
        $userId = TSession::getValue('userid');
        $unit = (object) SystemUser::find($userId)->get_unit();
        TTransaction::close();

        $regiaoCriteria = new TCriteria;
        $regiaoCriteria->add(new TFilter('unit_id', '=', $unit->id));
        
        $id            = new TEntry('id');
        $name          = new TEntry('name');
        $login         = new TEntry('login');
        $password      = new TPassword('password');
        $repassword    = new TPassword('repassword');
        $email         = new TEntry('email');
        $regiao_id      = new TDBCombo('regiao_id', 'permission', 'Regiao', 'id', 'nome', 'nome', $regiaoCriteria);
        //$unit_id     = new TDBCombo('system_unit_id','permission','SystemUnit','id','name', null, $criteria);
        $unit_id       = new TCombo('system_unit_id');
        //$groups      = new TDBCheckGroup('groups','permission','SystemGroup','id','name', null, $criteriaGroups);
        $frontpage_id  = new TDBUniqueSearch('frontpage_id', 'permission', 'SystemProgram', 'id', 'name', 'name');
        $phone         = new TEntry('phone');
        $address       = new TEntry('address');
        $function_name = new TEntry('function_name');
        $about         = new TEntry('about');

        $password->disableAutoComplete();
        $repassword->disableAutoComplete();

        $btn = $this->form->addAction( _t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink( _t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink(_t('Back'), new TAction(array('GerenteList','onReload')), 'far:arrow-alt-circle-left blue');

        $phone->setMask('(99) 9 9999-9999');

        $combo_items = [];
        $combo_items[$unit->id] = $unit->name;
        
        $unit_id->addItems($combo_items);
        $unit_id->setDefaultOption(false);
        $unit_id->setValue($unit->id);
        $unit_id->setEditable(false);

        $id->setSize('50%');
        $name->setSize('100%');
        $login->setSize('100%');
        $password->setSize('100%');
        $repassword->setSize('100%');
        $email->setSize('100%');
        $unit_id->setSize('100%');
        $frontpage_id->setSize('100%');
        $frontpage_id->setMinLength(1);
        
        // outros
        $id->setEditable(false);
        
        // validations
        $name->addValidation(_t('Name'), new TRequiredValidator);
        $login->addValidation('Login', new TRequiredValidator);
        //$email->addValidation('Email', new TEmailValidator);
        $unit_id->addValidation('Unidade Principal', new TRequiredValidator);
        $regiao_id->addValidation('Região', new TRequiredValidator);
        
        $this->form->addFields( [new TLabel('ID')], [$id],  [new TLabel('Name <span style="color:red">*</span>')], [$name] );
        $this->form->addFields( [new TLabel('Login <span style="color:red">*</span>')], [$login],  [new TLabel(_t('Email'))], [$email] );
        $this->form->addFields( [new TLabel(_t('Address'))], [$address],  [new TLabel(_t('Phone'))], [$phone] );
        $this->form->addFields( [new TLabel(_t('Function'))], [$function_name],  [new TLabel('Região <span style="color:red">*</span>')], [$regiao_id] );
        $this->form->addFields( [new TLabel(_t('Main unit'))], [$unit_id],  [new TLabel(_t('Front page'))], [$frontpage_id] );
        $this->form->addFields( [new TLabel('Senha <span style="color:red">*</span>')], [$password],  [new TLabel('Confirma senha <span style="color:red">*</span>')], [$repassword] ); 

        //$this->form->addHeaderActionLink(_t('Close'), new TAction([$this, 'onClose']), 'fa:times red');
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        //$container->add(new TXMLBreadCrumb('menu.xml', 'GerenteList'));
        $container->add($this->form);

        // add the container to the page
        parent::add($container);
    }

    public function onSave($param)
    {
        try
        {
            
            // open a transaction with database 'permission'
            TTransaction::open('permission');
            
            $data = $this->form->getData();
            $this->form->setData($data);
            
            $object = new SystemUser;
            $object->fromArray( (array) $data );

            unset($object->accepted_term_policy);

            $senha = $object->password;
            
            if( empty($object->login) )
            {
                throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Login')));
            }

            if (empty($param['regiao_id'])) {
                throw new Exception("O campo região é obrigatório.");
            }
            
            if( empty($object->id) )
            {
                if (SystemUser::newFromLogin($object->login) instanceof SystemUser)
                {
                    throw new Exception(_t('An user with this login is already registered'));
                }
                
                if (SystemUser::newFromEmail($object->email) instanceof SystemUser)
                {
                    throw new Exception(_t('An user with this e-mail is already registered'));
                }
                
                if ( empty($object->password) )
                {
                    throw new Exception(TAdiantiCoreTranslator::translate('The field ^1 is required', _t('Password')));
                }
                
                $object->active = 'Y';
            }
            
            if( $object->password )
            {
                if( $object->password !== $param['repassword'] )
                    throw new Exception(_t('The passwords do not match'));
                
                $object->password = md5($object->password);

                if ($object->id)
                {
                    SystemUserOldPassword::validate($object->id, $object->password);
                }
            }
            else
            {
                unset($object->password);
            }
            
            $object->store();
            
            $userGerente = $object->getUserGerenteForUser();

            if (empty($userGerente->id)) {
                $object->addUserGerente(new Regiao($data->regiao_id));
            }else {
                $object->editUserGerete(new Regiao($data->regiao_id));
            }

            if ($object->password)
            {
                SystemUserOldPassword::register($object->id, $object->password);
            }
            $object->clearParts();
            
            //ADICIONA ID DO GERENTE
            $object->addSystemUserGroup( new SystemGroup(3));

            
            if( !empty($data->units) )
            {
                foreach( $param['units'] as $unit_id )
                {
                    $object->addSystemUserUnit( new SystemUnit($unit_id) );
                }
            }
            
            if (!empty($data->program_list))
            {
                foreach ($data->program_list as $program_id)
                {
                    $object->addSystemUserProgram( new SystemProgram( $program_id ) );
                }
            }
            
            $data = new stdClass;
            $data->id = $object->id;
            TForm::sendData('form_Gerente_user', $data);
            
            // close the transaction
            TTransaction::close();
            $pos_action = new TAction(['GerenteList', 'onReload']);
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'), $pos_action);
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                // open a transaction with database 'permission'
                TTransaction::open('permission');
                
                // instantiates object System_user
                $object = new SystemUser($key);
                
                unset($object->password);
                
                $groups = array();
                $units  = array();
                
                if( $groups_db = $object->getSystemUserGroups() )
                {
                    foreach( $groups_db as $group )
                    {
                        $groups[] = $group->id;
                    }
                }
                
                if( $units_db = $object->getSystemUserUnits() )
                {
                    foreach( $units_db as $unit )
                    {
                        $units[] = $unit->id;
                    }
                }
                
                $program_ids = array();
                foreach ($object->getSystemUserPrograms() as $program)
                {
                    $program_ids[] = $program->id;
                }

                $regiao = $object->get_regiaoGerente();
                
                $object->program_list   = $program_ids;
                $object->groups         = $groups;
                $object->units          = $units;
                $object->regiao_id         = $regiao->id;

                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    public static function onClose($param)
    {
        TScript::create("Template.closeRightPanel()");
    }
}