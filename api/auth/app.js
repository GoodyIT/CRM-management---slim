
function UserProfileCtrl($scope,$http,$interval, $rootScope){
	$http.get("api/auth/").success(function(response) {
      	$scope.UserProfileFirstname = response.firstname;
        $scope.UserProfileLastname = response.lastname;
	});
    
}



function NavigationMenuCtrl($scope,$http,$interval, $rootScope){
	$scope.menuitems = [
    {'link': 'layouts',
     'icon': 'fa fa-diamond',
     'label': 'Link 1',
    },
    {'link': 'layouts',
     'icon': 'fa fa-diamond',
     'label': 'Link 1',
    },
     {'link': 'layouts',
     'icon': 'fa fa-diamond',
     'label': 'Link 1',
    }
  ];
    
}



angular
	.module('inspinia')
	.controller('UserProfileCtrl',UserProfileCtrl)
    .controller('NavigationMenuCtrl',NavigationMenuCtrl)
