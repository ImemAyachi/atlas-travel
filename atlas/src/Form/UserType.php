<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'first name...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'First name is required']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'First name must be at least {{ limit }} characters',
                    ]),
                ],
                'mapped' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'last name...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Last name is required']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Last name must be at least {{ limit }} characters',
                    ]),
                ],
                'mapped' => false,
            ])
            ->add('age', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'age...',
                    'class' => 'form-control',
                    'min' => 10,
                    'max' => 99,
                    'maxlength' => 2
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Age is required']),
                    new Positive(['message' => 'Age must be a valid number']),
                    new \Symfony\Component\Validator\Constraints\Range([
                        'min' => 10,
                        'max' => 99,
                        'notInRangeMessage' => 'Age must be between {{ min }} and {{ max }} years',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'email...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Email is required']),
                    new Email(['message' => 'Invalid email format']),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'phone number...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\+?[0-9\s\-\(\)]{8,20}$/',
                        'message' => 'Please enter a valid phone number',
                        'match' => true,
                    ]),
                ],
                'mapped' => false,
            ])
            ->add('address', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'address...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Address is required']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Please enter a complete address',
                    ]),
                ],
                'property_path' => 'adresse',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match',
                'mapped' => false,
                'first_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'password...',
                        'class' => 'form-control'
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'Password is required']),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Password must be at least {{ limit }} characters',
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'message' => 'Password must contain at least one uppercase letter',
                        ]),
                        new Regex([
                            'pattern' => '/[0-9]/',
                            'message' => 'Password must contain at least one number',
                        ]),
                        new Regex([
                            'pattern' => '/[!@#$%^&*(),.?":{}|<>]/',
                            'message' => 'Password must contain at least one special character',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'confirm password...',
                        'class' => 'form-control'
                    ],
                ],
            ])
            ->add('profile_image', FileType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'validation_groups' => ['Default', 'registration'],
        ]);
    }
} 