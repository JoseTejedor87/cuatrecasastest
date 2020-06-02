<?php

namespace App\Controller;

use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use App\Controller\Exception\NotPublishedException;

/**
 * Renders error or exception pages from a given FlattenException.
 */
class ErrorController extends AbstractController
{
    public function show(\Throwable $exception, DebugLoggerInterface $logger): Response
    {
        if ($this->getParameter('kernel.environment') !== 'dev') {
            $code = $exception->getCode();

            if ($exception instanceof NotPublishedException || $exception instanceof NotFoundHttpException) {
                $template = 'web/errors/404.html.twig';
            } elseif ($code >= 500) {
                $template = 'web/errors/500.html.twig';
            } else {
                $template = 'web/errors/error.html.twig';
            }
            return $this->render($template);
        } else {
            throw $exception;
        }
    }
}
