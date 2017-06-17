<?php

namespace Vidal\DrugBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;

class AdsAdmin extends Admin
{
    protected $datagridValues;

    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);

        if (!$this->hasRequest()) {
            $this->datagridValues = array(
                '_page' => 1,
                '_per_page' => 25,
                '_sort_order' => 'DESC',
                '_sort_by' => 'id'
            );
        }
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('sliders', 'sonata_type_collection',
                array(
                    'label'              => 'Слайдер (статьи)',
                    'by_reference'       => false,
                    'cascade_validation' => true,
                    'required'           => false,
                ),
                array(
                    'edit'         => 'inline',
                    'inline'       => 'table',
                    'allow_delete' => true
                )
            )
            ->add('type', 'choice', array('label' => 'Вид баннера', 'required' => true, 'choices' => array(
                'image' => 'Загружаемое изображение',
                'video' => 'Загружаемое видео',
                'youtube' => 'Ролик YouTube',
                'swiffy' => 'Баннер Swiffy',
            )))
            ->add('href', null, array('label' => 'Ссылка', 'required' => false))
            ->add('products-text', 'text', array('label' => 'Описания препаратов', 'required' => false, 'mapped' => false, 'attr' => array('class' => 'doc')))
            ->add('photo', 'iphp_file', array('label' => 'Загружаемое изображение JPG/PNG', 'required' => false))
            ->add('photoForUsersOnly', null, array('label' => 'Изображение только для специалистов', 'required' => false))
            ->add('photoStyles', 'text', array('label' => 'Стили изображения (style)', 'required' => false))
            ->add('video', 'iphp_file', array('label' => 'Загружаемое видео', 'required' => false))
            ->add('videoForUsersOnly', null, array('label' => 'Видео только для специалистов', 'required' => false))
            ->add('raw', null, array('label' => 'Ролики на YouTube (iframe)', 'required' => false))
            ->add('swiffy', null, array('label' => 'Баннер Swiffy', 'required' => false))
            ->add('enabled', null, array('label' => 'Активен', 'required' => false));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('enabled', null, array('label' => 'Активна'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('enabled', null, array('label' => 'Активен', 'template' => 'VidalDrugBundle:Sonata:swap_enabled.html.twig'))
            ->add('_action', 'actions', array(
                'label' => 'Действия',
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }
}