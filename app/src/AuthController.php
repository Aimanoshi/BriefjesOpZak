<?php

    class AuthController {
        protected \Doctrine\DBAL\Connection $db;
        protected \twig\Environment $twig;

        public function __construct () {

            $this->db = DatabaseConnector::getConnection();

            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../resources/templates');
            $this->twig = new \Twig\Environment($loader);
        }

        public function showLogin () {
            if (isset($_SESSION['user'])) {
                header('Location: /kanaal');
                exit();
            }

            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            $email = isset($_SESSION['flash']['email']) ? $_SESSION['flash']['email'] : '';
            unset($_SESSION['flash']);
            echo $this->twig->render('pages/inloggen.twig', ['email' => $email, 'formErrors' => $formErrors]);
        }

        public function login () {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            $formErrors = [];

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'login')) {

                $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
                $stmt->execute([$email]);
                $user = $stmt->fetchAssociative();

                if (($user !== false) && (password_verify($password, $user['password']))) {
                    $_SESSION['user'] = $user;
                    header('location: /kanaal');
                    exit();
                }
                else {
                    $formErrors[] = 'Invalid login credentials';
                    $_SESSION['flash'] = ['formErrors' => $formErrors, 'email' => $email];
                }
            }
            header('location: /login');
            exit();
        }

        public function showRegister () {
            $rollen = [
                ['rol' => 'user'],
                ['rol' => 'admin']
            ];
            echo $this->twig->render('pages/registreren.twig', ['rollen' => $rollen,]);
        }

        public function register () {
            $rollen = [
                ['rol' => 'user'],
                ['rol' => 'admin']
            ];
            $formErrors = [];

            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['wachtwoord']) ? trim($_POST['wachtwoord']) : '';
            $rol = isset($_POST['rol']) ? ($_POST['rol']) : '0';
            $kort = isset($_POST['kort']) ? trim($_POST['kort']) : '';

            if ((isset($_POST['moduleAction'])) && ($_POST['moduleAction'] == 'add-persoon')) {
                if ($password === '') {
                    $formErrors[] = "Geef een wachtwoord";
                }

                if ((trim($kort) === '') && (!ctype_alnum($kort))) {
                    $formErrors[] = "Geef een beschrijving";
                }

                if ((trim($email) === '')) {
                    $formErrors[] = "Geef een email";
                }

                if (($rol === '0') && !in_array($rol, ['user', 'admin'])) {
                    $formErrors[] = "Kies een geldige rol";
                }

                if (!$formErrors) {
                    $encryptedWachtwoord = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare('INSERT INTO users (email, password, rol ,wie) VALUES(?,?,?,?)');
                    $stmt->execute([$email, $encryptedWachtwoord, $rol, $kort]);

                    header('location:/login');
                    exit();
                }
            }

            echo $this->twig->render('pages/registreren.twig', [
                'email' => $_POST['email'],
                'rollen' => $rollen,
                'rol' => $rol,
                'kort' => $_POST['kort'],
                'formErrors' => $formErrors
            ]);
        }

        public function showOrganisatie () {
            //alle organisations ophalen
            $stmt = $this->db->prepare('SELECT * FROM organisations');
            $stmt->execute([]);
            $organisationsAssociative = $stmt->fetchAllAssociative();

            //postcode ophalen
            $stmt = $this->db->prepare('SELECT distinct(postcode) FROM organisations');
            $stmt->execute([]);
            $zipAssociative = $stmt->fetchAllAssociative();

            //Persisting
            $term = isset($_GET['term']) ? $_GET['term'] : '';
            $zipGet = (array_key_exists('zip', $_GET)) ? $_GET['zip'] : '';

            $formErrors = [];

            if ((isset($_GET['moduleAction'])) && ($_GET['moduleAction'] == 'search-SchoolClub')) {
                if (trim($term) === '') {
                    $formErrors[] = "Geef een geldige naam";
                }
                if (($zipGet === '') || (in_array($zipGet, $zipAssociative))) {
                    $formErrors[] = "Kies een geldige postcode";
                }
                if (!$formErrors) {
                    $stmt = $this->db->prepare('SELECT * FROM organisations WHERE name LIKE ? OR postcode LIKE ?');
                    $stmt->execute(['%' . $term . '%', $zipGet]);
                    $organisationsAssociative = $stmt->fetchAllAssociative();
                }
            }
            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            unset($_SESSION['flash']);

            echo $this->twig->render('pages/organisatiesKeuze.twig', ['organisations' => $organisationsAssociative, 'term' => htmlentities($term), 'zip' => htmlentities($zipGet), 'zips' => $zipAssociative, "formErrors" => $formErrors]);
        }

        public function organisatie () {
            $organisationsId = isset($_POST['organisations']) ? $_POST['organisations'] : array();

            $formErrors = [];

            if ((isset($_POST['moduleActionPost'])) && ($_POST['moduleActionPost'] == 'verder-SchoolClub')) {
                if (empty($organisationsId)) {
                    $formErrors[] = "Kies een organisatie";
                }

                if (!$formErrors) {
                    $_SESSION['id'] = ['OrganisatieId' => $organisationsId];
                    header('location:/register/kies_kanaal');
                    exit();
                }
                else {
                    $_SESSION['flash'] = ['formErrors' => $formErrors];
                }
            }
            header('location:/register/kies_organisatie');
            exit();
        }

        public function showkanaal () {
            $ids = isset($_SESSION['id']['OrganisatieId']) ? $_SESSION['id']['OrganisatieId'] : array();
            unset($_SESSION['id']);
            foreach ($ids as $id) {
                $stmt = $this->db->prepare('SELECT Id,name, description FROM channels WHERE organisations_Id = ?');
                $stmt->execute([$id]);
                $channelsAssociative[] = $stmt->fetchAllAssociative();//Ik kan niet alles afdrukken 1 per id
            }

            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            unset($_SESSION['flash']);
            echo $this->twig->render('pages/kanaalKeuze.twig', ['channels' => $channelsAssociative, "formErrors" => $formErrors]);
        }

        public function kanaal () {
            $kanallen = isset($_POST['kanaal']) ? $_POST['kanaal'] : '';

            $formErrors = [];

            if ((isset($_POST['moduleAction'])) && ($_POST['moduleAction'] == 'verder-SchoolClub')) {
                if (!$kanallen) {
                    $formErrors[] = "Kies een kanaal";
                }

                if (!$formErrors) {
                    $user = isset($_SESSION['user']) ? $_SESSION['user'] : 'lol';
                    //unset($_SESSION['user']);
                    $stmt = $this->db->prepare('INSERT INTO subscriptions (accepted, users_Id, channels_Id) VALUES(?,?,?)');
                    foreach ($kanallen as $kanaal) {
                        $stmt->execute([0, $user, $kanaal]);
                    }
                    header('location:/kanaal');
                    exit();
                }
                else {
                    $_SESSION['flash'] = ['formErrors' => $formErrors];
                }
            }
            header('location:/register/kies_kanaal');
            exit();
        }

        public function logout () {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() + (6*30*24*3600),
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            header('location: login');
            exit();
        }
    }