var categoryId = 0;
var currentExpenseLimit = 0;
var currentMonthSum = 0;
var balance = 0;
var valueInput = 0;
var spentAndValue = 0;

const currency = ' zł';
const positiveMessage = 'Możesz jeszcze wydać: ';
const negativeMessage = 'Uwaga, przekroczyłeś limit!';

function radioClickHandler(clickedRadio) {
    categoryId = clickedRadio.value;

    $('#limitSection').slideDown('slow');
    getLimitData();
    getCategoryCurrentMonthSumById();
}

//Hides limit section
function hideLimitSection() {
    $('#limitSection').slideUp('slow');
}

function optionChangeHandler(selectedOption) {
    categoryId = selectedOption.value;

    $('#limitSection').slideDown('slow');
    getLimitData();
    getCategoryCurrentMonthSumById();
}

//Hides limit section
function hideLimitSection() {
    $('#limitSection').slideUp('slow');
}

//AJAX
//
//Get category limit from db
function getLimitData() {
    $.ajax({
        type: 'POST',
        url: '/items/get-limit-data',
        dataType: 'json',
        data: {
            postCategoryId: categoryId
        },

        success: function(result) {
            setCurrentExpenseLimit(result.expense_limit);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}

//AJAX
//
//Get category expense Sum from current month from db
function getCategoryCurrentMonthSumById() {
    $.ajax({
        type: 'POST',
        url: '/items/get-category-current-month-sum-by-id',
        dataType: 'json',
        data: {
            postCategoryId: categoryId
        },

        success: function(result) {
            setCurrentMonthSum(result);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}

//Get data from value input, calculate spendAndValue & set in in label
function updateInput(valueInputBox = 0) {
    if(valueInputBox) {
        valueInput = parseFloat(valueInputBox.value);
    }

    spentAndValue = currentMonthSum + valueInput;
    $('#spentAndValueInput').text(spentAndValue.toFixed(2));
    setLimitSectionStyle();
}

//Set currentExpenseLimit value and set it in label
function setCurrentExpenseLimit(value) {
    currentExpenseLimit = parseFloat(value);
    $("#limit").text(currentExpenseLimit.toFixed(2));
}

//Set currentMonthSum value and set it in label
function setCurrentMonthSum(value) {
    currentMonthSum = parseFloat(value);
    $("#spent").text(currentMonthSum.toFixed(2));
}

//Calculate balance & set it in label
function setBalance() {
    balance = currentExpenseLimit - currentMonthSum;
    $("#balance").text(balance.toFixed(2));
}

//Change limit section color & set message depending on limit & value balance
function setLimitSectionStyle() {
    if(currentExpenseLimit >= spentAndValue) {
        $('#limitSection').removeClass('alert-danger').addClass('alert-success');
        $("#limitMessage").text(positiveMessage + balance.toFixed(2) + currency);
    } else {
        $('#limitSection').removeClass('alert-success').addClass('alert-danger');
        $("#limitMessage").text(negativeMessage);
    }    
}

//Run after all ajax requests finished
$(document).ajaxStop(function() {
    setBalance();
    updateInput();
    setLimitSectionStyle();
});