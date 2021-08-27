<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Models\User;
use \App\Models\Settings;

/**
 * Items model
 *
 * PHP version 7.0
 */
class Options extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
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
     * User ID
     * @var int
     */
    public $user_id;
	
	 public function getUser()
    {
		$user_id = User::findByID($this->user_id);
		
        return $user_id;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
        public static function showIncomesCategories($user_id)
    {
        $sql_incomes_categories = "SELECT incomes_category_assigned_to_users.name as name, incomes_category_assigned_to_users.id as id FROM incomes_category_assigned_to_users WHERE incomes_category_assigned_to_users.user_id = :user_id";
								
		$db = static::getDB();
		$query_select_incomes_categories = $db->prepare($sql_incomes_categories);
		$query_select_incomes_categories->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query_select_incomes_categories->execute();

        return $query_select_incomes_categories->fetchAll();

    }
	
	      public static function showExpensesCategories($user_id)
    {
        $sql_expenses_categories = "SELECT expenses_category_assigned_to_users.name as name, expenses_category_assigned_to_users.id as id, expenses_category_assigned_to_users.expense_limit as expense_limit, expenses_category_assigned_to_users.limit_active as limit_active FROM expenses_category_assigned_to_users WHERE expenses_category_assigned_to_users.user_id = :user_id";
								
		$db = static::getDB();
		$query_select_expenses_categories = $db->prepare($sql_expenses_categories);
		$query_select_expenses_categories->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query_select_expenses_categories->execute();

        return $query_select_expenses_categories->fetchAll();

    }
	
	  public static function showPaymentMethods($user_id)
    {
        $sql_payment_methods = "SELECT payment_methods_assigned_to_users.name as name, payment_methods_assigned_to_users.id as id FROM payment_methods_assigned_to_users WHERE payment_methods_assigned_to_users.user_id = :user_id";
								
		$db = static::getDB();
		$query_select_payment_methods = $db->prepare($sql_payment_methods);
		$query_select_payment_methods->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query_select_payment_methods->execute();

        return $query_select_payment_methods->fetchAll();

    }
	
	  public static function deleteIncomesAndExpenses($user_id)
    {
        $sql_delete_items = "DELETE FROM incomes WHERE user_id = :user_id; DELETE FROM expenses WHERE user_id = :user_id";
		
		$db = static::getDB();
		$query_delete_items = $db->prepare($sql_delete_items);
		$query_delete_items->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        
		return $query_delete_items->execute();

    }
	
	public function validateNewCategory()
    {
        // Login
       if ($this->name == '') {
            $this->errors[] = 'Nowa kategoria musi mieć jakąs nazwę';
        }
		
		if (static::CategoryExists($this->name, $this->user_id ?? null)) {
            $this->errors[] = 'Istnieje już kategoria o takiej nazwie';
        }
		
		if ((strlen($this->name)<3) || (strlen($this->name)>20))
		{
			$wszystko_OK=false;
			$this->errors[] ="Nazwa kategorii musi posiadać od 3 do 20 znaków!";
		}
		
		if (ctype_alnum($this->name)==false)
		{
			$wszystko_OK=false;
			$this->errors[] ="Nazwa kategorii może składać się tylko z liter i cyfr (bez polskich znaków)";
		}
		

	}
	
	 public static function categoryExists($categoryName, $ignore_id = null)
    {
        $category = static::findByCategory($categoryName);

        if ($category) {
            if ($category->id != $ignore_id) {
                return true;
            }
        }

        return false;
    }

    public static function findByCategory($categoryName)
    {
        $sql_check_category_id = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql_check_category_id);
        $stmt->bindValue(':name', htmlspecialchars($categoryName), PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	/*public function editIncomeCategory()
    {
        $this->validateIncomeCategory();

        if (empty($this->errors)) {
			
			$sql_check_category_id = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :selected_income_category';

			$db = static::getDB();
			$query_check_category_id = $db->prepare($sql_check_category_id);
			$query_check_category_id->bindValue(':selected_category', $selected_income_category, PDO::PARAM_STR);
			$query_check_category_id->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);

			$query_check_category_id->setFetchMode(PDO::FETCH_CLASS, get_called_class());

			$query_check_category_id->execute();
			
			$result = $query_check_category_id->fetch();
			//var_dump($income_id);
			$income_id = $result[0];
					
			$sql_income_category = $sql = 'UPDATE incomes_category_assigned_to_users
                    SET name = :name';;
		
			$db = static::getDB();
			$query_income_category = $db->prepare($sql_income_category);
			$query_income_category->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$query_income_category->bindValue(':name', $this->new_name, PDO::PARAM_STR);

            return $query_income_category->execute();
        }

        return false;
    }*/

}

