<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Payment;
use \App\Models\Options;
use \App\Messages;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
class Items extends Authenticated
{

    /**
     * Items index
     *
     * @return void
     */
    public function addIncomeAction()
    {
		if (! Auth::isLoggedIn()) {
			
			Auth::rememberRequestedPage();
			
			$this->redirect('/login');
		}
		
		$user_id = $_SESSION['user_id'];
		
		$arguments = [];
		$arguments['active_income'] = 'active';
		$arguments['income_category'] = Options::showIncomesCategories($user_id);
		//$arguments['categories'] = Income::getCategories();
		
        View::renderTemplate('Items/addIncome.html', $arguments);
    }
	
	public function createIncomeAction()
    {
        $income = new Income($_POST);

        if ($income->addIncome()) {
			
			Flash::addMessage('Nowy przychód został dodany!');

            $this->redirect('/');

        } else {

            View::renderTemplate('Items/addIncome.html', [
                'income' => $income
            ]);
			
			$this->redirect('/income');

        }
    }
	
	public function addExpenseAction()
    {
		if (! Auth::isLoggedIn()) {
			
			Auth::rememberRequestedPage();
			
			$this->redirect('/login');
		}
		
		$user_id = $_SESSION['user_id'];
		
		$arguments = [];
		$arguments['active_expense'] = 'active';
		$arguments['expense_category'] = Options::showExpensesCategories($user_id);
		$arguments['payment_methods'] = Options::showPaymentMethods($user_id);
		
        View::renderTemplate('Items/addExpense.html', $arguments);
    }
	
	public function createExpenseAction()
    {
        $expense = new Expense($_POST);

        if ($expense->addExpense()) {
			
			Flash::addMessage('Nowy wydatek został dodany!');
			
            $this->redirect('/');

        } else {

            View::renderTemplate('Items/addExpense.html', [
                'expense' => $expense
            ]);

        }
    }
	
	/**
     * AJAX - get category limit data
     *
     * @return void
     */
    public function getLimitDataAction()
    {
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
            $result = Expense::getCategoryById($categoryId);
        } else {
            $result = false;
        }
        
        header('Content-Type: application/json');
        echo json_encode($result);
    }
	
	/**
     * AJAX - get sum of expenses in category
     *
     * @return void
     */
    public function getCategoryCurrentMonthSumByIdAction()
    {
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
            $result = Expense::getCategoryCurrentMonthSumById($categoryId);
        } else {
            $result = false;
        }
        
        header('Content-Type: application/json');
        echo $result;
    }
	
	 public static function getCurrentDate()
    {
        if (isset($_SESSION['user_id'])) {

            return date('Y-m-d');
		}
	}

}
