<?php

declare(strict_types=1);

namespace App;

final class ApiAuth
{
    public function __construct(private Repository $repository)
    {
    }

    public function bearerToken(): string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if ($header === '' && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }

        if (preg_match('/Bearer\s+(.+)/i', $header, $matches) !== 1) {
            return '';
        }

        return trim($matches[1]);
    }

    public function user(): ?array
    {
        $token = $this->bearerToken();
        if ($token === '') {
            return null;
        }

        return $this->repository->getApiUserFromToken($token);
    }

    public function requireUser(): array
    {
        $user = $this->user();
        if (!$user) {
            ApiResponse::error('Authentication required.', 401);
            exit;
        }

        return $user;
    }

    public function requireRole(array $roles): array
    {
        $user = $this->requireUser();
        if (!in_array($user['role'] ?? '', $roles, true) && ($user['role'] ?? '') !== 'superadmin') {
            ApiResponse::error('You are not allowed to access this API resource.', 403);
            exit;
        }

        return $user;
    }
}
