const homeController = ($scope, $location, toast) => {
    /* Redirect if invalid token */
    if (!tokenIsValid(localStorage.getItem("userToken"))) {
        toast({
            className: "alert-danger",
            message: '<i class="far fa-times-circle"></i>&nbsp; You are not logged in.' 
        });
        $location.path("/login");
    }

    $scope.logOut = () => {
        localStorage.removeItem("userToken");
        $location.path("/login");
    }
}