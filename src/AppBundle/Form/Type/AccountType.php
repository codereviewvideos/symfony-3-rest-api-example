<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Transformer\EntityToIdObjectTransformer;
use AppBundle\Form\Transformer\ManyEntityToIdObjectTransformer;
use AppBundle\Repository\UserRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * AccountType constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
        ;

        $userTransformer = new EntityToIdObjectTransformer($this->userRepository);
        $userCollectionTransformer = new ManyEntityToIdObjectTransformer($userTransformer);

        $builder
            ->add(
                $builder->create('users', TextType::class)->addModelTransformer($userCollectionTransformer)
            )
        ;

//            ->add('files', EntityType::class, [
//                'class'     => 'AppBundle\Entity\File',
//                "property"  => "id",
//                "multiple"  => true,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\DTO\AccountDTO',
        ]);
    }

    public function getName()
    {
        return 'account';
    }
}