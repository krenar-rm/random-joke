<?php

declare(strict_types = 1);

namespace App\Form;

use App\Repository\JokeCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints;

/**
 * Форма для заявки на шутку
 */
class JokeRequestFormType extends AbstractType
{
    /**
     * Репозиторий категории
     *
     * @var JokeCategoryRepository
     */
    private $categoryRepository;

    /**
     * Конструктор
     *
     * @param JokeCategoryRepository $categoryRepository
     */
    public function __construct(JokeCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Создать форму
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr'        => ['placeholder' => 'Email'],
                'label'       => false,
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Email(
                        [
                            'message' => "The email '{{ value }}' is not a valid email.",
                            'mode'    => 'html5',
                        ]
                    ),
                ],
            ])
            ->add('category', ChoiceType::class, [
                'placeholder' => 'Выберите категорию...',
                'choices'     => $this->getCategories(),
                'label'       => false,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ]);
    }

    /**
     * Получить данные по категориям
     *
     * @return array
     */
    private function getCategories(): array
    {
        $categories = [];
        foreach ($this->categoryRepository->findAll() as $category) {
            $categories[$category->getCode()] = $category->getId();
        }

        return $categories;
    }
}
