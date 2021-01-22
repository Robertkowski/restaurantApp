<?php

namespace App\Controller\Space;

use App\Controller\ApiController;
use App\Entity\Space;
use App\Response\ApiResponse;
use App\Service\Space\SpaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tables")
 */
class SpaceController extends ApiController
{

    /**
     * @Route("/show", name="tables-list")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function showList(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filters = [];
        $state = $request->query->get('state');
        if ($state) {
            $filters = ['state' => $state];
        }
        $spaces = $entityManager->getRepository(Space::class)->getList($filters);
        return $this->render('tables/tables.html.twig', ['tables' => $spaces, 'state' => $state]);
    }

    /**
     * @Route("/new", name="new_table")
     * @param Request $request
     * @param SpaceService $spaceService
     * @return Response
     */
    public function new(Request $request, SpaceService $spaceService)
    {
        //$form = $this->createForm(ApartmentType::class);
        //$form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            $apartmentModel = $form->getData();
//            $apartmentService->create($apartmentModel);
//            $this->addFlash('success', 'Apartment added!');
//        }

     //   return $this->render('tables/new_table.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/edit/{space}", name="edit_table")
     * @param Space $space
     * @param Request $request
     * @param SpaceService $spaceService
     * @return Response
     */
    public function edit(Space $space, Request $request, SpaceService $spaceService): Response
    {
//        $apartmentModel = ApartmentModel::fromApartment($apartment);
//        $form = $this->createForm(ApartmentType::class, $apartmentModel);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//            $apartmentModel = $form->getData();
//            $apartmentService->update($apartment, $apartmentModel);
//            $this->addFlash('success', 'Apartment Updated!');
//            return $this->redirectToRoute('edit_apartment', [
//                'apartment' => $apartment->getId(),
//            ]);
//        }
//        return $this->render('tables/edit_table.html.twig', [
//            'form' => $form->createView()
//        ]);
        return $this->render('tables/edit_table.html.twig', []);
    }

    /**
     * @Route("", methods={"GET"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param int $offset
     * @param int $limit
     * @return ApiResponse
     */
    public function getList(Request $request, EntityManagerInterface $entityManager, int $offset = 0, int $limit = 25): ApiResponse
    {
        $filters = json_decode($request->query->get('filters'), true);

        if (!\is_array($filters)) {
            $filters = [];
        }
        $sortField = $request->query->get('sortField');
        $sortType = $request->query->get('sortType');
        $data = $entityManager->getRepository(Space::class)->getList($filters, $offset, $limit, $sortField, $sortType);
        return new ApiResponse($this->jmsSerialization($data));
    }

    /**
     * @Route("/{space}", methods={"GET"})
     * @param Space $space
     * @return ApiResponse
     */
    public function getAction(Space $space): ApiResponse
    {
        return new ApiResponse($this->jmsSerialization($space));
    }

    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @param SpaceService $spaceService
     * @return ApiResponse
     */
    public function postAction(Request $request, SpaceService $spaceService): ApiResponse
    {
        $model = json_decode($request->getContent());
        $spaceService->create($model);
        return new ApiResponse();
    }

    /**
     * @Route("/{space}/free", methods={"PUT"})
     * @param Space $space
     * @param EntityManagerInterface $entityManager
     * @return ApiResponse
     */
    public function putStatusFree(Space $space, EntityManagerInterface $entityManager): ApiResponse
    {
        $space->setState(Space::STATE_FREE);
        $entityManager->flush();
        return new ApiResponse();
    }

    /**
     * @Route("/{space}/reserved", methods={"PUT"})
     * @param Space $space
     * @param EntityManagerInterface $entityManager
     * @return ApiResponse
     */
    public function putStatusReserved(Space $space, EntityManagerInterface $entityManager): ApiResponse
    {
        $space->setState(Space::STATE_RESERVED);
        $entityManager->flush();
        return new ApiResponse();
    }

    /**
     * @Route("/{space}", methods={"PUT"})
     * @param Space $space
     * @param Request $request
     * @param SpaceService $spaceService
     * @return ApiResponse
     */
    public function putAction(Space $space, Request $request, SpaceService $spaceService): ApiResponse
    {
        $model = json_decode($request->getContent());
        $spaceService->update($space, $model);
        return new ApiResponse();
    }
}
