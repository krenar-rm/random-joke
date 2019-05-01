<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Entity\JokeRequest;
use App\Repository\JokeCategoryRepository;
use App\Repository\JokeRequestRepository;
use App\Form\JokeRequestFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контролер для работы с заявками на шутку
 */
class JokeRequestController extends AbstractController
{
    /**
     * Форма для заявки на шутку
     *
     * @return Response
     */
    public function buildForm(): Response
    {
        $form = $this->createForm(JokeRequestFormType::class);

        return $this->render('joke_request_form.html.twig', [
            'jokeRequestForm' => $form->createView(),
        ]);
    }

    /**
     * Обработка заявки на шутку
     *
     * @param Request                $request
     * @param FormFactoryInterface   $formFactory
     * @param JokeCategoryRepository $jokeCategoryRepository
     * @param JokeRequestRepository  $jokeRequestRepository
     *
     * @return Response
     */
    public function submit(
        Request $request,
        FormFactoryInterface $formFactory,
        JokeCategoryRepository $jokeCategoryRepository,
        JokeRequestRepository $jokeRequestRepository
    ): Response {
        $input = $request->get('joke_request_form');

        $form = $formFactory->create(JokeRequestFormType::class);
        $form->submit($input);

        if (!$form->isValid()) {
            throw new \Exception('Invalid data');
        }

        $jokeCategory = $jokeCategoryRepository->find($input['category']);

        $jokeRequest = new JokeRequest();
        $jokeRequest
            ->setEmail($input['email'])
            ->setCategory($jokeCategory);

        $jokeRequestRepository->save($jokeRequest);

        return $this->render('joke_request_submit.html.twig', [
            'email'      => $input['email'],
            'categoryId' => $input['category'],
        ]);
    }
}
