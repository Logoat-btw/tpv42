<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, TelType, ChoiceType, SubmitType
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Panier\Commande;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pays', TextType::class)
            ->add('ville', TextType::class)
            ->add('adresse', TextType::class)
            ->add('codePostal', TextType::class)
            ->add('paiement', ChoiceType::class, [
                'choices' => [
                    'Carte Bancaire' => 'carte',
                    'PayPal' => 'paypal',
                    'Virement' => 'virement'
                ]
            ])
            ->add('telephone', TelType::class)
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Commande::class]);
    }
}
