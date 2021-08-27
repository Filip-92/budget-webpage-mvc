<?php

namespace App;

/**
 * Messages definitions
 *
 * PHP version 7.0
 */
class Messages
{

    /**
     * Flash messages
     * @var string
     */
    const LOGIN_SUCCESS = 'Zalogowano pomyślnie';
    const LOGIN_FAIL = 'Logowanie nieudane, spróbuj ponownie';
    const LOGOUT_SUCCESS = 'Wylogowano pomślnie';
    const INCOME_ADD_SUCCESS = 'Przychód dodany pomyślnie';
    const INCOME_ADD_FAIL = 'Dodawanie przychodu nieudane, spróbuj ponownie';
    const EXPENSE_ADD_SUCCESS = 'Wydatek dodany pomyślnie';
    const EXPENSE_ADD_FAIL = 'Dodawanie wydatku nieudane, spróbuj ponownie';
    const CHANGES_SAVED = 'Zmiany zapisane';
    const BALANCE_BAD_DATA = 'Podano nieprawidłowe dane';
    const ACCESS_RESTRICTED = 'Zaloguj się, aby uzyskać dostęp do tej strony';

    /**
     * User data validation messages
     * @var string
     */
    const NAME_REQUIRED = 'Imię jest wymagane';
    const NAME_TOO_LONG = 'Imię jest za długie';
    const NAME_HAS_SPACE = 'Imię zawiera spację';
    const NAME_HAS_SPECIAL_CHAR = 'Imię zawiera znaki specjalne lub cyfry';
    const EMAIL_INVALID = 'Błędny adres email';
    const EMAIL_TAKEN = 'Email jest już używany';
    const PASSWORD_LENGTH = 'Hasło musi składać się z przynajmniej sześciu znaków';
    const PASSWORD_NEED_LETTER = 'Hasło musi zawierać przynajmniej jedną literę';
    const PASSWORD_NEED_DIGIT = 'Hasło musi zawierać przynajmniej jedną cyfrę';
    const DELETE_ACCOUNT_FAILED = "Podczas usuwania konta wystąpił błąd";

    /**
     * Add Expense & Income validation messages
     * @var string
     */
    const VALUE_REQUIRED = 'Kwota musi być podana';
    const VALUE_MUST_BE_NUMERIC = 'Kwota musi być wartością numeryczną';
    const VALUE_MUST_BE_GREATER_THAN_0 = 'Kwota musi być większa od 0';
    const METHOD_INVALID = 'Wybierz prawidłową metodę';
    const METHOD_REQUIRED = 'Metoda płatności musi być wybrana';
    const CATEGORY_INVALID = 'Wybierz prawidłową kategorię';
    const CATEGORY_REQUIRED = 'Kategoria musi być wybrana';
    const COMMENT_TOO_LONG = 'Komentarz może mieć maksymalnie 100 znaków';
    const CATEGORY_NAME_TOO_LONG = 'Nazwa kategorii może mieć maksymalnie 50 znaków';
    const CATEGORY_NAME_REQUIRED = 'Nazwa kategorii musi być podana';
    const CATEGORY_NAME_HAS_SPECIAL_CHAR = 'Nazwa kategorii zawiera znaki specjalne lub cyfry';
    const DATE_INVALID = 'Podana data jest nieprawidłowa';

}