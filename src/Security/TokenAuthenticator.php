<?php
// src/Security/TokenAuthenticator.php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;
    private $singleSignOnParameters;

    public function __construct(EntityManagerInterface $em, $singleSignOnParameters)
    {
        $this->em = $em;
        $this->singleSignOnParameters = $singleSignOnParameters;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $request->query->get('sso');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' =>$request->query->get('sso'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // $token = $credentials['token'];
        // if ($token === null) {
        //     return;
        // }
        
        $client = new \SoapClient(
            $this->singleSignOnParameters['url'],
            $this->singleSignOnParameters['options']
        );
        // $response  = $client->ValidateSSO([
        //     'input' => [
        //         'Data'      => $token,
        //         'SSOType'   =>  'SSO'
        //     ]
        // ]);

        //dd((array)$response->ValidateSSOResult->Data);
        // if ($response->ValidateSSOResult->Result) {
        //     $data = (array)$response->ValidateSSOResult->Data;
        //     return $this->em->getRepository(User::class)
        //         ->findOneBy(['user_id' => $data['Iniciales']]);
        // }
        
        return $this->em->getRepository(User::class)
            ->findOneBy(['user_id' => 'JTEB']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return new RedirectResponse($request->getBasePath() . $request->getPathInfo(), 301);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

}
