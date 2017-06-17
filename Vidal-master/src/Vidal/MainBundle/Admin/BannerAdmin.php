<?php
namespace Vidal\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BannerAdmin extends Admin
{
	protected $datagridValues;

	public function __construct($code, $class, $baseControllerName)
	{
		parent::__construct($code, $class, $baseControllerName);

		if (!$this->hasRequest()) {
			$this->datagridValues = array(
				'_page'       => 1,
				'_per_page'   => 25,
				'_sort_order' => 'DESC', // sort direction
				'_sort_by'    => 'created' // field name
			);
		}
	}

	protected function configureFormFields(FormMapper $formMapper)
	{
        $displayChoices = array(
            'logged' => 'Только зарегистрированным',
            'guest'  => 'Только незарегистрированным',
        );

		$formMapper
            ->add('group', null, array('label' => 'Баннерное место', 'required' => true))
            ->add('title', null, array('label' => 'Название баннера (для Google Analitics)', 'required' => true))
            ->add('link', null, array('label' => 'Ссылка', 'required' => true))
            ->add('alt', null, array('label' => 'ALT-тег', 'required' => false))
            ->add('forPage', 'text', array('label' => 'Для страницы', 'required' => false, 'help' => 'Баннер будет отображаться лишь на этих страницах. Адрес указывается БЕЗ использования протокола и домена. Варианты перечисляются через ; Пример: /drugs/maalox__42761; /novosti/*'))
            ->add('notForPage', 'text', array('label' => 'Не для страницы', 'required' => false, 'help' => 'Баннер будет скрываться на этих страницах. Адрес указывается БЕЗ использования протокола и домена. Варианты перечисляются через ; Пример: /drugs/maalox__42761; /novosti/*'))
            ->add('displayTo', 'choice', array('label' => 'Кому отображать', 'required' => false, 'choices' => $displayChoices, 'empty_value' => 'ВСЕМ'))
            ->add('enabled', null, array('label' => 'Активен', 'required' => false))
			->add('testMode', null, array('label' => 'Тестовый режим', 'required' => false, 'help'=> 'Виден лишь при добавлении в конец URL хвоста ?t=t'))
            ->add('indexPage', null, array('label' => 'Отображать только на главной странице', 'required' => false))
            ->add('mobile', null, array('label' => 'Отображать в мобильной версии', 'required' => false))
            ->add('mobileProduct', null, array('label' => 'Посреди описания препарата в мобильной версии', 'required' => false, 'help' => 'Если проставлена галочка, то в мобильной версии будет выводиться не СНИЗУ, а перед блоком Яндекс.Директа в описании препарата'))
			->add('position', null, array('label' => 'Позиция в группе'))
            ->add('mobilePosition', null, array('label' => 'Позиция в мобильной версии'))
            ->add('banner', 'iphp_file', array('label' => 'Баннер', 'required' => true))
            ->add('mobileBanner', 'iphp_file', array('label' => 'Баннер мобильной версии', 'required' => false))
            ->add('width', null, array('label' => 'Ширина', 'required' => false, 'help' => 'Ширина баннера (если не указано, то берется ширина группы)'))
            ->add('height', null, array('label' => 'Высота', 'required' => false, 'help' => 'Высота баннера (если не указано, то берется высота группы)'))
            ->add('mobileWidth', null, array('label' => 'Ширина моб.', 'required' => false, 'help' => 'Ширина моб. баннера (если не указано, то берется ширина группы)'))
            ->add('mobileHeight', null, array('label' => 'Высота моб.', 'required' => false, 'help' => 'Высота моб. баннера (если не указано, то берется высота группы)'))
            ->add('showEvent', null, array('label' => 'Событие показа баннера', 'required' => false, 'help' => 'Заполняется по желанию в дополнение к ивенту по умолчанию'))
            ->add('clickEvent', null, array('label' => 'Событие перехода баннера', 'required' => false, 'help' => 'Заполняется по желанию в дополнение к ивенту по умолчанию'))
        ;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper)
	{
		$datagridMapper
			->add('id')
            ->add('title', null, array('label' => 'Ссылка'))
			->add('link', null, array('label' => 'Ссылка'))
			->add('group', null, array('label' => 'Баннерное место'))
            ->add('mobile', null, array('label' => 'Отображать в мобильной версии'))
			->add('enabled', null, array('label' => 'Активен'));
	}

	protected function configureListFields(ListMapper $listMapper)
	{
		$listMapper
			->add('id')
            ->add('title', null, array('label' => 'Название баннера'))
			->add('link', null, array('label' => 'Ссылка'))
			->add('group', null, array('label' => 'Баннерное место'))
			->add('clicks', null, array('label' => 'Переходов'))
			->add('enabled', null, array('label' => 'Активен', 'template' => 'VidalDrugBundle:Sonata:swap_enabled_main.html.twig'))
            ->add('mobile', null, array('label' => 'Отображать в мобильной версии', 'template' => 'VidalDrugBundle:Sonata:swap_mobile_main.html.twig'))
            ->add('position', null, array('label' => 'Позиция в группе'))
            ->add('mobilePosition', null, array('label' => 'Позиция в мобильной версии'))
			->add('_action', 'actions', array(
				'label'   => 'Действия',
				'actions' => array(
					'edit'   => array(),
					'delete' => array(),
				)
			));
	}
}