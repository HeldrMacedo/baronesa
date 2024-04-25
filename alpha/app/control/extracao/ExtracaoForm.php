<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCheckGroup;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TNumeric;
use Adianti\Widget\Form\TSelect;
use Adianti\Widget\Form\TTime;
use Adianti\Wrapper\BootstrapFormBuilder;

class ExtracaoForm extends TPage
{
    protected $form;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_ExtracaoForm');
        $this->form->setFormTitle('Extração');
        $this->form->enableClientValidation();

        $descricao = new TEntry('descricao');
        $abreviacao = new TEntry('abreviacao');
        $horaLimite = new TTime('hora_limite');
        $premiacao_maxima = new TNumeric('premiacao_maxima', 2,',','.', true);
        $semanas =  new  TCheckGroup('semanas');
        $dataPrimeiroSorteio = new TDate('data_primeiro_sorteio');
        $limitePalpite = new TNumeric('limite_palpite', 2,',','.', true);

        $descricao->addValidation('Descricão', new TRequiredValidator);
        $horaLimite->addValidation('Hora Limite', new TRequiredValidator);
        $dataPrimeiroSorteio->addValidation('Data Primeiro Sorteio', new TRequiredValidator);
    
        $dataPrimeiroSorteio->setMask('dd/mm/yyyy');


        $semanas->addItems([
            'segunda' => 'Segunda',
            'terca' => 'Terça',
            'quarta' => 'Quarta',
            'quinta' => 'Quinta',
            'sexta' => 'Sexta',
            'sabado' => 'Sabado',
            'domingo' => 'Domingo',
        ]);

        $semanas->setUseButton();
        $semanas->setLayout('horizontal');

        $this->form->addFields([new TLabel('Descricão <span style="color:red">*</span>')], [$descricao], [new TLabel('Abreviacao')], [$abreviacao]);
        $this->form->addFields([new TLabel('Hora Limite <span style="color:red">*</span>')], [$horaLimite], [new TLabel('Data Primeiro Sorteio <span style="color:red">*</span>')], [$dataPrimeiroSorteio]);
        $this->form->addFields([new TLabel('Premiacao Maxima')], [$premiacao_maxima],[new TLabel('Limite Palpite')], [$limitePalpite]);
        $this->form->addFields([new TLabel('Dias da Semana')], [$semanas]);

        $btn = $this->form->addAction(_t('Save'), new TAction(array($this, 'onSave')), 'far:save');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addActionLink(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addActionLink(_t('Back'), new TAction(array('ExtracaoList','onReload')), 'far:arrow-alt-circle-left blue');

        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', 'SystemRoleList'));
        $container->add($this->form);
        
        parent::add($container);

    }

    public function onSave($param = null)
    {
        try{
            new TMessage('info', $param);
        }catch(\Exception $e){
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

    }

    public function onEdit()
    {

    }

}