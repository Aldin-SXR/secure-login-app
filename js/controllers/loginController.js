const loginController = ($scope, $http, toast) => {
    $scope.loginCount = 0;
    $scope.credentials = "";
    $scope.password = "";
    $scope.reCaptcha = "";
    $scope.loading = false;

    /* Set captcha response */
    $scope.setCaptchaResponse = (response) => {
        $scope.reCaptcha = response;
    }

    $scope.logIn = () => {
        /* Check for filled fields */
        if (!isEmpty($scope.credentials) && !isEmpty($scope.password)) {
            return;    
        }
        let credentials = {
            username: $scope.credentials,
            password: $scope.password
        };
        /* Handle captcha */
        if ($scope.loginCount >= 5) {
            credentials.captcha_response = $scope.reCaptcha;
        }
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/login", credentials).then(response => {
            toast({
                className: 'alert-success',
                message: response.data.message
            });
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