<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Form\ApplicationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/job_offer', name: 'offer_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $offers = $entityManager->getRepository(JobOffer::class)
            ->findAll();

        return $this->render('offer/index.html.twig',
    [
        'offers' => $offers,
    ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("job_offer/{id}/apply", name="offer_apply")
    */
    public function apply(JobOffer $offer, Request  $request)
    {
        $form = $this->createForm(ApplicationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {
            return new Response('<h1>Your app was received!</h1>');
        }

        return $this->render('offer/apply.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

}
