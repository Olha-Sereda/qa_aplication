<?php
/**
 * AnswerNouser type.
 */

namespace App\Form\Type;

use App\Entity\User;
use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AnswerNouserType.
 */
class AnswerNouserType extends AbstractType
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array                                        $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'user.email',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 180]),
                    ],
            ]
        );
        $builder ->add(
                'nickname',
                TextType::class,
                [
                    'label' => 'user.nickname',
                    'required' => true,
                    'constraints' => [
                        new Length(['min' => 3, 'max' => 16]),
                        new NotBlank(),
                    ] ,
                    'attr' => [
                        'minlength' => 3,
                        'maxlength' => 16,
                    ],
                ]
        );
        $builder->add(
            'content',
            TextType::class,
            [
                'label' => 'label_content',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );

        
        
    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Answer::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'answer';
    }
}
