<div id="sub_cats_grid_sectionf" class="row" >
  {foreach from=$sub_cats item=sub_cat key=key}
    <div class="sub_cat_holder col-12 col-md-6 row">
      <div class="sub_cat_img col-12 col-md-4">
        {if $sub_cat.0.image}
          <a href="{$sub_cat.0.url}">
            <div class="grid_sub_img_holder ">
              <img src="{$sub_cat.0.image.bySize.category_default.url}" alt="{$sub_cat.0.name}"  class="img-fluid slick-slide-image"/>
            </div>

          </a>
        {else}
          <div class="grid_sub_img_holder">
            <img class="slick-slide-image" src="/modules/iqitelementor/views/images/placeholder.png" alt="Image alt" />
          </div>
        {/if}
      </div>
      <div class="sub_cat_info col-12 col-md-8">
        <a href="{$sub_cat.0.url}">
          <h2>{$sub_cat.0.name}</h2>
          {$sub_cat.0.description nofilter}
        </a>
      </div>
    </div>
  {/foreach}
</div>
