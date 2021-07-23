<div class="home_reviews_list">
  {if $reviews}
      <div id="iqitreviews-list" class="">
          {foreach from=$reviews item="review"}
              <div class="iqitreviews-review row d-flex align-items-center" itemprop="review" itemscope itemtype="https://schema.org/Review">

                  <div class="review_product col-12 col-sm-2">
                    <a href="{$review.product_url}"><img src="{$review.cover_url}" alt=""></a>
                  </div>
                  <div class="review_content col-12 col-sm-8">

                    <div class="title" itemprop="name"><strong>{$review.title|truncate:50:'...'}</strong></div>



                    <div class="comment" itemprop="reviewBody">{$review.comment|nl2br nofilter}</div>

                    <div class="author">
                       <span itemprop="author" itemscope itemtype="https://schema.org/Person">{l s='By' d='Modules.Iqitreviews.Product-reviews'} <span itemprop="name">{$review.customer_name}</span></span>

                        {l s='on' d='Modules.Iqitreviews.Product-reviews'} <span itemprop="datePublished"> {dateFormat date=$review.date_add full=0}</span>
                    </div>
                  </div>
                  <div class="review_rating col-12 col-sm-2">
                    <div class="rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">

                        {section name="i" start=0 loop=5 step=1}
                            {if $review.rating le $smarty.section.i.index}
                                <i class="fa fa-star-o iqit-review-star"></i>
                            {else}
                                <i class="fa fa-star iqit-review-star"></i>
                            {/if}
                        {/section}
                        <meta itemprop="ratingValue" content="{$review.rating}"/>
                        <meta itemprop="bestRating" content="5"/>
                        <meta itemprop="worstRating" content="1"/>
                    </div>
                  </div>

              </div>
          {/foreach}
      </div>
  {/if}
</div>
