const recoverController = ($scope, $http, $location, toast, $routeParams, $timeout) => {
    $scope.loading = false;
    $scope.tokenIsValid = false;
    $scope.emailAddress = "";
    $scope.password = "";
    $scope.rePassword = "";
    
    /* If token ID is set */
    if ($routeParams.token) {
        $scope.loading = true;
        $http.get(API_URL + "/recover/" + $routeParams.token + "/validate").then(response => {
            $scope.tokenIsValid = true;
        }, error => {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; ' + error.data.message
            });
            $location.path("/login");
            console.log(error);
        }).finally(() => {
            $scope.loading = false;
        });
    }

    $scope.sendRecoveryRequest = () => {
        /* Check for filled fields */
        if (isEmpty($scope.emailAddress)) {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; You did not properly fill in all the fields.'
            });
            return;    
        }
        $scope.loading = true;
        let recovery_request = {
            email_address: $scope.emailAddress
        };

        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/recover", recovery_request).then(response => {
            toast({
                className: 'alert-success',
                message: '<i class="far fa-check-circle"></i>&nbsp; ' + response.data.message
            });
            $timeout(() => {
                $location.path("/login")
            }, 1000);
        }, error => {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; ' + error.data.message
            });
        }).finally(() => {
            $scope.loading = false;
        });
    }

    $scope.changePassword = () => {
        if (isEmpty($scope.password) || isEmpty($scope.rePassword)) {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; You did not properly fill in all the fields.'
            });
            return;    
        }

        /* Validate matching passwords */
        if ($scope.password !== $scope.rePassword) {
            toast({
                className: 'alert-danger',
                message: '<i class="far fa-times-circle"></i>&nbsp; The entered password do not match.'
            });
            return;      
        }

        let password_recovery = {
            recovery_token: $routeParams.token,
            password: $scope.password
        };
    
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/recover/password", password_recovery).then(response => {
            toast({
                className: 'alert-success',
                message: '<i class="far fa-check-circle"></i>&nbsp; ' + response.data.message
            });
            $timeout(() => {
                $location.path("/login")
            }, 1000);
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
}