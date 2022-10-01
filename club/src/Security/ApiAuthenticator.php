<?php

namespace App\Security;

use App\Entity\Device;
use App\Helper\EnumStatus\DeviceStatus;
use App\Helper\Exception\ApiException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        protected EntityManagerInterface $entityManager
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('apiKey');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $apiToken = $request->headers->get('apiKey');
        if (null === $apiToken) {
            throw new ApiException(message: "No API token provided", status: Response::HTTP_UNAUTHORIZED);
        }

        /** @var Device $device */
        $device = $this->entityManager->getRepository(Device::class)->findOneBy([
            'token' => $apiToken,
            'status' => DeviceStatus::ACTIVE->value,
        ]);

        if (!$device) {
            throw new ApiException(message: 'Токен авторизации пустой или не существует', status: Response::HTTP_FORBIDDEN);
        }

        if ($device->getUpdateAt() <= new \DateTime()) {
            $device->setStatus(DeviceStatus::EXPIRED->value);
            $this->entityManager->persist($device);
            $this->entityManager->flush();
            throw new ApiException(message: 'Ошибка авторизации, токен истек', status: Response::HTTP_UNAUTHORIZED);
        }
        $device->setUpdateAt((new \DateTime())->modify('+2 week'));
        $this->entityManager->persist($device);
        $this->entityManager->flush();
        return new SelfValidatingPassport(new UserBadge($device->getOwner()->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw new ApiException(
            message: 'Вы не авторизованы',
            status: Response::HTTP_UNAUTHORIZED
        );
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
