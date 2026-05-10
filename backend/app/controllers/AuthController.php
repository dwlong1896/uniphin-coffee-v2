<?php

class AuthController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['role']);
        }

        $error = $_GET['error'] ?? null;
        $registered = $_GET['registered'] ?? null;
        $this->view('auth/signin', ['error' => $error, 'registered' => $registered], null);
    }

    public function register(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirectByRole($_SESSION['role']);
        }

        $error = $_GET['error'] ?? null;
        $this->view('auth/signup', ['error' => $error], null);
    }

    public function handleLogin(): void
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->redirect($base . '/login?error=empty');
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === null || !$this->passwordMatches($password, (string) $user['password'])) {
            $this->redirect($base . '/login?error=invalid');
        }

        if (($user['status'] ?? '') !== 'active') {
            $this->redirect($base . '/login?error=banned');
        }

        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['address'] = $user['address'];
        $_SESSION['gender'] = $user['gender'];
        $_SESSION['birth_date'] = $user['birth_date'];

        $this->redirectByRole($user['role']);
    }

    public function handleRegister(): void
    {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($fullName === '' || $email === '' || $phone === '' || $password === '') {
            $this->redirect($base . '/register?error=empty');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect($base . '/register?error=email');
        }

        if (strlen($password) < 6) {
            $this->redirect($base . '/register?error=password');
        }

        if ($this->userModel->findByEmail($email) !== null) {
            $this->redirect($base . '/register?error=exists');
        }

        $nameParts = preg_split('/\s+/', $fullName) ?: [];
        $firstName = array_pop($nameParts);
        $lastName = trim(implode(' ', $nameParts));

        if ($firstName === null || $firstName === '') {
            $firstName = $fullName;
            $lastName = '';
        }

        $created = $this->userModel->createCustomer([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        if (!$created) {
            $this->redirect($base . '/register?error=failed');
        }

        $this->redirect($base . '/login?registered=1');
    }

    public function handleLogout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
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
            $this->redirect($base . '/admin/users');
        }

        $this->redirect($base . '/');
    }

    private function passwordMatches(string $plainPassword, string $storedPassword): bool
    {
        return password_verify($plainPassword, $storedPassword) || hash_equals($storedPassword, $plainPassword);
    }
}
