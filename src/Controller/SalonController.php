<?php

namespace App\Controller;

use App\Entity\Salon;
use App\Form\SalonType;
use App\Repository\SalonRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalonController extends AbstractController
{




    /**
     * @Route("/", name="salon_index", methods={"GET"})
     */
    public function index(Request $request,SalonRepository $salonRepository, PaginatorInterface $paginator  ): Response
    {
        $salonQuery = $this->getSalonQuery( $request, $salonRepository);
        $salons = $paginator->paginate(
            $salonQuery,
            $request->query->getInt('page',1),
            5
        );
        return $this->render('salon/index.html.twig', [
            'salons' => $salons,
        ]);
    }

    /**
     * @Route("/new", name="salon_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $salon = new Salon();
        $form = $this->createForm(SalonType::class, $salon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($salon);
            $entityManager->flush();

            return $this->redirectToRoute('salon_index');
        }

        return $this->render('salon/new.html.twig', [
            'salon' => $salon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="salon_show", methods={"GET"})
     */
    public function show(Salon $salon): Response
    {
        return $this->render('salon/show.html.twig', [
            'salon' => $salon,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="salon_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Salon $salon): Response
    {
        $form = $this->createForm(SalonType::class, $salon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('salon_index');
        }

        return $this->render('salon/edit.html.twig', [
            'salon' => $salon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="salon_delete", methods={"POST"})
     */
    public function delete(Request $request, Salon $salon): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salon->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($salon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('salon_index');
    }
    private function getSalonQuery(Request $request,SalonRepository $salonRepository){
        if ($request->query->getAlnum('filter')) {
            return $salonRepository->createQueryBuilder('salon')
                ->where('MATCH_AGAINST (salon.name,salon.street,salon.zip,salon.city,salon.email,salon.phone) AGAINST(:search boolean)>0')
                ->setParameter('search', $request->query->getAlnum('filter'));
        }
        return $salonRepository->createQueryBuilder('salon')
            ->getQuery();

    }
}
