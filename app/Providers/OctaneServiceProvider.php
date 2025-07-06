<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Facades\Octane;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OctaneServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Gọi sau mỗi request — để clear service hoặc reset trạng thái
        if (class_exists(Octane::class) && method_exists(Octane::class, 'afterRequest')) {
            Octane::afterRequest(function (Request $request, Response $response) {
                // \App\Services\MyService::clearCache();
            });
        }

        // Ví dụ khác: gọi định kỳ mỗi 3 giây (tick)
        // if (class_exists(Octane::class) && method_exists(Octane::class, 'tick')) {
        //     Octane::tick('clear-temp-data', function () {
        //         // Do something...
        //     }, 3);
        // }

        //  Octane::afterRequest(function (Request $request, Response $response) {
        //     if ($request->is('api/*')) {
        //         \App\Services\MyService::clearCache();
        //     }
        //  });
        //   Octane::afterRequest(function (Request $request, Response $response) {
        //        if ($response->getStatusCode() >= 500) {
        //           Log::error('Request failed', [
        //              'url' => $request->fullUrl(),
        //              'status' => $response->getStatusCode(),
        //          ]);
        //        }
        //  });

        //  Octane::afterRequest(function (Request $request, Response $response) {
        //       if ($request->user()?->isAdmin()) {
        //           AdminService::resetCache();
        //       }
        //  });
    }

    public function register(): void
    {
        // Nếu cần bind service riêng cho Octane
    }
}
