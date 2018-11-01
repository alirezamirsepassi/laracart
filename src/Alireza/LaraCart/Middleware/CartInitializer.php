<?php
namespace Alireza\LaraCart\Middleware;

use Closure;
use Illuminate\Http\Response;

class CartInitializer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cart_storage = config('laraCart.identifier_storage', 'session');
        $cart_key = uniqid('cart_');

        /** @var Response $response */
        $response = $next($request);
        switch ($cart_storage) {
            case "cookie":
                if($request->hasCookie('cart_id')) {
                    return $next($request);
                }

                $response = $response->withCookie(cookie()->forever('cart_id', $cart_key));
                break;
            case "session":
            default:
                if ($request->session()->exists('cart_id')) {
                    return $next($request);
                }

                \session(['cart_id' => $cart_key]);
                break;
        }
        return $response;
    }

}
