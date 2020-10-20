<div class="row" ng-app="handhApp" ng-controller="MainController">
	<div class="row">
        <div class="col-lg-4">
            <h4 class="text-left"><a href="#" ng-click="toggleMe()" style="font-weight: bolder; text-decoration: none;">Scheduling</a>    <a href="#" ng-click="toggleMe()" style="font-weight: bolder; text-decoration: none;"><span style="font-weight:normal;">|    Loading</span></a></h4>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
            <h4 class="text-right"><a href="#" class="undone" style="font-weight: bolder; text-decoration: none;">Create Load   <span class="glyphicon glyphicon-fullscreen" aria-hidden="true" style="color:#7E7E7E"></span></a></h4>
        </div>
    </div>
    <div class="col-lg-12 toggle"  ng-if="toggle">
        <div class="col-lg-6" ng-include="'js/angular_modules/partials/tables/load-lists.html'"></div>
        <div class="col-lg-6 batch" ng-include="'js/angular_modules/partials/tables/loading-lists.html'"></div>
    </div>
    <div class="col-lg-12 toggle"  ng-if="!toggle" ng-include="'js/angular_modules/partials/tables/compartment-loading.html'">
    </div>
</div>