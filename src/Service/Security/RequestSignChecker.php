<?php declare(strict_types=1);

namespace App\Service\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RequestSignChecker
{
    private string $requestSecret;

    public function __construct(string $requestSecret)
    {
        $this->requestSecret = $requestSecret;
    }

    public function checkSign(Request $request): void
    {
        if (!$request->query->has('sign')) {
            throw new BadRequestHttpException();
        } elseif (!$request->query->has('expires')) {
            throw new BadRequestHttpException();
        }

        $sign = $request->query->get('sign');
        $expires = $request->query->get('expires');
        $hash = hash_hmac('sha256', $expires, $this->requestSecret);

        if (!hash_equals($sign, $hash)) {
            throw new UnauthorizedHttpException('');
        }
    }
}
