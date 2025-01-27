<?php

namespace App\Controller\Api;

use App\Repository\OrganizationRepository;
use App\Service\RoleService;
use App\Util\JsonResponseHelper;
use App\Util\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/roles', name: 'role_')]
class RoleController extends AbstractController
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private RoleService            $roleService
    )
    {
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $page = RequestHelper::getPageParam($request, 'page', 1);
        $limit = RequestHelper::getPageParam($request, 'limit', 50, 1, 100);
        return JsonResponseHelper::success($this->roleService->getByOrganization($organization, $page, $limit));
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(Request $request, int $id): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        return JsonResponseHelper::success($this->roleService->getByOrganizationAndId($organization, $id));
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());
        return JsonResponseHelper::success($this->roleService->create($organization, $data));
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());
        return JsonResponseHelper::success($this->roleService->update($organization, $id, $data));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        return JsonResponseHelper::success($this->roleService->delete($organization, $id));
    }
}
