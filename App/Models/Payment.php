<?php

namespace App\Models;

use PDO;
use \App\Messages;
use \App\Models\Finance;
use \App\Controllers\Settings;

/**
 * Payment model
 *
 * PHP version 7.0
 */
class Payment extends Finance
{
    static $financeCategoryAsignedToUserTableName = 'payment_methods_assigned_to_users';
	static $financeCategoryDefaultTableName = 'payment_methods_default';
    protected $financeTableName = null; 

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return boolean True if validayion successful, false otherwise
     */
    public function validate()
    {
        //Check if payment method is set
        if(isset($this->methodId)) {
            //Check if given payment method is associated with current user
            if(!in_array($this->methodId, static::getCategoriesIds())) {
                $this->errors[] = Messages::METHOD_INVALID; 
            }
        } else {
            $this->errors[] = Messages::METHOD_REQUIRED; 
        }
        
        if(empty($this->errors)) {
            return true;
        }

        return false;
    }
	
	public static function updateDeletedPaymentMethod($categoryId)
	{		
		$sql_default = 'SELECT id FROM payment_methods_assigned_to_users WHERE name = :name AND user_id = :user_id';
		
		$name = 'Gotówka';
		$user_id = $_SESSION['user_id'];
		
		$db = static::getDB();
        $query_check_method_id = $db->prepare($sql_default);
        $query_check_method_id->bindValue(':name', $name, PDO::PARAM_STR);
        $query_check_method_id->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $query_check_method_id->execute();
		
		$result = $query_check_method_id->fetch();
		
		$payment_id = (int) $result[0];
		
		$sql_payment_method_id = 'UPDATE expenses SET payment_method_assigned_to_user_id = :new_id WHERE user_id = :user_id AND payment_method_assigned_to_user_id = :id';
		
		$query_deleted_payment_method = $db->prepare($sql_payment_method_id);
		$query_deleted_payment_method->bindValue(':user_id', $user_id, PDO::PARAM_STR);
		$query_deleted_payment_method->bindValue(':new_id', $payment_id, PDO::PARAM_INT);
		$query_deleted_payment_method->bindValue(':id', $categoryId, PDO::PARAM_INT);

        return $query_deleted_payment_method->execute();		
	}

    /**
     * Get method id
     *
     * @return mixed payment method id if it is set, false otherwise
     */
    public function getMethodId() {

        if(isset($this->methodId)) {
            return $this->methodId;
        }
        
        return false;
    }

    
}