<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Models\User;
use \App\Models\Finance;
use \App\Models\Payment;
use \App\Controllers\Settings;

/**
 * Items model
 *
 * PHP version 7.0
 */
class Expense extends Finance
{
	static $financeCategoryAsignedToUserTableName = 'expenses_category_assigned_to_users';
	static $financeCategoryDefaultTableName = 'expenses_category_default';
    protected $financeTableName = 'expenses';

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
    public function addExpense()
    {
        $this->validateExpense();

        if (empty($this->errors)) {

			//var_dump($expense_id);
			$expense_id = $this->categoryId;
			
			//var_dump($payment_method_id);
			$payment_method_id = $this->methodId;
					
			$sql_expense = "INSERT INTO expenses VALUES (NULL, :user_id, :expense_id, :payment_method_id, :expense_amount, :expense_date, :expense_comment)";
		
			$db = static::getDB();
			$query_expense = $db->prepare($sql_expense);
			$query_expense->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
			$query_expense->bindValue(':expense_id', $expense_id, PDO::PARAM_STR);
			$query_expense->bindValue(':expense_amount', $this->expense_amount, PDO::PARAM_STR);
			$query_expense->bindValue(':expense_date', $this->expense_date, PDO::PARAM_STR);
			$query_expense->bindValue(':payment_method_id', $payment_method_id, PDO::PARAM_STR);
			$query_expense->bindValue(':expense_comment', $this->expense_comment, PDO::PARAM_STR);

            return $query_expense->execute();
        }

        return false;
    }
	
	public static function getCategoryCurrentMonthSumById($id)
    {
        $db = static::getDB();

        $startDate = date('Y-m-01');
        $endDate = date("Y-m-t");

        $sql = 'SELECT SUM(amount) AS categorySum
            FROM expenses AS e, expenses_category_assigned_to_users AS ecu
            WHERE e.expense_category_assigned_to_user_id = ecu.id AND ecu.id = :categoryId
            AND e.user_id = :user_id AND date_of_expense BETWEEN :start_date AND :end_date';
        
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':categoryId', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $endDate, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchColumn();

        if(is_null($result)) {
            return 0;
        }
        
        return $result;
    } 
	
	public static function updateDeletedExpensesCategory($categoryId)
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
		
		$sql_income_category_id = 'UPDATE expenses SET expense_category_assigned_to_user_id = :new_id WHERE user_id = :user_id AND expense_category_assigned_to_user_id = :id';
		
		$query_deleted_expense_category = $db->prepare($sql_income_category_id);
		$query_deleted_expense_category->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$query_deleted_expense_category->bindValue(':new_id', $expense_id, PDO::PARAM_INT);
		$query_deleted_expense_category->bindValue(':id', $categoryId, PDO::PARAM_INT);

        return $query_deleted_expense_category->execute();		
	}

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validateExpense()
    {
        // Login
        if (isset($this->expense_amount))
		{
			$wszystko_OK=true;
		
			if($this->expense_amount<0)
			{
				$wszystko_OK=false;
				$this->errors[] ="Wartość wydatku musi być większa od 0";
			}
			if(!is_numeric($this->expense_amount))
			{
				$wszystko_OK=false;
				$this->errors[] ="Musisz podać wartość będącą liczbą arabską";
			}
			$expense_amount = str_replace(',','.',$this->expense_amount);
			
			$this->expense_date = filter_input(INPUT_POST, 'expense_date');
		
			if (strlen($this->categoryId)==0)
			{
				$wszystko_OK=false;
				$this->errors[] ="Należy wybrać kategorię wydatku";
			}
			
			$this->expense_comment = $_POST['expense_comment'];
			if (strlen($this->expense_comment)>30)
			{
				$wszystko_OK=false;
				$this->errors[] ="Komentarz może zawierać maksymalnie 30 znaków";
			}
		}
		else
		{
			$this->errors[] ="Musisz podać wartość wydatku";
		}
	}
}