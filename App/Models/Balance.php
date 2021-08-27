<?php


namespace App\Models;

use Core\View;
use PDO;
use DateTime;

/**
 * Balance model
 *
 * PHP version 7.4
 */
class Balance extends \Core\Model
{

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	    public static function showIncomes($firstDay, $lastDay)
    {
        $user_id =  $_SESSION['user_id'];
        $firstDate = $firstDay;
        $lastDate = $lastDay;

        $db = static::getDB();

        $sql_balance_incomes = "SELECT category_incomes.name as Category, 
                                SUM(incomes.amount) as Amount, incomes.date_of_income as Date, incomes.income_comment as Comment FROM incomes INNER JOIN 
                                incomes_category_assigned_to_users as category_incomes WHERE 
                                incomes.income_category_assigned_to_user_id = category_incomes.id AND 
                                incomes.user_id= :user_id AND incomes.date_of_income BETWEEN :first_date AND :second_date GROUP BY Category ORDER BY Amount DESC";
        $query_select_incomes_sum = $db->prepare($sql_balance_incomes);
        $query_select_incomes_sum->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query_select_incomes_sum->bindValue(':first_date', $firstDate, PDO::PARAM_STR);
        $query_select_incomes_sum->bindValue(':second_date', $lastDate, PDO::PARAM_STR);
        $query_select_incomes_sum->execute();

        return $query_select_incomes_sum->fetchAll();

    }

    public static function showExpenses($firstDay, $lastDay)
    {
        $user_id =  $_SESSION['user_id'];
        $firstDate = $firstDay;
        $lastDate = $lastDay;

        $db = static::getDB();

        $sql_balance_expenses = "SELECT category_expenses.name as Category, 
                                 SUM(expenses.amount) as Amount, expenses.date_of_expense as Date, expenses.expense_comment as Comment FROM expenses INNER JOIN expenses_category_assigned_to_users as category_expenses WHERE 
                                 expenses.expense_category_assigned_to_user_id = category_expenses.id AND
                                 expenses.user_id= :user_id AND expenses.date_of_expense BETWEEN :first_date AND :second_date GROUP BY Category ORDER BY Amount DESC";
        $query_select_expenses_sum = $db->prepare($sql_balance_expenses);
        $query_select_expenses_sum->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query_select_expenses_sum->bindValue(':first_date', $firstDate, PDO::PARAM_STR);
        $query_select_expenses_sum->bindValue(':second_date', $lastDate, PDO::PARAM_STR);
        $query_select_expenses_sum->execute();

        return $query_select_expenses_sum->fetchAll();
    }
	
	public static function isDatabaseEmpty()
	{
		$user_id =  $_SESSION['user_id'];
		
		 $db = static::getDB();
		 
		 $sql_balance_expenses = "SELECT category_expenses.name as Category, 
						 SUM(expenses.amount) as Amount, expenses.date_of_expense as Date, expenses.expense_comment as Comment FROM expenses INNER JOIN expenses_category_assigned_to_users as category_expenses WHERE 
						 expenses.expense_category_assigned_to_user_id = category_expenses.id AND
						 expenses.user_id= :user_id AND expenses.date_of_expense BETWEEN :first_date AND :second_date GROUP BY Category ORDER BY Amount DESC";
        $query_select_expenses_sum = $db->prepare($sql_balance_expenses);
        $query_select_expenses_sum->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query_select_expenses_sum->bindValue(':first_date', $firstDate, PDO::PARAM_STR);
        $query_select_expenses_sum->bindValue(':second_date', $lastDate, PDO::PARAM_STR);
        $query_select_expenses_sum->execute();

        return $query_select_expenses_sum->fetchAll();
	}

}
