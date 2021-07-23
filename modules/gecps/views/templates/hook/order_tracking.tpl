{block name='order_carriers'}
  {if $order.shipping}
    <div class="box">
      <table class="table table-striped table-bordered hidden-sm-down">
        <thead class="thead-default">
          <tr>
            <th>{l s='Product' d='Shop.Theme.Checkout'}</th>
            <th>{l s='Quantity' d='Shop.Theme.Checkout'}</th>
            <th>{l s='Carrier' d='Shop.Theme.Checkout'}</th>
            <th>{l s='Date' d='Shop.Theme.Global'}</th>
            <th>{l s='Tracking URL' d='Shop.Theme.Checkout'}</th>
            <th>{l s='Tracking number' d='Shop.Theme.Checkout'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$order_tracking item=tracking}
            <tr>
              {foreach from=$order.products item=product}
                {if $product.id_product == $tracking.id_product && $product.id_product_attribute == $tracking.id_product_attribute}
                  <td>
                    <div class="row">
                        {if isset($product.cover.bySize.cart_default.url)}
                            <div class="col-2">
                                <a href="{url entity='product' id=$product.product_id ipa=$product.id_product_attribute}">
                                    <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name}" class="img-fluid"/>
                                </a>
                            </div>
                        {/if}
                        <div class="col-10">
                            {if $product.product_reference}
                                {l s='Reference' d='Shop.Theme.Catalog'}: {$product.product_reference}
                            {/if}
                        </div>
                    </div>
                  </td>
                  <td>{$tracking.quantity}</td>
                  <td>{$tracking.carrier_name}</td>
                  <td>{$tracking.date}</td>
                  <td><a target="_blank" href="{$tracking.url_tracking}">{$tracking.url_tracking}</a></td>
                  <td>{$tracking.tracking_number}</td>
                {/if}
              {/foreach}
            </tr>
          {/foreach}
        </tbody>
      </table>
      <div class="hidden-md-up shipping-lines">
        {foreach from=$order_tracking item=tracking}
          <div class="shipping-line">
            {foreach from=$order.products item=product}
              {if $product.id_product == $tracking.id_product && $product.id_product_attribute == $tracking.id_product_attribute}
                <ul>
                  <li>
                    <div class="row">
                        {if isset($product.cover.bySize.cart_default.url)}
                            <div class="col-6">
                                <a href="{url entity='product' id=$product.product_id ipa=$product.id_product_attribute}">
                                    <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name}" class="img-fluid"/>
                                </a>
                            </div>
                        {/if}
                        <div class="col-6">
                            {if $product.product_reference}
                                {l s='Reference' d='Shop.Theme.Catalog'}: {$product.product_reference}
                            {/if}
                        </div>
                    </div>
                  </li>
                  <li><strong>{l s='Quantity' d='Shop.Theme.Checkout'} :</strong> {$tracking.quantity}</li>
                  <li><strong>{l s='Carrier' d='Shop.Theme.Checkout'} :</strong> {$tracking.carrier_name}</li>
                  <li><strong>{l s='Date' d='Shop.Theme.Checkout'} :</strong> {$tracking.date}</li>
                  <li><strong>{l s='Tracking URL' d='Shop.Theme.Checkout'} :</strong> <a target="_blank" href="{$tracking.url_tracking}">{$tracking.url_tracking}</a></li>
                  <li><strong>{l s='Tracking number' d='Shop.Theme.Checkout'} :</strong> {$tracking.tracking_number}</li>
                </ul>
                <br><hr><br>

              {/if}
            {/foreach}
          </div>
        {/foreach}
      </div>
    </div>
  {/if}
{/block}
