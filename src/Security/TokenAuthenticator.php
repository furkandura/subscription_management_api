<?php

namespace App\Security;

use App\Dto\GenericResponseDto;
use App\Repository\DeviceTokenRepository;
use App\Util\JWTUtil;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ParameterBagInterface $param,
        private DeviceTokenRepository $deviceTokenRepository
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('client-token');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $token = $request->headers->get('client-token');

        if (!$token) {
            throw new CustomUserMessageAuthenticationException('MISSING_TOKEN');
        }

        try {
            $tokenData = JWTUtil::decode($token, $this->param->get('jwt_secret'));
        } catch (Exception $e) {
            throw new CustomUserMessageAuthenticationException('INVALID_TOKEN');
        }

        if ($tokenData->exp < time()) {
            throw new CustomUserMessageAuthenticationException('TOKEN_EXPIRED');
        }

        return new SelfValidatingPassport(
            new UserBadge($token, function () use ($token, $tokenData) {
                return (new TokenUser())
                    ->setToken($token)
                    ->setApplicationId($tokenData->applicationId)
                    ->setDeviceId($tokenData->deviceId)
                    ->setDeviceOperatingSystem($tokenData->deviceOperatingSystem);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = (new GenericResponseDto())
            ->setCode(Response::HTTP_UNAUTHORIZED)
            ->setMessage(strtr($exception->getMessageKey(), $exception->getMessageData()));

        return new JsonResponse($response->toArray(), $response->getCode());
    }
}
