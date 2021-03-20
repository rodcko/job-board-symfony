<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Applicant;
use App\Form\ApplicationType;
use App\Form\JobOfferType;
use App\Repository\JobOfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/job/offer')]
class JobOfferController extends AbstractController
{

    /**
     * @IsGranted("ROLE_COMPANY_OWNER")
     * @Route("/", name="job_offer_index", methods={"GET"})
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $company = $user->getCompany();

        if (!$company) {
            return $this->redirectToRoute('company_create');
        }

        return $this->render('job_offer/index.html.twig', [
            'job_offers' => $company->getJobOffers(),
        ]);
    }

    /**
     * @IsGranted("ROLE_COMPANY_OWNER")
     * @Route("/", name="job_offer_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $jobOffer = new JobOffer();
        $jobOffer->setCompnay($this->getUser()->getCompnay());
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jobOffer);
            $entityManager->flush();

            return $this->redirectToRoute('job_offer_index');
        }

        return $this->render('job_offer/new.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'job_offer_show', methods: ['GET'])]
    public function show(JobOffer $jobOffer): Response
    {
        return $this->render('job_offer/show.html.twig', [
            'job_offer' => $jobOffer,
        ]);
    }

    /**
     * @Security("((is_granted('ROLE_COMPANY_OWNER') and jobOffer.getCompany() == user.getCompany()) or is_granted('ROLE_ADMIN')")
     * @Route("/{id}/edit", name="job_offer_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, JobOffer $jobOffer): Response
    {
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_offer_index');
        }

        return $this->render('job_offer/edit.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("((is_granted('ROLE_COMPANY_OWNER') and jobOffer.getCompany() == user.getCompany()) or is_granted('ROLE_ADMIN')")
     * @Route("/{id}", name="job_offer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, JobOffer $jobOffer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobOffer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jobOffer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_offer_index');
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Route("/{id}/apply", name="offer_apply")
    */
    public function apply(JobOffer $offer, Request  $request, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $applicant = new Applicant();
        $form = $this->createForm(ApplicationType::class, $applicant);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            $entityManager->persist($applicant);
            $entityManager->flush();

            $mailer->send(
                (new Email())
                    ->from('rodcko@live.com')
                    ->to($offer->getCompnay()->getOwner()->getEmail())
                    ->subject('New application received!')
                    ->html('<p>'.$applicant->getName().' applied for '.$offer->getTitle().'</p><p>Please contact to '.$applicant->getEmail().'</p>')
            );

            $this->addFlash('success', 'Your application has been received!');
            return $this->redirectToRoute('offer_index');
        }

        return $this->render('offer/apply.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

}
