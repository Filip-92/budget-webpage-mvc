const deleteModalText = "Czy na pewno chcesz usunąć kategorię \"";
const addCategoryModalTitle = "Dodaj nową kategorię";
const editCategoryModalTitle = "Edytuj kategorię";

let errorMessage;
var categoryId;
var categoryType;
var buttonType;

const categoryTemplate = ({ newCategoryName, newCategoryId, newCategoryType }) => `
    <div class="card-header row row1"	id="${newCategoryType}${newCategoryId}">
		<div class="col">
			<li>${newCategoryName}</li>
		</div>
		<div class="col-auto">
				<button class="btn btn-sm btn-danger p-0" onclick="getCategoryData('delete', \'${newCategoryType}\', ${newCategoryId})">
					<i class="icon-trash"></i>
				</button>			
				<button class="btn btn-sm btn-primary p-0" onclick="getCategoryData('edit', \'${newCategoryType}\', ${newCategoryId})">
					<i class="icon-pencil"></i>
				</button>
		</div>
	</div>
`;

//Add button handler
function addCategoryHandler(newCategoryType) {
    categoryType = newCategoryType;

    //Set proper modal title
    $('#editModalLabel').text(addCategoryModalTitle);
    //Reset category name & limit input
    $('#categoryName').val('');
    $('#limit').val(parseFloat(0).toFixed(2));
    $('#limitCheck').prop( "checked", false );
    //Set proper button function
    $('#editForm').attr('action', "javascript:addCategory()");

    switchLimitForm();

    $('#editModal').modal('show');
	errorMessage = document.querySelector("#error_message");
	errorMessage.innerText ="";
}

//Show limit form only for expense categories
function switchLimitForm() {
    if(categoryType == 'expense') {
        $('#limitForm').show();
    }
    else {
        $('#limitForm').hide();
    }
}

function showProperModal(result) {
    //Show proper modal
    switch (buttonType) {
        case 'edit':

            switchLimitForm();
            //Set proper modal title
            $('#editModalLabel').text(editCategoryModalTitle);
            //Set proper button function
            $('#editForm').attr('action', "javascript:updateCategory()");
            
            $('#categoryName').val(result.name);
            $('#limit').val(result.expense_limit);

            $('#limitCheck').prop( "checked", result.limit_active == 1 ? true : false);
            $('#editModal').modal('show');
            break;
        case 'delete':

            $('#deleteModal').modal('show');
            $('#deleteModalText').text(deleteModalText + result.name + "\"?"); //Replace and set the text back
            break;
    }
}

//Remove deleted category row from proper div & hide modal
function removeCategoryRow() {
    $('#deleteModal').modal('hide');
    $('#' + categoryType + categoryId).slideUp('medium', function() {this.remove();});
	//console.log($('#' + categoryType + categoryId));
}

//Append new category row to proper div & hide modal
function addCategoryRow(categoryName, returnedCategoryId) {
    $('#editModal').modal('hide');
    
    var currentCategoryRow = $([
        { newCategoryName: categoryName, newCategoryId: returnedCategoryId, newCategoryType: categoryType }
    ].map(categoryTemplate).join('')).appendTo('#'+categoryType+'CategoriesBody');

    currentCategoryRow.slideDown('slow');
}

//Update edited category row & hide modal
function updateCategoryRow(categoryName) {
    $('#editModal').modal('hide');
    
    $('#' + categoryType + categoryId).slideUp('medium', function() {

        $("li", this).text(categoryName);
        $(this).slideDown('medium');
    });

    
}

//AJAX
// Also edit & delete button action handler
//Get category data from db
function getCategoryData(clickedButtonType, clickedCategoryType, clickedCategoryId) {

    buttonType = clickedButtonType;
    categoryId = clickedCategoryId;
    categoryType = clickedCategoryType;

    $.ajax({
        type: 'POST',
        url: '/settings/getCategoryData',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },

        success: function(result) {
            showProperModal(result);
        },

        error: function(data){
            console.log(data);
        }
    });

}

//Delete selected category
function deleteCategory() {
    $.ajax({
        type: 'POST',
        url: '/settings/deleteCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType
        },
        success: removeCategoryRow(),

        error: function(xhr){
            console.log(xhr);
        }
    });
}

//Add new category to db
function addCategory() {

    categoryName = $('#categoryName').val();
    categoryLimit = $('#limit').val();
    categoryLimitState = $('#limitCheck').is(':checked');

    $.ajax({
        type: 'POST',
        url: '/settings/addCategory',
        dataType: 'json',
        data: {
            postCategoryType: categoryType,
            postCategoryName: categoryName,
            postCategoryLimitState: categoryLimitState,
            postCategoryLimit: categoryLimit
        },

        success: function(result) {
            addCategoryRow(categoryName, result);
			
        },

        error: function(xhr){
            //console.log(xhr);
			errorMessage.innerText = "Podana kategoria już istnieje!"
        }
    });
}

//Update category in db
function updateCategory() {

    categoryName = $('#categoryName').val();

    categoryLimit = $('#limit').val();
    categoryLimitState = $('#limitCheck').is(':checked');

    $.ajax({
        type: 'POST',
        url: '/settings/updateCategory',
        dataType: 'json',
        data: {
            postCategoryId: categoryId,
            postCategoryType: categoryType,
            postCategoryName: categoryName,
            postCategoryLimitState: categoryLimitState,
            postCategoryLimit: categoryLimit
        },

        success: function() {
            updateCategoryRow(categoryName);
        },

        error: function(xhr){
            alert(xhr.status);
        }
    });
}
								   
								  