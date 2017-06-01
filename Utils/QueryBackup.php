<?php

namespace Littlerobinson\QueryBuilderBundle\Utils;


use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Yaml\Yaml;

/**
 * Class QueryBackup
 * Singleton pattern
 * @package Littlerobinson\QueryBuilderBundle\Utils
 */
class QueryBackup
{
    public  $pdo;
    private $user;
    private $association;

    /**
     * QueryBackup constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $user        = [];
        $association = [];
        $this->pdo   = null;
        $rootDir     = $container->get('kernel')->getRootDir();
        $dbName      = 'querybuilderdb';

        try {
            $dbName      = str_replace(' ', '', Yaml::parse(file_get_contents($rootDir . '/config/config.yml'))['littlerobinson_query_builder']['database']['file_name']);
            $user        = Yaml::parse(file_get_contents($rootDir . '/config/config.yml'))['littlerobinson_query_builder']['user'];
            $association = Yaml::parse(file_get_contents($rootDir . '/config/config.yml'))['littlerobinson_query_builder']['association'];
        } catch (\Exception $e) {
            echo "Impossible de récupérer les données user et association dans le fichier de configuration : " . $e->getMessage();
        }

        $dbPath = $rootDir . '/config/' . $dbName . '.sqlite';
        $this->setUser($user);
        $this->setAssociation($association);

        try {
            $this->pdo = new \PDO('sqlite:' . $dbPath);
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            echo "Impossible d'accéder à la base de données SQLite : " . $e->getMessage();
        }
    }

    /**
     * create database
     */
    public function createDatabase()
    {
        if (null === $this->pdo) {
            return;
        }
        $this->pdo->query("CREATE TABLE IF NOT EXISTS query (
                                  id      INTEGER PRIMARY KEY AUTOINCREMENT,
                                  title    VARCHAR(80),
                                  user    TEXT,
                                  association   VARCHAR(80),
                                  value   TEXT,
                                  modified DATETIME,
                                  created DATETIME
                                );");
    }

    /**
     * @return array|bool
     */
    public function getList()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || null === $this->pdo) {
            return false;
        }

        $association = $this->association ?? null;

        $sth = $this->pdo->prepare("SELECT * FROM query WHERE association = :association OR association IS NULL");
        $sth->bindValue(':association', $association);
        $sth->execute();
        $result = $sth->fetchAll();

        return json_encode($result);
    }

    /**
     * @return array|bool
     * @return array|bool
     */
    public function findOne()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || null === $this->pdo) {
            return false;
        }
        $queryId = $_POST['query_id'] ?? null;

        $sth = $this->pdo->prepare("SELECT * FROM query WHERE id = :query_id LIMIT 1");
        $sth->bindValue(':query_id', $queryId, \PDO::PARAM_INT);
        $sth->execute();

        $result = $sth->fetchObject();

        return json_encode($result);
    }

    /**
     * @return bool
     */
    public function insert()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || null === $this->pdo) {
            return false;
        }

        $title       = $_POST['title'] ?? null;
        $user        = $_POST['is_private'] ? $this->user : null;
        $association = $this->association ?? null;
        $value       = json_encode($_POST['query']) ?? null;
        $modified    = new \DateTime();
        $created     = new \DateTime();

        if (!$value) {
            return false;
        }

        $stmt = $this->pdo->prepare("INSERT INTO 
                                query 
                                (user, title, \"association\", value, modified, created)
                            VALUES
                                (:user, :title, :association, :value, :modified, :created)");

        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':user', $user);
        $stmt->bindValue(':association', $association);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':modified', $modified->format('Y-m-d H:i:s'));
        $stmt->bindValue(':created', $created->format('Y-m-d H:i:s'));

        return $stmt->execute();
    }

    /**
     * @return bool
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || null === $this->pdo) {
            return false;
        }
        $title       = $_POST['title'] ?? null;
        $user        = $_POST['user'] ?? null;
        $association = $_POST['association'] ?? null;
        $value       = json_encode($_POST['query']) ?? null;
        $modified    = new \DateTime();

        if (!$value) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE query 
                                        SET
                                    user = :user , title = :title, \"association\" = :association, value = :value, modified = :modified");

        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':user', $user);
        $stmt->bindValue(':association', $association);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':modified', $modified->format('Y-m-d H:i:s'));

        return $stmt->execute();
    }

    /**
     * @return bool
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || null === $this->pdo) {
            return false;
        }
        $queryId = $_POST['query_id'] ?? null;

        if (null === $queryId) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM query WHERE id = :query_id");
        $stmt->bindValue(':query_id', $queryId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * @param array $user
     */
    private function setUser(array $user)
    {
        if (in_array($user['name'], $_COOKIE)) {
            switch ($user['type']) {
                case 'cookie':
                    $this->user = !@unserialize($_COOKIE[$user['name']]) ? $_COOKIE[$user['name']] : unserialize($_COOKIE[$user['name']]);
                    break;
                case "session":
                    $this->user = !@unserialize($_SESSION[$user['name']]) ? $_SESSION[$user['name']] : unserialize($_SESSION[$user['name']]);
                    break;
                default:
                    $this->user = !@unserialize($_COOKIE[$user['name']]) ? $_COOKIE[$user['name']] : unserialize($_COOKIE[$user['name']]);
                    break;
            }
        }
    }

    /**
     * @param array $association
     */
    private function setAssociation(array $association)
    {
        if (in_array($association['name'], $_COOKIE)) {
            switch ($association['type']) {
                case 'cookie':
                    $this->association = !@unserialize($_COOKIE[$association['name']]) ? $_COOKIE[$association['name']] : unserialize($_COOKIE[$association['name']]);
                    break;
                case "session":
                    $this->association = !@unserialize($_SESSION[$association['name']]) ? $_SESSION[$association['name']] : unserialize($_SESSION[$association['name']]);
                    break;
                default:
                    $this->association = !@unserialize($_COOKIE[$association['name']]) ? $_COOKIE[$association['name']] : unserialize($_COOKIE[$association['name']]);
                    break;
            }
        }
    }
}
