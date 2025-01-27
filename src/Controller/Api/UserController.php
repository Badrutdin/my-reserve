<?php

namespace App\Controller\Api;

use App\Repository\OrganizationRepository;
use App\Security\Auth;
use App\Service\UserService;
use App\Util\JsonResponseHelper;
use App\Util\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route(
    '/api/v1/users',
    name: 'user_',
    requirements: ['subdomain' => '[a-z0-9\-]+', 'host' => '.+'],
    host: '{subdomain}.{host}')]
class UserController extends AbstractController
{
    public function __construct(
        private OrganizationRepository $organizationRepository,
        private UserService            $userService,
        private Auth $auth
    )
    {
    }

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {

        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $page = RequestHelper::getPageParam($request, 'page', 1);
        $limit = RequestHelper::getPageParam($request, 'limit', 50, 1, 100);
        return JsonResponseHelper::success($this->userService->getByOrganization($organization, $page, $limit));
    }


    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(Request $request, int $id): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        return JsonResponseHelper::success($this->userService->getByOrganizationAndId($organization->getId(), $id));
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());
        return JsonResponseHelper::success($this->userService->create($organization, $data));
    }

    #[Route('/', name: 'bulk_update', methods: ['PUT'])]
    public function bulkUpdate(Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());


        if (count($data) > 50) {
            $data = array_slice($data, 0, 50);
        }
        $result = [];
        foreach ($data as $userData) {
            if (!isset($userData['id'])) {
                return JsonResponseHelper::error('Each user data must include an "id".', 400);
            }
            $id = $userData['id'];
            if (gettype($id) != 'integer') return JsonResponseHelper::error('field "id" must be of type integer, "' . gettype($id) . '" given', 400);
            unset($userData['id']);
            $result[] = $this->userService->update($organization, $id, $userData);
        }

        return JsonResponseHelper::success($result);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        $data = RequestHelper::decodeRequestData($request->getContent());
        return JsonResponseHelper::success($this->userService->update($organization, $id, $data));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, Request $request): JsonResponse
    {
        $organization = RequestHelper::getOrganization($request, $this->organizationRepository);
        return JsonResponseHelper::success($this->userService->delete($organization, $id));
    }
}
