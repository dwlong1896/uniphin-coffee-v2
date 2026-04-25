<?php

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Hiển thị form đăng nhập
    public function login(): void
    {
        // Đã đăng nhập rồi thì redirect luôn
        if (!empty($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['role']);
        }

        $error = $_GET['error'] ?? null;
        $this->view('auth/signin', ['error' => $error], null);
    }

    public function register(): void
    {
        $this->view('auth/signup', [], null);
    }

    // Xử lý form đăng nhập
    public function handleLogin(): void
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->redirect($base . '/login?error=empty');
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === null || $password !== $user['password']) {
            $this->redirect($base . '/login?error=invalid');
        }

        if ($user['status'] !== 'active') {
            $this->redirect($base . '/login?error=banned');
        }

        $_SESSION['user_id']    = $user['ID'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['role']       = $user['role'];
        $_SESSION['name']       = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['first_name'] = $user['first_name'];  
        $_SESSION['last_name']  = $user['last_name'];   
        $_SESSION['phone']      = $user['phone'];        
        $_SESSION['address']    = $user['address'];      
        $_SESSION['gender']     = $user['gender'];       
        $_SESSION['birth_date'] = $user['birth_date'];   

        $this->redirectByRole($user['role']);
    }

        public function handleLogout(): void
        {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy();

            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $this->redirect($base . '/');
        }

    private function redirectByRole(string $role): void
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        if ($role === 'admin') {
            $this->redirect($base . '/admin/dashboard');
        } else {
            $this->redirect($base . '/');
        }

    }
}