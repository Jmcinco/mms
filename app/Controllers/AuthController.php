<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\RoleRedirector;
use Config\Services;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(RoleRedirector::urlFor(session()->get('role')));
        }

        return view('login');
    }

    public function login()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));
        $recaptchaResponse = (string) $this->request->getPost('g-recaptcha-response');

        if (! $this->verifyRecaptcha($recaptchaResponse)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ]);
        }

        $user = $this->userModel->findByUsername($username);

        if (! $user || ! password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid username or password.'
            ]);
        }

        if (strtoupper($user['status']) !== 'ACTIVE') {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Account inactive.'
            ]);
        }

        session()->regenerate();

        session()->set([
            'user_id'    => $user['user_id'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'username'   => $user['username'],
            'role'       => strtoupper($user['role']),
            'logged_in'  => true,
        ]);

        return $this->response->setJSON([
            'status'   => true,
            'redirect' => \App\Libraries\RoleRedirector::urlFor($user['role']),
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url(''));
    }

    /**
     * Verifies the client's g-recaptcha-response token against Google's
     * siteverify endpoint. Fails closed: any missing token, network error,
     * or Google-reported failure blocks the login attempt.
     */
    protected function verifyRecaptcha(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $secret = env('recaptcha.secretKey');

        if (empty($secret)) {
            log_message('critical', 'reCAPTCHA secret key is not configured.');
            return false;
        }

        try {
            $client = Services::curlrequest();

            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $this->request->getIPAddress(),
                ],
                'http_errors' => false,
                'timeout'     => 5,
            ]);

            $result = json_decode($response->getBody(), true);

            return (bool) ($result['success'] ?? false);
        } catch (\Throwable $e) {
            log_message('error', 'reCAPTCHA verification request failed: ' . $e->getMessage());
            return false;
        }
    }
}