<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event)
    {
//        if ($event->getResponse()->getStatusCode() == 200) {
//            $data = json_decode($event->getResponse()->getContent(), true);
//            if (!isset($data['data'])) {
//                $res['data'] = $data;
//                $event->getResponse()->setContent(json_encode($res));
//            }
//        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}