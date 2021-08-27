/**
 * Add jQuery Validation plugin method for a valid password
 * 
 * Valid passwords contain at least one letter and one number.
 */
$.validator.addMethod('validPassword',
    function(value, element, param) {

        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/) == null) {
                return false;
            }
        }

        return true;
    },
    'Password must contain at least one letter and number'
);

/*
 * Add jQuery Validation plugin method for a valid name
 *
 * Valid name has no special characters
 */
$.validator.addMethod('noSpecialChars',
    function(value, element, param) {
        if (value.match(/[^a-ząćęłńóśźżĄĘŁŃÓŚŹŻ\s]+/i)) {
            return false;
        }
        return true;
    },
    'Value contains special characters'
);

/*
 * Add jQuery Validation plugin method for a valid name
 *
 * Valid name has no white characters
 */
$.validator.addMethod('noSpaces',
    function(value, element, param) {
        if (value.match(/\s/)) {
            return false;
        }
        return true;
    },
    'Value contains spaces'
);

/**
 * Set jQuery Validation plugin default settings
 * 
 * Add bootstrap classes depending on validation state
 */
$.validator.setDefaults({
    highlight: function(element) {
        $(element).closest('.form-control').addClass('is-invalid');
    },
    unhighlight: function(element) {
        $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid')
    },
    errorElement: 'div',
    errorClass: 'invalid-feedback mt-0 mb-2',
    errorPlacement: function(error, element) {
        if(element.parent('.input-group').length) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    }
});        

/**
 * Run signup form validation when page was fully loaded
 */
$(document).ready(function() {

    /**
     * Validate the form
     */
    $('#formSignup').validate({
        rules: {
            name: {
                required: true,
                maxlength: 50,
                noSpecialChars: true,
                noSpaces: true
            },
            email: {
                required: true,
                email: true,
                remote: '/account/validate-email',
                maxlength: 50
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            },
            password_confirmation: {
                equalTo: '#password'
            }
        },
        messages: {
            name: {
                required: 'Imię jest wymagane',
                maxlength: 'Imię jest za długie',
                noSpecialChars: 'Usuń znaki spacjalne oraz cyfry',
                noSpaces: 'Imię nie może zawierać spacji'
            },
            email: {
                required: 'Email jest wymagany',
                email: 'Błędny adres email',
                remote: 'Email jest już używany',
                maxlength: 'Email jest za długi'
            },
            password: {
                required: 'Hasło jest wymagane',
                minlength: 'Wprowadź minimum 6 znaków',
                validPassword: 'Hasło musi zawierać znak oraz cyfrę'
            },
            password_confirmation: {
                required: 'Wprowadź hasło ponownie',
                equalTo: 'Hasła muszą być takie same'
            }
        }
    });
});


/**
 * Run edit account validation when page was fully loaded
 */
$(document).ready(function() {

    /**
     * Validate the form
     */
    $('#formEdit').validate({
        rules: {
            name: {
                required: true,
                maxlength: 50,
                noSpecialChars: true,
                noSpaces: true
            },
            password: {
                required: false,
                minlength: 6,
                validPassword: true
            },
            password_confirmation: {
                equalTo: '#password'
            }
        },
        messages: {
            name: {
                required: 'Imię jest wymagane',
                maxlength: 'Imię jest za długie',
                noSpecialChars: 'Usuń znaki spacjalne oraz cyfry',
                noSpaces: 'Imię nie może zawierać spacji'
            },
            password: {
                required: 'Hasło jest wymagane',
                minlength: 'Wprowadź minimum 6 znaków',
                validPassword: 'Hasło musi zawierać znak oraz cyfrę'
            },
            password_confirmation: {
                required: 'Wprowadź hasło ponownie',
                equalTo: 'Hasła muszą być takie same'
            }
        }
    });
});