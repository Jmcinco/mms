<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Please log in.');
        }
        
        $userModel = new UserModel();
        $user = $userModel->find($session->get('user_id'));

        if (!$user || strtoupper($user['status']) !== 'ACTIVE') {
            $session->destroy();
            return redirect()->to('/')->with('error', 'Your account is inactive. Please contact an administrator.');
        }

        if ($arguments && !in_array($session->get('role'), $arguments, true)) {
            return redirect()->to('/')->with('error', 'Unauthorized access.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        
    }
}