<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\RoleRedirector;

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
    $username = trim((string)$this->request->getPost('username'));
    $password = trim((string)$this->request->getPost('password'));

    $user = $this->userModel->findByUsername($username);

    if (!$user || !password_verify($password, $user['password'])) {
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
}