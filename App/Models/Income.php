<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Models\User;
use \App\Models\Finance;
use \App\Controllers\Settings;

/**
 * Items model
 *
 * PHP version 7.0
 */
class Income extends Finance
{
	static $financeCategoryAsignedToUserTableName = 'incomes_category_assigned_to_users';
	static $financeCategoryDefaultTableName = 'incomes_category_default';
    protected $financeTableName = 'incomes'; 

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
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function addIncome()
    {
        $this->validateIncome();

        if (empty($this->errors)) {

			//var_dump($income_id);
			$income_id = $this->categoryId;
					
			$sql_income = "INSERT INTO incomes VALUES (NULL, :user_id, :income_id, :income_amount, :income_date, :income_comment)";
		
			$db = static::getDB();
			$query_income = $db->prepare($sql_income);
			$query_income->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$query_income->bindValue(':income_id', $income_id, PDO::PARAM_STR);
			$query_income->bindValue(':income_amount', $this->income_amount, PDO::PARAM_STR);
			$query_income->bindValue(':income_date', $this->income_date, PDO::PARAM_STR);
			$query_income->bindValue(':income_comment', $this->income_comment, PDO::PARAM_STR);

            return $query_income->execute();
        }

        return false;
    }
	
	public static function updateDeletedIncomesCategory($categoryId)
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
		
		$sql_income_category_id = 'UPDATE incomes SET income_category_assigned_to_user_id = :new_id WHERE user_id = :user_id AND income_category_assigned_to_user_id = :id';
		
		$query_deleted_income_category = $db->prepare($sql_income_category_id);
		$query_deleted_income_category->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query_deleted_income_category->bindValue(':new_id', $income_id, PDO::PARAM_INT);
		$query_deleted_income_category->bindValue(':id', $categoryId, PDO::PARAM_INT);

        return $query_deleted_income_category->execute();		
	}
	
	/*public function addIncomeCategory($name)
    {
        //$finance = new Finance($_POST);

        //$finance ->validateCategory($name);
		$this->validateIncomeCategory($name);

		if(empty($this->errors))
		{
            $sql = "INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)";

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
	
	public function validateIncomeCategory($name)
    {
        $categories = $this -> getUserIncomeCategories();

        foreach ($categories as $category)
        {
            if (strtolower($category['name']) == strtolower($name)) {
                $this -> errors = 'Istnieje już taka kategoria';
            }
        }
        if (mb_strlen($name) > 20) {
            $this->errors[] = 'Nazwa może zawierać maksymalnie 20 znaków.';
        } else if (mb_strlen($name) < 3) {
            $this->errors[] = 'Nazwa musi zawierać przynajmniej 3 znaki.';
        }
        if (preg_match('/^[a-z ąęćżźńłóś]+$/i', $name) == 0) {
            $this->errors[] = 'Nazwa może zawierać tylko litery i spacje.';
        }
    }

    public static function getUserIncomeCategories() {

        $user_id =  $_SESSION['user_id'];

        $db = static::getDB();

        $query = $db->prepare("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id");
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll();
    }*/
	
	public function validateIncome()
    {
        // Login
        if (isset($this->income_amount))
		{
			$wszystko_OK=true;
			
			$income_amount = $this->income_amount;
			
			if($this->income_amount<0)
			{
				$wszystko_OK=false;
				$this->errors[] ="Wartość przychodu musi być większa od 0";
			}
			
			if(!is_numeric($this->income_amount))
			{
				$wszystko_OK=false;
				$this->errors[] ="Musisz podać wartość będącą liczbą arabską";
			}
			$income_amount = str_replace(',','.',$income_amount);
			
			$income_date = filter_input(INPUT_POST, $this->income_date);
			//$this->categoryId = filter_input(INPUT_POST, $this->categoryId, FILTER_SANITIZE_STRING);
			
			$income_comment = $this->income_comment;
			if (strlen($this->income_comment)>30)
			{
				$wszystko_OK=false;
				$this->errors[] ="Komentarz może zawierać maksymalnie 30 znaków";
			}
			if($this->income_comment = "")
			{
				$this->income_comment = str_replace(' ','-',$income_comment );
			}

		}
		else if($this->income_amount = "")
		{
			$this->errors[] ="Musisz podać wartość przychodu";
		}
	}

}