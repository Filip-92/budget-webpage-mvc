<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Options;
use \App\Models\User;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Models\Payment;
use \App\Models\Finance;

/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Settings extends Authenticated
{
    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

	public function incomesAction()
    {
        $user_id =  $_SESSION['user_id'];

        $this->incomesCategories($user_id);
    }
	
	public function expensesAction()
    {
        $user_id =  $_SESSION['user_id'];

        $this->expensesCategories($user_id);
    }

    /**
     * Show the form for editing the profile
     *
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile
     *
     * @return void
     */
    public function updateAction()
    {
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany zostały zapisane');

            $this->redirect('/settings');

        } else {

            View::renderTemplate('settings/options.html', [
                'user' => $this->user
            ]);

        }
    }
	
	 public function deleteItemsAction()
    {
		$user_id = $_SESSION['user_id'];
		
        if (Options::deleteIncomesAndExpenses($user_id)) {

            Flash::addMessage('Wszystkie przychody i wydatki zostały trwale usunięte!');

            $this->redirect('/settings');

        } else {

            View::renderTemplate('settings/options.html', [
                'user' => $this->user
            ]);

        }
    }
	
	    /**
     * Delete account
     *
     * @return void
     */
    public function deleteAccountAction() {
        
		$user_id = $_SESSION['user_id'];
		
        if(User::deleteAccount($user_id)) {
            Auth::logout();
            $this->redirect('/account/delete_success');

        }
        else {
            Flash::AddMessage('Usuwanie konta nie powiodło się', Flash::ERROR);
            $this->redirect('/settings');
        }
    }
	
	public function indexAction() {
        View::renderTemplate('Settings/options.html', [
            'incomeCategories' => Income::getCategories(),
            'expenseCategories' => Expense::getCategories(),
            'paymentCategories' => Payment::getCategories(),
            'user' => $this->user
        ]);
    }

    /**
     * AJAX - get category name
     *
     * @return void
     */
    public function getCategoryDataAction() {
        
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
        }

        if(isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if($categoryId && $categoryType) {
            switch($categoryType) {
                case 'income':
                    $result = Income::getCategoryById($categoryId);
                    break;
    
                case 'expense':
                    $result = Expense::getCategoryById($categoryId);
                    break;
    
                case 'payment':
                    $result = Payment::getCategoryById($categoryId);
                    break;
    
                default:
                    $result = false;
                    break;
            }
    
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }
	
    /**
     * AJAX - Add category
     *
     * @return void
     */
    public function addCategoryAction() {
        
        if (isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if (isset($_POST['postCategoryName'])) {
            $categoryName = $_POST['postCategoryName'];
        }

        $categoryLimit = 0;
        if (isset($_POST['postCategoryLimit'])) {
            $categoryLimit = $_POST['postCategoryLimit'];
        }
        
        if (isset($_POST['postCategoryLimitState'])) {
            $categoryLimitState = filter_var($_POST['postCategoryLimitState'], FILTER_VALIDATE_BOOLEAN);
        }
		
		if($categoryType && $categoryName) 
		{
            switch($categoryType) {
                case 'income':
					$income = new Income($_POST);
					//$this->validateCategory($categoryName, $categoryType);
					$result = $income->addIncomeCategory($categoryName, $categoryType);
                    break;
    
                case 'expense':
					$expense = new Expense($_POST);
					//$this->validateCategory($categoryName, $categoryType);
					$result = $expense->addExpenseCategory($categoryName, $categoryType, $categoryLimit, $categoryLimitState);
                    break;
    
                case 'payment':
					$payment = new Payment($_POST);
					//$this->validateCategory($categoryName, $categoryType);
					$result = $payment->addPaymentCategory($categoryName, $categoryType);
                    break;
    
                default:
                    $result = false;
                    break;
            }
			
            header('Content-Type: application/json');
            echo json_encode($result);
        }
		return false;
    }
	
	/*public function validateCategoryAction()
    {
		$categoryType = 'income';
		
		$categoryName = $_COOKIE['categoryName'];
		
		if($categoryType && $categoryName) 
		{
            switch($categoryType) {
                case 'income':
					$is_valid = ! $this->validateNewIncomeCategory($categoryName, $_GET['ignore_id'] ?? null);
                    break;
    
                case 'expense':
					$is_valid = ! $this->validateNewExpenseCategory($categoryName, $_GET['ignore_id'] ?? null);
                    break;
    
                case 'payment':
					$is_valid = ! $this->validateNewPaymentCategory($categoryName, $_GET['ignore_id'] ?? null);
                    break;
    
                default:
                    $result = false;
                    break;
            }

			header('Content-Type: application/json');
			echo json_encode($is_valid);
		}
		return false;
	}*/
	
	public function validateCategoryAction()
    {
		$isEqual = ! $this->validateNewIncomeCategory($_GET['categoryName'], $_GET['ignore_id'] ?? null);

		header('Content-Type: application/json');
		echo json_encode($isEqual);
	}
	
	public function validateNewIncomeCategoryAction($name, $ignore_id = null)
	{
        $categories = Finance::getUserIncomeCategories();
		
		$name = 'Inne';

		if($categories)
		{
			$isEqual = true;
			foreach ($categories as $category)
			{
				if (strtolower($category['name']) == strtolower($name)) {
					$isEqual = false;
				}
			}
			return $isEqual;
		}
    }
	
	public function validateNewExpenseCategoryAction($name, $ignore_id = null)
	{
        $categories = Finance::getUserExpenseCategories();

        foreach ($categories as $category)
        {
            if (strtolower($category['name']) == strtolower($name)) {
                 return false;
            }
        }
		return true;
    }
	
	public function validateNewPaymentCategoryAction($name, $ignore_id = null)
	{
        $categories = Finance::getUserPaymentCategories();

        foreach ($categories as $category)
        {
            if (strtolower($category['name']) == strtolower($name)) {
                 return false;
            }
        }
		return true;
    }
	
    /**
     * AJAX - delete category
     *
     * @return void
     */
    public function deleteCategoryAction() {
        
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
        }

        if(isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if($categoryId && $categoryType) {
            switch($categoryType) {
                case 'income':
					$sql = Income::updateDeletedIncomesCategory($categoryId);
                    $result = Finance::deleteIncomeCategoryById($categoryId);
                    break;
    
                case 'expense':
					$sql = Expense::updateDeletedExpensesCategory($categoryId);
                    $result = Finance::deleteExpenseCategoryById($categoryId);
                    break;
    
                case 'payment':
					$sql = Payment::updateDeletedPaymentMethod($categoryId);
                    $result = Finance::deletePaymentMethodById($categoryId);
                    break;
    
                default:
                    $result = false;
                    break;
            }
    
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

    /**
     * AJAX - update category
     *
     * @return void
     */
    public function updateCategoryAction() {
        
        if(isset($_POST['postCategoryId'])) {
            $categoryId = $_POST['postCategoryId'];
        }

        if(isset($_POST['postCategoryType'])) {
            $categoryType = $_POST['postCategoryType'];
        }

        if(isset($_POST['postCategoryName'])) {
            $categoryName = $_POST['postCategoryName'];
        }

        $categoryLimit = 0;
        if (isset($_POST['postCategoryLimit'])) {
            $categoryLimit = $_POST['postCategoryLimit'];
        }
        
        if (isset($_POST['postCategoryLimitState'])) {
            $categoryLimitState = filter_var($_POST['postCategoryLimitState'], FILTER_VALIDATE_BOOLEAN);;
        }

        if($categoryId && $categoryType && $categoryName) {
            switch($categoryType) {
                case 'income':
                    $result = Finance::updateIncomeCategoryById($categoryName, $categoryId);
                    break;
    
                case 'expense':
                    $result = Finance::updateExpenseCategoryById($categoryName, $categoryId, $categoryLimit, $categoryLimitState);
                    break;
    
                case 'payment':
                    $result = Finance::updatePaymentMethodById($categoryName, $categoryId);
                    break;
    
                default:
                    $result = false;
                    break;
            }
    
            header('Content-Type: application/json');
            echo json_encode($result);
        }
    }

}
