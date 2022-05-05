<?php declare(strict_types=1);

class JWTCodec
{
    public function __construct(private string $key) {}

    public function encode(array $payload): string
    {
        $header = $this->base64_urlEncode(json_encode(['typ' => 'JWT','alg' => 'HS256']));

        $payload = $this->base64_urlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $header . '.' . $payload,
            $this->key,
            true
        );

        $signature = $this->base64_urlEncode($signature);

        return $header . '.' . $payload . '.' . $signature; 
    }

    public function decode(string $token): array
    {
        if (1 !== preg_match('/^(?P<header>.*)\.(?P<payload>.*)\.(?P<signature>.*)$/', $token, $match)) {
            throw new InvalidArgumentException('Invalid token format');
        }

        $signature = hash_hmac(
            'sha256',
            $match['header'] . '.' . $match['payload'],
            $this->key,
            true
        );

        $signatureFromToken = $this->base64_urlDecode($match['signature']);

        if (! hash_equals($signature, $signatureFromToken)) {
            throw new InvalidSignatureException();
        }

        $payload = json_decode($this->base64_urlDecode($match['payload']), true);

        if ($payload['exp'] < time()) {
            throw new TokenExpiredException();
        }

        return $payload;
    }

    private function base64_urlEncode(string $text): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
    }

    private function base64_urlDecode(string $text): string
    {
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $text));
    }
}
