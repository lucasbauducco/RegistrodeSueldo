<?php

namespace App\Form;

use App\Entity\Recibo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CargarRecibosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('archivos', FileType::class, [
            'attr' => ['class' => 'form-control camposEstandar', 'placeholder' => 'Año Cargado'],
            'multiple' => true,
            
        ])
        // unmapped means that this field is not associated to any entity property
        
    
        // ...
        ->add('mes', ChoiceType::class, [
            'attr' => ['class' => 'form-control camposEstandar', 'placeholder' => 'Año Cargado'],
            'choices'  => [
                '' => '',
                'Enero' => 'Enero',
                'Febrero' => 'Febrero',
                'Marzo' => 'Marzo',
                'Abril' => 'Abril',
                'Mayo' => 'Mayo',
                'Junio' => 'Junio',
                'SAC Junio' => 'SAC Junio',
                'Julio' => 'Julio',
                'Agosto' => 'Agosto',
                'Septiembre' => 'Septiembre',
                'Octubre' => 'Octubre',
                'Noviembre' => 'Noviembre',
                'Diciembre' => 'Diciembre',
                'SAC Diciembre' => 'SAC Diciembre',
            ],
        ])
        ->add('fecha', DateType::class, [
            'attr' => array('class' => 'form-control camposEstandar', 'placeholder' => 'Año Cargado'),
            'widget' => 'single_text'
        ])
        
        ->add('Guardar',SubmitType::class)
    
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           
        ]);
    }
}
