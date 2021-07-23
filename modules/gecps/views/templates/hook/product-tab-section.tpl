{if $asp_packs}
<section class="product-packs block-section">
  <h4 class="section-title"><span>{l s='Packs' mod='gecps'}</span></h4>

    <div class="tab-pane in" id="packs">
      {foreach from=$asp_packs item=asp_pack key=key name=name}
        <div class="elementor-products products row products-grid pack_products_grid">

            {foreach from=$asp_pack item="product"}
            {include file="catalog/_partials/miniatures/product.tpl" product=$product elementor=true nbMobile=6 nbTablet=4 nbDesktop=2}
            {/foreach}
            <div class="pack_products_grid_btn col-12 col-sm-2">
            {include file="catalog/_partials/miniatures/product-asp-pack-2.tpl"}
            </div>


        </div>

      {/foreach}
    </div>
</section>
{/if}
{if $asp_recommended_options}
<section class="product-recommended-options block-section">
  <h4 class="section-title"><span>{l s='Recommended option' mod='gecps'}</span></h4>

    <div class="tab-pane in" id="recommended_options">
            <div class="elementor-products">
              <div class="products slick-products-carousel products-grid slick-default-carousel slick-arrows-middle" data-slider_options='{ "slidesToShow":4,"slidesToScroll":2,"itemsPerColumn":2,"dots":true }'>
                {foreach from=$asp_recommended_options item="product"}
                {include file="catalog/_partials/miniatures/product-small-2.tpl" product=$product elementor=true nbMobile=6 nbTablet=4 nbDesktop=2}
                {/foreach}
              </div>
            </div>
    </div>

</section>
{/if}

<section id="product-details-wrapper" class="product-details-section block-section">
    <h4 class="section-title"><span>{l s='Technical sheet' mod='gecps'}</span></h4>
    <div class="section-content">
        {block name='product_details'}
            {include file='catalog/_partials/product-details.tpl'}
        {/block}
    </div>
</section>
{if $product.attachments || $asp_extermal_links}
<section class="product-attachments block-section">
  <h4 class="section-title"><span>{l s='Attachments' d='Shop.Theme.Catalog'}</span></h4>
    {block name='product_attachments'}
      <div class="tab-pane in section-content" id="attachments">
        {if $product.attachments}
          {foreach from=$product.attachments item=attachment}
              <div class="attachment">
                  <p> <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.description}</a></p>
              </div>
          {/foreach}
        {/if}
        {if $asp_extermal_links}
          {foreach from=$asp_extermal_links item=link key=key name=name}
          <div class="attachment attachment_{$link.reference}">
              <p> <a href="{$link.link}" target="_blank">{*{l s='Reference' d='Shop.Theme.Catalog'} <strong>{$link.reference}</strong> : *}{$link.title}</a></p>
          </div>
          {/foreach}
        </div>
      {/if}
    {/block}
</section>
{/if}






{if $asp_instructions}
<section class="product-instructions block-section">
  <h4 class="section-title"><span>{l s='Instructions' d='Shop.Theme.Catalog'}</span></h4>

    <div class="tab-pane in" id="instructions">
      {$asp_instructions nofilter}
    </div>

</section>
{/if}
