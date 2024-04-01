<?php

namespace App\Service;

use App\Dto\GenericResponseDto;
use App\Dto\RegisterRequestDto;
use App\Dto\RegisterResponseDto;
use App\Entity\Device;
use App\Entity\DeviceToken;
use App\Repository\ApplicationRepository;
use App\Repository\DeviceRepository;
use App\Repository\DeviceTokenRepository;
use App\Repository\LanguageRepository;
use App\Util\JWTUtil;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeviceService
{
    public function __construct(
        private LanguageRepository     $languageRepository,
        private ApplicationRepository  $applicationRepository,
        private DeviceTokenRepository  $deviceTokenRepository,
        private DeviceRepository       $deviceRepository,
        private ParameterBagInterface  $param,
        private EntityManagerInterface $em
    )
    {
    }

    public function register(RegisterRequestDto $request): RegisterResponseDto
    {
        $device = $this->createOrGetDevice($request);
        $deviceToken = $this->createOrUpdateToken($device);

        return (new RegisterResponseDto())
            ->setClientToken($deviceToken->getToken());
    }

    private function createOrGetDevice(RegisterRequestDto $request): Device
    {
        $device = $this->deviceRepository->findByDevice($request->uid, $request->appId);

        if ($device) {
            return $device;
        }

        $application = $this->applicationRepository->findByAppId($request->appId);
        if (!$application) {
            throw new NotFoundHttpException('NOT_FOUND_APPLICATION');
        }

        $language = $this->languageRepository->findBySlug($request->language);
        if (!$language) {
            throw new NotFoundHttpException('NOT_FOUND_LANGUAGE');
        }

        $device = new Device();
        $device->setUid($request->uid);
        $device->setApplication($application);
        $device->setLanguage($language);
        $device->setOperatingSystem($request->operatingSystem);

        $this->em->persist($device);
        $this->em->flush();

        return $device;
    }

    private function createOrUpdateToken(Device $device): DeviceToken
    {
        $deviceToken = $this->deviceTokenRepository->findByDevice($device);
        $expireDate = (new DateTime())->modify('+1 hour');

        $payload = [
            'deviceId' => $device->getId(),
            'deviceOperatingSystem' => $device->getOperatingSystem(),
            'applicationId' => $device->getApplication()->getId(),
            'exp' => $expireDate->getTimestamp()
        ];

        $jwtToken = JWTUtil::encode($this->param->get('jwt_secret'), $payload);

        if (!$deviceToken) {
            $deviceToken = new DeviceToken();
            $deviceToken->setDevice($device);
        }

        $deviceToken->setToken($jwtToken);
        $deviceToken->setExpiredAt($expireDate);

        $this->em->persist($deviceToken);
        $this->em->flush();

        return $deviceToken;
    }
}