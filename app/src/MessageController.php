<?php

    class MessageController {
        protected \Doctrine\DBAL\Connection $db;
        protected \twig\Environment $twig;
        protected $user;

        public function __construct () {
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit();
            }
            $this->user = $_SESSION['user'];
            $this->db = DatabaseConnector::getConnection();

            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../resources/templates');
            $this->twig = new \Twig\Environment($loader);
            $basePath = __DIR__ . '/../';

            require_once $basePath . 'src/functions.php';
        }

        public function home () {
            echo $this->twig->render('pages/index.twig', [
                'user' => $this->user['Id'],
                'rol' => $this->user['rol']
            ]);
        }

        public function organisaties () {
            //postcode ophalen
            $stmt = $this->db->prepare('SELECT distinct(postcode) FROM organisations');
            $stmt->execute([]);
            $zipAssociative = $stmt->fetchAllAssociative();

            $rol = true;

            $stmt = $this->db->prepare('SELECT * FROM users WHERE Id= ?');
            $stmt->execute([$this->user['Id']]);
            $userRol = $stmt->fetchAssociative();

            //TOON ORG MET FILTER
            $term = isset($_GET['term']) ? $_GET['term'] : '';
            $zipGet = (array_key_exists('zip', $_GET)) ? $_GET['zip'] : '';

            $formErrors = [];
            $organisaties = [];

            if (isset($_GET['moduleAction']) && ($_GET['moduleAction'] == 'search-SchoolClub')) {
                if (trim($term) === '') {
                    $formErrors[] = "Geef een geldige naam";
                }
                if (($zipGet === '') || (in_array($zipGet, $zipAssociative))) {
                    $formErrors[] = "Kies een geldige postcode";
                }
                if (!$formErrors) {
                    if ($userRol['rol'] == 'user') {
                        $stmt = $this->db->prepare('SELECT * FROM organisations where name = ? AND postcode = ?');
                        $stmt->execute([$term, $zipGet]);
                        $organisaties = $stmt->fetchAllAssociative();
                        $rol = false;
                    }
                    else {
                        $stmt = $this->db->prepare('SELECT * FROM organisations WHERE users_Id = ?');
                        $stmt->execute([$this->user['Id']]);
                        $organisaties = $stmt->fetchAllAssociative();
                    }
                }else{
                    $_SESSION['flash'] = ['formErrors' => $formErrors];
                }
            }else {
                //TOON ORG ZONDER FILTER
                if ($userRol['rol'] == 'user') {
                    $stmt = $this->db->prepare('SELECT * FROM organisations');
                    $stmt->execute([]);
                    $organisaties = $stmt->fetchAllAssociative();
                    $rol = false;
                }
                else {
                    $stmt = $this->db->prepare('SELECT * FROM organisations WHERE users_Id = ?');
                    $stmt->execute([$this->user['Id']]);
                    $organisaties = $stmt->fetchAllAssociative();
                }
            }
            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            unset($_SESSION['flash']);

            //ECHO
            echo $this->twig->render('pages/organisatie.twig', [
                'organisaties' => $organisaties,
                'rol' => $this->user['rol'],
                'auth' => true,
                'term' => htmlentities($term),
                'zip' => htmlentities($zipGet),
                'zips' => $zipAssociative,
                "formErrors" => $formErrors
            ]);
        }

        public function showOrganisatieDetail ($id) {
            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            unset($_SESSION['flash']);
            $rol = true;

            if ($this->user['rol'] == 'user') {
                $organisatie = getOneOrganisation($id);
                $kanalen = getChannels($id);
                $rol = false;
            }
            else {
                $stmt = $this->db->prepare('SELECT * FROM organisations WHERE Id = ? AND users_Id = ? ');
                $stmt->execute([$id, $this->user['Id']]);
                $organisatie = $stmt->fetchAssociative();
                if (!$organisatie) {
                    header('Location: /organisatie');
                    exit();
                }

                $stmt = $this->db->prepare('SELECT channels.Id, channels.name, channels.description, channels.organisations_Id FROM channels INNER JOIN organisations ON organisations_Id = organisations.Id WHERE organisations_Id = ? AND users_Id = ?');
                $stmt->execute([$id, $this->user['Id']]);
                $kanalen = $stmt->fetchAllAssociative();
                /* if (! $kanalen){
                     header('Location: /organisatie');
                     exit();
                 }*/
            }
            echo $this->twig->render('pages/organisatieInfo.twig', [
                'organisatie' => $organisatie,
                'kanalen' => $kanalen,
                'rol' => $rol,
                'formErrors' => $formErrors
            ]);
        }

        public function organisatieDetail ($id) {
            $naam = isset($_POST['naam']) ? trim($_POST['naam']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $postcode = isset($_POST['postcode']) ? trim($_POST['postcode']) : '';
            $omschrijving = isset($_POST['omschrijving']) ? trim($_POST['omschrijving']) : '';

            $formErrors = [];

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'wijziging')) {

                if ((trim($naam) === '')) {
                    $formErrors[] = "Geef een Naam";
                }
                if ((trim($address) === '')) {
                    $formErrors[] = "Geef een Adress";
                }
                if ((trim($postcode) === '')) {
                    $formErrors[] = "Geef een postcode";
                }
                if ((trim($omschrijving) === '') && (!ctype_alnum($omschrijving))) {
                    $formErrors[] = "Geef een omschrijving";
                }

                if (!$formErrors) {
                    $stmt = $this->db->prepare('UPDATE organisations SET name = ?, address = ?,  postcode = ? , description = ? WHERE Id = ?');
                    $stmt->execute([$naam, $address, $postcode, $omschrijving, $id]);
                    header('location:/organisatie');
                    exit();
                }
                else {
                    $_SESSION['flash'] = ['formErrors' => $formErrors];
                }
            }
            header('location:/organisatie/' . $id . '/detail');
            exit();
        }

        public function showOrganisatieCreate () {

            // als user kan je dit pagina niet zien
            if ($this->user['rol'] != 'admin') {
                header('location:/organisatie');
                exit();
            }

            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            $naam = isset($_SESSION['flash']['naam']) ? $_SESSION['flash']['naam'] : '';
            $address = isset($_SESSION['flash']['address']) ? $_SESSION['flash']['address'] : '';
            $postcode = isset($_SESSION['flash']['postcode']) ? $_SESSION['flash']['postcode'] : '';
            $omschrijving = isset($_SESSION['flash']['omschrijving']) ? $_SESSION['flash']['omschrijving'] : '';
            unset($_SESSION['flash']);

            echo $this->twig->render('pages/organisatieCreeren.twig', [
                'formErrors' => $formErrors,
                'naam' => $naam,
                'address' => $address,
                'postcode' => $postcode,
                'omschrijving' => $omschrijving,
                'auth' => true]);
        }

        public function organisatieCreate () {
            $naam = isset($_POST['naam']) ? trim($_POST['naam']) : '';
            $address = isset($_POST['address']) ? trim($_POST['address']) : '';
            $postcode = isset($_POST['postcode']) ? trim($_POST['postcode']) : '';
            $omschrijving = isset($_POST['omschrijving']) ? trim($_POST['omschrijving']) : '';

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'VoegToe')) {
                if ($naam === '') {
                    $formErrors[] = "Geef een Naam";
                }
                if ((trim($address) === '')) {
                    $formErrors[] = "Geef een Adress";
                }
                if ((trim($postcode) === '')) {
                    $formErrors[] = "Geef een postcode";
                }
                if ((trim($omschrijving) === '') && (!ctype_alnum($omschrijving))) {
                    $formErrors[] = "Geef een omschrijving";
                }

                if (!$formErrors) {
                    $stmt = $this->db->prepare('INSERT INTO organisations (name, address, postcode ,description,users_Id) VALUES(?,?,?,?,?)');
                    $stmt->execute([$naam, $address, $postcode, $omschrijving, $this->user['Id']]);
                    //$_SESSION['organisatieId'] = $this->db->lastInsertId();
                    header('location:/organisatie');
                    exit();
                }
                else {
                    $_SESSION['flash'] = ['formErrors' => $formErrors,
                        'naam' => $naam,
                        'address' => $address,
                        'postcode' => $postcode,
                        'omschrijving' => $omschrijving];
                    header('location:/organisatie/create');
                    exit();
                }
            }
        }

        public function showKanaalCreate ($id) {
            if ($this->user['rol'] != 'admin') {
                header('location:/organisatie');
                exit();
            }
            $stmt = $this->db->prepare('SELECT * FROM `organisations` LEFT JOIN users ON organisations.users_Id = users.Id WHERE users_Id = ? AND organisations.Id = ?');
            $stmt->execute([$this->user['Id'], $id]);
            $kanaal = $stmt->fetchAssociative();
            if (!$kanaal) {
                header('Location: /organisatie');
                exit();
            }

            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            $naam = isset($_SESSION['flash']['naam']) ? $_SESSION['flash']['naam'] : '';
            $omschrijving = isset($_SESSION['flash']['omschrijving']) ? $_SESSION['flash']['omschrijving'] : '';
            unset($_SESSION['flash']);

            echo $this->twig->render('pages/kanaalCreëren.twig', [
                'formErrors' => $formErrors,
                'naam' => $naam,
                'omschrijving' => $omschrijving,
                'id' => $id,
                'autho' => true
            ]);
        }

        public function kanaalCreate ($id) {
            $naam = isset($_POST['naam']) ? trim($_POST['naam']) : '';
            $omschrijving = isset($_POST['omschrijving']) ? trim($_POST['omschrijving']) : '';

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'VoegToe')) {
                if ((trim($naam) === '')) {
                    $formErrors[] = "Geef een Naam";
                }
                if ((trim($omschrijving) === '') && (!ctype_alnum($omschrijving))) {
                    $formErrors[] = "Geef een omschrijving";
                }

                if (!$formErrors) {
                    $stmt = $this->db->prepare('INSERT INTO channels (name,description,organisations_Id) VALUES(?,?,?)');
                    $stmt->execute([$naam, $omschrijving, $id]);
                    $_SESSION['kanaalId'] = $this->db->lastInsertId();
                    header('location:/organisatie/' . $id . '/detail');
                    exit();
                }
                else {
                    $_SESSION['flash'] = ['formErrors' => $formErrors,
                        'naam' => $naam,
                        'omschrijving' => $omschrijving];
                    header('location:/organisatie/' . $id . '/kanaal/create');
                    exit();
                }
            }
        }

        public function kanaalDetail ($id) {
            if ($this->user['rol'] === 'user') {
                $kanaal = getOneChannel($id);
            }

            // als je niet de beheerder bent van dit kanaal dan heb je geen toegang tot andere kanalen.
            if ($this->user['rol'] === 'admin') {
                $stmt = $this->db->prepare('SELECT channels.Id, channels.name, organisations.name AS organisation_name, channels.description,channels.organisations_Id FROM `channels`
                                                LEFT JOIN organisations ON organisations_Id = organisations.Id 
                                                WHERE channels.Id =? AND organisations.users_Id = ?');
                $stmt->execute([$id, $this->user['Id']]);
                $kanaal = $stmt->fetchAssociative();
                if (!$kanaal) {
                    header('Location: /organisatie');
                    exit();
                }

            }

            echo $this->twig->render('pages/kanaalInfo.twig', [
                'kanaal' => $kanaal,
                'rol' => $this->user['rol']
            ]);
        }

        public function geabonneerdKanaal () {
            $channelId = isset($_SESSION['flash']['channelId']) ? $_SESSION['flash']['channelId'] : '';
            unset($_SESSION['flash']);

            $kanalen = [];
            if ($this->user['rol'] == 'user') {
                $kanalen = getSubscribedChannels($this->user['Id']);
            }
            if ($this->user['rol'] == 'admin') {
                $stmt = $this->db->prepare('SELECT channels.Id  AS channels_Id, channels.name, organisations.name AS organisation_name FROM `channels` LEFT JOIN organisations ON organisations_Id = organisations.Id WHERE organisations.users_Id = ?');
                $stmt->execute([$this->user['Id']]);
                $kanalen = $stmt->fetchAllAssociative();
            }
            echo $this->twig->render('pages/Kanaal.twig', [
                'kanalen' => $kanalen,
                'rol' => $this->user['rol']
            ]);
        }

        public function verlaatKanaal(){
            $channelId = isset($_POST['channel_id']) ? trim($_POST['channel_id']) : '';

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'delete')){
                $stmt = $this->db->prepare('DELETE FROM subscriptions WHERE subscriptions.users_Id = ? AND subscriptions.channels_Id = ?');
                $stmt->execute([$this->user['Id'], $channelId]);
                header('location: /kanaal');
                exit();
            }

        }

        public function kanaalBerichten ($id) {
            if ($this->user['rol'] == 'user') {
                $stmt = $this->db->prepare('SELECT `title`, messages.description, `added_date` FROM `messages` 
                                            LEFT JOIN channels ON messages.channels_Id = channels.Id 
                                            LEFT JOIN subscriptions ON subscriptions.channels_Id = channels.Id 
                                            WHERE messages.channels_Id = ? AND subscriptions.users_Id = ? AND subscriptions.accepted = ?');
                $stmt->execute([$id, $this->user['Id'], 1]);
                $berichten = $stmt->fetchAllAssociative();
            }
            if ($this->user['rol'] == 'admin') {
                $stmt = $this->db->prepare('SELECT `title`, messages.description, `added_date` FROM `messages` 
                                            LEFT JOIN channels ON messages.channels_Id = channels.Id 
                                            LEFT JOIN organisations ON organisations.Id = channels.organisations_Id
                                            LEFT JOIN users ON organisations.users_Id = users.Id
                                            WHERE messages.channels_Id = ? AND users.Id = ?');
                $stmt->execute([$id, $this->user['Id']]);
                $berichten = $stmt->fetchAllAssociative();
            }

            echo $this->twig->render('pages/bericht.twig', ['berichten' => $berichten]);
        }

        public function aboneer ($id) {
            $stmt = $this->db->prepare('SELECT * FROM subscriptions WHERE users_Id = ? AND channels_Id = ?');
            $stmt->execute([$this->user['Id'], $id]);
            $userAssociative = $stmt->fetchAssociative();

            if (!$userAssociative) {
                $stmt = $this->db->prepare('INSERT INTO subscriptions (accepted, users_Id, channels_Id) VALUES(?,?,?)');
                $stmt->execute([0, $this->user['Id'], $id]);
                $message = "Jouw verzoek is naar de beheerder van dit kanaaal gestuurd";
                echo("<script type='text/javascript'>alert('$message');</script>");
            }
            else {
                $message = "Je bent all geaboneerd op deze kanaal";
                echo("<script type='text/javascript'>alert('$message');</script>");
            }
            header('location: /kanaal');
            exit();
        }

        public function showVoegBerichten ($id) {
            $formErrors = isset($_SESSION['flash']['formErrors']) ? $_SESSION['flash']['formErrors'] : [];
            $titel = isset($_SESSION['flash']['titel']) ? $_SESSION['flash']['titel'] : '';
            $bericht = isset($_SESSION['flash']['bericht']) ? $_SESSION['flash']['bericht'] : '';
            unset($_SESSION['flash']);

            // als je niet de beheerder bent van dit kanaal, dan kan je ook geen berichten plaatsen.
            $stmt = $this->db->prepare('SELECT channels.Id, channels.name, organisations.name AS organisation_name, channels.description FROM `channels`
                                                LEFT JOIN organisations ON organisations_Id = organisations.Id 
                                                WHERE channels.Id =? AND organisations.users_Id = ?');
            $stmt->execute([$id, $this->user['Id']]);
            $kanaal = $stmt->fetchAssociative();
            if (!$kanaal) {
                header('Location: /kanaal');
                exit();
            }

            echo $this->twig->render('pages/voeg_bericht.twig', [
                'formErrors' => $formErrors,
                'titel' => $titel,
                'bericht' => $bericht,
                'id' => $id,
                'autho' => true
            ]);
        }

        public function voegBerichten ($id) {
            $titel = isset($_POST['titel']) ? trim($_POST['titel']) : '';
            $bericht = isset($_POST['bericht']) ? trim($_POST['bericht']) : '';

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'voegtoe')) {
                if ((trim($titel) === '')) {
                    $formErrors[] = "Geef een titel";
                }
                if ((trim($bericht) === '') && (!ctype_alnum($bericht))) {
                    $formErrors[] = "Schrijf uw bericht";
                }

                if (!$formErrors) {
                    $stmt = $this->db->prepare('INSERT INTO messages (title, description, added_date, channels_Id) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$titel, $bericht, date("Y-m-d"), $id]);
                    $_SESSION['kanaalId'] = $this->db->lastInsertId();
                    header('location:/kanaal/' . $id . '/berichten');
                    exit();
                }
                else {
                    $_SESSION['flash'] = ['formErrors' => $formErrors,
                        'titel' => $titel,
                        'bericht' => $bericht];
                    header('location:/kanaal/' . $id . '/voegbericht');
                    exit();
                }
            }
        }

        public function deleteKanaal ($id) {
            $stmt = $this->db->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
            $stmt = $this->db->prepare('DELETE channels FROM channels 
                        LEFT JOIN organisations ON organisations_Id = organisations.Id                        
                        WHERE channels.Id = ? AND organisations_Id = ? AND organisations.users_Id = ?');
            $stmt->execute([$id,6, $this->user['Id']]);
            $stmt = $this->db->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
            header('location:/kanaal');
            exit();
        }

        public function showAbonnees (){
            if ($this->user['rol'] != 'admin'){
                header('Location: /organisatie');
            }

            $userId = isset($_SESSION['flash']['userId']) ? $_SESSION['flash']['userId'] : '';
            $channelId = isset($_SESSION['flash']['channelId']) ? $_SESSION['flash']['channelId'] : '';
            unset($_SESSION['flash']);

            $stmt = $this->db->prepare('SELECT users.Id AS userid, users.wie, channels.Id AS channelid, channels.name AS channelname FROM `subscriptions`
                                            LEFT JOIN users ON subscriptions.users_Id = users.Id
                                            LEFT JOIN channels ON subscriptions.channels_Id = channels.Id
                                            LEFT JOIN organisations ON channels.organisations_Id = organisations.Id
                                            WHERE organisations.users_Id = ? AND accepted = ?');
            $stmt->execute([$this->user['Id'], 0]);
            $abonnees = $stmt->fetchAllAssociative();

            echo $this->twig->render('pages/abonnees.twig', [
                'users' => $abonnees,
                'userId' => $userId,
                'channelId' => $channelId,
                'rol' => $this->user['rol']
            ]);
        }

        public function abonnees(){
            $userId = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
            $channelId = isset($_POST['channel_id']) ? trim($_POST['channel_id']) : '';

            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'accept')){
                $stmt = $this->db->prepare('UPDATE subscriptions SET accepted = ? WHERE subscriptions.users_Id = ? AND subscriptions.channels_Id = ?');
                $stmt->execute([1, $userId, $channelId]);
                header('location: /abonnees');
                exit();
            }
            if (isset($_POST['moduleAction']) && ($_POST['moduleAction'] == 'delete')){
                $stmt = $this->db->prepare('DELETE FROM subscriptions WHERE subscriptions.users_Id = ? AND subscriptions.channels_Id = ?');
                $stmt->execute([$userId, $channelId]);
                header('location: /abonnees');
                exit();
            }
        }

        public function delete ($id) {
            $stmt = $this->db->prepare('SELECT * FROM subscriptions WHERE accepted = ? AND users_Id = ? AND channels_Id = ?');
            $stmt->execute([1, $this->user, $id]);
            $userAssociative = $stmt->fetchAssociative();

            if ($userAssociative) {
                $stmt = $this->db->prepare('DELETE FROM subscriptions WHERE channels_Id = ? AND users_Id = ?');
                $stmt->execute([$id, $this->user]);
                header('location:/kanaal');
                exit();
            }
            header('location:/kanaal');
            exit();
        }


    }