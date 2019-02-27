# SSSD - Project

This is a requirements list for the project from Secure Software Systems Development course (IBU, 3rd year IT).

## Project Requirements

### Login/Registration Form

#### Registration Form
- name
- e-mail address
- username
- password
- repeat password

##### Validations
- Username - unique
- Email address - unique
- Email address validation
- No special characters for username (alphanumeric, dash, underscore)
- Password  
    - min 6 characters
    - one capital character
    - at least one special character
    - one number

#### Login Form
    - username or email
    - password

##### Validations
- captcha (after 5 incorrect login attempts)
- remember me
- 2-factor authentication (user can choose)
    - by SMS
    - by app-generated code (Google Authenticator or Authy)
    - hardware key (_BONUS +10%_)

#### Forgot Password
- username or e-mail address
- get an email with a reset link
    - expires in 5 minutes
- reset page
    - new password
    - confirm password
    - go back to login form

### Technologies

- PHP MySQL/MariaDB
- FlightPHP
- OpenAPI - documentation
- HIBP (Have I been pwned?) API - validate passwords