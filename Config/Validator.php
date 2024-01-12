<?php

namespace Config;

use Config\Mongo\MongoDatabase;
use Config\Mysql\MysqlDatabase;

class Validator
{
  private $errors = [];
  private $fieldErrors = [];
  protected $db;
  protected $mongo;

  public function __construct()
  {
    // $this->db = new MysqlDatabase();
    // $this->mongo = new MongoDatabase();
    // $this->mongo->connect();
  }

  public function itIsNotArrayOrEmpty($value, $message, $field)
  {
    if (!is_array($value) || empty($value)) {
      $this->addError($field, $message);
    }
  }

  public function isEmpty($value, $message, $field)
  {
    if (empty($value) || trim($value) === '') {
      $this->addError($field, $message);
    }
  }

  public function isTrue($value, $message)
  {
    if ($value) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isRequired($value, $message)
  {
    if (empty($value) || strlen($value) <= 0) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function hasMinLen($value, $min, $message)
  {
    if (empty($value) || strlen($value) < $min) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function hasMaxLen($value, $max, $message)
  {
    if (empty($value) || strlen($value) > $max) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isFixedLen($value, $len, $message)
  {
    if (strlen($value) !== $len) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isEmail($value, $message)
  {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->errors[] = ['message' => $message];
      return false;
    }
    return true;
  }

  public function isExists($value, $field, $collectionName, $message)
  {
    try {
      $collection = $this->mongo->getCollection($collectionName);
      $document = $this->mongo->query($collection, [$field => $value]);

      if ($document) {
        $this->errors[] = ['message' => $message];
      }
    } catch (\Exception $e) {
      // Ocorreu um erro na consulta
      echo "Erro: " . $e->getMessage();
    }
  }

  public function isUnique($value, $field, $table, $message)
  {
    try {
      $result = $this->db->selectOne($table, $field, "$field = ?", [$value]);

      if ($result) {
        $this->errors[] = ['message' => $message];
      }
    } catch (\Exception $e) {
      // Ocorreu um erro na consulta
      echo "Erro: " . $e->getMessage();
    }
  }

  public function isUniqueAndStatus($email, $status, $table)
  {
    try {
      $result = $this->db->selectOne($table, 'status', 'email = ?', [$email]);

      if ($result && $result['status'] == $status) {
        return true;
      }
    } catch (\Exception $e) {
      // Ocorreu um erro na consulta
      echo "Erro: " . $e->getMessage();
    }

    return false;
  }

  public function comparePasswords($value1, $value2, $message)
  {
    if ($value1 !== $value2) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isSecurePassword($value, $message, $requiredMessage, $lengthMessage, $uppercaseMessage, $lowercaseMessage, $numberMessage, $specialMessage)
  {
    $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';

    if ($value !== null && !preg_match($pattern, $value)) {
      $error = [
        'message' => $message,
        'details' => []
      ];

      if (empty($value)) {
        $error['details']['required'] = $requiredMessage;
      } else {
        if (strlen($value) < 8) {
          $error['details']['length'] = $lengthMessage;
        }

        if (!preg_match('/[A-Z]/', $value)) {
          $error['details']['uppercase'] = $uppercaseMessage;
        }

        if (!preg_match('/[a-z]/', $value)) {
          $error['details']['lowercase'] = $lowercaseMessage;
        }

        if (!preg_match('/\d/', $value)) {
          $error['details']['number'] = $numberMessage;
        }

        if (!preg_match('/[@$!%*#?&]/', $value)) {
          $error['details']['special'] = $specialMessage;
        }

        unset($error['details']['required']);
      }

      $this->errors[] = $error;
    }
  }

  public function isValid()
  {
    return empty($this->errors);
  }

  public function isNumber($value, $message)
  {
    if (!is_numeric($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isInteger($value, $message)
  {
    if (!is_int($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isFloat($value, $message)
  {
    if (!is_float($value) || !is_finite($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isInRange($value, $min, $max, $message)
  {
    if ($value < $min || $value > $max) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isName($value, $message)
  {
    if (!preg_match('/^[A-Za-zÀ-ÿ]+$/u', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isTextWithoutNumbers($value, $message)
  {
    if (!preg_match('/^[a-zA-ZÀ-ÿ\s\']+$/u', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isURL($value, $message)
  {
    if (!filter_var($value, FILTER_VALIDATE_URL)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isAlpha($value, $message)
  {
    if (!ctype_alpha($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isAlphanumeric($value, $message)
  {
    if (!ctype_alnum($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isUpperCase($value, $message)
  {
    if ($value !== mb_strtoupper($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isLowerCase($value, $message)
  {
    if ($value !== mb_strtolower($value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isPhone($value, $message)
  {
    if (!preg_match('^\(+[0-9]{2,3}\)[0-9]{4}-[0-9]{4}$^', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isCellphone($value, $message)
  {
    if (!preg_match('^\(+[0-9]{2,3}\)[0-9]{5}-[0-9]{4}$^', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isCPF($value, $message)
  {
    if (!preg_match('^([0-9]){3}\.([0-9]){3}\.([0-9]){3}-([0-9]){2}$^', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isCNPJ($value, $message)
  {
    if (!preg_match('/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})-(\d{2})$/', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  public function isBrazilianReal($value, $message)
  {
    if (!preg_match('/^\d+(,\d{1,2})?$/', $value)) {
      $this->errors[] = ['message' => $message];
    }
  }

  private function addError($field, $message)
  {
    if (!isset($this->fieldErrors[$field])) {
      $this->fieldErrors[$field] = [];
    }

    $this->fieldErrors[$field][] = ['message' => $message];
  }

  public function errors()
  {
    $result = ['fields' => $this->errors];

    foreach ($this->fieldErrors as $field => $errors) {
      $result['fields'][$field] = array_column($errors, 'message');
    }

    return $result;
  }

  public function clear()
  {
    $this->errors = [];
  }
}
