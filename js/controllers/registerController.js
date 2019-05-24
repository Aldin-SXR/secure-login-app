const registerController = ($scope, $http, $location, toast) => {
    $scope.name = "";
    $scope.user_name = "";
    $scope.email_address = "";
    $scope.phone_number = "";
    $scope.password = "";
    $scope.rePassword = "";
    $scope.loading = false;
    $scope.otpLink = ""; 

    $scope.register = () => {
        /* Check empty fields */
        if (isEmpty($scope.name) || isEmpty($scope.password) || isEmpty($scope.user_name) || 
            isEmpty($scope.email_address) || isEmpty($scope.phone_number) || isEmpty($scope.rePassword)) {   
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

        let user = {
            name: $scope.name,
            user_name: $scope.user_name,
            email_address: $scope.email_address,
            phone_number: $scope.phone_number,
            password: $scope.password
        };
    
        /* Handle API endpoint call */
        $scope.loading = true;
        $http.post(API_URL + "/register", user).then(response => {
            $scope.registerData = response.data.data;
            toast({
                className: 'alert-success',
                message: '<i class="far fa-check-circle"></i>&nbsp; ' + response.data.message
            });
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

    $scope.goToLogin = () => {
        $location.path("/login");
    }

    $scope.backToRegistration = () => {
        $scope.name = "";
        $scope.user_name = "";
        $scope.email_address = "";
        $scope.phone_number = "";
        $scope.password = "";
        $scope.rePassword = "";
        $scope.loading = false;
        $scope.otpLink = ""; 
        $scope.registerData = null;
    }
}