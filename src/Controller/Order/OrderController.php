<?php

namespace App\Controller\Order;

use App\Controller\ApiController;
use App\Entity\Order;
use App\Response\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Order\OrderService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/orders")
 */
class OrderController extends ApiController
{

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
        $data = $entityManager->getRepository(Order::class)->getList($filters, $offset, $limit, $sortField, $sortType);
        return new ApiResponse($this->jmsSerialization($data));
    }

    /**
     * @Route("/{order}", methods={"GET"})
     * @param Order $order
     * @return ApiResponse
     */
    public function getAction(Order $order): ApiResponse
    {
        return new ApiResponse($this->jmsSerialization($order));
    }

    /**
     * @Route("", methods={"POST"})
     * @param Request $request
     * @param OrderService $orderService
     * @return ApiResponse
     */
    public function postAction(Request $request, OrderService $orderService): ApiResponse
    {
        $model = json_decode($request->getContent());
        $orderService->create($model);
        return new ApiResponse();
    }

    /**
     * @Route("/{order}", methods={"PUT"})
     * @param Order $order
     * @param Request $request
     * @param OrderService $orderService
     * @return ApiResponse
     */
    public function putAction(Order $order, Request $request, OrderService $orderService): ApiResponse
    {
        $model = json_decode($request->getContent());
        $orderService->update($order, $model);
        return new ApiResponse();
    }
}