<?php

namespace App\Services;

class SignatureService
{
    public function generate(array $data): string
    {
        ksort($data);

        $payload = http_build_query($data);

        return hash_hmac(
            'sha256',
            $payload,
            config('services.signature.secret')
        );
    }

    public function verify(array $data, string $signature): bool
    {
        $expectedSignature = $this->generate($data);

        return hash_equals($expectedSignature, $signature);
    }
}
