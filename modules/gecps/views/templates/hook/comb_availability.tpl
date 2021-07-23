{block name='product_availability'}
    {if $product.show_availability && $product.availability_message}
        <span id="product-availability"
              class="badge {if $product.availability == 'available'} {if $product.quantity <= 0  && !$product.allow_oosp} badge-danger product-unavailable  {elseif $product.quantity <= 0  && $product.allow_oosp}badge-warning product-unavailable-allow-oosp {else}badge-success product-available{/if}{elseif $product.availability == 'last_remaining_items'}badge-warning product-last-items{else}badge-danger product-unavailable{/if}">
      {if $product.availability == 'available'}
          <i class="fa fa-check rtl-no-flip" aria-hidden="true"></i>
          {if $product.stock_available >= 9999}
            {l s='Available' mod='gecps'}
          {else}
            {$product.availability_message}
          {/if}
      {elseif $product.availability == 'last_remaining_items'}
          <i class="fa fa-exclamation" aria-hidden="true"></i>
                                         {$product.availability_message}
      {else}
          {if isset($product.available_date) && $product.available_date != '0000-00-00' && $product.available_date|strtotime > $smarty.now}
            {l s='Available soon' mod='gecps'}
          {else}
            <i class="fa fa-ban" aria-hidden="true"></i>
            {$product.availability_message}
          {/if}
      {/if}
    </span>
    {/if}
{/block}
