<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<style media="screen">
  .holder { border:1px solid }
  .row { margin: 20px 0}
</style>
<div class="">
  <div class="row">
    <div class="col-lg-6 holder">
      <h2>Download files</h2>
      <form class="" action="" method="get">
        <button type="submit" name="action" value="downloadStockFile">download stock file</button>
        <button type="submit" name="action" value="downloadProductFile">download product file</button>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6 holder">
      <h2>Stock import</h2>
      <form class="" action="/modules/gecps/scripts/sync_stock.php" method="get">
        <input type="hidden" name="key" value="zddzdjjdhffkljgkjfhmedfklgegjht">
        <button type="submit" name="action" value="importProductsStocks">Import products stocks</button>
      </form>
    </div>
    <div class="col-lg-6 holder">
      <h2>Product import</h2>
      <form class="" action="" method="get">
        <label for="from">from</label>
        <input type="text" name="from" value="0">
        <label for="to">to</label>
        <input type="text" name="to" value="99999">
        <label for="auto">auto</label>
        <input type="text" name="auto" value="0"><br>
        <label for="add_only">add_only</label>
        <input type="checkbox" name="add_only" value="1">
        <label for="add_only">diff_only</label>
        <input type="checkbox" name="diff_only" value="1">
        <label for="add_only">force</label>
        <input type="checkbox" name="force" value="1">
        <label for="reset_images">reset_images</label>
        <input type="checkbox" name="reset_images" value="1">
        <label for="ref">ref</label>
        <input type="text" name="ref" value="">
        <button type="submit" name="action" value="importProducts">Import products</button>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6 holder">
      <h2>Update Category Info</h2>
      <form class="" action="/scraper" method="get">
        <button type="submit" name="action" value="updateCategoriesInfoFromFO">updateCategoriesInfoFromFO</button>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6 holder">
      <h2>Update facet filters</h2>
      <form class="" action="/scraper" method="get">
        <button type="submit" name="action" value="updateCategoriesFilters">updateCategoriesFilters</button>
      </form>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-6 holder">
      <h2>export order</h2>
      <form class="" action="/export_orders" method="get">
        <label for="from">from</label>
        <input type="text" name="from" value="0">
        <label for="to">to</label>
        <input type="text" name="to" value="0">
        <label for="limit">limit</label>
        <input type="text" name="limit" value="0">
        <button type="submit" name="action" value="exportOrders">exportOrders</button>
      </form>
    </div>
  </div>
</div>
