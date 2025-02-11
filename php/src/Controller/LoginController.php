<?php

namespace AIO\Controller;

use AIO\Auth\AuthManager;
use AIO\Container\Container;
use AIO\ContainerDefinitionFetcher;
use AIO\Docker\DockerActionManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginController
{
    private AuthManager $authManager;

    public function __construct(AuthManager $authManager) {
        $this->authManager = $authManager;
    }

    public function TryLogin(Request $request, Response $response, $args) : Response {
        $userName = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password'];
        if($this->authManager->CheckCredentials($userName, $password)) {
            $this->authManager->SetAuthState(true);
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function GetTryLogin(Request $request, Response $response, $args) : Response {
        $token = $request->getQueryParams()['token'];
        if($this->authManager->CheckToken($token)) {
            $this->authManager->SetAuthState(true);
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function Logout(Request $request, Response $response, $args) : Response
    {
        $this->authManager->SetAuthState(false);
        return $response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}
