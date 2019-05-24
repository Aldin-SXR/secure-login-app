const loginController = ($scope, $http, toast, $location) => {
    /* Variable setup */
    $scope.loginCount = 0;
    $scope.credentials = "";
    $scope.password = "";
    $scope.reCaptcha = "";
    $scope.loading = false;
    $scope.authCode = "";
    $scope.authSent = false,
    $scope.options = [
        { name: 'SMS', value: 'sms' },
        { name: 'Google OTP', value: 'otp' }
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

    /* Set  authentication method */
    $scope.setAuthenticationMethod = (method)=> {
        $scope.authMethod = method;
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
            if (!isNotEmpty($scope.reCaptcha)) {
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
            $scope.loginData = response.data.data;
        }, error => {
            /* Increase login attempts on incorrect login */
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; ' + error.data.message
            });
            $scope.loginCount++;
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
            auth_code: $scope.authCode
        };
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/verify", auth_data).then(response => {
            toast({
                className: 'alert-success',
                message: '<i class="far fa-check-circle"></i>&nbsp; ' + response.data.message
            });
            localStorage.setItem("userToken", response.data.data.jwt);
            $location.path("/home");
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