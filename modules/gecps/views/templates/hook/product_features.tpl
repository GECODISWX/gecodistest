{block name='product_features'}
    {if $product.grouped_features}
        <section class="product-features">
          {if $description}
            <div class="product-description ">
                <div class="rte-content">{$description nofilter}</div>
            </div><br>
          {/if}
            <dl class="data-sheet">
                {foreach from=$f item=feature}
                    {if $feature.type==1}
                    <dt class="name feature_title">{$feature.name}</dt>
                    <dd class="value feature_title"></dd>
                    {else}
                    <dt class="name">{$feature.name}</dt>
                    <dd class="value">{$feature.value|escape:'htmlall'|nl2br nofilter}</dd>

                    {/if}

                {/foreach}
            </dl>
        </section>
    {/if}
{/block}
