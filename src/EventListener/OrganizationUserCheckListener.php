<?php
namespace App\EventListener;

use App\Entity\Organization;
use App\Exception\ApiException;
use App\Repository\OrganizationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsEventListener]
final  class OrganizationUserCheckListener
{

    public function __construct(private  Security $security, private  OrganizationRepository $organizationRepository)
    {

    }

    public function __invoke(RequestEvent $event): void
    {
        // Получаем текущий запрос
        $request = $event->getRequest();

        // Делаем проверку только для запросов, которые требуют проверки принадлежности к организации
        if ($this->isApiRequest($request)) {
            $currentUser = $this->security->getUser();
            $organization = $this->getOrganizationFromRequest($request);

            // Если пользователь не найден или организация не найдена, выбрасываем исключение
            if (!$currentUser || !$organization) {
                throw new AccessDeniedHttpException('Access Denied');
            }

            // Проверяем, является ли пользователь членом организации
            if (!$this->isUserInOrganization($currentUser, $organization)) {
                throw new AccessDeniedHttpException('You are not a member of this organization');
            }
        }
    }

    private function isApiRequest(Request $request): bool
    {
        // Убедитесь, что это API-запрос (например, проверяем путь или что-то еще)
        return strpos($request->getPathInfo(), '/api/') === 0;
    }

    private function getOrganizationFromRequest(Request $request): ?Organization
    {
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];
        try {
            $organization = $this->organizationRepository->findOneBy(['subdomain' => $subdomain]);

        } catch (\Exception $e) {
            ApiException::invalidOrganizationException($subdomain);
        }
        if (!$organization) {
            ApiException::organizationNotFoundException($subdomain);
        }
        return $organization;
    }

    private function isUserInOrganization($user, Organization $organization): bool
    {
        // Проверяем, состоит ли пользователь в организации
        return $user->getOrganizations()->contains($organization);
    }
}
