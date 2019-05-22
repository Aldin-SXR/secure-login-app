const loginController = ($scope, $http, toast) => {
    $scope.loginCount = 0;
    $scope.emailAddress = "";
    $scope.password = "";
    $scope.reCaptcha = "";
    $scope.loading = false;

    /* Set captcha response */
    $scope.setCaptchaResponse = (response) => {
        $scope.reCaptcha = response;
    }

    $scope.logIn = () => {
        /* Check for filled fields */
        if (!isEmpty($scope.emailAddress) && !isEmpty($scope.password)) {
            return;    
        }
        /* Handle captcha */
        let captcha = { };
        if ($scope.loginCount >= 5) {
            captcha.captcha_response = $scope.reCaptcha;
        }
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/login", captcha).then(response => {
            console.log(response);
        }, error => {
            /* Increase login attempts on incorrect login */
            toast({
                className: 'alert-danger',
                message: error.data.message
            });
            $scope.loginCount++;
        }).finally(() => {
            $scope.loading = false;
        });
    }
}