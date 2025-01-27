<?php

namespace App\Controller\Api;

use App\Repository\OrganizationRepository;
use App\Service\PositionService;
use App\Util\JsonResponseHelper;
use App\Util\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/positions', name: 'position_')]
class PositionController extends AbstractController
{
    public function __construct(private OrganizationRepository $organizationRepository, private PositionService $positionService)
    {
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $page = RequestHelper::getPageParam($request, 'page', 1);
        $limit = RequestHelper::getPageParam($request, 'limit', 50, 1, 100);
        return JsonResponseHelper::success($this->positionService->getByOrganization($organization, $page, $limit));
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(Request $request, int $id): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        return JsonResponseHelper::success($this->positionService->getById($organization, $id));
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());
        return JsonResponseHelper::success($this->positionService->create($organization, $data));
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());
        return JsonResponseHelper::success($this->positionService->update($organization, $id, $data));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        return JsonResponseHelper::success($this->positionService->delete($organization, $id));
    }
}
