<?php

final class ErrorCodes extends Enum
{
    public const LOGIN_USERNAME_NOTFOUND = 'Gebruikersnaam of Wachtwoord incorrect';
    public const LOGIN_PASSWORD_INCORRECT = 'Gebruikersnaam of Wachtwoord incorrect';
    public const LOGIN_USERNAME_NOT_ENTERED = 'Gebruikernaam niet ingevuld';
    public const LOGIN_PASSWORD_NOT_ENTERED = 'Wachtwoord niet ingevuld';
    public const LOGIN_JWT_SIGNATURE_INVALID = 'De JWT-token heeft een invalide handtekening';

    public const CHANGE_PASSWORD_NOT_AUTHENTICATED = 'U bent niet ingelogd. Wachtwoord kan niet worden veranderd';
    public const CHANGE_PASSWORD_CURRENT_NOT_ENTERED = 'Huidige wachtwoord niet ingevuld';
    public const CHANGE_PASSWORD_NEW_NOT_ENTERED = 'Nieuwe wachtwoord niet ingevuld';
    public const CHANGE_PASSWORD_CONFIRM_NOT_ENTERED = 'Bevestig nieuwe wachtwoord niet ingevuld';
    public const CHANGE_PASSWORD_INCORRECT_CURRENT = 'Huidige wachtwoord klopt niet';
    public const CHANGE_PASSWORD_NEWPASSWORDS_DIFFERENT = 'Het nieuwe wachtwoord en bevestig wachtwoord komen niet overeen';

    public const APPOINTMENT_NO_ID_GIVEN = 'Afspraak ID niet meegeleverd';

    public const PERMISSION_CANT_VIEW_PAGE = "Jij kan deze pagina niet bekijken";
    public const PERMISSION_CANT_SHOW_APPOINTMENT_DATA = "Jij kan geen afspraak-gegevens bekijken";
    public const PERMISSION_CANT_SHOW_CUSTOMER_DATA = "Jij kan geen klant-gegevens bekijken";
    public const PERMISSION_CANT_SHOW_ALL_CUSTOMERS = "Jij kan niet de lijst met klanten bekijken";
    public const PERMISSION_CANT_SHOW_ACCOUNTS = "Jij kan niet de lijst met accounts bekijken";
    public const PERMISSION_CANT_SHOW_ACCOUNTTYPES = "Jij kan niet de lijst met account-types bekijken";
}