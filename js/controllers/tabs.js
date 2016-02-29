angular.module('MakerMRP')

//////////////////////////////////////////////////////////////////////
// TABS Controller
//
.controller('TabsController', function($scope, $http){
  $scope.tabs = [
    {
      link: "#/products",
      label: "products"
    },
    {
      link: "#/parts",
      label: "parts"
    },
    {
      link: "#/types",
      label: "types"
    },
    {
      link: "#/subtypes",
      label: "subtypes"
    }
  ];
  $scope.selected = $scope.tabs[0];

  $scope.setSelectedTab = function(tab){
    $scope.selected = tab;
  };

  $scope.tabClass = function(tab){
    return (tab.label === $scope.selected.label) ? "active" : "";
  };

});
