<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Flash;
use \App\Auth;
use \App\Messages;

/**
 * Finance abstract model
 *
 * PHP version 7.0
 */
abstract class Finance extends \Core\Model
{
    /**
     * Class config - You need to override it in child class!
     *
     * @var string
     */
    static protected $financeCategoryAsignedToUserTableName;
    protected $financeTableName; 

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Get all the finance categories data
     *
     * @return mixed Finance object if found, false otherwise
     */
    public static function getCategories()
    {
        $sql = "SELECT *
                FROM ".static::$financeCategoryAsignedToUserTableName.
               " WHERE user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetchAll();
    } 

    /**
     * Get finance data by id, if expense == true get also limit value
     *
     * @return mixed Finance object if found, false otherwise
     */
    public static function getCategoryById($id)
    {
        $sql = "SELECT *
        FROM ".static::$financeCategoryAsignedToUserTableName.
        " WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    } 

    /**
     * Add finance category
     *
     * @return mixed inserted row id if category added, false otherwise
     */
    public static function addCategory($name, $limit = 0, $categoryLimitState = false)
    {
        if(true) 
		{
            $sql = "INSERT INTO ".static::$financeCategoryAsignedToUserTableName.
            "(user_id, name"; 

            if($limit || $categoryLimitState) {
                $sql .= ", expense_limit, limit_active";
            } 

            $sql .= ") VALUES (:user_id, :name";

            if($limit || $categoryLimitState) {
                $sql .= ", :expense_limit, :limit_active";
            } 

            $sql .= ")";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR);     

            if($limit || $categoryLimitState) {
                $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':limit_active', $categoryLimitState, PDO::PARAM_BOOL);
            }

            if($stmt->execute()) {
                return $db->lastInsertId();
            }
        }
        return false;
    } 
	
	public function addIncomeCategory($name, $categoryType)
    {
        //Validate name
		$this->validateCategory($name);

		if(empty($this->errors))
		{
            $sql = "INSERT INTO incomes_category_assigned_to_users
            (user_id, name) VALUES (:user_id, :name)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR);     

            if($stmt->execute()) {
                return $db->lastInsertId();
            }
        }
        return false;
    } 
	
	public function addExpenseCategory($name, $categoryType, $limit = 0, $categoryLimitState = false)
    {
        //Validate name
		$this->validateCategory($name);

		if(empty($this->errors))
		{
            $sql = "INSERT INTO expenses_category_assigned_to_users (user_id, name"; 

            if($limit || $categoryLimitState) {
                $sql .= ", expense_limit, limit_active";
            } 

            $sql .= ") VALUES (:user_id, :name";

            if($limit || $categoryLimitState) {
                $sql .= ", :expense_limit, :limit_active";
            } 

            $sql .= ")";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR);     
			
			if($limit || $categoryLimitState) {
                $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':limit_active', $categoryLimitState, PDO::PARAM_BOOL);
            }

            if($stmt->execute()) {
                return $db->lastInsertId();
            }
        }
        return false;
    } 
	
	public function addPaymentCategory($name, $categoryType)
    {
        //Validate name
		$this->validateCategory($name);

		if(empty($this->errors))
		{

            $sql = "INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR);     

            if($stmt->execute()) {
                return $db->lastInsertId();
            }
        }
        return false;
    } 
	
	public function validateCategory($name)
    {
        $categories = $this -> getUserCategories();

        foreach ($categories as $category)
        {
            if (strtolower($category['name']) == strtolower($name)) {
                $this -> errors = 'Istnieje już taka kategoria';
				echo "<meta http-equiv=\"refresh\" content=\"0;URL=options.html\">";
				Flash::addMessage('Istnieje już taka kategoria', Flash::WARNING);
            }
        }
        if (mb_strlen($name) > 20) {
            $this->errors[] = 'Nazwa może zawierać maksymalnie 20 znaków.';
			Flash::addMessage('Nazwa może zawierać maksymalnie 20 znaków.', Flash::WARNING);
        } else if (mb_strlen($name) < 3) {
            $this->errors[] = 'Nazwa musi zawierać przynajmniej 3 znaki.';
			Flash::addMessage('Nazwa musi zawierać przynajmniej 3 znaki.', Flash::WARNING);
        }
        if (preg_match('/^[a-z ąęćżźńłóś]+$/i', $name) == 0) {
            $this->errors[] = 'Nazwa może zawierać tylko litery i spacje.';
			Flash::addMessage('Nazwa może zawierać tylko litery i spacje.', Flash::WARNING);
        }
    }
	
    public static function getUserCategories() {

        $db = static::getDB();

        $query = $db->prepare('SELECT id, name FROM '.static::$financeCategoryAsignedToUserTableName.' WHERE user_id = :user_id');
        $query->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }
	
	public static function getUserIncomeCategories() 
	{
        $db = static::getDB();

        $query = $db->prepare('SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id');
        $query->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }
	
	public static function getUserExpenseCategories() 
	{
        $db = static::getDB();

        $query = $db->prepare('SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id');
        $query->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }
	 
	public static function getUserPaymentCategories() 
	{
        $db = static::getDB();

        $query = $db->prepare('SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id');
        $query->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }

    /**
     * Delete finance category by id
     *
     * @return boolean true if category deleted, false otherwise
     */
    public static function deleteIncomeCategoryById($id)
    {
		$sql_default = 'SELECT id FROM incomes_category_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$name = 'Inne';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_category_id = $db->prepare($sql_default);
        $query_check_category_id->bindValue(':name', $name, PDO::PARAM_STR);
        $query_check_category_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$query_check_category_id->execute();
		
		$result = $query_check_category_id->fetch();
		
		$income_id = (int) $result[0];
		
		if($income_id == $id)
		{		
			Flash::addMessage('Nie możesz usunąć kategorii "Inne"', Flash::WARNING);
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=options.html\">";
			//header('Location: options');
			return false;
		}
		else
		{
        $sql = "DELETE FROM incomes_category_assigned_to_users WHERE id = :id AND user_id = :user_id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        return $stmt->execute();
		}
    } 
	
	public static function deleteExpenseCategoryById($id)
    {
		$sql_default = 'SELECT id FROM expenses_category_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$name = 'Inne wydatki';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_category_id = $db->prepare($sql_default);
        $query_check_category_id->bindValue(':name', $name, PDO::PARAM_STR);
        $query_check_category_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$query_check_category_id->execute();
		
		$result = $query_check_category_id->fetch();
		
		$expense_id = (int) $result[0];
		
		if($expense_id == $id)
		{
			Flash::addMessage('Nie możesz usunąć kategorii "Inne Wydatki"', Flash::WARNING);
			//$this->redirect(Auth::getReturnToPage());
			return false;
		}
		else
		{
        $sql = "DELETE FROM expenses_category_assigned_to_users WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        return $stmt->execute();
		}
    } 
	
	 public static function deletePaymentMethodById($id)
    {
		$sql_default = 'SELECT id FROM payment_methods_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$name = 'Gotówka';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_category_id = $db->prepare($sql_default);
        $query_check_category_id->bindValue(':name', $name, PDO::PARAM_STR);
        $query_check_category_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $query_check_category_id->execute();
		
		$result = $query_check_category_id->fetch();
		
		$payment_id = (int) $result[0];
		
		if($payment_id == $id)
		{
			Flash::addMessage('Nie możesz usunąć kategorii "Gotówka"', Flash::WARNING);
			return false;
		}
		else
		{
        $sql = "DELETE FROM payment_methods_assigned_to_users WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        return $stmt->execute();
		}
    }

    /**
     * Update finance category by id
     *
     * @return boolean true if category updated, false otherwise
     */
    public static function updateIncomeCategoryById($name, $id)
    {
		$sql_default = 'SELECT id FROM incomes_category_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$default_name = 'Inne';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_category_id = $db->prepare($sql_default);
        $query_check_category_id->bindValue(':name', $default_name, PDO::PARAM_STR);
        $query_check_category_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$query_check_category_id->execute();
		
		$result = $query_check_category_id->fetch();
		
		$income_id = (int) $result[0];
		
		if($income_id == $id)
		{		
			Flash::addMessage('Nie możesz edytować kategorii "Inne"', Flash::WARNING);
			//header('Location: options');
			return false;
		}
		else
		{
        $sql = "UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        return $stmt->execute();
		}
    } 
	
	public static function updateExpenseCategoryById($name, $id, $limit = 0, $categoryLimitState = false)
    {
		$sql_default = 'SELECT id FROM expenses_category_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$default_name = 'Inne wydatki';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_category_id = $db->prepare($sql_default);
        $query_check_category_id->bindValue(':name', $default_name, PDO::PARAM_STR);
        $query_check_category_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		
		$query_check_category_id->execute();
		
		$result = $query_check_category_id->fetch();
		
		$expense_id = (int) $result[0];
		
		if($expense_id == $id)
		{
			Flash::addMessage('Nie możesz edytować kategorii "Inne Wydatki"', Flash::WARNING);
			//$this->redirect(Auth::getReturnToPage());
			return false;
		}
		else
		{
        $sql = "UPDATE expenses_category_assigned_to_users SET name = :name";
                
        if($limit || $categoryLimitState) {
            $sql .=", expense_limit = :expense_limit, limit_active = :limit_active";
        }

        $sql .= " WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        
        if($limit || $categoryLimitState) {
            $stmt->bindValue(':expense_limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':limit_active', $categoryLimitState, PDO::PARAM_BOOL);
        }

        return $stmt->execute();
		}
    } 
	
	public static function updatePaymentMethodById($default_name, $id)
    {
		$sql_default = 'SELECT id FROM payment_methods_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$default_name = 'Gotówka';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_category_id = $db->prepare($sql_default);
        $query_check_category_id->bindValue(':name', $default_name, PDO::PARAM_STR);
        $query_check_category_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $query_check_category_id->execute();
		
		$result = $query_check_category_id->fetch();
		
		$payment_id = (int) $result[0];
		
		if($payment_id == $id)
		{
			Flash::addMessage('Nie możesz edytować kategorii "Gotówka"', Flash::WARNING);
			return false;
		}
		else
		{
        $sql = "UPDATE payment_methods_assigned_to_users SET name = :name WHERE id = :id AND user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', htmlspecialchars($name), PDO::PARAM_STR); 
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);     
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        return $stmt->execute();
		}
    } 

    /**
     * Get all the finance categories id's
     *
     * @return mixed id array if found, false otherwise
     */
    protected function getCategoriesIds()
    {
        $sql = "SELECT id
                FROM ".static::$financeCategoryAsignedToUserTableName.
               " WHERE user_id = :user_id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');
    } 

    /**
     * Validate given date
     *
     * @return boolean  True if the date is correct, false otherwise
     */
    private function validateDate($date)
    {
		$var = $date;
        $this->dateInput = implode("-", array_reverse(explode("/", $var)));
		return $this->dateInput;
    }
	
	/*private function validateDate($date)
    {
        $date1 = strtr($date, '/', '-');
		$date = date('Y-m-d', strtotime($date1));
		$this->dateInput = $date;
		return $this->dateInput;
    }*/
}
