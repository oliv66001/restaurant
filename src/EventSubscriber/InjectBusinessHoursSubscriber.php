<?php

namespace App\EventSubscriber;

use App\Repository\BusinessHoursRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InjectBusinessHoursSubscriber implements EventSubscriberInterface
{
    private $businessHoursRepository;

    public function __construct(BusinessHoursRepository $businessHoursRepository)
    {
        $this->businessHoursRepository = $businessHoursRepository;
    }

    public function onControllerEvent(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if (method_exists($controller, 'setBusinessHours')) {
            $business_hours = $this->businessHoursRepository->findAllOrderedByDay();
            $controller->setBusinessHours($business_hours);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onControllerEvent',
        ];
    }
}
