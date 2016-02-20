angular.module('MakerMRP')
.directive('mmrpTable', function(){
  return {
    restrict: 'E',
    templateUrl: "templates/table.html",
    controller: function($scope, $http) {
      $http.get($scope.api).then(function(response){
        // For JSON responses, response.data contains the result
        $scope.columns = response.data.columns;
        $scope.data = response.data.data;
        console.log('Success', $scope.data);
      }, function(err) {
        console.error('Error', err.status);
        $scope.errors = err.status;
        // err.status will contain the status code
      });
    },
    scope: {
      api: "@",
    }
  };
});
