<?php

namespace App\Controller;

use App\Entity\JobOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/offer', name: 'offer_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $offers = $entityManager->getRepository(JobOffer::class)
            ->findAll();

        return $this->render('offer/index.html.twig',
    [
        'offers' => $offers,
    ]);
    }

    #[Route('/job_offer/{id}/apply', name: 'offer_apply')]
    public function apply(int $id, EntityManagerInterface $entityManager)
    {

        $offer = $entityManager->getRepository(JobOffer::class)
            ->find($id);

        return $this->render('offer/apply.html.twig', [
            'offer' => $offer,
        ]);
    }

}
