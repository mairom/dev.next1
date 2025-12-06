<md-toolbar class="toolbar-white">
  <div class="md-toolbar-tools">
    <md-button class="md-icon-button" aria-label="File">
      <md-icon><i class="ion-document text-muted"></i></md-icon>
    </md-button>
    <h2 flex md-truncate><?php echo lang2('phasecodes'); ?></h2>
    <div class="ciuis-external-search-in-table">
      <input ng-model="filter.phase_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>">
      <md-button class="md-icon-button" aria-label="Search">
        <md-icon><i class="ion-search text-muted"></i></md-icon>
      </md-button>
    </div>
    <md-button ng-click="Create()" class="md-icon-button" aria-label="New">
      <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
      <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
    </md-button>
  </div>
</md-toolbar>

<!-- Use this md-table-container, if you want to implement table only, but don't forgot to match search input model with table filter  -->
<md-table-container>
  <table md-table md-progress="promise">
    <thead md-head md-order="query.order">
      <tr md-row>
        <th md-column md-order-by="phase_code_number"><span>Phase</span></th>
        <th md-column md-order-by="phase_code_description"><span>Description</span></th>
      </tr>
    </thead>
    <tbody md-body>
      <tr md-row md-select="phasecode" md-select-id="name" md-auto-select ng-repeat="phasecode in phasecodes | orderBy: query.order | limitTo: query.limit : (query.page -1) * query.limit | filter: filter.phase_search" class="cursor">
        <td md-cell ng-bind="phasecode.phase_code_number"></td>
        <td md-cell ng-bind="phasecode.phase_code_description"></td>
      </tr>
    </tbody>
  </table>
</md-table-container>
<md-table-pagination md-limit="query.limit" md-limit-options="limitOptions" md-page="query.page" md-total="{{phasecodes.length}}" ></md-table-pagination>

<script type="text/javascript">
  // ====================
  // ===Angular Script===
  // ====================


  // ==start== To display the progress-bar in table head
  var deferred = $q.defer(); // Don't forget to include $q in controller function, i.e. where the $scope is defined
  $scope.promise = deferred.promise;
  // ==end==

  // ==start== To hide OR stop the progress-bar
  deferred.resolve();
  // ==end==

  // ==start== Paste this in your controller or function 
  $scope.query = {
    order: 'name',
    limit: 5,
    page: 1
  };
  // ==end==

  // ==start== Page limit options
  $scope.limitOptions = [5, 10, 15, 20];
  // ==end==
</script>
