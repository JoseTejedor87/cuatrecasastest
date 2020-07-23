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
        // default
        $template = 'web/errors/error.html.twig';

        // For not found exceptions
        if ($exception instanceof NotFoundHttpException || $exception instanceof NotPublishedException) {
            $template = 'web/errors/404.html.twig';
        }
        // for any other exception
        else {
            // Just for fatal errors and NON DEV environment
            if ($this->getParameter('kernel.environment') !== 'dev') {
                $template = 'web/errors/500.html.twig';
            }
            // For DEV environment, raise the exception
            else {
                throw $exception;
            }
        }
        return $this->render($template);
    }
}
