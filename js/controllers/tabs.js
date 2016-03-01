angular.module('MakerMRP')

//////////////////////////////////////////////////////////////////////
// TABS Controller
//
.controller('TabController', function($scope){
  $scope.tabs = [
    {
      link: "products",
      label: "products"
    },
    {
      link: "parts",
      label: "parts"
    },
    {
      link: "types",
      label: "types"
    },
    {
      link: "subtypes",
      label: "subtypes"
    }
  ];
});
