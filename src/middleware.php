<?php
class AuthenticateMiddleware
{
    public function __invoke($request, $response, $next, $args)
    {
        if(is_null($_SESSION['user'])){
            return $response->withRedirect(PATH, $args);
        }else{
            $response = $next($request, $response);
        }

        return $response;
    }
}