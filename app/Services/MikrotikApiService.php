<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MikrotikApiService
{
    private $ip;
    private $port;
    private $user;
    private $pass;
    private $timeout = 3;
    private $min_ver = 7.12;
    public $connected = false;

    /**
     * @param object|array $mkt اطلاعات ورود سرور
     */
    public function __construct($mkt)
    {
        // پشتیبانی از آبجکت یا آرایه برای راحتی استفاده
        $this->user = is_array($mkt) ? ($mkt['mikrotik_username'] ?? 'admin') : ($mkt->mikrotik_username ?? 'admin');
        $this->pass = is_array($mkt) ? ($mkt['mikrotik_password'] ?? '') : ($mkt->mikrotik_password ?? '');
        $this->ip   = is_array($mkt) ? ($mkt['l2tp_address'] ?? '') : ($mkt->l2tp_address ?? '');
        $this->port = is_array($mkt) ? ($mkt['mikrotik_port'] ?? '443') : ($mkt->mikrotik_port ?? '443');
    }

    /**
     * هسته مرکزی ارسال درخواست‌ها (جلوگیری از تکرار کدهای cURL)
     */
    private function sendRequest(string $method, string $cmd, array $data = [])
    {

        $url = "https://{$this->ip}:{$this->port}/rest{$cmd}";

        try {
            // استفاده از HTTP Client لاراول به جای cURL خام
            $request = Http::withoutVerifying() // معادل CURLOPT_SSL_VERIFYPEER => false
            ->withBasicAuth($this->user, $this->pass)
                ->timeout($this->timeout)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]);

            if (in_array($method, ['get', 'delete'])) {
                $response = $request->$method($url);
            } else {
                $response = $request->$method($url, $data);
            }

            return [
                'code' => $response->status(),
                'data' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error("Mikrotik API Error: " . $e->getMessage());
            return [
                'code' => 0,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    // ==========================================
    // متدهای اصلی کلاس
    // ==========================================

    public function connect()
    {
        $out = ['ok' => 0, 'error' => '', 'data' => []];

        $res = $this->bs_mkt_rest_api_get('/system/package?name=routeros');

        if ($res['ok'] && !empty($res['data']) && !empty($res['data'][0]['version'])) {
            if ($res['data'][0]['version'] >= $this->min_ver) {
                $this->connected = true;
                $out['ok'] = 1;
                $out['data'] = $res['data'][0];
            } else {
                $out['error'] = "RoS version " . $res['data'][0]['version'] . " is not supported, minimal version " . $this->min_ver;
            }
        } else {
            $out['error'] = $res['error'] ?? 'خطا در ارتباط با سرور میکروتیک';
        }

        return $out;
    }

    public function getAllUsers(): array
    {
        $out = ['ok' => 0, 'error' => '', 'data' => []];

        if (!$this->connected) {
            $out['error'] = "Router is not connected";
            return $out;
        }

        $res = $this->bs_mkt_rest_api_get('/ppp/secret');
        if ($res['ok'] && !empty($res['data'])) {
            $out['ok'] = 1;
            $out['data'] = $res['data'];
        } else {
            $out['error'] = $res['error'] ?? 'Failed to get users';
        }

        return $out;
    }

    public function getAllUsernames(): array
    {
        $users = $this->getAllUsers();
        if ($users['ok'] && !empty($users['data'])) {
            return array_column($users['data'], 'name');
        }
        return [];
    }


    public function identity()
    {
        $out = ['ok' => 0, 'identity' => 'not_set'];

        if (!$this->connected) {
            $out['error'] = "Router is not connected";
            return $out;
        }

        $res = $this->bs_mkt_rest_api_get('/system/identity');
        if ($res['ok'] && !empty($res['data']) && !empty($res['data']['name'])) {
            $out['ok'] = 1;
            $out['identity'] = $res['data']['name'];
        }

        return $out;
    }

    public function bs_mkt_rest_api_get($cmd)
    {

        $out = ['ok' => 0, 'error' => '', 'data' => []];
        $response = $this->sendRequest('get', $cmd);

        if ($response['code'] == 0) {
            $out['error'] = 'No output from mikrotik: ' . ($response['error'] ?? 'Timeout');
            return $out;
        }

        if (!empty($response['data'])) {
            $out['data'] = $response['data'];
        }

        if ($response['code'] == 200 && empty($response['data']['error'])) {
            $out['ok'] = 1;
        }

        return $out;
    }

    public function bs_mkt_rest_api_del($cmd)
    {
        $out = ['ok' => 0, 'error' => ''];
        $response = $this->sendRequest('delete', $cmd);

        if ($response['code'] == 0) {
            $out['error'] = 'ERROR: no output from router';
            return $out;
        }

        // میکروتیک برای عملیات حذف موفق کد 204 برمی‌گرداند
        if ($response['code'] == 204 && empty($response['data'])) {
            $out['ok'] = 1;
        } else {
            $out['error'] = $this->formatErrorMessage($response['data']);
        }

        return $out;
    }

    public function bs_mkt_rest_api_upd($cmd, $data)
    {
        $out = ['ok' => 0, 'error' => ''];
        $response = $this->sendRequest('patch', $cmd, $data); // PATCH for Mikrotik Updates

        if ($response['code'] == 0) {
            $out['error'] = 'ERROR: no output from router';
            return $out;
        }

        if ($response['code'] == 200 && !empty($response['data']['.id'])) {
            $out['ok'] = 1;
            $out['data'] = $response['data'];
        } else {
            $out['error'] = $this->formatErrorMessage($response['data']);
        }

        return $out;
    }

    public function bs_mkt_rest_api_post($cmd, $data)
    {
        $out = ['ok' => 0, 'error' => '', 'data' => []];
        $response = $this->sendRequest('post', $cmd, $data);

        if ($response['code'] == 0) {
            $out['error'] = 'No output from mikrotik';
            return $out;
        }

        if (!empty($response['data'])) {
            $out['data'] = $response['data'];
        }

        if ($response['code'] == 200 && empty($response['data']['error'])) {
            $out['ok'] = 1;
        }

        return $out;
    }

    public function bs_mkt_rest_api_add($cmd, $data)
    {
        $out = ['ok' => 0, 'error' => ''];
        $response = $this->sendRequest('put', $cmd, $data); // PUT for Mikrotik Creates

        if ($response['code'] == 0) {
            $out['error'] = 'ERROR: no output from router';
            return $out;
        }

        if ($response['code'] == 201 && !empty($response['data']['.id'])) {
            $out['ok'] = 1;
            $out['data'] = $response['data'];
        } else {
            $out['error'] = $this->formatErrorMessage($response['data']);
        }

        return $out;
    }

    /**
     * فرمت کردن ارورهای میکروتیک به یک رشته خوانا
     */
    private function formatErrorMessage($data)
    {
        if (empty($data) || empty($data['message'])) {
            return 'Unknown Error from RouterOS';
        }

        $error = '[' . ($data['error'] ?? 'ERROR') . '] ' . $data['message'];
        if (!empty($data['detail'])) {
            $error .= ', ' . $data['detail'];
        }

        return $error;
    }
}
