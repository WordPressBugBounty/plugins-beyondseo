<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\Security\Http\Authenticator;

use BeyondSEODeps\Symfony\Component\HttpFoundation\Request;
use BeyondSEODeps\Symfony\Component\HttpFoundation\Response;
use BeyondSEODeps\Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use BeyondSEODeps\Symfony\Component\HttpKernel\HttpKernelInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\AuthenticationException;
use BeyondSEODeps\Symfony\Component\Security\Core\Exception\BadCredentialsException;
use BeyondSEODeps\Symfony\Component\Security\Core\Security;
use BeyondSEODeps\Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use BeyondSEODeps\Symfony\Component\Security\Core\User\UserProviderInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use BeyondSEODeps\Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use BeyondSEODeps\Symfony\Component\Security\Http\HttpUtils;
use BeyondSEODeps\Symfony\Component\Security\Http\ParameterBagUtils;

/**
 * @author Wouter de Jong <wouter@wouterj.nl>
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class FormLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    private $httpUtils;
    private $userProvider;
    private $successHandler;
    private $failureHandler;
    private array $options;
    private $httpKernel;

    public function __construct(HttpUtils $httpUtils, UserProviderInterface $userProvider, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options)
    {
        $this->httpUtils = $httpUtils;
        $this->userProvider = $userProvider;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->options = array_merge([
            'username_parameter' => '_username',
            'password_parameter' => '_password',
            'check_path' => '/login_check',
            'post_only' => true,
            'form_only' => false,
            'enable_csrf' => false,
            'csrf_parameter' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ], $options);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, $this->options['login_path']);
    }

    public function supports(Request $request): bool
    {
        return ($this->options['post_only'] ? $request->isMethod('POST') : true)
            && $this->httpUtils->checkRequestPath($request, $this->options['check_path'])
            && ($this->options['form_only'] ? 'form' === $request->getContentType() : true);
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $passport = new Passport(
            new UserBadge($credentials['username'], [$this->userProvider, 'loadUserByIdentifier']),
            new PasswordCredentials($credentials['password']),
            [new RememberMeBadge()]
        );
        if ($this->options['enable_csrf']) {
            $passport->addBadge(new CsrfTokenBadge($this->options['csrf_token_id'], $credentials['csrf_token']));
        }

        if ($this->userProvider instanceof PasswordUpgraderInterface) {
            $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));
        }

        return $passport;
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new UsernamePasswordToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return $this->failureHandler->onAuthenticationFailure($request, $exception);
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [];
        $credentials['csrf_token'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['csrf_parameter']);

        if ($this->options['post_only']) {
            $credentials['username'] = ParameterBagUtils::getParameterBagValue($request->request, $this->options['username_parameter']);
            $credentials['password'] = ParameterBagUtils::getParameterBagValue($request->request, $this->options['password_parameter']) ?? '';
        } else {
            $credentials['username'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['username_parameter']);
            $credentials['password'] = ParameterBagUtils::getRequestParameterValue($request, $this->options['password_parameter']) ?? '';
        }

        if (!\is_string($credentials['username']) && !$credentials['username'] instanceof \Stringable) {
            throw new BadRequestHttpException(sprintf('The key "%s" must be a string, "%s" given.', $this->options['username_parameter'], \gettype($credentials['username'])));
        }

        $credentials['username'] = trim($credentials['username']);

        if (\strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Invalid username.');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function setHttpKernel(HttpKernelInterface $httpKernel): void
    {
        $this->httpKernel = $httpKernel;
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        if (!$this->options['use_forward']) {
            return parent::start($request, $authException);
        }

        $subRequest = $this->httpUtils->createRequest($request, $this->options['login_path']);
        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        if (200 === $response->getStatusCode()) {
            $response->setStatusCode(401);
        }

        return $response;
    }
}
