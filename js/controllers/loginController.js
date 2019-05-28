const loginController = ($scope, $http, toast, $location, vcRecaptchaService) => {
    /* Variable setup */
    $scope.loginCount = 0;
    $scope.credentials = "";
    $scope.password = "";
    $scope.reCaptcha = "";
    $scope.loading = false;
    $scope.authCode = "";
    $scope.authSent = false,
    $scope.rememberMe = false;
    $scope.options = [
        { name: 'SMS', value: 'sms' },
        { name: 'Google OTP', value: 'otp' },
        { name: 'Hardware key', value: 'fido' }
    ];
    $scope.authMethod = "sms";

    if (tokenIsValid(localStorage.getItem("userToken"))) {
        toast({
            className: "alert-info",
            message: '<i class="fas fa-info-circle"></i>&nbsp; You are already logged in.' 
        });
        $location.path("/home");
    }

    /* Set captcha response */
    $scope.setCaptchaResponse = (response) => {
        $scope.reCaptcha = response;
    }

    $scope.onCaptchaCreate = (id) => {
        $scope.captchaId = id;
    }

    /* Set  authentication method */
    $scope.setAuthenticationMethod = (method)=> {
        $scope.authMethod = method;
        $scope.authCode = "";
    }

    $scope.setRememberMe = () => {
        $scope.rememberMe = !$scope.rememberMe;
    }

    $scope.goToHome = (response) => {
        toast({
            className: 'alert-success',
            message: '<i class="far fa-check-circle"></i>&nbsp; ' + response.data.message
        });
        localStorage.setItem("userToken", response.data.data.jwt);
        $location.path("/home");
    }

    /* Load reCaptcha site key*/
    $scope.reloadCaptcha = () => {
        $scope.reCaptcha = "";
        vcRecaptchaService.reload($scope.captchaId);
    }

    $scope.logIn = () => {
        /* Check for filled fields */
        if (isEmpty($scope.credentials) || isEmpty($scope.password)) {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; You did not properly fill in all the fields.'
            });
            return;    
        }
        let credentials = {
            username: $scope.credentials,
            password: $scope.password
        };
        /* Handle captcha */
        if ($scope.loginCount >= 5) {
            credentials.captcha_response = $scope.reCaptcha;
            /* Prevent login on empty captcha */
            if (isEmpty($scope.reCaptcha)) {
                toast({
                    className: 'alert-danger',
                    message: '<i class="far fa-times-circle"></i>&nbsp; You did not complete the captcha.'
                });
                return;    
            }
        }
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/login", credentials).then(response => {
            /* Handle bypassed authorization (Received JWT) */
            if (response.data.data.jwt) {
                $scope.goToHome(response);
            } else {
                /* Receive authorization data */
                $scope.loginData = response.data.data;
            }
        }, error => {
            /* Increase login attempts on incorrect login */
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; ' + error.data.message
            });
            $scope.loginCount++;
            /* Reload captcha if failed */
            if ($scope.loginCount > 5) {
                $scope.reloadCaptcha();
            }
        }).finally(() => {
            $scope.loading = false;
        });
    }

    /* Send SMS authentication code */
    $scope.sendAuthCode = () => {
        $scope.loading = true;
        let auth_data = {
            login_hash: $scope.loginData.login_hash
        };
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/sms", auth_data).then(response => {
            toast({
                className: 'alert-success',
                message: '<i class="far fa-check-circle"></i>&nbsp; ' + response.data.message
            });
            $scope.authSent = true;
        }, error => {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; ' + error.data.message
            });
        }).finally(() => {
            $scope.loading = false;
        });
    }

    /** Verify login attempt */
    $scope.verify = () => {
        let auth_data = {
            login_hash: $scope.loginData.login_hash,
            auth_type: $scope.authMethod,
            auth_code: $scope.authCode,
            remember_me: $scope.rememberMe
        };
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/verify", auth_data).then(response => {
            $scope.goToHome(response);
        }, error => {
            /* Increase login attempts on incorrect login */
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; ' + error.data.message
            });
        }).finally(() => {
            $scope.loading = false;
        });
    }

    $scope.backToLogin = () => {
        $scope.loginCount = 0;
        $scope.credentials = "";
        $scope.password = "";
        $scope.reCaptcha = "";
        $scope.loading = false;
        $scope.authCode = "";  
        $scope.loginData = "";  
        $scope.authSent = false;
    }
}