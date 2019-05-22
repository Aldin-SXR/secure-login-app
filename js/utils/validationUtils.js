const isEmpty = (field) => {
    return field && field !== "" && field !== null && field !== [];
}

/** jQuery. */
$(document).ready(function (e) {
    $('.ui.form')
        .form({
            fields: {
                credentials: {
                    identifier: 'credentials',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please enter your username or e-mail address.'
                        }
                    ]
                },
                name: {
                    identifier: 'name',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please enter your name and surname.'
                        }
                    ]
                },
                user_name: {
                    identifier: 'user_name',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please enter your username.'
                        },
                        {
                            type: 'regExp',
                            value: /^[a-zA-Z0-9_-]+$/,
                            prompt: 'Only alphanumeric characters, - and _ are allowed in the username.'
                        }
                    ]
                },
                email_address: {
                    identifier: 'email_address',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please enter your e-mail address.'
                        },
                        {
                            type: 'email',
                            prompt: 'Please enter a valid e-mail address.'
                        }
                    ]
                },
                phone_number: {
                    identifier: 'phone_number',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please enter your phone number.'
                        }
                    ]
                },
                password: {
                    identifier: 'password',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please enter your password.'
                        },
                        {
                            type: 'regExp',
                            value: /(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[-+_!@#$%^&*.,?])/,
                            prompt: 'The password should contain at least one lowercase letter, one uppercase letter, one special character and one number.'
                        },
                        {
                            type: 'length[6]',
                            prompt: 'Your password must be at least 6 characters long.'
                        }
                    ]
                },
                rePassword: {
                    identifier: 'rePassword',
                    rules: [
                        {
                            type: 'empty',
                            prompt: 'Please re-type your password.'
                        }
                    ]
                }
            }, 
            onSuccess: function(e) {
                e.preventDefault();
            }
        });
});