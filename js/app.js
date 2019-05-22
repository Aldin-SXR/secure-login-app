/** AngularJS */
let app = angular.module("sssd-app", ["ngRoute", "ngAnimate", "ngSanitize", "angularjsToast", "vcRecaptcha"]);

app.config(function ($routeProvider) {
    $routeProvider
        .when("/", {
            templateUrl: "views/login.html"
        })
        .when("/login", {
            templateUrl: "views/login.html"
        })
        .when("/register", {
            templateUrl: "views/register.html"
        }).when("/home", {
            templateUrl: "views/home.html"
        });
});

/** Controllers */
app.controller("loginController", loginController);
app.controller("registerController", registerController);
app.controller("homeController", homeController);