<?php
/**
 * Created by PhpStorm.
 * User: ios
 * Date: 2018/12/24
 * Time: 7:09 PM
 */

namespace App\Http\Middleware;

use Closure;
class SetLocal
{
    public function handle($request,Closure $next){
//        app()->setLocale($request->getLocale());
        app()->setLocale('zh-CN');
        return $next($request);
    }
}